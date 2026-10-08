<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminQuestionReviewController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = LessonProgress::where('stage_number', 4)
            ->with(['user.studentClass', 'chapter'])
            ->orderBy('created_at', 'desc');

        if (!$user->isAdmin() && $user->role === 'staff') {
            $classIds = $user->classes()->pluck('classes.id')->toArray();
            $query->whereHas('user', function($q) use ($classIds) {
                $q->whereIn('class_id', $classIds);
            });
        }

        $submissions = $query->get();
        
        $pendingReviews = $submissions->where('is_reviewed', false);
        $completedReviews = $submissions->where('is_reviewed', true);

        return view('admin.question_reviews.index', compact('pendingReviews', 'completedReviews'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'score' => 'required|numeric|min:0'
        ]);

        $progress = LessonProgress::findOrFail($id);
        
        $user = Auth::user();
        if (!$user->isAdmin() && $user->role === 'staff') {
            $classIds = $user->classes()->pluck('classes.id')->toArray();
            if (!in_array($progress->user->class_id, $classIds)) {
                return back()->with('error', 'You are not authorized to review this submission.');
            }
        }

        $progress->score = $request->score;
        $progress->is_reviewed = true;
        $progress->save();

        // Notify the student
        $progress->user->notify(new \App\Notifications\Stage4ReviewedNotification($progress));

        return back()->with('success', 'Score updated successfully.');
    }
}
