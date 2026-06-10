<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Worksheet;
use App\Services\AutoRuleEngine;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user    = Auth::user()->load('studentClass');
        $courses = Course::where('class_id', $user->class_id)
                         ->where('is_active', true)
                         ->with(['chapters'])
                         ->orderBy('order')
                         ->get();

        $pendingWorksheets = Worksheet::where('user_id', $user->id)
                                      ->pending()
                                      ->orderBy('due_date')
                                      ->take(3)
                                      ->get();

        $recentXp = $user->xpTransactions()
                         ->latest()
                         ->take(5)
                         ->get();

        // Today's suggested lesson — first incomplete lesson across all courses
        $nextLesson = null;
        foreach ($courses as $course) {
            foreach ($course->chapters as $chapter) {
                foreach ($chapter->lessons as $lesson) {
                    if (!$lesson->isCompletedBy($user)) {
                        $nextLesson = $lesson;
                        break 3;
                    }
                }
            }
        }

        // Attendance for current month
        $now = Carbon::now();
        $attendanceDates = Attendance::where('created_for', $user->id)
            ->whereYear('attendance_date',  $now->year)
            ->whereMonth('attendance_date', $now->month)
            ->pluck('attendance_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
            ->toArray();

        return view('student.dashboard', compact(
            'user', 'courses', 'pendingWorksheets', 'recentXp', 'nextLesson', 'attendanceDates'
        ));
    }

    public function markAttendance()
    {
        $user  = Auth::user();
        $isNew = $user->markAttendanceToday(); // creates record + updates streak

        if ($isNew) {
            $engine = new AutoRuleEngine($user);
            $engine->fire('login');             // fires XP / badge rules
            return back()->with('success', '🎉 Attendance marked! XP awarded!');
        }

        return back()->with('info', 'Attendance already marked for today.');
    }
}
