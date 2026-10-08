<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AssignedEbook;
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
        $courses = AssignedEbook::where('class_id', $user->class_id)
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

        $alreadyRequested = \Illuminate\Support\Facades\DB::table('notifications')
            ->where('type', 'App\Notifications\AttendanceRequested')
            ->whereJsonContains('data->student_id', $user->id)
            ->whereJsonContains('data->date', $now->toDateString())
            ->exists();

        return view('student.dashboard', compact(
            'user', 'courses', 'pendingWorksheets', 'recentXp', 'nextLesson', 'attendanceDates', 'alreadyRequested'
        ));
    }

    public function markAttendance()
    {
        $user  = Auth::user();
        $today = now()->toDateString();
        
        $alreadyMarked = \App\Models\Attendance::where('created_for', $user->id)
            ->where('attendance_date', $today)
            ->exists();
            
        if ($alreadyMarked) {
            return back()->with('info', 'Attendance already marked for today.');
        }

        $alreadyRequested = \Illuminate\Support\Facades\DB::table('notifications')
            ->where('type', 'App\Notifications\AttendanceRequested')
            ->whereJsonContains('data->student_id', $user->id)
            ->whereJsonContains('data->date', $today)
            ->exists();

        if ($alreadyRequested) {
            return back()->with('info', 'Attendance request already sent for today.');
        }

        // Notify Admins
        $admins = \App\Models\User::where('user_type', 1)
            ->where('institute_id', $user->institute_id)
            ->get();
            
        // Notify Teachers
        $teachers = \App\Models\User::where('user_type', 2)
            ->where('institute_id', $user->institute_id)
            ->whereHas('classes', function($q) use ($user) {
                $q->where('classes.id', $user->class_id);
            })->get();

        $notifiables = $admins->merge($teachers);
        \Illuminate\Support\Facades\Notification::send($notifiables, new \App\Notifications\AttendanceRequested($user, $today));

        return back()->with('success', 'Attendance requested! Awaiting teacher approval.');
    }

    public function markNotificationsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    public function readNotification($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        $link = $notification->data['link'] ?? '#';
        return redirect($link);
    }
}
