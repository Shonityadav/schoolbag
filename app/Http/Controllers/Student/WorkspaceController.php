<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('student.workspace', compact('user'));
    }

    public function profile()
    {
        $user = auth()->user();
        
        $attendanceData = \App\Models\Attendance::where('created_for', $user->id)
            ->get(['attendance_date', 'status'])
            ->mapWithKeys(function ($att) {
                return [\Carbon\Carbon::parse($att->attendance_date)->toDateString() => $att->status];
            });

        return view('student.profile', compact('user', 'attendanceData'));
    }
}
