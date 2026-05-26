<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** World-map grid of all subjects for this student's class */
    public function index()
    {
        $user    = Auth::user();
        $courses = Course::where(function($query) use ($user) {
                             $query->where('class_id', $user->class_id)
                                   ->orWhere('user_id', $user->id);
                         })
                         ->where('is_active', true)
                         ->withCount('chapters')
                         ->orderBy('order')
                         ->get();

        return view('student.courses.index', compact('user', 'courses'));
    }

    /** Chapter islands for a single course */
    public function show(int $id)
    {
        $user    = Auth::user();
        $course  = Course::where(function($query) use ($user) {
                             $query->where('class_id', $user->class_id)
                                   ->orWhere('user_id', $user->id);
                         })->findOrFail($id);
        $chapters = $course->chapters()->with(['lessons', 'quiz'])->get();

        // Determine unlock state for each chapter
        $chaptersData = $chapters->map(function ($chapter) use ($user) {
            return [
                'chapter'   => $chapter,
                'unlocked'  => $chapter->isUnlockedFor($user),
                'completed' => $chapter->quiz ? $chapter->quiz->hasPassedBy($user->id) : false,
                'lessons_done' => $chapter->lessons->filter(fn($l) => $l->isCompletedBy($user))->count(),
                'lessons_total' => $chapter->lessons->count(),
            ];
        });

        return view('student.courses.show', compact('user', 'course', 'chaptersData'));
    }

    public function stage(\Illuminate\Http\Request $request, int $id, int $chapter_id, int $stage)
    {
        $user = Auth::user();
        $course = Course::where(function($query) use ($user) {
                             $query->where('class_id', $user->class_id)
                                   ->orWhere('user_id', $user->id);
                         })->findOrFail($id);
        
        $chapter = \App\Models\Chapter::where('course_id', $id)->findOrFail($chapter_id);

        if ($stage == 5) {
            $quiz = $chapter->quiz;
            abort_if(!$quiz, 404, 'Quiz not found');
            return redirect()->route('student.quizzes.show', $quiz->id);
        }

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
        
        if ($stage == 2) {
            // Stage 2: Hard Words -> MCQs
            $mcqs = \App\Models\Lesson::getHardWordsMcqs();
        } elseif ($stage == 3) {
            // Stage 3: Activity Mission -> Match the Following
            $matchPairs = \App\Models\Lesson::getActivityMatchPairs();
        } else {
            // Other Stages -> Ebook Pages
            $ebookPages = \Illuminate\Support\Facades\DB::table('ebook_pages')
                            ->where('ebook_id', $course->ebook_id ?? 2)
                            ->orderBy('position')
                            ->get();
        }

        // Pass course_id and stage for the next buttons
        return view('student.lessons.showdetails', compact('user', 'lesson', 'isCompleted', 'nextLesson', 'course', 'stage', 'chapter_id', 'ebookPages', 'mcqs', 'matchPairs'));
    }
}
