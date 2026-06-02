<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentClass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminClassController extends Controller
{
    /**
     * List all classes with search + pagination.
     */
    public function index(Request $request)
    {
        $query = StudentClass::withCount(['students', 'courses'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('name', 'like', "%$s%");
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
            'name'  => 'required|string|max:255|unique:classes,name',
            'level' => 'required|integer|min:1|max:12',
            'icon'  => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
        ]);

        StudentClass::create([
            'name'  => $data['name'],
            'level' => $data['level'],
            'icon'  => $data['icon'] ?? 'bi-building',
            'color' => $data['color'] ?? '#4F46E5',
        ]);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(StudentClass $class)
    {
        return view('admin.classes.form', compact('class'));
    }

    /**
     * Update class.
     */
    public function update(Request $request, StudentClass $class)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255', Rule::unique('classes')->ignore($class->id)],
            'level' => 'required|integer|min:1|max:12',
            'icon'  => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
        ]);

        $class->update([
            'name'  => $data['name'],
            'level' => $data['level'],
            'icon'  => $data['icon'] ?? 'bi-building',
            'color' => $data['color'] ?? '#4F46E5',
        ]);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class updated successfully.');
    }

    /**
     * Delete class.
     */
    public function destroy(StudentClass $class)
    {
        if ($class->students()->count() > 0) {
            return redirect()->route('admin.classes.index')
                ->with('error', 'Cannot delete class with existing students. Please reassign them first.');
        }

        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class deleted successfully.');
    }
}
