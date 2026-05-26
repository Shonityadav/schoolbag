<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Student\StudentAuthController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Student\LessonController;
use App\Http\Controllers\Student\QuizController;
use App\Http\Controllers\Student\EbookController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;

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

        Route::get('/quizzes/{id}',         [QuizController::class, 'show'])->name('quizzes.show');
        Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
        Route::get('/quizzes/result/{id}',  [QuizController::class, 'result'])->name('quizzes.result');

        Route::get('/ebooks',                [EbookController::class, 'index'])->name('ebooks');
        Route::get('/ebooks/{id}',           [EbookController::class, 'show'])->name('ebooks.show');
        Route::get('/ebooks/{id}/toc',       [EbookController::class, 'toc'])->name('ebooks.toc');
        Route::post('/ebooks/{id}/download', [EbookController::class, 'download'])->name('ebooks.download');
        Route::post('/ebooks/{id}/assign',   [EbookController::class, 'assign'])->name('ebooks.assign');

        Route::get('/profile', [StudentProfileController::class, 'index'])->name('profile');

        Route::post('/attendance/mark', [DashboardController::class, 'markAttendance'])->name('attendance.mark');
    });
});

