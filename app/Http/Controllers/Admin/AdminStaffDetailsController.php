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
            'employee_id' => 'nullable|string|max:50',
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
                'employee_id' => $data['employee_id'] ?? null,
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
    public function show(User $staff)
    {
        abort_unless($staff->user_type == 2 && $staff->institute_id == auth()->user()->institute_id, 404);
        $staff->load('staff.category', 'classes', 'permissions');
        return view('admin.staff_details.show', compact('staff'));
    }

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
            'employee_id' => 'nullable|string|max:50',
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
            'user_img' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $staff->name  = $data['name'];
        $staff->email = $data['email'];
        $staff->phone = $data['phone'] ?? null;

        if (!empty($data['password'])) {
            $staff->password = Hash::make($data['password']);
        }

        if ($request->hasFile('user_img')) {
            $instituteId = auth()->user()->institute_id;
            $uploadDir = public_path("uploads/institute-{$instituteId}/user");
            if (!\Illuminate\Support\Facades\File::exists($uploadDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($uploadDir, 0755, true);
            }
            $file = $request->file('user_img');
            $staffDetail = $staff->staff;
            $filename = ($staffDetail->employee_id ?? $staff->id) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $staff->user_img = "uploads/institute-{$instituteId}/user/{$filename}";
        }

        $staff->save();
        if ($staff->staff) {

            $staff->staff->update([
                'staff_category_id' => $data['staff_category_id'],
                'designation' => $data['designation'] ?? null,
                'department' => $data['department'] ?? null,
                'employee_id' => $data['employee_id'] ?? null,
                'salary' => $data['salary'] ?? null,
            ]);

        } else {

            StaffDetails::create([
                'created_for' => $staff->id,
                'institute_id' => $staff->institute_id,
                'staff_category_id' => $data['staff_category_id'],
                'designation' => $data['designation'] ?? null,
                'department' => $data['department'] ?? null,
                'employee_id' => $data['employee_id'] ?? null,
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
            ['name', 'email', 'phone', 'password', 'employee_id', 'salary'],
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
            $employee_id= trim($map['employee_id']?? '');
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
                'employee_id' => $employee_id ?: null,
                'salary' => $salary !== '' ? floatval($salary) : null,
                'joining_date' => now(),
            ]);
            $imported++;
        }
        fclose($handle);

        return redirect()->route('admin.staff_details.create')
            ->with('import_result', compact('imported', 'skipped', 'errors'));
    }

    public function uploadPhotosForm()
    {
        $staffMembers = User::where('user_type', 2)
            ->where('institute_id', auth()->user()->institute_id)
            ->with('staff')
            ->orderBy('name')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'employee_id' => $s->staff->employee_id ?? null,
                    'has_image' => !empty($s->user_img),
                ];
            });

        return view('admin.staff_details.upload_photos', compact('staffMembers'));
    }

    public function processUploadPhotos(Request $request)
    {
        $request->validate([
            'upload_type' => 'required|in:single,bulk',
            'user_id' => 'required_if:upload_type,single|exists:users,id',
            'photo' => 'required_if:upload_type,single|image|mimes:jpg,jpeg,png|max:5120', // 5MB
            'zip_file' => 'required_if:upload_type,bulk|mimes:zip|max:51200', // 50MB
        ]);

        $instituteId = auth()->user()->institute_id;
        $uploadType = $request->upload_type;

        if ($uploadType === 'single') {
            $user = User::findOrFail($request->user_id);
            if ($user->institute_id != $instituteId || $user->user_type != 2) {
                abort(403);
            }

            $file = $request->file('photo');
            $filename = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = public_path("uploads/institute-{$instituteId}/user");
            
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file->move($path, $filename);

            $user->user_img = "uploads/institute-{$instituteId}/user/{$filename}";
            $user->save();

            return redirect()->route('admin.staff_details.index')->with('success', 'Staff photo uploaded successfully.');
        }

        if ($uploadType === 'bulk') {
            $zipFile = $request->file('zip_file');
            
            $zip = new \ZipArchive;
            if ($zip->open($zipFile->getPathname()) === TRUE) {
                $extractPath = storage_path('app/temp/zip_' . time());
                $zip->extractTo($extractPath);
                $zip->close();

                $files = \Illuminate\Support\Facades\File::allFiles($extractPath);
                $imported = 0;
                $skipped = 0;

                foreach ($files as $file) {
                    $ext = strtolower($file->getExtension());
                    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                        $skipped++;
                        continue;
                    }

                    $filenameWithoutExt = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    
                    $staff = StaffDetails::where('institute_id', $instituteId)
                        ->where('employee_id', $filenameWithoutExt)
                        ->first();

                    if ($staff && $staff->user) {
                        if (!empty($staff->user->user_img) && !$request->has('overwrite_existing')) {
                            $skipped++;
                            continue;
                        }

                        $newFilename = $staff->user->id . '_' . time() . '.' . $ext;
                        $destPath = public_path("uploads/institute-{$instituteId}/user");
                        
                        if (!file_exists($destPath)) {
                            mkdir($destPath, 0777, true);
                        }

                        \Illuminate\Support\Facades\File::copy($file->getPathname(), $destPath . '/' . $newFilename);

                        $staff->user->user_img = "uploads/institute-{$instituteId}/user/{$newFilename}";
                        $staff->user->save();
                        $imported++;
                    } else {
                        $skipped++;
                    }
                }

                \Illuminate\Support\Facades\File::deleteDirectory($extractPath);

                return redirect()->route('admin.staff_details.index')->with('success', "Bulk upload completed. {$imported} photos imported, {$skipped} skipped.");
            } else {
                return back()->with('error', 'Failed to open ZIP file.');
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
                
                if (!isset($info['extension'])) continue;

                $ext = strtolower($info['extension']);
                if (!in_array($ext, ['jpg', 'jpeg', 'png'])) continue;

                $empId = $info['filename'];

                $staff = StaffDetails::with('user')
                    ->where('institute_id', $instituteId)
                    ->where('employee_id', $empId)
                    ->first();

                if ($staff && $staff->user) {
                    $matched[] = [
                        'filename' => $info['basename'],
                        'employee_id' => $empId,
                        'name' => $staff->user->name,
                        'has_existing' => !empty($staff->user->user_img),
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

