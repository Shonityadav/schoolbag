<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\StudentDetails;
use Illuminate\Support\Facades\DB;

class AdminStudentDetailsController extends Controller
{

    private function canAccessStudent(User $studentUser)
    {
        $user = auth()->user();

        if ($user->user_type == 1) {
            return true;
        }

        $assignedClassIds = $user->classes()
            ->pluck('classes.id')
            ->toArray();

        return StudentDetails::where(
            'created_for',
            $studentUser->id
        )
        ->whereIn('class_id', $assignedClassIds)
        ->exists();
    }
    /**
     * List all students with search + pagination.
     */
    public function index(Request $request)
    {
        abort_unless(
            auth()->user()->hasPermission('student_details.view'),
            403
        );

        $user = auth()->user();

        $query = User::select('users.*')
            ->join('student_details', 'users.id', '=', 'student_details.created_for')
            ->where('users.user_type', 3)
            ->where('users.institute_id', $user->institute_id)
            ->with(['student.class']);

        // Staff can only see assigned classes
        if ($user->user_type != 1) {

            $assignedClassIds = $user->classes()
                ->pluck('classes.id')
                ->toArray();

            $query->whereHas('student', function ($q) use ($assignedClassIds) {
                $q->whereIn('class_id', $assignedClassIds);
            });
        }

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('users.name', 'like', "%{$search}%")
                ->orWhere('users.email', 'like', "%{$search}%")
                ->orWhere('users.phone', 'like', "%{$search}%");

            });
        }

        if ($request->filled('class_id')) {

            $query->whereHas('student', function ($q) use ($request) {

                $q->where('class_id', $request->class_id);

            });
        }

        $students = $query->orderBy('student_details.roll_no', 'asc')
            ->paginate(15)
            ->withQueryString();

        if ($user->user_type == 1) {

            // Institute Admin sees all classes
            $classes = ClassModel::where(
                'institute_id',
                $user->institute_id
            )
            ->orderBy('standard')
            ->get();

        } else {

            // Staff sees only assigned classes
            $classes = $user->classes()
                ->orderBy('standard')
                ->get();
        }
        return view(
            'admin.student_details.index',
            compact('students', 'classes')
        );
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $classes = ClassModel::where('institute_id', auth()->user()->institute_id)->orderBy('standard')->get();
        return view('admin.student_details.form', [
            'student' => null,
            'classes' => $classes,
        ]);
    }

    /**
     * Store a new student.
     */
    public function store(Request $request)
    {
        abort_unless(
            auth()->user()->hasPermission('student_details.create'),
            403
        );

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'class_id' => 'required|exists:classes,id',
            'roll_no'  => 'nullable|string|max:50',
            'password' => 'required|string|min:6|confirmed',
        ]);

        DB::transaction(function () use ($data) {

            $user = User::create([
                'institute_id' => auth()->user()->institute_id,
                'name'         => $data['name'],
                'email'        => $data['email'],
                'phone'        => $data['phone'] ?? null,
                'password'     => Hash::make($data['password']),
                'role'         => 'student',
                'user_type'    => 3,
            ]);

            StudentDetails::create([
                'created_for'  => $user->id,
                'institute_id' => auth()->user()->institute_id,
                'class_id'     => $data['class_id'],
                'roll_no'      => $data['roll_no'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.student_details.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(User $student)
    {
        abort_unless(
            auth()->user()->hasPermission('student_details.edit'),
            403
        );

        abort_unless(
            $this->canAccessStudent($student),
            403
        );
        // abort_unless($student->role === 'student' && $student->institute_id == auth()->user()->institute_id, 404);
        $classes = ClassModel::where('institute_id', auth()->user()->institute_id)->orderBy('standard')->get();
        return view('admin.student_details.form', compact('student', 'classes'));
    }

    /**
     * Update student.
     */
    public function update(Request $request, User $student)
{
    abort_unless(
        auth()->user()->hasPermission('student_details.edit'),
        403
    );

    abort_unless(
        $this->canAccessStudent($student),
        403
    );

    $data = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => ['required', 'email', Rule::unique('users')->ignore($student->id)],
        'phone'    => 'nullable|string|max:20',
        'class_id' => [
            'required',
            Rule::exists('classes', 'id')
                ->where('institute_id', auth()->user()->institute_id)
        ],
        'roll_no'  => 'nullable|string|max:50',
        'password' => 'nullable|string|min:6|confirmed',
    ]);

    DB::transaction(function () use ($student, $data) {

        // Update User table
        $student->name  = $data['name'];
        $student->email = $data['email'];
        $student->phone = $data['phone'] ?? null;

        if (!empty($data['password'])) {
            $student->password = Hash::make($data['password']);
        }

        $student->save();

        // Update Student table
        StudentDetails::updateOrCreate(
            [
                'created_for' => $student->id
            ],
            [
                'institute_id' => $student->institute_id,
                'class_id'     => $data['class_id'],
                'roll_no'      => $data['roll_no'] ?? null,
            ]
        );
    });

    return redirect()
        ->route('admin.student_details.index')
        ->with('success', 'Student updated successfully.');
}

    /**
     * Delete student.
     */
    public function destroy(User $student)
    {
        abort_unless(
            auth()->user()->hasPermission('student_details.edit'),
            403
        );

        abort_unless(
            $this->canAccessStudent($student),
            403
        );

        // Delete associated student record to prevent orphan data
        if ($student->student) {
            $student->student->delete();
        }

        $student->delete();

        return redirect()->route('admin.student_details.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Download a sample CSV template.
     */
    public function sampleCsv()
    {
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="students_sample.csv"'];
        $rows = [
            ['name', 'email', 'phone', 'password', 'roll_no'],
            ['Aarav Sharma',   'aarav@school.com',   '9876543210', 'pass1234', '101'],
            ['Priya Nair',     'priya@school.com',   '9876543211', 'pass1234', '102'],
            ['Rohan Mehta',    'rohan@school.com',   '',           'pass1234', '103'],
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
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'class_id' => 'required|exists:classes,id',
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

            $map = [];
            foreach ($headers as $i => $h) $map[$h] = $data[$i] ?? '';

            $name     = trim($map['name']     ?? '');
            $email    = trim($map['email']    ?? '');
            $phone    = trim($map['phone']    ?? '');
            $password = trim($map['password'] ?? '');
            $roll_no  = trim($map['roll_no']  ?? '');

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
                'name'         => $name,
                'email'        => $email,
                'phone'        => $phone ?: null,
                'password'     => Hash::make($password),
                'role'         => 'student',
                'user_type'    => 3,
            ]);

            StudentDetails::create([
                'created_for'  => $user->id,
                'institute_id' => auth()->user()->institute_id,
                'class_id'     => $request->class_id,
                'roll_no'      => $roll_no ?: null,
            ]);
            $imported++;
        }
        fclose($handle);

        return redirect()->route('admin.student_details.create')
            ->with('import_result', compact('imported', 'skipped', 'errors'));
    }
}

