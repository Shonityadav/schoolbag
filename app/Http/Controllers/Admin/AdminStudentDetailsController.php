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
            'admission_number' => 'nullable|string|max:50',
            'roll_no'  => 'nullable|string|max:50',
            'fee'      => 'nullable|numeric|min:0',
            'fee_period' => 'nullable|string|max:255',
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
                'admission_number' => $data['admission_number'] ?? null,
                'roll_no'      => $data['roll_no'] ?? null,
                'fee'          => $data['fee'] ?? null,
                'fee_period'   => $data['fee_period'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.student_details.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Show edit form.
     */
    public function show(User $student)
    {
        abort_unless(in_array($student->user_type, [1, 3]) && $student->institute_id == auth()->user()->institute_id, 404);
        $student->load('student.class');
        return view('admin.student_details.show', compact('student'));
    }

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
        'admission_number' => 'nullable|string|max:50',
        'roll_no'  => 'nullable|string|max:50',
        'fee'      => 'nullable|numeric|min:0',
        'fee_period' => 'nullable|string|max:255',
        'password' => 'nullable|string|min:6|confirmed',
        'user_img' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
    ]);

    DB::transaction(function () use ($student, $data, $request) {

        // Update User table
        $student->name  = $data['name'];
        $student->email = $data['email'];
        $student->phone = $data['phone'] ?? null;

        if (!empty($data['password'])) {
            $student->password = Hash::make($data['password']);
        }

        if ($request->hasFile('user_img')) {
            $instituteId = auth()->user()->institute_id;
            $uploadDir = public_path("uploads/institute-{$instituteId}/user");
            if (!\Illuminate\Support\Facades\File::exists($uploadDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($uploadDir, 0755, true);
            }
            $file = $request->file('user_img');
            $studentDetail = $student->student;
            $filename = ($studentDetail->admission_number ?? $student->id) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $student->user_img = "uploads/institute-{$instituteId}/user/{$filename}";
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
                'admission_number' => $data['admission_number'] ?? null,
                'roll_no'      => $data['roll_no'] ?? null,
                'fee'          => $data['fee'] ?? null,
                'fee_period'   => $data['fee_period'] ?? null,
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
            ['name', 'email', 'phone', 'password', 'admission_number', 'roll_no', 'fee', 'fee_period'],
            ['Aarav Sharma',   'aarav@school.com',   '9876543210', 'pass1234', 'ADM-001', '101', '5000', 'Monthly'],
            ['Priya Nair',     'priya@school.com',   '9876543211', 'pass1234', 'ADM-002', '102', '15000', 'Quarterly'],
            ['Rohan Mehta',    'rohan@school.com',   '',           'pass1234', 'ADM-003', '103', '60000', 'Yearly'],
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
            $password   = trim($map['password']   ?? '');
            $admission_number = trim($map['admission_number'] ?? '');
            $roll_no    = trim($map['roll_no']    ?? '');
            $fee        = trim($map['fee']        ?? '');
            $fee_period = trim($map['fee_period'] ?? '');

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
                'admission_number' => $admission_number ?: null,
                'roll_no'      => $roll_no ?: null,
                'fee'          => $fee !== '' ? floatval($fee) : null,
                'fee_period'   => $fee_period ?: null,
            ]);
            $imported++;
        }
        fclose($handle);

        return redirect()->route('admin.student_details.create')
            ->with('import_result', compact('imported', 'skipped', 'errors'));
    }

    public function uploadPhotosForm()
    {
        $students = StudentDetails::with('user')
            ->where('institute_id', auth()->user()->institute_id)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->user->id,
                    'name' => $s->user->name,
                    'admission_number' => $s->admission_number,
                    'has_image' => !empty($s->user->user_img)
                ];
            });

        return view('admin.student_details.upload_photos', compact('students'));
    }

    public function processUploadPhotos(Request $request)
    {
        $request->validate([
            'upload_type' => 'required|in:single,bulk',
        ]);

        $instituteId = auth()->user()->institute_id;
        $uploadDir = public_path("uploads/institute-{$instituteId}/user");
        if (!\Illuminate\Support\Facades\File::exists($uploadDir)) {
            \Illuminate\Support\Facades\File::makeDirectory($uploadDir, 0755, true);
        }

        if ($request->upload_type === 'single') {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            ]);

            $user = User::where('id', $request->user_id)->where('institute_id', $instituteId)->firstOrFail();
            
            $file = $request->file('photo');
            $studentDetail = $user->student;
            $filename = ($studentDetail->admission_number ?? $user->id) . '.' . $file->getClientOriginalExtension();
            
            $file->move($uploadDir, $filename);
            
            $user->user_img = "uploads/institute-{$instituteId}/user/{$filename}";
            $user->save();

            return redirect()->back()->with('success', 'Photo uploaded successfully.');
        }

        if ($request->upload_type === 'bulk') {
            $request->validate([
                'zip_file' => 'required|mimes:zip|max:51200', // max 50MB
            ]);

            $zipFile = $request->file('zip_file');
            $zip = new \ZipArchive;
            if ($zip->open($zipFile->getPathname()) === TRUE) {
                $tempDir = storage_path('app/temp_zip_extract_' . time());
                $zip->extractTo($tempDir);
                $zip->close();

                $files = \Illuminate\Support\Facades\File::allFiles($tempDir);
                $imported = 0;
                $skipped = 0;

                foreach ($files as $file) {
                    $ext = strtolower($file->getExtension());
                    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                        $skipped++;
                        continue;
                    }

                    $filenameWithoutExt = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    
                    // Match admission_number
                    $student = StudentDetails::where('institute_id', $instituteId)
                        ->where('admission_number', $filenameWithoutExt)
                        ->first();

                    if ($student && $student->user) {
                        if (!empty($student->user->user_img) && !$request->has('overwrite_existing')) {
                            $skipped++;
                            continue;
                        }

                        $newFilename = $filenameWithoutExt . '.' . $ext;
                        \Illuminate\Support\Facades\File::copy($file->getPathname(), $uploadDir . '/' . $newFilename);
                        
                        $student->user->user_img = "uploads/institute-{$instituteId}/user/{$newFilename}";
                        $student->user->save();
                        $imported++;
                    } else {
                        $skipped++;
                    }
                }

                \Illuminate\Support\Facades\File::deleteDirectory($tempDir);

                return redirect()->back()->with('success', "Bulk upload completed. $imported photos imported, $skipped skipped.");
            } else {
                return redirect()->back()->with('error', 'Failed to open the ZIP file.');
            }
        }
    }

    public function previewZipUpload(Request $request)
    {
        $request->validate([
            'zip_file' => 'required|mimes:zip|max:51200', // max 50MB
        ]);

        $zipFile = $request->file('zip_file');
        $zip = new \ZipArchive;
        if ($zip->open($zipFile->getPathname()) === TRUE) {
            $matched = [];
            $unmatched = [];

            $instituteId = auth()->user()->institute_id;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $info = pathinfo($filename);
                
                // Skip directories
                if (!isset($info['extension'])) continue;

                $ext = strtolower($info['extension']);
                if (!in_array($ext, ['jpg', 'jpeg', 'png'])) continue;

                // Base filename without extension is the admission number
                $admNo = $info['filename'];

                // Query database
                $student = StudentDetails::with('user')
                    ->where('institute_id', $instituteId)
                    ->where('admission_number', $admNo)
                    ->first();

                if ($student && $student->user) {
                    $matched[] = [
                        'filename' => $info['basename'],
                        'admission_number' => $admNo,
                        'name' => $student->user->name,
                        'has_existing' => !empty($student->user->user_img),
                    ];
                } else {
                    $unmatched[] = $info['basename'];
                }
            }

            $zip->close();

            return response()->json([
                'success' => true,
                'matched' => $matched,
                'unmatched' => $unmatched
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to open ZIP file.'], 400);
    }
}

