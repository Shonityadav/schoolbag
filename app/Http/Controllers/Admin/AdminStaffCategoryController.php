<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffCategory;
use Illuminate\Http\Request;

class AdminStaffCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = StaffCategory::where(
            'institute_id',
            auth()->user()->institute_id
        );

        if ($request->filled('search')) {

            $query->where(
                'name',
                'like',
                '%' . $request->search . '%'
            );
        }

        $categories = $query
            ->latest()
            ->paginate(15);

        return view(
            'admin.staff-categories.index',
            compact('categories')
        );
    }

    public function create()
    {
        return view(
            'admin.staff-categories.form',
            [
                'category' => null
            ]
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        StaffCategory::create([
            'institute_id' => auth()->user()->institute_id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
        ChatRoom::create([
            'institute_id' => auth()->user()->institute_id,
            'name' => $category->name,
            'type' => 'staff_category',
            'staff_category_id' => $category->id,
        ]);

        return redirect()
            ->route('admin.staff-categories.index')
            ->with(
                'success',
                'Category created successfully.'
            );
    }

    public function edit(StaffCategory $staffCategory)
    {
        abort_unless(
            $staffCategory->institute_id ==
            auth()->user()->institute_id,
            404
        );

        return view(
            'admin.staff-categories.form',
            [
                'category' => $staffCategory
            ]
        );
    }

    public function update(
        Request $request,
        StaffCategory $staffCategory
    ) {
        abort_unless(
            $staffCategory->institute_id ==
            auth()->user()->institute_id,
            404
        );

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $staffCategory->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.staff-categories.index')
            ->with(
                'success',
                'Category updated successfully.'
            );
    }

    public function destroy(
        StaffCategory $staffCategory
    ) {
        abort_unless(
            $staffCategory->institute_id ==
            auth()->user()->institute_id,
            404
        );

        if ($staffCategory->staffs()->count()) {

            return back()->with(
                'error',
                'Category has staff assigned.'
            );
        }

        $staffCategory->delete();

        return redirect()
            ->route('admin.staff-categories.index')
            ->with(
                'success',
                'Category deleted successfully.'
            );
    }
}