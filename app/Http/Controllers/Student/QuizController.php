<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\AutoRuleEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(int $id)
    {
        $user = Auth::user();
        $quiz = Quiz::with(['questions', 'chapter.course'])->findOrFail($id);

        abort_unless($quiz->chapter->course->class_id === $user->class_id, 403);
        abort_unless($quiz->chapter->isUnlockedFor($user), 403, 'Complete the previous chapter first!');

        $bestAttempt = $quiz->bestAttemptFor($user->id);

        return view('student.quizzes.show', compact('user', 'quiz', 'bestAttempt'));
    }

    public function submit(Request $request, int $id)
    {
        $user = Auth::user();
        $quiz = Quiz::with(['questions', 'chapter'])->findOrFail($id);

        abort_unless($quiz->chapter->course->class_id === $user->class_id, 403);

        $answers = $request->input('answers', []);
        $score = 0;
        $total = $quiz->questions->count();

        foreach ($quiz->questions as $question) {
            $chosen = $answers[$question->id] ?? null;
            if ($chosen === $question->correct_option) {
                $score++;
            }
        }

        $percentage = $total > 0 ? (int) round(($score / $total) * 100) : 0;
        $threshold  = $quiz->chapter->unlock_threshold;
        $status     = $percentage >= $threshold ? 'pass' : 'fail';
        $xpEarned   = $status === 'pass' ? $quiz->xp_reward : (int) ($quiz->xp_reward * 0.3);

        $attempt = QuizAttempt::create([
            'user_id'        => $user->id,
            'quiz_id'        => $quiz->id,
            'score'          => $score,
            'total_questions' => $total,
            'percentage'     => $percentage,
            'status'         => $status,
            'answers'        => $answers,
            'xp_earned'      => $xpEarned,
            'completed_at'   => now(),
        ]);

        // Award XP
        $user->addXp($xpEarned, 'quiz', $quiz->id, "Quiz: {$quiz->title} ({$percentage}%)");

        // Fire auto rules
        $engine = new AutoRuleEngine($user);
        $engine->fire($status === 'pass' ? 'quiz_pass' : 'quiz_fail', [
            'quiz_id'    => $quiz->id,
            'percentage' => $percentage,
            'source_id'  => $quiz->id,
        ]);

        if ($status === 'pass') {
            // Check if ALL lessons in chapter done → fire chapter_complete
            $allLessonsDone = $quiz->chapter->lessons->every(fn($l) => $l->isCompletedBy($user));
            if ($allLessonsDone) {
                $engine->fire('chapter_complete', ['chapter_id' => $quiz->chapter_id]);
            }
        }

        return redirect()->route('student.quizzes.result', $attempt->id);
    }

    public function result(int $attemptId)
    {
        $user    = Auth::user();
        $attempt = QuizAttempt::with(['quiz.chapter.course', 'quiz.questions'])->findOrFail($attemptId);

        abort_unless($attempt->user_id === $user->id, 403);

        $nextChapter = $attempt->status === 'pass'
            ? $attempt->quiz->chapter->nextChapter()
            : null;

        return view('student.quizzes.result', compact('user', 'attempt', 'nextChapter'));
    }
}
