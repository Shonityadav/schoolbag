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
    public function index()
    {
        $user    = Auth::user();
        $courses = AssignedEbook::where(function($query) use ($user) {
                             $query->where('class_id', $user->class_id)
                                   ->orWhere('user_id', $user->id);
                         })
                         ->where('is_active', true)
                         ->withCount('chapters')
                         ->orderBy('order')
                         ->get();

        return view('student.assigned_ebooks.index', compact('user', 'courses'));
    }

    /** Chapter islands for a single course */
    public function show(int $id)
    {
        $user    = Auth::user();
        $course  = AssignedEbook::where(function($query) use ($user) {
                             $query->where('class_id', $user->class_id)
                                   ->orWhere('user_id', $user->id);
                         })->findOrFail($id);
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
        $course = AssignedEbook::where(function($query) use ($user) {
                             $query->where('class_id', $user->class_id)
                                   ->orWhere('user_id', $user->id);
                         })->findOrFail($id);
        
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
                            $pages = \Illuminate\Support\Facades\DB::table('ebook_pages')
                                ->where('ebook_id', $ebookId)
                                ->whereBetween('position', [$ebookChapter->start_page, $ebookChapter->end_page ?? $ebookChapter->start_page])
                                ->get();

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
                            $pages = \Illuminate\Support\Facades\DB::table('ebook_pages')
                                ->where('ebook_id', $ebookId)
                                ->whereBetween('position', [$ebookChapter->start_page, $ebookChapter->end_page ?? $ebookChapter->start_page])
                                ->get();

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
                        }
                    }
                }
            }
        } else {
            // Other Stages -> Ebook Pages
            $ebookId = $course->getResolvedEbookId();
            $query = \Illuminate\Support\Facades\DB::table('ebook_pages');
            
            if ($ebookId) {
                $query->where('ebook_id', $ebookId);
                $ebookChapter = \App\Models\EbookChapter::where('ebook_id', $ebookId)
                                    ->where('chapter_number', $chapter->order + 1)
                                    ->first();

                if ($ebookChapter && $ebookChapter->start_page) {
                    $start = $ebookChapter->start_page;
                    if ($ebookChapter->end_page) {
                        $query->whereBetween('position', [$start, $ebookChapter->end_page]);
                    } else {
                        $query->where('position', '>=', $start);
                    }
                }
            } else {
                // If no ebook resolved, don't show any pages
                $query->where('id', -1);
            }

            $ebookPages = $query->orderBy('position')->get();
        }

        // Pass course_id and stage for the next buttons
        return view('student.lessons.showdetails', compact('user', 'lesson', 'isCompleted', 'nextLesson', 'course', 'stage', 'chapter_id', 'ebookPages', 'mcqs', 'matchPairs', 'generationError'));
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
            ['text' => 'You are an expert educational content creator. Please extract 10 "hard words" (difficult vocabulary) from the text on these book pages and create 10 multiple-choice questions (MCQs) that test the meaning of these words. Format as a pure JSON array of objects: [{"question": "What is the meaning of the word \'example\'?", "options": ["(a) option 1", "(b) option 2", "(c) option 3", "(d) option 4"], "correct": 2}]. Where "correct" is the 0-based index of the correct option. Do NOT include markdown formatting or backticks around the JSON. Return only the raw JSON.']
        ];

        foreach ($pages as $page) {
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

        $response = \Illuminate\Support\Facades\Http::post($url, [
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
            ['text' => 'You are an expert educational content creator. Please extract 5 conceptual "Match the Following" pairs from the text on these book pages. These pairs could match a term to its definition, a cause to its effect, or two related concepts. Format as a pure JSON array of objects: [{"left": "Term", "right": "Definition"}]. Keep each item VERY concise. STRICT RULE: Do not exceed 3 words for the left side, and do not exceed 3 words for the right side. Do NOT include markdown formatting or backticks around the JSON. Return only the raw JSON.']
        ];

        foreach ($pages as $page) {
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

        $response = \Illuminate\Support\Facades\Http::post($url, [
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
}
