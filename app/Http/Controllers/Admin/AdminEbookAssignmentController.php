<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ebook;
use App\Models\ClassModel;
use App\Models\ClassEbook;

class AdminEbookAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $institute_id = auth()->user()->institute_id;
        
        $query = Ebook::query();
        
        if ($request->filled('publication')) {
            $query->where('publication', 'like', '%' . $request->publication . '%');
        }
        if ($request->filled('subject')) {
            $query->where('subject', 'like', '%' . $request->subject . '%');
        }
        if ($request->filled('standard')) {
            $query->where('standard', $request->standard);
        }
        
        $ebooks = $query->paginate(20);
        
        // Fetch classes for the dropdown
        $classes = ClassModel::where('institute_id', $institute_id)->get();
        
        // Fetch assigned ebooks to show badges (optional, but helpful)
        $assignedEbooks = ClassEbook::where('institute_id', $institute_id)
            ->with('classModel')
            ->get()
            ->groupBy('ebook_id');
            
        // Get unique subjects, publications, standards for filters
        $filters = [
            'subjects' => Ebook::select('subject')->distinct()->whereNotNull('subject')->pluck('subject'),
            'publications' => Ebook::select('publication')->distinct()->whereNotNull('publication')->pluck('publication'),
            'standards' => Ebook::select('standard')->distinct()->whereNotNull('standard')->pluck('standard'),
        ];

        return view('admin.ebook_assignments.index', compact('ebooks', 'classes', 'assignedEbooks', 'filters'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'ebook_ids' => 'required|array',
            'ebook_ids.*' => 'exists:ebooks,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        $institute_id = auth()->user()->institute_id;
        $class_id = $request->class_id;

        // Verify class belongs to institute
        $class = ClassModel::where('id', $class_id)->where('institute_id', $institute_id)->firstOrFail();

        $count = 0;
        foreach ($request->ebook_ids as $ebook_id) {
            $assignment = ClassEbook::firstOrCreate([
                'ebook_id' => $ebook_id,
                'class_id' => $class_id,
                'institute_id' => $institute_id,
            ], [
                'created_by' => auth()->id(),
            ]);
            
            if ($assignment->wasRecentlyCreated) {
                $count++;
            } else {
                // Optionally update updated_by if it already exists, or just skip
                $assignment->updated_by = auth()->id();
                $assignment->save();
            }
        }

        return redirect()->back()->with('success', "Successfully assigned {$count} new Ebook(s) to {$class->standard} {$class->section}");
    }
}