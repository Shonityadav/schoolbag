<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user    = Auth::user()->load(['studentClass', 'studentBadges.badge']);
        $badges  = $user->studentBadges->map(fn($sb) => $sb->badge);

        $xpHistory = $user->xpTransactions()->latest()->take(20)->get();

        // Build 12-week attendance heatmap
        $attendances = Attendance::where('user_id', $user->id)
            ->where('date', '>=', now()->subWeeks(12))
            ->pluck('date')
            ->map(fn($d) => $d->toDateString())
            ->toArray();

        $quizStats = [
            'total'  => $user->quizAttempts()->count(),
            'passed' => $user->quizAttempts()->where('status', 'pass')->count(),
            'avg'    => $user->quizAttempts()->avg('percentage') ?? 0,
        ];

        return view('student.profile.index', compact(
            'user', 'badges', 'xpHistory', 'attendances', 'quizStats'
        ));
    }
}
