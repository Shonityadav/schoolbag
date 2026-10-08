<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\AssignedEbook;
use Illuminate\Support\Facades\Auth;

class AssignedEbookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** World-map grid of all subjects for this student's class */
    public function index(\Illuminate\Http\Request $request)
    {
        $user    = Auth::user();
        $query = AssignedEbook::where('user_id', $user->id)
                         ->where('is_active', true);
                         
        if ($request->has('subject')) {
            $query->where('subject', 'like', '%' . $request->subject . '%');
        }

        $courses = $query->withCount('chapters')
                         ->orderBy('order')
                         ->get();

        return view('student.assigned_ebooks.index', compact('user', 'courses'));
    }

    public function details(int $id)
    {
        $user    = Auth::user();
        $course  = AssignedEbook::where('user_id', $user->id)->findOrFail($id);

        return view('student.assigned_ebooks.details', compact('user', 'course'));
    }

    /** Chapter islands for a single course */
    public function show(int $id)
    {
        $user    = Auth::user();
        $course  = AssignedEbook::where('user_id', $user->id)->findOrFail($id);
        $chapters = $course->chapters()->with(['lessons'])->get();

        // Determine unlock state for each chapter
        $chaptersData = $chapters->map(function ($chapter) use ($user) {
            return [
                'chapter'   => $chapter,
                'unlocked'  => $chapter->isUnlockedFor($user),
                'completed' => $chapter->isCompletedBy($user),
                'lessons_done' => $chapter->lessons->filter(fn($l) => $l->isCompletedBy($user))->count(),
                'lessons_total' => $chapter->lessons->count(),
            ];
        });

        return view('student.assigned_ebooks.show', compact('user', 'course', 'chaptersData'));
    }

    public function stage(\Illuminate\Http\Request $request, int $id, int $chapter_id, int $stage)
    {
        $user = Auth::user();
        $course = AssignedEbook::where('user_id', $user->id)->findOrFail($id);
        
        $chapter = \App\Models\Chapter::where('course_id', $id)->findOrFail($chapter_id);



        $lesson = \App\Models\Lesson::where('chapter_id', $chapter_id)
            ->orderBy('order')
            ->skip($stage - 1)
            ->firstOrFail();

        $isCompleted = $lesson->isCompletedBy($user);
        $nextLesson = \App\Models\Lesson::where('chapter_id', $lesson->chapter_id)
                             ->where('order', '>', $lesson->order)
                             ->orderBy('order')
                             ->first();

        // Fetch ebook pages (use dynamic ebook_id from course if available, fallback to 2)
        $ebookPages = [];
        $mcqs = [];
        $matchPairs = [];
        $generationError = null;
        $isGenerating = false;
        $shortQuestions = [];
        
        if ($stage == 2) {
            // Stage 2: Hard Words -> MCQs
            $mcqs = [];
            $ebookId = $course->getResolvedEbookId();
            
            if ($ebookId) {
                $ebookChapter = \App\Models\EbookChapter::where('ebook_id', $ebookId)
                    ->where('chapter_number', $chapter->order + 1)
                    ->first();

                if ($ebookChapter) {
                    $ebookChapterStage = \App\Models\EbookChapterStage::where('ebook_chapter_id', $ebookChapter->id)
                        ->where('stage_number', 2)
                        ->first();

                    if ($ebookChapterStage) {
                        $quesType = \App\Models\QuestionType::where('type', 'like', '%Multiple%')->orWhere('type', 'MCQ')->first();
                        $quesTypeId = $quesType ? $quesType->id : null;

                        $dbQuestions = \App\Models\EbookQuestion::where('ebook_id', $ebookId)
                            ->where('chapter_id', $ebookChapter->id)
                            ->where('stage_id', $ebookChapterStage->id)
                            ->get();

                        if ($dbQuestions->isNotEmpty()) {
                            $mcqs = $dbQuestions->map(function ($q) {
                                $qData = is_string($q->question) ? json_decode($q->question, true) : $q->question;
                                return [
                                    'question' => $qData['question'] ?? 'Question missing',
                                    'options' => $qData['options'] ?? [],
                                    'correct' => (int) $q->answer
                                ];
                            })->toArray();
                        } else {
                            if (\Illuminate\Support\Facades\Cache::has('generating_chapter_' . $ebookChapter->id . '_stage_2')) {
                                $isGenerating = true;
                            } else {
                                \Illuminate\Support\Facades\Cache::put('generating_chapter_' . $ebookChapter->id . '_stage_2', true, 300);
                                try {
                                    $pages = $this->getEbookPagesArray($course, $ebookId, $ebookChapter);
                                    $generatedMcqs = $this->generateHardWordsFromGemini($pages);

                                    if ($generatedMcqs && is_array($generatedMcqs)) {
                                        \Illuminate\Support\Facades\Log::info('Gemini generated ' . count($generatedMcqs) . ' questions successfully.');
                                        $mcqs = $generatedMcqs;

                                        foreach ($mcqs as $mcq) {
                                            \App\Models\EbookQuestion::create([
                                                'ebook_id' => $ebookId,
                                                'chapter_id' => $ebookChapter->id,
                                                'stage_id' => $ebookChapterStage->id,
                                                'ques_type_id' => $quesTypeId,
                                                'question' => json_encode([
                                                    'question' => $mcq['question'],
                                                    'options' => $mcq['options']
                                                ]),
                                                'answer' => (string) $mcq['correct'],
                                                'subject' => $course->title
                                            ]);
                                        }
                                    } else {
                                        \Illuminate\Support\Facades\Log::error('Gemini generated null or invalid array.');
                                        $generationError = "Our AI is currently taking a break! Please try refreshing the page in a few moments to generate the Hard Words.";
                                    }
                                } finally {
                                    \Illuminate\Support\Facades\Cache::forget('generating_chapter_' . $ebookChapter->id . '_stage_2');
                                }
                            }
                        }
                    } else {
                        \Illuminate\Support\Facades\Log::error('EbookChapterStage not found for chapter ' . $ebookChapter->id);
                    }
                } else {
                    \Illuminate\Support\Facades\Log::error('EbookChapter not found for ebook ' . $ebookId . ' and chapter_number ' . ($chapter->order + 1));
                }
            }
        } elseif ($stage == 3) {
            // Stage 3: Activity Mission -> Match the Following
            $matchPairs = [];
            $ebookId = $course->getResolvedEbookId();
            
            if ($ebookId) {
                $ebookChapter = \App\Models\EbookChapter::where('ebook_id', $ebookId)
                    ->where('chapter_number', $chapter->order + 1)
                    ->first();

                if ($ebookChapter) {
                    $ebookChapterStage = \App\Models\EbookChapterStage::where('ebook_chapter_id', $ebookChapter->id)
                        ->where('stage_number', 3)
                        ->first();

                    if ($ebookChapterStage) {
                        $quesType = \App\Models\QuestionType::where('type', 'like', '%Match%')->orWhere('type', 'Activity')->first();
                        $quesTypeId = $quesType ? $quesType->id : null;

                        $dbQuestions = \App\Models\EbookQuestion::where('ebook_id', $ebookId)
                            ->where('chapter_id', $ebookChapter->id)
                            ->where('stage_id', $ebookChapterStage->id)
                            ->get();

                        if ($dbQuestions->isNotEmpty()) {
                            $matchPairs = $dbQuestions->map(function ($q) {
                                return [
                                    'left' => $q->question,
                                    'right' => $q->answer
                                ];
                            })->toArray();
                        } else {
                            if (\Illuminate\Support\Facades\Cache::has('generating_chapter_' . $ebookChapter->id . '_stage_3')) {
                                $isGenerating = true;
                            } else {
                                \Illuminate\Support\Facades\Cache::put('generating_chapter_' . $ebookChapter->id . '_stage_3', true, 300);
                                try {
                                    $pages = $this->getEbookPagesArray($course, $ebookId, $ebookChapter);
                                    $generatedPairs = $this->generateActivityFromGemini($pages);

                                    if ($generatedPairs && is_array($generatedPairs)) {
                                        \Illuminate\Support\Facades\Log::info('Gemini generated ' . count($generatedPairs) . ' match pairs successfully.');
                                        $matchPairs = $generatedPairs;

                                        foreach ($matchPairs as $pair) {
                                            \App\Models\EbookQuestion::create([
                                                'ebook_id' => $ebookId,
                                                'chapter_id' => $ebookChapter->id,
                                                'stage_id' => $ebookChapterStage->id,
                                                'ques_type_id' => $quesTypeId,
                                                'question' => $pair['left'],
                                                'answer' => $pair['right'],
                                                'subject' => $course->title
                                            ]);
                                        }
                                    } else {
                                        $generationError = "Our AI is currently taking a break! Please try refreshing the page in a few moments to generate the Activity.";
                                    }
                                } finally {
                                    \Illuminate\Support\Facades\Cache::forget('generating_chapter_' . $ebookChapter->id . '_stage_3');
                                }
                            }
                        }
                    }
                }
            }
        } elseif ($stage == 4) {
            // Stage 4: Exercise Mission -> Short Subjective Questions
            $shortQuestions = [];
            $ebookId = $course->getResolvedEbookId();
            
            if ($ebookId) {
                $ebookChapter = \App\Models\EbookChapter::where('ebook_id', $ebookId)
                    ->where('chapter_number', $chapter->order + 1)
                    ->first();

                if ($ebookChapter) {
                    $ebookChapterStage = \App\Models\EbookChapterStage::where('ebook_chapter_id', $ebookChapter->id)
                        ->where('stage_number', 4)
                        ->first();

                    if ($ebookChapterStage) {
                        $quesType = \App\Models\QuestionType::where('type', 'like', '%Subjective%')->orWhere('type', 'Short Answer')->first();
                        $quesTypeId = $quesType ? $quesType->id : null;

                        $dbQuestions = \App\Models\EbookQuestion::where('ebook_id', $ebookId)
                            ->where('chapter_id', $ebookChapter->id)
                            ->where('stage_id', $ebookChapterStage->id)
                            ->get();

                        if ($dbQuestions->isNotEmpty()) {
                            $shortQuestions = $dbQuestions->map(function ($q) {
                                return [
                                    'id' => $q->id,
                                    'question' => $q->question
                                ];
                            })->toArray();
                        } else {
                            if (\Illuminate\Support\Facades\Cache::has('generating_chapter_' . $ebookChapter->id . '_stage_4')) {
                                $isGenerating = true;
                            } else {
                                \Illuminate\Support\Facades\Cache::put('generating_chapter_' . $ebookChapter->id . '_stage_4', true, 300);
                                try {
                                    $pages = $this->getEbookPagesArray($course, $ebookId, $ebookChapter);
                                    $generatedQs = $this->generateShortQuestionsFromGemini($pages);

                                    if ($generatedQs && is_array($generatedQs)) {
                                        \Illuminate\Support\Facades\Log::info('Gemini generated ' . count($generatedQs) . ' short questions successfully.');
                                        
                                        foreach ($generatedQs as $q) {
                                            $created = \App\Models\EbookQuestion::create([
                                                'ebook_id' => $ebookId,
                                                'chapter_id' => $ebookChapter->id,
                                                'stage_id' => $ebookChapterStage->id,
                                                'ques_type_id' => $quesTypeId,
                                                'question' => $q['question'],
                                                'answer' => null,
                                                'subject' => $course->title
                                            ]);
                                            $shortQuestions[] = [
                                                'id' => $created->id,
                                                'question' => $q['question']
                                            ];
                                        }
                                    } else {
                                        $generationError = "Our AI is currently taking a break! Please try refreshing the page in a few moments to generate the Exercise Questions.";
                                    }
                                } finally {
                                    \Illuminate\Support\Facades\Cache::forget('generating_chapter_' . $ebookChapter->id . '_stage_4');
                                }
                            }
                        }
                    }
                }
            }
        } else {
            // Other Stages -> Ebook Pages
            $ebookId = $course->getResolvedEbookId();
            $ebookPages = collect();
            
            if ($ebookId) {
                $ebookChapter = \App\Models\EbookChapter::where('ebook_id', $ebookId)
                                    ->where('chapter_number', $chapter->order + 1)
                                    ->first();

                if ($ebookChapter && $ebookChapter->start_page) {
                    $ebookPages = $this->getEbookPagesArray($course, $ebookId, $ebookChapter);
                }
            }
        }

        // Pass course_id and stage for the next buttons
        return view('student.lessons.showdetails', compact('user', 'lesson', 'isCompleted', 'nextLesson', 'course', 'stage', 'chapter_id', 'ebookPages', 'mcqs', 'matchPairs', 'shortQuestions', 'generationError', 'isGenerating'));
    }

    public function generateRemainingStages(int $id, int $chapter_id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Release session lock so the UI isn't blocked for other requests while Gemini generates content
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $course = AssignedEbook::find($id);
        if (!$course || $course->user_id !== $user->id) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        $chapter = \App\Models\Chapter::where('course_id', $id)->find($chapter_id);
        if (!$chapter) {
            return response()->json(['error' => 'Chapter not found'], 404);
        }
        $course = AssignedEbook::findOrFail($id);
        $chapter = \App\Models\Chapter::where('course_id', $id)->findOrFail($chapter_id);
        $ebookId = $course->getResolvedEbookId();
        
        $ebookChapter = \App\Models\EbookChapter::where('ebook_id', $ebookId)
            ->where('chapter_number', $chapter->order + 1)
            ->first();

        if (!$ebookChapter) {
            return response()->json(['error' => 'Ebook chapter not found'], 404);
        }

        \Illuminate\Support\Facades\Cache::put('generating_chapter_' . $ebookChapter->id . '_stage_2', true, 300);
        \Illuminate\Support\Facades\Cache::put('generating_chapter_' . $ebookChapter->id . '_stage_3', true, 300);
        \Illuminate\Support\Facades\Cache::put('generating_chapter_' . $ebookChapter->id . '_stage_4', true, 300);

        // Fetch pages needed for generation
        $pages = $this->getEbookPagesArray($course, $ebookId, $ebookChapter);

        // Stage 2 (Hard Words)
        $stage2 = \App\Models\EbookChapterStage::where('ebook_chapter_id', $ebookChapter->id)->where('stage_number', 2)->first();
        if ($stage2) {
            $hasQuestions = \App\Models\EbookQuestion::where('ebook_id', $ebookId)
                ->where('chapter_id', $ebookChapter->id)
                ->where('stage_id', $stage2->id)
                ->exists();
                
            if (!$hasQuestions) {
                $mcqs = $this->generateHardWordsFromGemini($pages);
                if ($mcqs && is_array($mcqs)) {
                    $quesType = \App\Models\QuestionType::where('type', 'like', '%Multiple%')->orWhere('type', 'MCQ')->first();
                    foreach ($mcqs as $mcq) {
                        \App\Models\EbookQuestion::create([
                            'ebook_id' => $ebookId,
                            'chapter_id' => $ebookChapter->id,
                            'stage_id' => $stage2->id,
                            'ques_type_id' => $quesType ? $quesType->id : null,
                            'question' => json_encode([
                                'question' => $mcq['question'],
                                'options' => $mcq['options']
                            ]),
                            'answer' => (string) $mcq['correct'],
                            'subject' => $course->title
                        ]);
                    }
                }
            }
        }

        // Stage 3 (Activity)
        $stage3 = \App\Models\EbookChapterStage::where('ebook_chapter_id', $ebookChapter->id)->where('stage_number', 3)->first();
        if ($stage3) {
            $hasQuestions = \App\Models\EbookQuestion::where('ebook_id', $ebookId)
                ->where('chapter_id', $ebookChapter->id)
                ->where('stage_id', $stage3->id)
                ->exists();
                
            if (!$hasQuestions) {
                $matchPairs = $this->generateActivityFromGemini($pages);
                if ($matchPairs && is_array($matchPairs)) {
                    $quesType = \App\Models\QuestionType::where('type', 'like', '%Match%')->orWhere('type', 'Activity')->first();
                    foreach ($matchPairs as $pair) {
                        \App\Models\EbookQuestion::create([
                            'ebook_id' => $ebookId,
                            'chapter_id' => $ebookChapter->id,
                            'stage_id' => $stage3->id,
                            'ques_type_id' => $quesType ? $quesType->id : null,
                            'question' => $pair['left'],
                            'answer' => $pair['right'],
                            'subject' => $course->title
                        ]);
                    }
                }
            }
        }

        // Stage 4 (Exercise)
        $stage4 = \App\Models\EbookChapterStage::where('ebook_chapter_id', $ebookChapter->id)->where('stage_number', 4)->first();
        if ($stage4) {
            $hasQuestions = \App\Models\EbookQuestion::where('ebook_id', $ebookId)
                ->where('chapter_id', $ebookChapter->id)
                ->where('stage_id', $stage4->id)
                ->exists();
                
            if (!$hasQuestions) {
                $shortQs = $this->generateShortQuestionsFromGemini($pages);
                if ($shortQs && is_array($shortQs)) {
                    $quesType = \App\Models\QuestionType::where('type', 'like', '%Subjective%')->orWhere('type', 'Short Answer')->first();
                    foreach ($shortQs as $q) {
                        \App\Models\EbookQuestion::create([
                            'ebook_id' => $ebookId,
                            'chapter_id' => $ebookChapter->id,
                            'stage_id' => $stage4->id,
                            'ques_type_id' => $quesType ? $quesType->id : null,
                            'question' => $q['question'],
                            'answer' => null,
                            'subject' => $course->title
                        ]);
                    }
                }
            }
            
            \Illuminate\Support\Facades\Cache::forget('generating_chapter_' . $ebookChapter->id . '_stage_4');
        } else {
            \Illuminate\Support\Facades\Cache::forget('generating_chapter_' . $ebookChapter->id . '_stage_4');
        }

        return response()->json(['success' => true]);
    }

    public function checkGenerationStatus(int $id, int $chapter_id, int $stage_number)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['ready' => false]);
        
        $course = AssignedEbook::find($id);
        if (!$course || $course->user_id !== $user->id) return response()->json(['ready' => false]);
        
        $chapter = \App\Models\Chapter::where('course_id', $id)->find($chapter_id);
        if (!$chapter) return response()->json(['ready' => false]);
        
        $ebookId = $course->getResolvedEbookId();
        if (!$ebookId) return response()->json(['ready' => false]);
        
        $ebookChapter = \App\Models\EbookChapter::where('ebook_id', $ebookId)
            ->where('chapter_number', $chapter->order + 1)
            ->first();
            
        if (!$ebookChapter) return response()->json(['ready' => false]);
        
        $isGenerating = \Illuminate\Support\Facades\Cache::has('generating_chapter_' . $ebookChapter->id . '_stage_' . $stage_number);
        return response()->json(['ready' => !$isGenerating]);
    }

    private function getEbookPagesArray($course, $ebookId, $ebookChapter)
    {
        $start = $ebookChapter->start_page;
        $end = $ebookChapter->end_page ?? $start;

        if ($course->ebook_url) {
            $parsedUrl = parse_url($course->ebook_url);
            $baseUrl = (isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) ? ($parsedUrl['scheme'] . '://' . $parsedUrl['host']) : '';
            
            if ($baseUrl) {
                $pagesList = [];
                for ($i = $start; $i <= $end; $i++) {
                    $pagesList[] = (object) [
                        'url' => $baseUrl . '/uploads/ebook/ebook-' . $ebookId,
                        'title' => $i . '.jpg',
                        'position' => $i,
                        'is_external' => true
                    ];
                }
                return collect($pagesList);
            }
        }
        
        $query = \Illuminate\Support\Facades\DB::table('ebook_pages')->where('ebook_id', $ebookId);
        if ($ebookChapter->end_page) {
            $query->whereBetween('position', [$start, $ebookChapter->end_page]);
        } else {
            $query->where('position', '>=', $start);
        }
        
        return $query->orderBy('position')->get();
    }

    private function generateHardWordsFromGemini($pages)
    {
        $apiKey = env('GEMINI_API_KEY') ?: getenv('GEMINI_API_KEY');
        if (!$apiKey) {
            \Illuminate\Support\Facades\Log::error('GEMINI_API_KEY is missing or null.');
            return null;
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

        $parts = [
            ['text' => 'You are an expert educational content creator. Please extract 10 "hard words" (difficult vocabulary) from the text on these book pages and create 10 multiple-choice questions (MCQs) that test the meaning of these words. Format as a pure JSON array of objects: [{"question": "What is the meaning of the word \'example\'?", "options": ["(a) option 1", "(b) option 2", "(c) option 3", "(d) option 4"], "correct": 2}]. Where "correct" is the 0-based index of the correct option. IMPORTANT: Generate the questions, options, and words in the EXACT SAME LANGUAGE as the text found in the images (e.g., if the text is in Hindi, output Hindi questions and options). Do NOT include markdown formatting or backticks around the JSON. Return only the raw JSON.']
        ];

        foreach ($pages as $page) {
            if (isset($page->is_external) && $page->is_external) {
                $imageUrl = rtrim($page->url, '/') . '/' . $page->title;
                $imgResponse = \Illuminate\Support\Facades\Http::get($imageUrl);
                if ($imgResponse->successful()) {
                    $imageData = base64_encode($imgResponse->body());
                    $parts[] = [
                        'inline_data' => [
                            'mime_type' => 'image/jpeg',
                            'data' => $imageData
                        ]
                    ];
                }
            } else {
                $imagePath = public_path(rtrim($page->url, '/') . '/' . $page->title);
                if (file_exists($imagePath)) {
                    $imageData = base64_encode(file_get_contents($imagePath));
                    $mimeType = mime_content_type($imagePath);
                    
                    $parts[] = [
                        'inline_data' => [
                            'mime_type' => $mimeType,
                            'data' => $imageData
                        ]
                    ];
                }
            }
        }

        $response = \Illuminate\Support\Facades\Http::timeout(120)->post($url, [
            'contents' => [
                ['parts' => $parts]
            ]
        ]);

        if ($response->successful()) {
            $result = $response->json();
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $text = $result['candidates'][0]['content']['parts'][0]['text'];
                $text = str_replace(['```json', '```'], '', $text);
                $decoded = json_decode(trim($text), true);
                if (is_array($decoded)) {
                    return $decoded;
                } else {
                    \Illuminate\Support\Facades\Log::error('Gemini JSON decode failed. Raw text: ' . $text);
                }
            } else {
                \Illuminate\Support\Facades\Log::error('Gemini response missing text part. Response: ' . json_encode($result));
            }
        } else {
            \Illuminate\Support\Facades\Log::error('Gemini API failed with status ' . $response->status() . '. Body: ' . $response->body());
        }

        return null;
    }

    private function generateActivityFromGemini($pages)
    {
        $apiKey = env('GEMINI_API_KEY') ?: getenv('GEMINI_API_KEY');
        if (!$apiKey) {
            \Illuminate\Support\Facades\Log::error('GEMINI_API_KEY is missing or null.');
            return null;
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

        $parts = [
            ['text' => 'You are an expert educational content creator. Please extract 5 conceptual "Match the Following" pairs from the text on these book pages. These pairs could match a term to its definition, a cause to its effect, or two related concepts. Format as a pure JSON array of objects: [{"left": "Term", "right": "Definition"}]. Keep each item VERY concise. STRICT RULE: Do not exceed 3 words for the left side, and do not exceed 3 words for the right side. IMPORTANT: Generate the pairs in the EXACT SAME LANGUAGE as the text found in the images. Do NOT include markdown formatting or backticks around the JSON. Return only the raw JSON.']
        ];

        foreach ($pages as $page) {
            if (isset($page->is_external) && $page->is_external) {
                $imageUrl = rtrim($page->url, '/') . '/' . $page->title;
                $imgResponse = \Illuminate\Support\Facades\Http::get($imageUrl);
                if ($imgResponse->successful()) {
                    $imageData = base64_encode($imgResponse->body());
                    $parts[] = [
                        'inline_data' => [
                            'mime_type' => 'image/jpeg',
                            'data' => $imageData
                        ]
                    ];
                }
            } else {
                $imagePath = public_path(rtrim($page->url, '/') . '/' . $page->title);
                if (file_exists($imagePath)) {
                    $imageData = base64_encode(file_get_contents($imagePath));
                    $mimeType = mime_content_type($imagePath);
                    
                    $parts[] = [
                        'inline_data' => [
                            'mime_type' => $mimeType,
                            'data' => $imageData
                        ]
                    ];
                }
            }
        }

        $response = \Illuminate\Support\Facades\Http::timeout(120)->post($url, [
            'contents' => [
                ['parts' => $parts]
            ]
        ]);

        if ($response->successful()) {
            $result = $response->json();
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $text = $result['candidates'][0]['content']['parts'][0]['text'];
                $text = str_replace(['```json', '```'], '', $text);
                $decoded = json_decode(trim($text), true);
                if (is_array($decoded)) {
                    return $decoded;
                } else {
                    \Illuminate\Support\Facades\Log::error('Gemini JSON decode failed for Activity. Raw text: ' . $text);
                }
            } else {
                \Illuminate\Support\Facades\Log::error('Gemini response missing text part for Activity. Response: ' . json_encode($result));
            }
        } else {
            \Illuminate\Support\Facades\Log::error('Gemini API failed for Activity with status ' . $response->status() . '. Body: ' . $response->body());
        }

        return null;
    }

    private function generateShortQuestionsFromGemini($pages)
    {
        $apiKey = env('GEMINI_API_KEY') ?: getenv('GEMINI_API_KEY');
        if (!$apiKey) {
            \Illuminate\Support\Facades\Log::error('GEMINI_API_KEY is missing or null.');
            return null;
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

        $parts = [
            ['text' => 'You are an expert educational content creator. Please extract 5 short subjective exercise questions from the text on these book pages. These questions should test the student\'s understanding of the chapter. Format as a pure JSON array of objects: [{"question": "What is the main idea of this chapter?"}]. IMPORTANT: Generate the questions in the EXACT SAME LANGUAGE as the text found in the images. Do NOT include markdown formatting or backticks around the JSON. Return only the raw JSON.']
        ];

        foreach ($pages as $page) {
            if (isset($page->is_external) && $page->is_external) {
                $imageUrl = rtrim($page->url, '/') . '/' . $page->title;
                $imgResponse = \Illuminate\Support\Facades\Http::get($imageUrl);
                if ($imgResponse->successful()) {
                    $imageData = base64_encode($imgResponse->body());
                    $parts[] = [
                        'inline_data' => [
                            'mime_type' => 'image/jpeg',
                            'data' => $imageData
                        ]
                    ];
                }
            } else {
                $imagePath = public_path(rtrim($page->url, '/') . '/' . $page->title);
                if (file_exists($imagePath)) {
                    $imageData = base64_encode(file_get_contents($imagePath));
                    $mimeType = mime_content_type($imagePath);
                    
                    $parts[] = [
                        'inline_data' => [
                            'mime_type' => $mimeType,
                            'data' => $imageData
                        ]
                    ];
                }
            }
        }

        $response = \Illuminate\Support\Facades\Http::timeout(120)->post($url, [
            'contents' => [
                ['parts' => $parts]
            ]
        ]);

        if ($response->successful()) {
            $result = $response->json();
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $text = $result['candidates'][0]['content']['parts'][0]['text'];
                $text = str_replace(['```json', '```'], '', $text);
                $decoded = json_decode(trim($text), true);
                if (is_array($decoded)) {
                    return $decoded;
                } else {
                    \Illuminate\Support\Facades\Log::error('Gemini JSON decode failed for short questions. Raw text: ' . $text);
                }
            }
        }
        return null;
    }

    public function generateChaptersMap(int $id)
    {
        $user = Auth::user();
        $course = AssignedEbook::where(function($query) use ($user) {
                             $query->where('class_id', $user->class_id)
                                   ->orWhere('user_id', $user->id);
                         })->findOrFail($id);

        if (!$course->ebook_url) {
            return response()->json(['error' => 'This is not an external flipbook.'], 400);
        }

        $ebookId = $course->getResolvedEbookId();
        if (!$ebookId) {
            $extractedId = basename(parse_url($course->ebook_url, PHP_URL_PATH) ?? '');
            if (!is_numeric($extractedId)) {
                return response()->json(['error' => 'Could not extract Ebook ID from URL.'], 400);
            }
            $ebookId = $extractedId;
        }

        $cachedChapters = \App\Models\EbookChapter::where('ebook_id', $ebookId)->get();

        if ($cachedChapters->isEmpty()) {
            $parsedUrl = parse_url($course->ebook_url);
            $baseUrl = (isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) ? ($parsedUrl['scheme'] . '://' . $parsedUrl['host']) : '';
            if (!$baseUrl) {
                return response()->json(['error' => 'Invalid ebook URL.'], 400);
            }

            $inlineData = [];
            for ($i = 2; $i <= 5; $i++) {
                $url = $baseUrl . '/uploads/ebook/ebook-' . $ebookId . '/' . $i . '.jpg';
                $response = \Illuminate\Support\Facades\Http::get($url);
                if ($response->successful()) {
                    $imageData = base64_encode($response->body());
                    $inlineData[] = [
                        'inlineData' => [
                            'mimeType' => 'image/jpeg',
                            'data' => $imageData
                        ]
                    ];
                }
            }

            if (empty($inlineData)) {
                return response()->json(['error' => 'No index pages found on remote server.'], 404);
            }

            $prompt = "You are an expert document structure analyzer.\nThese images are pages from a book's Table of Contents.\n\nTASK:\n1. Read ALL entries in order.\n2. Extract every chapter / lesson / unit.\n3. Use the listed page number as start_page.\n4. Identify the printed page number(s) on the Table of Contents pages themselves.\n\nOUTPUT RULES:\n- Return ONLY valid JSON\n- Return a JSON OBJECT with two keys: 'index_pages' (string, e.g., '3,4' or 'iii,iv') and 'chapters' (list of objects).\n- Each object in 'chapters' MUST have:\n  {\"title\": string, \"start_page\": number, \"section\": string | null}\n- 'start_page' MUST be an integer\n- Do NOT include explanations\n\nExample:\n{\n  \"index_pages\": \"3,4\",\n  \"chapters\": [\n    {\"title\": \"Chapter 1\", \"start_page\": 5, \"section\": \"Part A\"}\n  ]\n}";

            $apiKey = env('GEMINI_API_KEY');
            $geminiResponse = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->timeout(120)->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey, [
                'contents' => [
                    [
                        'parts' => array_merge(
                            [['text' => $prompt]],
                            $inlineData
                        )
                    ]
                ]
            ]);

            if (!$geminiResponse->successful()) {
                \Illuminate\Support\Facades\Log::error('Gemini API Error: ' . $geminiResponse->body());
                return response()->json(['error' => 'Failed to connect to AI service.'], 500);
            }

            $responseData = $geminiResponse->json();
            $textResult = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            \Illuminate\Support\Facades\Log::info('Gemini Raw Response: ' . $textResult);

            $textResult = trim($textResult);
            if (str_starts_with($textResult, '```json')) {
                $textResult = substr($textResult, 7);
                if (str_ends_with($textResult, '```')) {
                    $textResult = substr($textResult, 0, -3);
                }
            } elseif (str_starts_with($textResult, '```')) {
                $textResult = substr($textResult, 3);
                if (str_ends_with($textResult, '```')) {
                    $textResult = substr($textResult, 0, -3);
                }
            }
            $textResult = trim($textResult);
            
            $parsedData = json_decode($textResult, true);
            if (!is_array($parsedData) || !isset($parsedData['chapters'])) {
                return response()->json(['error' => 'Invalid AI response format.'], 500);
            }

            $chaptersData = $parsedData['chapters'];
            $chapterNumber = 1;
            $totalExtracted = count($chaptersData);
            
            foreach ($chaptersData as $index => $data) {
                $startPage = (int) ($data['start_page'] ?? 0);
                $endPage = null;
                if ($index < $totalExtracted - 1 && isset($chaptersData[$index + 1]['start_page'])) {
                    $endPage = (int) $chaptersData[$index + 1]['start_page'] - 1;
                }

                $ebChapter = \App\Models\EbookChapter::create([
                    'ebook_id' => $ebookId,
                    'chapter_number' => $chapterNumber,
                    'chapter_name' => $data['title'] ?? 'Untitled Chapter',
                    'start_page' => $startPage,
                    'end_page' => $endPage,
                    'total_stages' => 4
                ]);

                $stageNames = ['Reading Mission', 'Hard Words', 'Activity Mission', 'Exercise Mission'];
                $descriptions = [
                    'Explore the pages of the ebook.',
                    'Master the difficult words found in this chapter.',
                    'Play fun activities to test your understanding.',
                    'Complete exercises to test your knowledge.'
                ];
                
                foreach ($stageNames as $sIndex => $sName) {
                    \App\Models\EbookChapterStage::create([
                        'ebook_id' => $ebookId,
                        'ebook_chapter_id' => $ebChapter->id,
                        'stage_number' => $sIndex + 1,
                        'stage_name' => $sName,
                        'description' => $descriptions[$sIndex]
                    ]);
                }
                
                $chapterNumber++;
            }
        }

        $courseChapters = $course->chapters()->count();
        if ($courseChapters == 0) {
            $ebookChapters = \App\Models\EbookChapter::where('ebook_id', $ebookId)
                ->orderBy('chapter_number')
                ->get();

            foreach ($ebookChapters as $index => $ebChapter) {
                $chapter = \App\Models\Chapter::create([
                    'course_id' => $course->id,
                    'title' => $ebChapter->chapter_name ?? 'Chapter ' . ($index + 1),
                    'description' => 'Pages ' . $ebChapter->start_page . ' to ' . $ebChapter->end_page,
                    'order' => $index,
                    'unlock_threshold' => 0,
                    'xp_reward' => 50,
                    'is_active' => true,
                ]);

                $stageNames = ['Reading Mission', 'Hard Words', 'Activity Mission', 'Exercise Mission'];
                foreach ($stageNames as $lessonIndex => $name) {
                    \App\Models\Lesson::create([
                        'chapter_id' => $chapter->id,
                        'title' => $name,
                        'description' => 'Complete the ' . strtolower($name) . ' challenge.',
                        'content' => '',
                        'order' => $lessonIndex,
                        'xp_reward' => 20,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Chapters successfully generated!']);
    }
}
