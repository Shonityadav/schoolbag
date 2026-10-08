<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class AdminFeeStructureController extends Controller
{
    public function index()
    {
        $instituteId = auth()->user()->institute_id;
        $fees = FeeStructure::where('institute_id', $instituteId)->with('classModel', 'creator')->get();
        $classes = ClassModel::where('institute_id', $instituteId)->orderBy('standard')->get();
        
        return view('admin.fees.index', compact('fees', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,quarterly,half_yearly,yearly,one_time',
            'description' => 'nullable|string',
        ]);

        $instituteId = auth()->user()->institute_id;
        
        // Ensure class belongs to institute
        $class = ClassModel::where('institute_id', $instituteId)->findOrFail($request->class_id);

        FeeStructure::create([
            'institute_id' => $instituteId,
            'class_id' => $class->id,
            'title' => $request->title,
            'amount' => $request->amount,
            'frequency' => $request->frequency,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.fees.index')->with('success', 'Fee structure created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,quarterly,half_yearly,yearly,one_time',
            'description' => 'nullable|string',
        ]);

        $instituteId = auth()->user()->institute_id;
        $fee = FeeStructure::where('institute_id', $instituteId)->findOrFail($id);
        
        // Ensure class belongs to institute
        $class = ClassModel::where('institute_id', $instituteId)->findOrFail($request->class_id);

        $fee->update([
            'class_id' => $class->id,
            'title' => $request->title,
            'amount' => $request->amount,
            'frequency' => $request->frequency,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.fees.index')->with('success', 'Fee structure updated successfully.');
    }

    public function destroy($id)
    {
        $instituteId = auth()->user()->institute_id;
        $fee = FeeStructure::where('institute_id', $instituteId)->findOrFail($id);
        $fee->delete();
        
        return redirect()->route('admin.fees.index')->with('success', 'Fee structure deleted successfully.');
    }
}
