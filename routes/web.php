<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Student\StudentAuthController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Student\LessonController;

use App\Http\Controllers\Student\EbookController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;

use App\Http\Controllers\Admin\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect main site to guest dashboard (welcome page)
Route::get('/', function () {
    return redirect()->route('student.welcome');
});

/*
|--------------------------------------------------------------------------
| School Bag — Student Platform Routes
|--------------------------------------------------------------------------
*/
Route::prefix('student')->name('student.')->group(function () {

    // Guest only
    Route::middleware('guest:student')->group(function () {
        Route::get('/welcome',   function () { return view('student.guest_dashboard'); })->name('welcome');
        Route::get('/login',     [StudentAuthController::class, 'showLogin'])->name('login');
        Route::post('/login',    [StudentAuthController::class, 'login'])->name('login.submit');
        Route::get('/register',  [StudentAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [StudentAuthController::class, 'register'])->name('register.submit');
    });

    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');

    // Authenticated student
    Route::middleware('auth:student')->group(function () {
        Route::get('/dashboard',         [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/subjects',          [CourseController::class, 'index'])->name('courses.index');
        Route::get('/subjects/{id}',     [CourseController::class, 'show'])->name('courses.show');
        Route::get('/subjects/{id}/chapter/{chapter_id}/stage{stage}', [CourseController::class, 'stage'])->name('courses.stage');

        Route::get('/lessons/{id}',      [LessonController::class, 'show'])->name('lessons.show');
        Route::post('/lessons/{id}/done',[LessonController::class, 'complete'])->name('lessons.complete');



        Route::get('/ebooks',                [EbookController::class, 'index'])->name('ebooks');
        Route::get('/ebooks/{id}',           [EbookController::class, 'show'])->name('ebooks.show');
        Route::get('/ebooks/{id}/toc',       [EbookController::class, 'toc'])->name('ebooks.toc');
        Route::post('/ebooks/{id}/download', [EbookController::class, 'download'])->name('ebooks.download');
        Route::post('/ebooks/{id}/assign',   [EbookController::class, 'assign'])->name('ebooks.assign');

        Route::get('/profile', [StudentProfileController::class, 'index'])->name('profile');
        Route::get('/profile/change-password', [StudentProfileController::class, 'changePassword'])->name('profile.change_password');
        Route::post('/profile/verify-otp', [StudentProfileController::class, 'verifyOtp'])->name('profile.verify_otp');
        Route::post('/profile/resend-otp', [StudentProfileController::class, 'resendOtp'])->name('profile.resend_otp');
        Route::post('/profile/update-password', [StudentProfileController::class, 'updatePassword'])->name('profile.update_password');
        Route::post('/profile/avatar', [StudentProfileController::class, 'updateAvatar'])->name('profile.update_avatar');

        Route::get('/terms-and-conditions', function () { return view('student.terms'); })->name('terms');

        Route::post('/attendance/mark', [DashboardController::class, 'markAttendance'])->name('attendance.mark');
    });
});



/*
|--------------------------------------------------------------------------
| Admin Panel Routes — Completely separate from Student routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\AdminAdminsController;
use App\Http\Controllers\Admin\AdminClassController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminEbookAssignmentController;

Route::prefix('admin')->name('admin.')->group(function () {
    
    // Guest Admin Routes
    Route::middleware('guest')->group(function () {
        Route::get('/login',     [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login',    [AdminAuthController::class, 'login'])->name('login.submit');
        Route::get('/register',  [AdminAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AdminAuthController::class, 'register'])->name('register.submit');
    });

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Authenticated Admin Routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Students CRUD
        Route::get('/students',                [AdminStudentController::class, 'index'])->middleware('permission:students.view')->name('students.index');
        Route::get('/students/create',         [AdminStudentController::class, 'create'])->middleware('permission:students.create')->name('students.create');
        Route::post('/students',               [AdminStudentController::class, 'store'])->middleware('permission:students.create')->name('students.store');
        Route::get('/students/{student}/edit', [AdminStudentController::class, 'edit'])->middleware('permission:students.edit')->name('students.edit');
        Route::put('/students/{student}',      [AdminStudentController::class, 'update'])->middleware('permission:students.edit')->name('students.update');
        Route::delete('/students/{student}',   [AdminStudentController::class, 'destroy'])->middleware('permission:students.delete')->name('students.destroy');
        Route::get('/students/sample-csv',     [AdminStudentController::class, 'sampleCsv'])->middleware('permission:students.edit')->name('students.sample-csv');
        Route::post('/students/import',        [AdminStudentController::class, 'importCsv'])->middleware('permission:students.edit')->name('students.import');

        // Staff CRUD
        Route::get('/staff',                   [AdminStaffController::class, 'index'])->middleware('permission:staff.view')->name('staff.index');
        Route::get('/staff/create',            [AdminStaffController::class, 'create'])->middleware('permission:staff.create')->name('staff.create');
        Route::post('/staff',                  [AdminStaffController::class, 'store'])->middleware('permission:staff.create')->name('staff.store');
        Route::get('/staff/{staff}/edit',      [AdminStaffController::class, 'edit'])->middleware('permission:staff.edit')->name('staff.edit');
        Route::put('/staff/{staff}',           [AdminStaffController::class, 'update'])->middleware('permission:staff.edit')->name('staff.update');
        Route::delete('/staff/{staff}',        [AdminStaffController::class, 'destroy'])->middleware('permission:staff.delete')->name('staff.destroy');
        Route::get('/staff/sample-csv',        [AdminStaffController::class, 'sampleCsv'])->middleware('permission:staff.edit')->name('staff.sample-csv');
        Route::post('/staff/import',           [AdminStaffController::class, 'importCsv'])->middleware('permission:staff.edit')->name('staff.import');

        // Admins CRUD
        Route::get('/admins',                   [AdminAdminsController::class, 'index'])->name('admins.index');
        Route::get('/admins/create',            [AdminAdminsController::class, 'create'])->name('admins.create');
        Route::post('/admins',                  [AdminAdminsController::class, 'store'])->name('admins.store');
        Route::get('/admins/{staff}/edit',      [AdminAdminsController::class, 'edit'])->name('admins.edit');
        Route::put('/admins/{staff}',           [AdminAdminsController::class, 'update'])->name('admins.update');
        Route::delete('/admins/{staff}',        [AdminAdminsController::class, 'destroy'])->name('admins.destroy');
        Route::get('/admins/sample-csv',        [AdminAdminsController::class, 'sampleCsv'])->name('admins.sample-csv');
        Route::post('/admins/import',           [AdminAdminsController::class, 'importCsv'])->name('admins.import');

        // Classes CRUD
        Route::get('/classes',               [AdminClassController::class, 'index'])->name('classes.index');
        Route::get('/classes/create',        [AdminClassController::class, 'create'])->name('classes.create');
        Route::post('/classes',              [AdminClassController::class, 'store'])->name('classes.store');
        Route::get('/classes/{class}/edit',  [AdminClassController::class, 'edit'])->name('classes.edit');
        Route::put('/classes/{class}',       [AdminClassController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{class}',    [AdminClassController::class, 'destroy'])->name('classes.destroy');

        // Attendance
        Route::get('/attendance',             [AdminAttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/bulk-mark',  [AdminAttendanceController::class, 'markBulk'])->name('attendance.markBulk');
        Route::post('/attendance/{id}/mark',  [AdminAttendanceController::class, 'mark'])->name('attendance.mark');
        Route::post('/attendance/{id}/unmark',[AdminAttendanceController::class, 'unmark'])->name('attendance.unmark');

        // Ebook Assignments
        Route::get('/ebook-assignments',        [AdminEbookAssignmentController::class, 'index'])->name('ebook_assignments.index');
        Route::post('/ebook-assignments/assign', [AdminEbookAssignmentController::class, 'assign'])->name('ebook_assignments.assign');
    });
});

