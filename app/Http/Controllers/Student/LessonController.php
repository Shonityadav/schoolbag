<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Services\AutoRuleEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(int $id)
    {
        $user   = Auth::guard('student')->user();
        $lesson = Lesson::with('chapter.course')->findOrFail($id);

        // Guard: lesson must belong to student's class
        abort_unless($lesson->chapter->course->class_id === $user->class_id, 403);

        $isCompleted = $lesson->isCompletedBy($user);
        $nextLesson  = Lesson::where('chapter_id', $lesson->chapter_id)
                             ->where('order', '>', $lesson->order)
                             ->orderBy('order')
                             ->first();

        // Fetch ebook pages (hardcoded ebook_id = 2 as requested)
        $ebookPages = [];
        $mcqs = [];
        
        if ($lesson->order == 1) {
            // Stage 2: Hard Words -> MCQs
            $ebookId = $lesson->chapter->course->ebook_id;
            
            // Get original ebook chapter based on chapter order
            $ebookChapter = \App\Models\EbookChapter::where('ebook_id', $ebookId)
                ->where('chapter_number', $lesson->chapter->order + 1)
                ->first();
                
            if ($ebookChapter) {
                // Get ebook chapter stage (Hard Words is stage 2)
                $ebookChapterStage = \App\Models\EbookChapterStage::where('ebook_chapter_id', $ebookChapter->id)
                    ->where('stage_number', 2)
                    ->first();
                    
                if ($ebookChapterStage) {
                    $quesType = \App\Models\QuestionType::where('type', 'Hard Word')->first();
                    $quesTypeId = $quesType ? $quesType->id : null;
                    
                    $dbQuestions = \App\Models\EbookQuestion::where('ebook_id', $ebookId)
                        ->where('chapter_id', $ebookChapter->id)
                        ->where('stage_id', $ebookChapterStage->id)
                        ->get();
                        
                    if ($dbQuestions->isNotEmpty()) {
                        // Load from DB
                        $mcqs = $dbQuestions->map(function ($q) {
                            $parsed = json_decode($q->question, true);
                            return [
                                'question' => $parsed['question'] ?? $q->question,
                                'options' => $parsed['options'] ?? [],
                                'correct' => (int) $q->answer
                            ];
                        })->toArray();
                    } else {
                        // Generate with Gemini
                        $pages = \Illuminate\Support\Facades\DB::table('ebook_pages')
                            ->where('ebook_id', $ebookId)
                            ->whereBetween('position', [$ebookChapter->start_page, $ebookChapter->end_page ?? $ebookChapter->start_page])
                            ->get();
                            
                        $generatedMcqs = $this->generateHardWordsFromGemini($pages);
                        
                        if ($generatedMcqs && is_array($generatedMcqs)) {
                            \Illuminate\Support\Facades\Log::info('Gemini generated ' . count($generatedMcqs) . ' questions successfully.');
                            $mcqs = $generatedMcqs;
                            
                            // Save to DB
                            foreach ($mcqs as $mcq) {
                                $questionJson = json_encode([
                                    'question' => $mcq['question'],
                                    'options' => $mcq['options']
                                ]);
                                
                                \App\Models\EbookQuestion::create([
                                    'ebook_id' => $ebookId,
                                    'chapter_id' => $ebookChapter->id,
                                    'stage_id' => $ebookChapterStage->id,
                                    'ques_type_id' => $quesTypeId,
                                    'question' => $questionJson,
                                    'answer' => (string) $mcq['correct'],
                                    'subject' => $lesson->chapter->course->title
                                ]);
                            }
                        } else {
                            \Illuminate\Support\Facades\Log::error('Gemini generated null or invalid array.');
                        }
                    }
                } else {
                    \Illuminate\Support\Facades\Log::error('EbookChapterStage not found for chapter ' . $ebookChapter->id);
                }
            } else {
                \Illuminate\Support\Facades\Log::error('EbookChapter not found for ebook ' . $ebookId . ' and chapter_number ' . ($lesson->chapter->order + 1));
            }
            
            // Fallback if empty
            if (empty($mcqs)) {
                $mcqs = [
                    ['question' => 'What colour is a pumpkin?', 'options' => ['(a) Black', '(b) White', '(c) Orange', '(d) Blue'], 'correct' => 2],
                    ['question' => 'What is the opposite of cold?', 'options' => ['(a) Freezing', '(b) Hot', '(c) Cool', '(d) Warm'], 'correct' => 1],
                ];
            }
        } else {
            // Other Stages -> Ebook Pages
            $ebookPages = \Illuminate\Support\Facades\DB::table('ebook_pages')
                            ->where('ebook_id', 2)
                            ->orderBy('position')
                            ->get();
        }

        return view('student.lessons.showdetails', compact('user', 'lesson', 'isCompleted', 'nextLesson', 'ebookPages', 'mcqs'));
    }

    public function complete(Request $request, int $id)
    {
        $user   = Auth::guard('student')->user();
        $lesson = Lesson::with('chapter.course')->findOrFail($id);
        abort_unless($lesson->chapter->course->class_id === $user->class_id || $lesson->chapter->course->user_id === $user->id, 403);

        $score = null;
        $answersJson = null;

        if ($request->has('answers')) {
            $answersDecoded = json_decode($request->input('answers'), true);
            $answersJson = $request->input('answers');
            $score = 0;
            
            // Evaluate MCQs if this is the Hard Words stage (order == 1) or Exercise (order == 3)
            if ($lesson->order == 1 || $lesson->order == 3) {
                $ebookId = $lesson->chapter->course->ebook_id;
                $ebookChapter = \App\Models\EbookChapter::where('ebook_id', $ebookId)->where('chapter_number', $lesson->chapter->order + 1)->first();
                if ($ebookChapter) {
                    $stageNumber = $lesson->order == 1 ? 2 : 4;
                    $ebookChapterStage = \App\Models\EbookChapterStage::where('ebook_chapter_id', $ebookChapter->id)->where('stage_number', $stageNumber)->first();
                    if ($ebookChapterStage) {
                        $dbQuestions = \App\Models\EbookQuestion::where('ebook_id', $ebookId)
                            ->where('chapter_id', $ebookChapter->id)
                            ->where('stage_id', $ebookChapterStage->id)
                            ->orderBy('id')
                            ->get();
                        
                        if ($dbQuestions->isNotEmpty()) {
                            foreach ($dbQuestions as $index => $q) {
                                if (isset($answersDecoded[$index]) && (string)$answersDecoded[$index] === (string)$q->answer) {
                                    $score++;
                                }
                            }
                        } else {
                            // Fallback to hardcoded logic if no DB questions
                            $mcqs = \App\Models\Lesson::getHardWordsMcqs();
                            foreach ($mcqs as $index => $mcq) {
                                if (isset($answersDecoded[$index]) && $answersDecoded[$index] == $mcq['correct']) {
                                    $score++;
                                }
                            }
                        }
                    }
                }
            } else if ($lesson->order == 2) {
                // Activity Mission (Matching)
                $matchPairs = [];
                $ebookId = $lesson->chapter->course->ebook_id ?? 2;
                $ebookChapter = \App\Models\EbookChapter::where('ebook_id', $ebookId)
                    ->where('chapter_number', $lesson->chapter->order + 1)
                    ->first();
                
                if ($ebookChapter) {
                    $ebookChapterStage = \App\Models\EbookChapterStage::where('ebook_chapter_id', $ebookChapter->id)
                        ->where('stage_number', 3)
                        ->first();
                        
                    if ($ebookChapterStage) {
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
                        }
                    }
                }
                
                if (empty($matchPairs)) {
                    $matchPairs = \App\Models\Lesson::getActivityMatchPairs();
                }
                $correctMatches = 0;
                if (isset($answersDecoded['match']) && is_array($answersDecoded['match'])) {
                    foreach ($matchPairs as $pair) {
                        if (isset($answersDecoded['match'][$pair['left']]) && $answersDecoded['match'][$pair['left']] === $pair['right']) {
                            $correctMatches++;
                        }
                    }
                }
                if (count($matchPairs) > 0) {
                    $score = (int) round(($correctMatches / count($matchPairs)) * 10);
                } else {
                    $score = 10;
                }
            } else {
                // Reading Mission (order == 0)
                // Automatically grant full score (10) for participation
                $score = 10;
            }
        }
        
        $timeTaken = $request->input('time_taken');

        $course = $lesson->chapter->course;
        $className = $course->studentClass ? $course->studentClass->name : null;
        $ebookId = $course->ebook_id ?? 2;
        $ebookChapter = \App\Models\EbookChapter::where('ebook_id', $ebookId)
            ->where('chapter_number', $lesson->chapter->order + 1)
            ->first();
            
        if (!$ebookChapter) {
            return back()->with('error', 'Ebook chapter not found.');
        }

        $progress = LessonProgress::firstOrNew([
            'user_id' => $user->id,
            'chapter_id' => $ebookChapter->id,
            'stage_number' => $lesson->order + 1,
        ]);

        $previousAttempts = $progress->stage_attempt_number ?? 0;
        $isFirstAttempt = !$progress->exists;
        
        $progress->stage_attempt_number = $previousAttempts + 1;
        $progress->ebook_id = $ebookId;
        $progress->publication_name = 'Acetech Bookstore';
        $progress->subject = $course->title;
        $progress->standard = $className;

        if ($isFirstAttempt || $score > $progress->score) {
            $progress->completed = true;
            $progress->completed_at = now();
            $progress->answers = $answersJson;
            $progress->score = $score;
            $progress->time_taken = $timeTaken;
        }

        $progress->save();

        if ($previousAttempts === 0) {
            // Award XP only on the first attempt
            $user->addXp($lesson->xp_reward, 'lesson', $lesson->id, "Completed: {$lesson->title}");
        }

        // Redirect to next lesson
        $nextLesson = Lesson::where('chapter_id', $lesson->chapter_id)
                            ->where('order', '>', $lesson->order)
                            ->orderBy('order')
                            ->first();

        // Check if chapter was just completed
        $unlockedAvatar = null;
        if (!$nextLesson && $previousAttempts === 0) {
            if ($lesson->chapter->isCompletedBy($user)) {
                $unlockedAvatar = $user->awardChestDrop();
            }
        }

        if ($nextLesson) {
            if ($request->has('course_id') && $request->has('stage') && $request->has('chapter_id')) {
                return redirect()->route('student.courses.stage', [
                    'id' => $request->input('course_id'),
                    'stage' => $request->input('stage') + 1,
                    'chapter_id' => $request->input('chapter_id')
                ])->with('success', '+' . $lesson->xp_reward . ' XP earned! 🌟');
            }
            
            return redirect()->route('student.lessons.show', $nextLesson->id)
                             ->with('success', '+' . $lesson->xp_reward . ' XP earned! 🌟');
        }

        $redirect = redirect()->route('student.courses.show', $lesson->chapter->course_id)
            ->with('success', 'Stage completed! ' . ($score !== null ? 'Your score: ' . $score . '/10' : ''));

        if ($unlockedAvatar) {
            $redirect->with('chest_unlocked', $unlockedAvatar);
        }

        return $redirect;
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
                // Clean markdown if present
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
}
