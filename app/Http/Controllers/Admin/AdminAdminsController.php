<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminAdminsController extends Controller
{
    /**
     * List all staff with search + pagination.
     */
    public function index(Request $request)
    {
        $query = User::where('user_type', 1)
            ->where('institute_id', auth()->user()->institute_id)
            ->with(['permissions', 'classes'])
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%");
            });
        }

        $admins = $query->paginate(15)->withQueryString();

        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $allPermissions = \App\Models\Permission::all();
        $allClasses = \App\Models\ClassModel::orderBy('standard')->get();
        
        return view('admin.admins.form', [
            'member' => null,
            'allPermissions' => $allPermissions,
            'allClasses' => $allClasses
        ]);
    }

    /**
     * Store a new staff member.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'classes' => 'nullable|array',
            'classes.*' => 'exists:classes,id',
        ]);

        $admin = User::create([
            'institute_id' => auth()->user()->institute_id,
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => 'admin',
            'user_type'=> 1,
        ]);

        if (isset($data['permissions'])) {
            $admin->permissions()->sync($data['permissions']);
        }
        
        if (isset($data['classes'])) {
            $admin->classes()->sync($data['classes']);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(User $admin)
    {
        abort_unless($admin->user_type == 1 && $admin->institute_id == auth()->user()->institute_id, 404);
        
        $allPermissions = \App\Models\Permission::all();
        $allClasses = \App\Models\ClassModel::orderBy('standard')->get();
        
        return view('admin.admins.form', [
            'member' => $admin,
            'allPermissions' => $allPermissions,
            'allClasses' => $allClasses
        ]);
    }

    /**
     * Update staff member.
     */
    public function update(Request $request, User $admin)
    {
        abort_unless($admin->user_type == 1 && $admin->institute_id == auth()->user()->institute_id, 404);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($admin->id)],
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'classes' => 'nullable|array',
            'classes.*' => 'exists:classes,id',
        ]);

        $admin->name  = $data['name'];
        $admin->email = $data['email'];
        $admin->phone = $data['phone'] ?? null;

        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();

        if (isset($data['permissions'])) {
            $admin->permissions()->sync($data['permissions']);
        } else {
            $admin->permissions()->detach();
        }
        
        if (isset($data['classes'])) {
            $admin->classes()->sync($data['classes']);
        } else {
            $admin->classes()->detach();
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin updated successfully.');
    }

    /**
     * Delete staff member.
     */
    public function destroy(User $admin)
    {
        abort_unless($admin->user_type == 1 && $admin->institute_id == auth()->user()->institute_id, 404);
        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin deleted successfully.');
    }

    /**
     * Download a sample CSV template for staff.
     */
    public function sampleCsv()
    {
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="admin_sample.csv"'];
        $rows = [
            ['name', 'email', 'phone', 'password'],
            ['Priya Sharma',  'priya@school.com',  '9876543210', 'pass1234'],
            ['Ravi Kumar',    'ravi@school.com',   '9876543211', 'pass1234'],
            ['Meena Iyer',    'meena@school.com',  '',           'pass1234'],
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
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:2048']);

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

            User::create([
                'institute_id' => auth()->user()->institute_id,
                'name'     => $name,
                'email'    => $email,
                'phone'    => $phone ?: null,
                'password' => Hash::make($password),
                'role'     => 'admin',
                'user_type'=> 1,
            ]);
            $imported++;
        }
        fclose($handle);

        return redirect()->route('admin.admins.create')
            ->with('import_result', compact('imported', 'skipped', 'errors'));
    }
}

