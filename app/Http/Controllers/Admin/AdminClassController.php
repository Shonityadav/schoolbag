<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminClassController extends Controller
{
    /**
     * List all classes with search + pagination.
     */
    public function index(Request $request)
    {
        $query = ClassModel::where('institute_id', auth()->user()->institute_id)
            ->with(['ebooks:id,name'])
            ->withCount(['students'])
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('standard', 'like', "%$s%")
                  ->orWhere('section', 'like', "%$s%");
            });
        }

        $classes = $query->paginate(15)->withQueryString();

        return view('admin.classes.index', compact('classes'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.classes.form', ['class' => null]);
    }

    /**
     * Store a new class.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'standard'    => 'required|string|max:100',
            'section'     => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        ClassModel::create([
            'institute_id' => Auth::user()->institute_id ?? 1,
            'standard'     => $data['standard'],
            'section'      => $data['section'],
            'description'  => $data['description'],
        ]);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(ClassModel $class)
    {
        abort_unless($class->institute_id == auth()->user()->institute_id, 404);
        return view('admin.classes.form', ['class' => $class]);
    }

    /**
     * Update class.
     */
    public function update(Request $request, ClassModel $class)
    {
        abort_unless($class->institute_id == auth()->user()->institute_id, 404);
        $data = $request->validate([
            'standard'    => 'required|string|max:100',
            'section'     => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $class->update([
            'standard'    => $data['standard'],
            'section'     => $data['section'],
            'description' => $data['description'],
        ]);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class updated successfully.');
    }

    /**
     * Delete class.
     */
    public function destroy(ClassModel $class)
    {
        abort_unless($class->institute_id == auth()->user()->institute_id, 404);
        if ($class->students()->count() > 0) {
            return redirect()->route('admin.classes.index')
                ->with('error', 'Cannot delete class with existing students. Please reassign them first.');
        }

        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class deleted successfully.');
    }
}

