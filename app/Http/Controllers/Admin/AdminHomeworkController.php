<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminHomeworkController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Admins can see all classes, teachers can only see assigned classes
        if ($user->user_type == 1) {
            $classes = ClassModel::where('institute_id', $user->institute_id)->get();
        } else {
            $classes = $user->classes()->where('institute_id', $user->institute_id)->get();
        }

        // Get recent homeworks for these classes
        $classIds = $classes->pluck('id');
        $homeworks = Homework::whereIn('class_id', $classIds)
            ->with(['studentClass', 'teacher'])
            ->orderBy('for_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('admin.homework.index', compact('classes', 'homeworks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'for_date' => 'required|date',
            'content'  => 'required|string',
        ]);

        $user = Auth::user();

        // Check if teacher is assigned to this class
        if ($user->user_type != 1 && !$user->classes()->where('classes.id', $request->class_id)->exists()) {
            return back()->with('error', 'You are not assigned to this class.');
        }

        Homework::updateOrCreate(
            [
                'institute_id' => $user->institute_id,
                'class_id'     => $request->class_id,
                'for_date'     => $request->for_date,
            ],
            [
                'user_id'      => $user->id,
                'content'      => $request->content,
            ]
        );

        return back()->with('success', 'Homework assigned successfully.');
    }

    public function destroy(Homework $homework)
    {
        $user = Auth::user();
        if ($homework->institute_id !== $user->institute_id) {
            return back()->with('error', 'Unauthorized.');
        }

        $homework->delete();
        return back()->with('success', 'Homework deleted successfully.');
    }
}
