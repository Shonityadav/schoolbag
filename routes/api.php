<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstituteAuthController;
use App\Http\Controllers\InstituteTransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/institute/signup', [InstituteAuthController::class, 'institutesignup']);
Route::post('/institutelogin', [InstituteAuthController::class, 'institutelogin']);
Route::post('/institutelogout', [InstituteAuthController::class, 'institutelogout']);
Route::post('/institute/add-user', [InstituteAuthController::class, 'addUser']);
Route::post('/institute/bulk-students', [InstituteAuthController::class, 'bulkStudents']);
Route::post('/institute/bulk-staff', [InstituteAuthController::class, 'bulkStaff']);
Route::get('/institute/users', [InstituteAuthController::class, 'instituteUsers']);
Route::get('/institute/staffs', [InstituteAuthController::class, 'instituteStaffs']);
Route::get('/institute/students', [InstituteAuthController::class, 'instituteStudents']);
Route::post('/institute/mark-attendance', [InstituteAuthController::class, 'markBulkAttendance']);
Route::post('/institute/classes', [InstituteAuthController::class, 'addClass']);
Route::get('/institute/classes', [InstituteAuthController::class, 'classes']);
Route::post('/institute/attendance', [InstituteAuthController::class, 'fetchAttendance']);
Route::post('/institute/AttendanceReport', [InstituteAuthController::class, 'AttendanceReport']);
Route::post('/forgot-password', [InstituteAuthController::class, 'forgotPassword']);
Route::post('/reset-password', [InstituteAuthController::class, 'resetPassword']);
Route::post(
    '/institute/assign-permissions',
    [InstituteAuthController::class, 'assignPermissions']
);
Route::post(
    '/institute/assign-class',
    [InstituteAuthController::class, 'assignClass']
);
Route::prefix('institute')->group(function () {

    // ADMIN
    Route::resource('transactions', InstituteTransactionController::class);
   // Route::get('/transactions', [InstituteTransactionController::class, 'index']);
  //  Route::get('/transactions/{id}', [InstituteTransactionController::class, 'show']);

    // STUDENT / STAFF
    Route::get('/my-transactions', [InstituteTransactionController::class, 'myTransactions']);
});