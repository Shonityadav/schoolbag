<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\Api\BookShelfController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any('/uidToUrl/{val}', function ($val) {
	$result = \App\Models\Qrcode::where('uid', $val)->orWhere('isbn', $val)->first(); 
    return json_encode($result);
});
Route::any('/uidToUrl2/{val}', function ($val, Request $request) {
	//dd($request->uid);
	$result = \App\Models\Qrcode::where('uid', $request->uid)->orWhere('isbn', $val)->first(); 
    return json_encode($result);
});

Route::post('/attendance', [AttendanceController::class, 'markAttendance'])->name('markAttendance');

Route::any('login', function (Request $request)
    {
        try {
        
            $user = User::where([
                'email' => $request->email,
            ])->first();
            
            if($user){
                if (Hash::check( $request->password, $user->password)) 
                    return response()->json(['result' => 'true', 'resCode' => '101', 'resMess' => 'Valid result!', 'data' => $user]);
                else
                    return response()->json(['result' => 'false', 'resCode' => '201', 'resMess' => 'Invalid Password!']);
            }else
                return response()->json(['result' => 'false', 'resCode' => '201', 'resMess' => 'Invalid User Name!']);
                
        } catch (\Exception $e) {
            return response()->json(['result' => 'false', 'resCode' => '201', 'resMess' => $e->getMessage()]);
            // return $e->getMessage();
        }
    });
Route::any('register-user', function (Request $request)
    {
        try{
            $data = $request->input();
            
            $user = User::create([
                'email' => $request->email,
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
            ]);
            
            #$user->sendEmailVerificationNotification();#
            
            $arrdata = [
                "type"=>"new_registration",
                "name"=>$request->name,
                "email"=>$request->user
            ];
            
            
            if ($user) {
                return response()->json(['result' => 'true', 'resCode' => '101', 'resMess' => 'Registered Successfully! Please verify email.']);
            }
            else
                return response()->json(['result' => 'false', 'resCode' => '201', 'resMess' => 'Error contact admin!']);
        }
        catch(\Exception $e){
            return response()->json(['result' => 'false', 'resCode' => '201', 'resMess' => $e->getMessage()]);
        }
    });


Route::get('/product', [BookShelfController::class, 'product'])->name('product');
// API to get unique publications
Route::get('/publication', [BookShelfController::class, 'getPublications']);

// API to get unique standards
Route::get('/standard', [BookShelfController::class, 'getStandards']);

// API to get unique subjects
Route::get('/subject', [BookShelfController::class, 'getSubjects']);

// API to search
Route::get('/search', [BookShelfController::class, 'search']);

// API to get location
Route::get('/locations', [LocationController::class, 'index']);

Route::get('user/delete', [UserController::class, 'userDelete'])->name('user.delete');


Route::post('/contact', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email',
        'message' => 'required|string',
    ]);

    try {
        // ---------------------------
        // Send confirmation to user
        // ---------------------------
        Mail::send([], [], function ($message) use ($validated) {
            $message->from('connect@acetechnoid.com', 'Acetechnoid');
            $message->to($validated['email']);
            $message->subject('Thank you for contacting Acetechnoid');

            $message->setBody("
                <h2>Hello {$validated['name']},</h2>
                <p>Thank you for contacting Acetechnoid.</p>
                <p>We received your message:</p>
                <blockquote style=\"border-left:3px solid #ddd;padding-left:12px;color:#333;\">{$validated['message']}</blockquote>
                <p>We will get back to you shortly.</p>
                <br>
                <p>Regards,<br>Acetechnoid Team</p>
            ", 'text/html');
        });

        // ---------------------------------------------------
        // Send email to admins (connect@... and acetechnoid@gmail.com)
        // ---------------------------------------------------
        $adminRecipients = [
            'connect@acetechnoid.com',
            'acetechnoid@gmail.com'
        ];

        Mail::send([], [], function ($message) use ($validated, $adminRecipients) {
            $message->from('connect@acetechnoid.com', 'Acetechnoid Website');
            $message->to($adminRecipients);
            $message->subject('New Contact Form Submission — Acetechnoid');

            // Build a neat HTML table with details
            $html = '
                <h3>New Contact Form Submission</h3>
                <table style="width:100%;border-collapse:collapse;font-family:Arial,Helvetica,sans-serif;">
                  <tr>
                    <td style="padding:8px;border:1px solid #e6e6e6;background:#f7f7f7;width:160px;"><strong>Name</strong></td>
                    <td style="padding:8px;border:1px solid #e6e6e6;">' . e($validated['name']) . '</td>
                  </tr>
                  <tr>
                    <td style="padding:8px;border:1px solid #e6e6e6;background:#f7f7f7;"><strong>Email</strong></td>
                    <td style="padding:8px;border:1px solid #e6e6e6;">' . e($validated['email']) . '</td>
                  </tr>
                  <tr>
                    <td style="padding:8px;border:1px solid #e6e6e6;background:#f7f7f7;"><strong>Message</strong></td>
                    <td style="padding:8px;border:1px solid #e6e6e6;">' . nl2br(e($validated['message'])) . '</td>
                  </tr>
                  <tr>
                    <td style="padding:8px;border:1px solid #e6e6e6;background:#f7f7f7;"><strong>Received At</strong></td>
                    <td style="padding:8px;border:1px solid #e6e6e6;">' . now()->toDateTimeString() . '</td>
                  </tr>
                </table>
                <p style=\"margin-top:12px;color:#666;font-size:13px;\">This message was submitted from the website contact form.</p>
            ';

            $message->setBody($html, 'text/html');
        });

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
//Route::post('/ping', fn () => response()->json(['pong' => true]));

Route::post('/institute/signup', [InstituteAuthController::class, 'institutesignup']);
Route::post('/institutelogin', [InstituteAuthController::class, 'institutelogin']);
Route::post('/institutelogout', [InstituteAuthController::class, 'institutelogout']);
Route::post('/institute/add-user', [InstituteAuthController::class, 'addUser']);
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
Route::prefix('institute')->group(function () {

    // ADMIN
    Route::resource('transactions', InstituteTransactionController::class);
   // Route::get('/transactions', [InstituteTransactionController::class, 'index']);
  //  Route::get('/transactions/{id}', [InstituteTransactionController::class, 'show']);

    // STUDENT / STAFF
    Route::get('/my-transactions', [InstituteTransactionController::class, 'myTransactions']);
});
