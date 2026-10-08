<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Student\StudentAuthController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\AssignedEbookController;
use App\Http\Controllers\Student\LessonController;

use App\Http\Controllers\Student\EbookController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;

use App\Http\Controllers\Student\WorkspaceController;
use App\Http\Controllers\Student\StudentChatController;
use App\Http\Controllers\Student\ChatController;
use App\Http\Controllers\Student\PaymentController;
use App\Http\Controllers\Student\AiTestPaperController;

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

// Public ID Card Verification
use App\Http\Controllers\IdCardVerificationController;
Route::get('/verify/{token}', [IdCardVerificationController::class, 'verify'])->name('idcard.verify');

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
        
        // WhatsApp Login Routes
        Route::post('/whatsapp/send-otp', [StudentAuthController::class, 'sendWhatsappOtp'])->name('whatsapp.send_otp');
        Route::post('/whatsapp/verify-otp', [StudentAuthController::class, 'verifyWhatsappOtp'])->name('whatsapp.verify_otp');
        Route::post('/whatsapp/register', [StudentAuthController::class, 'registerWhatsappUser'])->name('whatsapp.register');
    });

    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');

    // Authenticated student
    Route::middleware('auth:student')->group(function () {
        Route::get('/dashboard',         [DashboardController::class, 'index'])->name('dashboard');
        
        // Workspace
        Route::get('/workspace', [WorkspaceController::class, 'index'])->name('workspace');
        Route::get('/workspace/homework-history', [WorkspaceController::class, 'homeworkHistory'])->name('workspace.homework_history');
        Route::get('/workspace/profile', [WorkspaceController::class, 'profile'])->name('workspace.profile');

        Route::get('/assigned-ebooks',          [AssignedEbookController::class, 'index'])->name('assigned_ebooks.index');
        Route::get('/assigned-ebooks/{id}/details', [AssignedEbookController::class, 'details'])->name('assigned_ebooks.details');
        Route::get('/assigned-ebooks/{id}',     [AssignedEbookController::class, 'show'])->name('assigned_ebooks.show');
        Route::post('/assigned-ebooks/{id}/generate-map', [AssignedEbookController::class, 'generateChaptersMap'])->name('assigned_ebooks.generate_map');
        Route::get('/assigned-ebooks/{id}/chapter/{chapter_id}/stage{stage}', [AssignedEbookController::class, 'stage'])->name('assigned_ebooks.stage');
        Route::post('/assigned-ebooks/{id}/chapter/{chapter_id}/generate-remaining-stages', [AssignedEbookController::class, 'generateRemainingStages'])->name('assigned_ebooks.generate_remaining_stages');
        Route::get('/assigned-ebooks/{id}/chapter/{chapter_id}/stage/{stage_number}/check-generation-status', [AssignedEbookController::class, 'checkGenerationStatus'])->name('assigned_ebooks.check_generation_status');

        Route::get('/lessons/{id}',      [LessonController::class, 'show'])->name('lessons.show');
        Route::post('/lessons/{id}/done',[LessonController::class, 'complete'])->name('lessons.complete');

        // AI Test Paper Routes
        Route::get('/aitest/create', [AiTestPaperController::class, 'create'])->name('aitest.create');
        Route::post('/aitest/save', [AiTestPaperController::class, 'store'])->name('aitest.store');
        Route::get('/aitest/{id}', [AiTestPaperController::class, 'home'])->name('aitest');
        Route::get('/ai-testpaper', [AiTestPaperController::class, 'showPaper'])->name('aitest.showPaper');

        Route::get('/notifications/{id}/read', [\App\Http\Controllers\Student\DashboardController::class, 'readNotification'])->name('notifications.read');
        Route::post('/notifications/mark-read', [\App\Http\Controllers\Student\DashboardController::class, 'markNotificationsRead'])->name('notifications.markAllRead');

        Route::get('/ebooks',                [EbookController::class, 'index'])->name('ebooks');
        Route::get('/ebooks/{id}',           [EbookController::class, 'show'])->name('ebooks.show');
        Route::get('/ebooks/{id}/toc',       [EbookController::class, 'toc'])->name('ebooks.toc');
        Route::post('/ebooks/{id}/download', [EbookController::class, 'download'])->name('ebooks.download');
        Route::post('/ebooks/{id}/assign',   [EbookController::class, 'assign'])->name('ebooks.assign');
        Route::post('/ebooks/scan-qr',       [EbookController::class, 'scanQr'])->name('ebooks.scan_qr');
        Route::post('/ebooks/confirm-qr-assign', [EbookController::class, 'confirmAssignQr'])->name('ebooks.confirm_qr_assign');

        Route::get('/profile', [StudentProfileController::class, 'index'])->name('profile');
        Route::get('/profile/change-password', [StudentProfileController::class, 'changePassword'])->name('profile.change_password');
        Route::post('/profile/verify-otp', [StudentProfileController::class, 'verifyOtp'])->name('profile.verify_otp');
        Route::post('/profile/resend-otp', [StudentProfileController::class, 'resendOtp'])->name('profile.resend_otp');
        Route::post('/profile/update-password', [StudentProfileController::class, 'updatePassword'])->name('profile.update_password');
        Route::post('/profile/avatar', [StudentProfileController::class, 'updateAvatar'])->name('profile.update_avatar');
        Route::get('/profile/feedback', [StudentProfileController::class, 'feedback'])->name('profile.feedback');
        Route::post('/profile/feedback', [StudentProfileController::class, 'storeFeedback'])->name('profile.store_feedback');
        
        // Fee Payments
        Route::post('/fees/pay', [PaymentController::class, 'initiatePayment'])->name('fees.pay');
        Route::post('/fees/callback', [PaymentController::class, 'verifyPayment'])->name('fees.callback');
        Route::get('/fees/cancel', [PaymentController::class, 'cancelPayment'])->name('fees.cancel');

        Route::get('/app-rules', function () { return view('student.terms'); })->name('terms');
        Route::get('/app-privacy', function () { return view('student.privacy'); })->name('privacy');

        Route::post('/attendance/mark', [DashboardController::class, 'markAttendance'])->name('attendance.mark');

        // Chat with Ebook Routes
        Route::get('chat_with_paper', [ChatController::class, 'index'])->name('chat_with_paper');
        Route::post('/chat_with_paper/save-index', [ChatController::class,'saveIndexPage'])->name('chat.saveIndex');
        Route::post('chat/save-chapters', [ChatController::class, 'saveChapters'])->name('chat.saveChapters');

        // Chat Widget Routes
        Route::get('/chat/rooms', [StudentChatController::class, 'fetchRooms'])->name('chat.rooms');
        Route::get('/chat/rooms/{room}/messages', [StudentChatController::class, 'fetchMessages'])->name('chat.messages');
        Route::post('/chat/rooms/{room}/send', [StudentChatController::class, 'sendMessage'])->name('chat.send');
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
use App\Http\Controllers\Admin\AdminIdCardController;
use App\Http\Controllers\Admin\AdminHomeworkController;
use App\Http\Controllers\Admin\AdminQuestionReviewController;

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
        Route::get('/student-details',                [AdminStudentDetailsController::class, 'index'])->middleware('permission:students.view')->name('student_details.index');
        Route::get('/student-details/create',         [AdminStudentDetailsController::class, 'create'])->middleware('permission:students.create')->name('student_details.create');
        Route::get('/student-details/upload-photos',  [AdminStudentDetailsController::class, 'uploadPhotosForm'])->middleware('permission:students.create')->name('student_details.upload-photos');
        Route::post('/student-details/upload-photos', [AdminStudentDetailsController::class, 'processUploadPhotos'])->middleware('permission:students.create')->name('student_details.upload-photos.submit');
        Route::post('/student-details/upload-photos/preview', [AdminStudentDetailsController::class, 'previewZipUpload'])->middleware('permission:students.create')->name('student_details.upload-photos.preview');
        Route::post('/student-details',               [AdminStudentDetailsController::class, 'store'])->middleware('permission:students.create')->name('student_details.store');
        Route::get('/student-details/sample-csv',     [AdminStudentDetailsController::class, 'sampleCsv'])->middleware('permission:students.edit')->name('student_details.sample-csv');
        Route::post('/student-details/import',        [AdminStudentDetailsController::class, 'importCsv'])->middleware('permission:students.edit')->name('student_details.import');
        Route::get('/student-details/{student}/edit', [AdminStudentDetailsController::class, 'edit'])->middleware('permission:students.edit')->name('student_details.edit');
        Route::get('/student-details/{student}',      [AdminStudentDetailsController::class, 'show'])->middleware('permission:students.view')->name('student_details.show');
        Route::post('/student-details/{student}/fee-payment', [AdminStudentDetailsController::class, 'storeFeePayment'])->middleware('permission:students.edit')->name('student_details.fee_payment');
        Route::put('/student-details/{student}',      [AdminStudentDetailsController::class, 'update'])->middleware('permission:students.edit')->name('student_details.update');
        Route::delete('/student-details/{student}',   [AdminStudentDetailsController::class, 'destroy'])->middleware('permission:students.edit')->name('student_details.destroy');

        // Staff CRUD
        Route::get('/staff-details',                  [AdminStaffDetailsController::class, 'index'])->middleware('permission:staff.view')->name('staff_details.index');
        Route::get('/staff-details/create',           [AdminStaffDetailsController::class, 'create'])->middleware('permission:staff.create')->name('staff_details.create');
        Route::get('/staff-details/upload-photos',    [AdminStaffDetailsController::class, 'uploadPhotosForm'])->middleware('permission:staff.create')->name('staff_details.upload-photos');
        Route::post('/staff-details/upload-photos',   [AdminStaffDetailsController::class, 'processUploadPhotos'])->middleware('permission:staff.create')->name('staff_details.upload-photos.submit');
        Route::post('/staff-details/upload-photos/preview', [AdminStaffDetailsController::class, 'previewZipUpload'])->middleware('permission:staff.create')->name('staff_details.upload-photos.preview');
        Route::post('/staff-details',                 [AdminStaffDetailsController::class, 'store'])->middleware('permission:staff.create')->name('staff_details.store');
        Route::get('/staff-details/sample-csv',        [AdminStaffDetailsController::class, 'sampleCsv'])->middleware('permission:staff.edit')->name('staff_details.sample-csv');
        Route::post('/staff-details/import',           [AdminStaffDetailsController::class, 'importCsv'])->middleware('permission:staff.edit')->name('staff_details.import');
        Route::get('/staff-details/{staff}/edit',      [AdminStaffDetailsController::class, 'edit'])->middleware('permission:staff.edit')->name('staff_details.edit');
        Route::get('/staff-details/{staff}',           [AdminStaffDetailsController::class, 'show'])->middleware('permission:staff.view')->name('staff_details.show');
        Route::put('/staff-details/{staff}',           [AdminStaffDetailsController::class, 'update'])->middleware('permission:staff.edit')->name('staff_details.update');
        Route::delete('/staff-details/{staff}',        [AdminStaffDetailsController::class, 'destroy'])->middleware('permission:staff.delete')->name('staff_details.destroy');

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

        // Notifications
        Route::get('/notifications/{id}/read', [\App\Http\Controllers\Admin\AdminAttendanceController::class, 'readNotification'])->name('notifications.read');
        Route::post('/notifications/mark-read', [\App\Http\Controllers\Admin\AdminAttendanceController::class, 'markNotificationsRead'])->name('notifications.markAllRead');

        // Fees
        Route::get('/fees',                   [\App\Http\Controllers\Admin\AdminFeeStructureController::class, 'index'])->name('fees.index');
        Route::post('/fees',                  [\App\Http\Controllers\Admin\AdminFeeStructureController::class, 'store'])->name('fees.store');
        Route::put('/fees/{fee}',             [\App\Http\Controllers\Admin\AdminFeeStructureController::class, 'update'])->name('fees.update');
        Route::delete('/fees/{fee}',          [\App\Http\Controllers\Admin\AdminFeeStructureController::class, 'destroy'])->name('fees.destroy');

        // Attendance
        Route::get('/attendance',             [AdminAttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/bulk-mark',  [AdminAttendanceController::class, 'markBulk'])->name('attendance.markBulk');

        // Homework
        Route::get('/homework',               [AdminHomeworkController::class, 'index'])->name('homework.index');
        Route::post('/homework',              [AdminHomeworkController::class, 'store'])->name('homework.store');
        Route::delete('/homework/{homework}', [AdminHomeworkController::class, 'destroy'])->name('homework.destroy');
        Route::post('/attendance/{id}/mark',  [AdminAttendanceController::class, 'mark'])->name('attendance.mark');
        Route::post('/attendance/{id}/unmark',[AdminAttendanceController::class, 'unmark'])->name('attendance.unmark');

        // Chat
        Route::get('/chat',[AdminChatController::class,'index'])->name('chat.index');
        Route::get('/chat/sidebar-sync',[AdminChatController::class,'sidebarSync'])->name('chat.sidebar_sync');
        Route::get('/chat/{room}',[AdminChatController::class,'show'])->name('chat.show');
        Route::post('/chat/{room}/send',[AdminChatController::class,'send'])->name('chat.send');
        Route::get('/chat/{room}/sync',[AdminChatController::class,'sync'])->name('chat.sync');
        Route::post('/chat/{room}/typing',[AdminChatController::class,'typing'])->name('chat.typing');
        Route::get('/chat/message/{message}/info',[AdminChatController::class,'messageInfo'])->name('chat.message_info');

        // Ebook Assignments
        Route::get('/ebook-assignments',        [AdminEbookAssignmentController::class, 'index'])->name('ebook_assignments.index');
        Route::post('/ebook-assignments/assign', [AdminEbookAssignmentController::class, 'assign'])->name('ebook_assignments.assign');

        // ID Card Management
        Route::prefix('id-cards')->name('id_cards.')->group(function() {
            Route::get('/', [AdminIdCardController::class, 'index'])->middleware('permission:idcard.view')->name('index');
            Route::get('/create', [AdminIdCardController::class, 'create'])->middleware('permission:idcard.edit')->name('create');
            Route::post('/', [AdminIdCardController::class, 'store'])->middleware('permission:idcard.edit')->name('store');
            Route::get('/{template}/edit', [AdminIdCardController::class, 'edit'])->middleware('permission:idcard.edit')->name('edit');
            Route::put('/{template}', [AdminIdCardController::class, 'update'])->middleware('permission:idcard.edit')->name('update');
            Route::delete('/{template}', [AdminIdCardController::class, 'destroy'])->middleware('permission:idcard.edit')->name('destroy');
            
            // Designer routes
            Route::get('/{template}/designer', [AdminIdCardController::class, 'designer'])->middleware('permission:idcard.edit')->name('designer');
            Route::post('/{template}/save-layout', [AdminIdCardController::class, 'saveLayout'])->middleware('permission:idcard.edit')->name('save_layout');
            Route::post('/{template}/preview', [AdminIdCardController::class, 'preview'])->middleware('permission:idcard.view')->name('preview');
            Route::post('/{template}/publish', [AdminIdCardController::class, 'publish'])->middleware('permission:idcard.edit')->name('publish');
            Route::post('/{template}/duplicate', [AdminIdCardController::class, 'duplicate'])->middleware('permission:idcard.edit')->name('duplicate');
            Route::post('/{template}/archive', [AdminIdCardController::class, 'archive'])->middleware('permission:idcard.edit')->name('archive');
            
            // Assets
            Route::post('/assets/upload', [AdminIdCardController::class, 'uploadAsset'])->middleware('permission:idcard.edit')->name('assets.upload');
            Route::delete('/assets/{asset}', [AdminIdCardController::class, 'deleteAsset'])->middleware('permission:idcard.edit')->name('assets.destroy');
            
            // Settings
            Route::get('/settings', [AdminIdCardController::class, 'settings'])->middleware('permission:idcard.settings')->name('settings');
            Route::post('/settings', [AdminIdCardController::class, 'updateSettings'])->middleware('permission:idcard.settings')->name('settings.update');
            
            // Downloads and Bulk Print
            Route::get('/downloads', [AdminIdCardController::class, 'downloads'])->middleware('permission:idcard.bulk_print')->name('downloads');
            Route::post('/bulk-print', [AdminIdCardController::class, 'bulkPrint'])->middleware('permission:idcard.bulk_print')->name('bulkPrint');
            
            // Revoke Card
            Route::post('/revoke/{card}', [AdminIdCardController::class, 'revokeCard'])->middleware('permission:idcard.edit')->name('revoke');
        });
        
        // Question Reviews
        Route::get('/question-reviews', [AdminQuestionReviewController::class, 'index'])->name('question_reviews.index');
        Route::put('/question-reviews/{id}', [AdminQuestionReviewController::class, 'update'])->name('question_reviews.update');
    });
});
