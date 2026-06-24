<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffDetails;
use App\Models\Permission;
use App\Models\ClassModel;
use App\Models\StaffCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminStaffDetailsController extends Controller
{
    private function canAccessStaff(User $staffUser)
    {
        $user = auth()->user();

        if ($user->user_type == 1) {
            return true;
        }

        // Must check if the requested staff user belongs to a category the current user manages
        if (!$staffUser->staff) {
            return false; // Or true if unassigned? Let's say false.
        }

        $assignedCategoryIds = $user->managedCategories()
            ->pluck('staff_categories.id')
            ->toArray();

        return in_array($staffUser->staff->staff_category_id, $assignedCategoryIds);
    }
    /**
     * List all staff with search + pagination.
     */
    public function index(Request $request)
    {
        $query = User::where('user_type', 2)
            ->where('institute_id', auth()->user()->institute_id)
            ->with([
                'permissions',
                'classes',
                'staff.category'
            ])
            ->latest();

        // Category restriction
        if (auth()->user()->user_type != 1) {

            $categoryIds = auth()->user()
                ->managedCategories
                ->pluck('id')
                ->toArray();

            $query->whereHas('staff', function ($q) use ($categoryIds) {
                $q->whereIn('staff_category_id', $categoryIds);
            });
        }

        if ($request->filled('search')) {

            $s = $request->search;

            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $staff = $query->paginate(15)->withQueryString();

        return view(
            'admin.staff_details.index',
            compact('staff')
        );
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $allPermissions = Permission::all();

        $allClasses = ClassModel::where(
            'institute_id',
            auth()->user()->institute_id
        )->orderBy('standard')->get();

        $categories = StaffCategory::where(
            'institute_id',
            auth()->user()->institute_id
        )->orderBy('name')->get();

        return view('admin.staff_details.form', [
            'member' => null,
            'allPermissions' => $allPermissions,
            'allClasses' => $allClasses,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a new staff member.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',

            'staff_category_id' => 'required|exists:staff_categories,id',
            'designation' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'employ_id' => 'nullable|string|max:50',
            'salary' => 'nullable|numeric|min:0',

            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',

            'classes' => 'nullable|array',
            'classes.*' => 'exists:classes,id',
            
            'managed_categories' => 'nullable|array',
            'managed_categories.*' => 'exists:staff_categories,id',
        ]);

        DB::transaction(function () use ($data) {

            $staffUser = User::create([
                'institute_id' => auth()->user()->institute_id,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'role' => 'staff',
                'user_type' => 2,
            ]);

            StaffDetails::create([
                'created_for' => $staffUser->id,
                'institute_id' => auth()->user()->institute_id,
                'staff_category_id' => $data['staff_category_id'],
                'designation' => $data['designation'] ?? null,
                'department' => $data['department'] ?? null,
                'employ_id' => $data['employ_id'] ?? null,
                'salary' => $data['salary'] ?? null,
                'joining_date' => now(),
            ]);

            $staffUser->permissions()->sync(
                $data['permissions'] ?? []
            );

            $staffUser->classes()->sync(
                $data['classes'] ?? []
            );

            $staffUser->managedCategories()->sync(
                $data['managed_categories'] ?? []
            );
        });

        return redirect()
            ->route('admin.staff_details.index')
            ->with('success', 'Staff member created successfully.');
    }
    /**
     * Show edit form.
     */
    public function edit(User $staff)
    {
        abort_unless(
            $staff->user_type == 2 &&
            $staff->institute_id == auth()->user()->institute_id,
            404
        );

        abort_unless(
            $this->canAccessStaff($staff),
            403
        );

        $allPermissions = Permission::all();

        $allClasses = ClassModel::where(
            'institute_id',
            auth()->user()->institute_id
        )->get();

        $categories = StaffCategory::where(
            'institute_id',
            auth()->user()->institute_id
        )->get();

        return view('admin.staff_details.form', [
            'member' => $staff->load([
                'permissions',
                'classes',
                'staff'
            ]),
            'allPermissions' => $allPermissions,
            'allClasses' => $allClasses,
            'categories' => $categories,
        ]);
    }

    /**
     * Update staff member.
     */
    public function update(Request $request, User $staff)
    {
        abort_unless($staff->user_type == 2 && $staff->institute_id == auth()->user()->institute_id, 404);

        abort_unless($this->canAccessStaff($staff), 403);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'staff_category_id' => 'required|exists:staff_categories,id',
            'designation' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'employ_id' => 'nullable|string|max:50',
            'salary' => 'nullable|numeric|min:0',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($staff->id)],
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'classes' => 'nullable|array',
            'classes.*' => 'exists:classes,id',
            'managed_categories' => 'nullable|array',
            'managed_categories.*' => 'exists:staff_categories,id',
        ]);

        $staff->name  = $data['name'];
        $staff->email = $data['email'];
        $staff->phone = $data['phone'] ?? null;

        if (!empty($data['password'])) {
            $staff->password = Hash::make($data['password']);
        }

        $staff->save();
        if ($staff->staff) {

            $staff->staff->update([
                'staff_category_id' => $data['staff_category_id'],
                'designation' => $data['designation'] ?? null,
                'department' => $data['department'] ?? null,
                'employ_id' => $data['employ_id'] ?? null,
                'salary' => $data['salary'] ?? null,
            ]);

        } else {

            StaffDetails::create([
                'created_for' => $staff->id,
                'institute_id' => $staff->institute_id,
                'staff_category_id' => $data['staff_category_id'],
                'designation' => $data['designation'] ?? null,
                'department' => $data['department'] ?? null,
                'employ_id' => $data['employ_id'] ?? null,
                'salary' => $data['salary'] ?? null,
                'joining_date' => now(),
            ]);
        }

        if (isset($data['permissions'])) {
            $staff->permissions()->sync($data['permissions']);
        } else {
            $staff->permissions()->detach();
        }
        
        if (isset($data['classes'])) {
            $staff->classes()->sync($data['classes']);
        } else {
            $staff->classes()->detach();
        }

        if (isset($data['managed_categories'])) {
            $staff->managedCategories()->sync($data['managed_categories']);
        } else {
            $staff->managedCategories()->detach();
        }

        return redirect()->route('admin.staff_details.index')
            ->with('success', 'Staff member updated successfully.');
    }

    /**
     * Delete staff member.
     */
    public function destroy(User $staff)
    {
        abort_unless($staff->user_type == 2 && $staff->institute_id == auth()->user()->institute_id, 404);

        abort_unless($this->canAccessStaff($staff), 403);

        if ($staff->staff) {
            $staff->staff->delete();
        }

        $staff->delete();

        return redirect()->route('admin.staff_details.index')
            ->with('success', 'Staff member deleted successfully.');
    }

    /**
     * Download a sample CSV template for staff.
     */
    public function sampleCsv()
    {
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="staff_sample.csv"'];
        $rows = [
            ['name', 'email', 'phone', 'password', 'employ_id', 'salary'],
            ['Priya Sharma',  'priya@school.com',  '9876543210', 'pass1234', 'EMP-001', '50000.00'],
            ['Ravi Kumar',    'ravi@school.com',   '9876543211', 'pass1234', 'EMP-002', '45000.00'],
            ['Meena Iyer',    'meena@school.com',  '',           'pass1234', 'EMP-003', '48000.00'],
        ];
        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $row) fputcsv($out, $row);
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk import staff from CSV.
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'staff_category_id' => 'required|exists:staff_categories,id'
        ]);

        $file    = $request->file('csv_file');
        $handle  = fopen($file->getPathname(), 'r');
        $headers = array_map(fn($h) => strtolower(trim($h)), fgetcsv($handle));

        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $row      = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            if (empty(array_filter($data))) continue;

            $map      = [];
            foreach ($headers as $i => $h) $map[$h] = $data[$i] ?? '';

            $name     = trim($map['name']     ?? '');
            $email    = trim($map['email']    ?? '');
            $phone    = trim($map['phone']    ?? '');
            $password = trim($map['password'] ?? '');
            $employ_id= trim($map['employ_id']?? '');
            $salary   = trim($map['salary']   ?? '');

            if (!$name || !$email || strlen($password) < 6) {
                $errors[] = ['row' => $row, 'message' => "Missing or invalid name/email/password for '{$email}'"];
                $skipped++;
                continue;
            }
            if (User::where('email', $email)->exists()) {
                $errors[] = ['row' => $row, 'message' => "Email '{$email}' already exists — skipped."];
                $skipped++;
                continue;
            }

            $user = User::create([
                'institute_id' => auth()->user()->institute_id,
                'name'     => $name,
                'email'    => $email,
                'phone'    => $phone ?: null,
                'password' => Hash::make($password),
                'role'     => 'staff',
                'user_type'=> 2,
            ]);

            StaffDetails::create([
                'created_for' => $user->id,
                'institute_id' => $user->institute_id,
                'staff_category_id' => $request->staff_category_id,
                'employ_id' => $employ_id ?: null,
                'salary' => $salary !== '' ? floatval($salary) : null,
                'joining_date' => now(),
            ]);
            $imported++;
        }
        fclose($handle);

        return redirect()->route('admin.staff_details.create')
            ->with('import_result', compact('imported', 'skipped', 'errors'));
    }
}

