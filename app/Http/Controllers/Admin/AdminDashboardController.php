<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Hardcoded dashboard stats — replace with DB queries later
        $stats = [
            'total_students'     => 1284,
            'total_staff'        => 47,
            'total_classes'      => 32,
            'active_courses'     => 18,
            'monthly_revenue'    => 284500,
            'pending_fees'       => 42800,
            'new_admissions'     => 23,
            'attendance_rate'    => 91,
        ];

        $recentTransactions = [
            ['id' => 'TXN-2001', 'student' => 'Aarav Sharma',    'class' => 'Grade 5-A', 'amount' => 12500, 'status' => 'paid',    'date' => '01 Jun 2025'],
            ['id' => 'TXN-2002', 'student' => 'Priya Nair',      'class' => 'Grade 3-B', 'amount' =>  8750, 'status' => 'paid',    'date' => '01 Jun 2025'],
            ['id' => 'TXN-2003', 'student' => 'Rohan Mehta',     'class' => 'Grade 7-C', 'amount' => 15000, 'status' => 'pending', 'date' => '31 May 2025'],
            ['id' => 'TXN-2004', 'student' => 'Sneha Iyer',      'class' => 'Grade 2-A', 'amount' =>  7200, 'status' => 'paid',    'date' => '30 May 2025'],
            ['id' => 'TXN-2005', 'student' => 'Karan Patel',     'class' => 'Grade 6-B', 'amount' => 13800, 'status' => 'overdue', 'date' => '28 May 2025'],
            ['id' => 'TXN-2006', 'student' => 'Ananya Krishnan', 'class' => 'Grade 4-C', 'amount' =>  9500, 'status' => 'paid',    'date' => '27 May 2025'],
        ];

        $topClasses = [
            ['name' => 'Grade 7-A', 'student_details' => 42, 'attendance' => 96, 'progress' => 88],
            ['name' => 'Grade 5-B', 'student_details' => 38, 'attendance' => 93, 'progress' => 82],
            ['name' => 'Grade 3-A', 'student_details' => 40, 'attendance' => 90, 'progress' => 79],
            ['name' => 'Grade 6-C', 'student_details' => 36, 'attendance' => 88, 'progress' => 75],
        ];

        $recentStudents = [
            ['name' => 'Mihika Reddy',   'class' => 'Grade 1-A', 'joined' => '01 Jun 2025', 'avatar' => '👧'],
            ['name' => 'Dev Choudhary',  'class' => 'Grade 4-B', 'joined' => '01 Jun 2025', 'avatar' => '👦'],
            ['name' => 'Tara Pillai',    'class' => 'Grade 2-C', 'joined' => '31 May 2025', 'avatar' => '👧'],
            ['name' => 'Arjun Bose',     'class' => 'Grade 8-A', 'joined' => '30 May 2025', 'avatar' => '👦'],
            ['name' => 'Ishita Kapoor',  'class' => 'Grade 5-B', 'joined' => '29 May 2025', 'avatar' => '👧'],
        ];

        return view('admin.dashboard', compact(
            'stats', 'recentTransactions', 'topClasses', 'recentStudents'
        ));
    }
}
