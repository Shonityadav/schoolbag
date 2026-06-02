<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminStaffController extends Controller
{
    /**
     * List all staff with search + pagination.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'admin')
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%");
            });
        }

        $staff = $query->paginate(15)->withQueryString();

        return view('admin.staff.index', compact('staff'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.staff.form', ['member' => null]);
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
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => 'admin',
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(User $staff)
    {
        abort_unless($staff->role === 'admin', 404);
        return view('admin.staff.form', ['member' => $staff]);
    }

    /**
     * Update staff member.
     */
    public function update(Request $request, User $staff)
    {
        abort_unless($staff->role === 'admin', 404);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($staff->id)],
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $staff->name  = $data['name'];
        $staff->email = $data['email'];
        $staff->phone = $data['phone'] ?? null;

        if (!empty($data['password'])) {
            $staff->password = Hash::make($data['password']);
        }

        $staff->save();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    /**
     * Delete staff member.
     */
    public function destroy(User $staff)
    {
        abort_unless($staff->role === 'admin', 404);
        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }

    /**
     * Download a sample CSV template for staff.
     */
    public function sampleCsv()
    {
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="staff_sample.csv"'];
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
                'name'     => $name,
                'email'    => $email,
                'phone'    => $phone ?: null,
                'password' => Hash::make($password),
                'role'     => 'admin',
            ]);
            $imported++;
        }
        fclose($handle);

        return redirect()->route('admin.staff.create')
            ->with('import_result', compact('imported', 'skipped', 'errors'));
    }
}

