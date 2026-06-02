<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminStudentController extends Controller
{
    /**
     * List all students with search + pagination.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'student')
            ->with('studentClass')
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%");
            });
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $students = $query->paginate(15)->withQueryString();
        $classes  = ClassModel::orderBy('standard')->get();

        return view('admin.students.index', compact('students', 'classes'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $classes = ClassModel::orderBy('standard')->get();
        return view('admin.students.form', [
            'student' => null,
            'classes' => $classes,
        ]);
    }

    /**
     * Store a new student.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'class_id' => 'nullable|exists:classes,id',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'class_id' => $data['class_id'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => 'student',
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(User $student)
    {
        abort_unless($student->role === 'student', 404);
        $classes = ClassModel::orderBy('standard')->get();
        return view('admin.students.form', compact('student', 'classes'));
    }

    /**
     * Update student.
     */
    public function update(Request $request, User $student)
    {
        abort_unless($student->role === 'student', 404);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($student->id)],
            'phone'    => 'nullable|string|max:20',
            'class_id' => 'nullable|exists:classes,id',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $student->name     = $data['name'];
        $student->email    = $data['email'];
        $student->phone    = $data['phone'] ?? null;
        $student->class_id = $data['class_id'] ?? null;

        if (!empty($data['password'])) {
            $student->password = Hash::make($data['password']);
        }

        $student->save();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Delete student.
     */
    public function destroy(User $student)
    {
        abort_unless($student->role === 'student', 404);
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Download a sample CSV template.
     */
    public function sampleCsv()
    {
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="students_sample.csv"'];
        $rows = [
            ['name', 'email', 'phone', 'class_name', 'password'],
            ['Aarav Sharma',   'aarav@school.com',   '9876543210', 'Class 1', 'pass1234'],
            ['Priya Nair',     'priya@school.com',   '9876543211', 'Class 2', 'pass1234'],
            ['Rohan Mehta',    'rohan@school.com',   '',           '',        'pass1234'],
        ];
        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $row) fputcsv($out, $row);
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk import students from CSV.
     */
    public function importCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:2048']);

        $file    = $request->file('csv_file');
        $handle  = fopen($file->getPathname(), 'r');
        $headers = array_map(fn($h) => strtolower(trim($h)), fgetcsv($handle));

        $classes = ClassModel::pluck('id', 'standard')->all(); // standard => id map
        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $row      = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            if (empty(array_filter($data))) continue;

            $map = [];
            foreach ($headers as $i => $h) $map[$h] = $data[$i] ?? '';

            $name     = trim($map['name']     ?? '');
            $email    = trim($map['email']    ?? '');
            $phone    = trim($map['phone']    ?? '');
            $classNm  = trim($map['class_name'] ?? '');
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
                'class_id' => $classNm && isset($classes[$classNm]) ? $classes[$classNm] : null,
                'password' => \Illuminate\Support\Facades\Hash::make($password),
                'role'     => 'student',
            ]);
            $imported++;
        }
        fclose($handle);

        return redirect()->route('admin.students.create')
            ->with('import_result', compact('imported', 'skipped', 'errors'));
    }
}

