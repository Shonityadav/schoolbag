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
        $user   = Auth::user();
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
            $mcqs = [
                ['question' => 'What colour is a pumpkin?', 'options' => ['(a) Black', '(b) White', '(c) Orange', '(d) Blue'], 'correct' => 2],
                ['question' => 'What is the opposite of cold?', 'options' => ['(a) Freezing', '(b) Hot', '(c) Cool', '(d) Warm'], 'correct' => 1],
                ['question' => 'Which animal is known as the king of the jungle?', 'options' => ['(a) Elephant', '(b) Tiger', '(c) Lion', '(d) Bear'], 'correct' => 2],
                ['question' => 'How many legs does a spider have?', 'options' => ['(a) 6', '(b) 8', '(c) 10', '(d) 12'], 'correct' => 1],
                ['question' => 'What comes after Monday?', 'options' => ['(a) Sunday', '(b) Wednesday', '(c) Tuesday', '(d) Friday'], 'correct' => 2],
                ['question' => 'What do bees make?', 'options' => ['(a) Honey', '(b) Milk', '(c) Juice', '(d) Water'], 'correct' => 0],
                ['question' => 'Which is the largest planet in our solar system?', 'options' => ['(a) Earth', '(b) Mars', '(c) Jupiter', '(d) Saturn'], 'correct' => 2],
                ['question' => 'How many colors are in a rainbow?', 'options' => ['(a) 5', '(b) 6', '(c) 7', '(d) 8'], 'correct' => 2],
                ['question' => 'What do you use to write on a blackboard?', 'options' => ['(a) Pen', '(b) Pencil', '(c) Chalk', '(d) Marker'], 'correct' => 2],
                ['question' => 'What is the color of the sky on a clear day?', 'options' => ['(a) Red', '(b) Green', '(c) Yellow', '(d) Blue'], 'correct' => 3],
            ];
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
        $user   = Auth::user();
        $lesson = Lesson::with('chapter.course')->findOrFail($id);
        abort_unless($lesson->chapter->course->class_id === $user->class_id || $lesson->chapter->course->user_id === $user->id, 403);

        $score = null;
        $answersJson = null;

        if ($request->has('answers') && $request->input('answers') !== '{}') {
            $answersDecoded = json_decode($request->input('answers'), true);
            $answersJson = $request->input('answers');
            $score = 0;
            
            // Only evaluate if this is the Hard Words stage (or order == 1)
            if ($lesson->order == 1) {
                $mcqs = \App\Models\Lesson::getHardWordsMcqs();
                foreach ($mcqs as $index => $mcq) {
                    if (isset($answersDecoded[$index]) && $answersDecoded[$index] == $mcq['correct']) {
                        $score++;
                    }
                }
            }
        }
        
        $timeTaken = $request->input('time_taken');

        $previousAttempts = LessonProgress::where('user_id', $user->id)
                                          ->where('lesson_id', $lesson->id)
                                          ->count();

        // Extract extra details
        $course = $lesson->chapter->course;
        $className = $course->studentClass ? $course->studentClass->name : null;

        $extraData = [
            'ebook_id' => 2,
            'publication_name' => 'Acetech Bookstore',
            'subject' => $course->title,
            'standard' => $className,
            'stage_number' => $lesson->order + 1,
            'stage_attempt_number' => $previousAttempts + 1,
        ];

        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            array_merge([
                'completed'    => true,
                'completed_at' => now(),
                'answers'      => $answersJson,
                'score'        => $score,
                'time_taken'   => $timeTaken,
            ], $extraData)
        );

        if ($previousAttempts === 0) {
            // Award XP only on the first attempt
            $user->addXp($lesson->xp_reward, 'lesson', $lesson->id, "Completed: {$lesson->title}");
        }

        // Redirect to next lesson or chapter quiz
        $nextLesson = Lesson::where('chapter_id', $lesson->chapter_id)
                            ->where('order', '>', $lesson->order)
                            ->orderBy('order')
                            ->first();

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

        return redirect()->route('student.courses.show', $lesson->chapter->course_id)
                         ->with('success', 'Chapter complete! 🎉');
    }
}
