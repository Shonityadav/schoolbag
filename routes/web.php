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
    Route::middleware('guest')->group(function () {
        Route::get('/welcome',   function () { return view('student.guest_dashboard'); })->name('welcome');
        Route::get('/login',     [StudentAuthController::class, 'showLogin'])->name('login');
        Route::post('/login',    [StudentAuthController::class, 'login'])->name('login.submit');
        Route::get('/register',  [StudentAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [StudentAuthController::class, 'register'])->name('register.submit');
    });

    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');

    // Authenticated student
    Route::middleware('auth')->group(function () {
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
use App\Http\Controllers\Admin\AdminClassController;

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
        Route::get('/students',                [AdminStudentController::class, 'index'])->name('students.index');
        Route::get('/students/create',         [AdminStudentController::class, 'create'])->name('students.create');
        Route::post('/students',               [AdminStudentController::class, 'store'])->name('students.store');
        Route::get('/students/{student}/edit', [AdminStudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}',      [AdminStudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{student}',   [AdminStudentController::class, 'destroy'])->name('students.destroy');
        Route::get('/students/sample-csv',     [AdminStudentController::class, 'sampleCsv'])->name('students.sample-csv');
        Route::post('/students/import',        [AdminStudentController::class, 'importCsv'])->name('students.import');

        // Staff CRUD
        Route::get('/staff',                   [AdminStaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create',            [AdminStaffController::class, 'create'])->name('staff.create');
        Route::post('/staff',                  [AdminStaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{staff}/edit',      [AdminStaffController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{staff}',           [AdminStaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{staff}',        [AdminStaffController::class, 'destroy'])->name('staff.destroy');
        Route::get('/staff/sample-csv',        [AdminStaffController::class, 'sampleCsv'])->name('staff.sample-csv');
        Route::post('/staff/import',           [AdminStaffController::class, 'importCsv'])->name('staff.import');

        // Classes CRUD
        Route::get('/classes',               [AdminClassController::class, 'index'])->name('classes.index');
        Route::get('/classes/create',        [AdminClassController::class, 'create'])->name('classes.create');
        Route::post('/classes',              [AdminClassController::class, 'store'])->name('classes.store');
        Route::get('/classes/{class}/edit',  [AdminClassController::class, 'edit'])->name('classes.edit');
        Route::put('/classes/{class}',       [AdminClassController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{class}',    [AdminClassController::class, 'destroy'])->name('classes.destroy');
    });
});

