<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\Attendance;
use App\Services\AutoRuleEngine;
use Illuminate\Support\Carbon;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
{
    abort_unless(
        auth()->user()->hasPermission('attendance.view'),
        403
    );

    $date = $request->input(
        'date',
        Carbon::today()->toDateString()
    );

    $requestedType = $request->input('user_type');

    $canViewStudents = true; // Assuming attendance.view grants access to view student attendance
    $canViewStaff    = true; // Assuming attendance.view grants access to view staff attendance

    if ($requestedType) {
        $userType = $requestedType;
    } else {
        $userType = '3'; // Default to students
    }
    
    $classId = $request->input('class_id');

    $authUser = auth()->user();

    $query = User::with([
        'student.class',
        'attendances' => function ($q) use ($date) {
            $q->where('attendance_date', $date);
        }
    ])
    ->where('institute_id', $authUser->institute_id);

    // =====================
    // STUDENTS
    // =====================
if ($userType == '3') {

    $query->where('user_type', 3);

    // Staff restriction
    if ($authUser->user_type != 1) {

        $assignedClassIds = $authUser->classes
            ->pluck('id')
            ->toArray();

        $query->whereHas('student', function ($q) use ($assignedClassIds) {
            $q->whereIn('class_id', $assignedClassIds);
        });
    }

    // No class selected
    if (empty($classId)) {

        $query->whereRaw('1 = 0');

    } else {

        $query->whereHas('student', function ($q) use ($classId) {
            $q->where('class_id', $classId);
        });
    }
}

    // =====================
    // STAFF
    // =====================
    elseif ($userType == '2') {

        $query->where('user_type', 2);
    }

    $users = $query->get();

    // =====================
    // CLASSES DROPDOWN
    // =====================
    if ($authUser->user_type == 1) {

        $classes = ClassModel::where(
            'institute_id',
            $authUser->institute_id
        )->withCount('students')->get();

    } else {

        $classes = $authUser->classes()->withCount('students')->get();
    }

    return view(
        'admin.attendance.index',
        compact(
            'users',
            'date',
            'userType',
            'classId',
            'classes'
        )
    );
}

    public function mark(Request $request, $id)
    {
        abort_unless(
            auth()->user()->hasPermission('attendance.create'),
            403
        );
        $date = $request->input('date', Carbon::today()->toDateString());
        $status = $request->input('status', 'Present');
        
        $user = User::whereIn('user_type', [2, 3])->findOrFail($id);

        Attendance::updateOrCreate(
            [
                'created_for' => $user->id,
                'attendance_date' => $date,
            ],
            [
                'institute_id' => $user->institute_id,
                'status' => $status,
                'created_by' => auth()->id(),
            ]
        );
        
        if ($user->user_type == 3 && $date === Carbon::today()->toDateString() && $status === 'Present') {
            $yesterday = Carbon::yesterday()->toDateString();
            if ($user->last_streak_date && $user->last_streak_date->toDateString() === $yesterday) {
                $user->increment('streak_count');
            } else if (!$user->last_streak_date || $user->last_streak_date->toDateString() !== $date) {
                $user->streak_count = 1;
            }
            if (!$user->last_streak_date || $user->last_streak_date->toDateString() !== $date) {
                $user->last_streak_date = $date;
                $user->save();
                
                // Fire XP/Badge rules for login/attendance
                $engine = new AutoRuleEngine($user);
                $engine->fire('login');
            }
        }

        return redirect()->back()->with('success', 'Attendance marked as ' . $status . ' for ' . $user->name);
    }

    public function unmark(Request $request, $id)
    {
        abort_unless(
            auth()->user()->hasPermission('attendance.create'),
            403
        );
        $date = $request->input('date', Carbon::today()->toDateString());
        
        $user = User::whereIn('user_type', [2, 3])->findOrFail($id);

        Attendance::where('created_for', $user->id)->where('attendance_date', $date)->delete();

        return redirect()->back()->with('success', 'Attendance unmarked for ' . $user->name);
    }

    public function markBulk(Request $request)
    {
        abort_unless(
            auth()->user()->hasPermission('attendance.create'),
            403
        );
        $date = $request->input('attendance_date', Carbon::today()->toDateString());
        $records = $request->input('records', []);
        
        $instituteId = auth()->user()->institute_id;
        
        foreach ($records as $record) {
            $userId = $record['created_for'] ?? null;
            $status = $record['status'] ?? '';
            
            if (!$userId) continue;
            
            if (empty($status) || $status == 'Clear') {
                // Clear the attendance
                Attendance::where('created_for', $userId)->where('attendance_date', $date)->delete();
                continue;
            }
            
            $user = User::where('institute_id', $instituteId)->find($userId);
            if (!$user) continue;

            Attendance::updateOrCreate(
                [
                    'created_for' => $userId,
                    'attendance_date' => $date,
                ],
                [
                    'institute_id' => $instituteId,
                    'status' => $status,
                    'created_by' => auth()->id(),
                ]
            );
            
            // Streak logic for students if present today
            if ($user->user_type == 3 && $date === Carbon::today()->toDateString() && $status === 'Present') {
                $yesterday = Carbon::yesterday()->toDateString();
                // Prevent duplicate streak increment for today
                if (!$user->last_streak_date || $user->last_streak_date->toDateString() !== $date) {
                    if ($user->last_streak_date && $user->last_streak_date->toDateString() === $yesterday) {
                        $user->increment('streak_count');
                    } else {
                        $user->streak_count = 1;
                    }
                    $user->last_streak_date = $date;
                    $user->save();
                    
                    // Fire XP/Badge rules for login/attendance
                    $engine = new AutoRuleEngine($user);
                    $engine->fire('login');
                }
            }
        }
        
        return back()->with('success', 'Attendance marked successfully!');
    }

    public function readNotification($id)
    {
        $notification = auth()->user()->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
            
            if (isset($notification->data['action_url'])) {
                return redirect($notification->data['action_url']);
            }
        }
        
        return back();
    }

    public function markNotificationsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}