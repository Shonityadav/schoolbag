<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Student\StudentAuthController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\AssignedEbookController;
use App\Http\Controllers\Student\LessonController;

use App\Http\Controllers\Student\EbookController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;

use App\Http\Controllers\Student\WorkspaceController;

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
        
        // Workspace
        Route::get('/workspace', [WorkspaceController::class, 'index'])->name('workspace');
        Route::get('/workspace/profile', [WorkspaceController::class, 'profile'])->name('workspace.profile');

        Route::get('/assigned-ebooks',          [AssignedEbookController::class, 'index'])->name('assigned_ebooks.index');
        Route::get('/assigned-ebooks/{id}',     [AssignedEbookController::class, 'show'])->name('assigned_ebooks.show');
        Route::get('/assigned-ebooks/{id}/chapter/{chapter_id}/stage{stage}', [AssignedEbookController::class, 'stage'])->name('assigned_ebooks.stage');

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
use App\Http\Controllers\Admin\AdminStudentDetailsController;
use App\Http\Controllers\Admin\AdminStaffDetailsController;
use App\Http\Controllers\Admin\AdminAdminsController;
use App\Http\Controllers\Admin\AdminClassController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminEbookAssignmentController;
use App\Http\Controllers\Admin\AdminStaffCategoryController;
use App\Http\Controllers\Admin\AdminChatController;

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
        Route::get('/student-details',                [AdminStudentDetailsController::class, 'index'])->middleware('permission:student_details.view')->name('student_details.index');
        Route::get('/student-details/create',         [AdminStudentDetailsController::class, 'create'])->middleware('permission:student_details.create')->name('student_details.create');
        Route::post('/student-details',               [AdminStudentDetailsController::class, 'store'])->middleware('permission:student_details.create')->name('student_details.store');
        Route::get('/student-details/{student}/edit', [AdminStudentDetailsController::class, 'edit'])->middleware('permission:student_details.edit')->name('student_details.edit');
        Route::put('/student-details/{student}',      [AdminStudentDetailsController::class, 'update'])->middleware('permission:student_details.edit')->name('student_details.update');
        Route::delete('/student-details/{student}',   [AdminStudentDetailsController::class, 'destroy'])->middleware('permission:student_details.delete')->name('student_details.destroy');
        Route::get('/student-details/sample-csv',     [AdminStudentDetailsController::class, 'sampleCsv'])->middleware('permission:student_details.edit')->name('student_details.sample-csv');
        Route::post('/student-details/import',        [AdminStudentDetailsController::class, 'importCsv'])->middleware('permission:student_details.edit')->name('student_details.import');

        // Staff CRUD
        Route::get('/staff-details',                   [AdminStaffDetailsController::class, 'index'])->middleware('permission:staff.view')->name('staff_details.index');
        Route::get('/staff-details/create',            [AdminStaffDetailsController::class, 'create'])->middleware('permission:staff.create')->name('staff_details.create');
        Route::post('/staff-details',                  [AdminStaffDetailsController::class, 'store'])->middleware('permission:staff.create')->name('staff_details.store');
        Route::get('/staff-details/{staff}/edit',      [AdminStaffDetailsController::class, 'edit'])->middleware('permission:staff.edit')->name('staff_details.edit');
        Route::put('/staff-details/{staff}',           [AdminStaffDetailsController::class, 'update'])->middleware('permission:staff.edit')->name('staff_details.update');
        Route::delete('/staff-details/{staff}',        [AdminStaffDetailsController::class, 'destroy'])->middleware('permission:staff.delete')->name('staff_details.destroy');
        Route::get('/staff-details/sample-csv',        [AdminStaffDetailsController::class, 'sampleCsv'])->middleware('permission:staff.edit')->name('staff_details.sample-csv');
        Route::post('/staff-details/import',           [AdminStaffDetailsController::class, 'importCsv'])->middleware('permission:staff.edit')->name('staff_details.import');

        // Staff Categories
        Route::resource('staff-categories', AdminStaffCategoryController::class)->except(['show']);

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

        // Chat
        Route::get('/chat',[AdminChatController::class,'index'])->name('chat.index');
        Route::get('/chat/sidebar-sync',[AdminChatController::class,'sidebarSync'])->name('chat.sidebar_sync');
        Route::get( '/chat/{room}',[AdminChatController::class,'show'])->name('chat.show');
        Route::post('/chat/{room}/send',[AdminChatController::class,'send'])->name('chat.send');
        Route::get('/chat/{room}/sync',[AdminChatController::class,'sync'])->name('chat.sync');
        Route::post('/chat/{room}/typing',[AdminChatController::class,'typing'])->name('chat.typing');
        Route::get('/chat/message/{message}/info',[AdminChatController::class,'messageInfo'])->name('chat.message_info');

        // Ebook Assignments
        Route::get('/ebook-assignments',        [AdminEbookAssignmentController::class, 'index'])->name('ebook_assignments.index');
        Route::post('/ebook-assignments/assign', [AdminEbookAssignmentController::class, 'assign'])->name('ebook_assignments.assign');
    });
});
