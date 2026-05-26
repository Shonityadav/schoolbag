<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AcetechnoidController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\InchargeController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\AllotDepartmentController;
use App\Http\Controllers\EmployeeController;

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CircularController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HumanResourceController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\TCController;
use App\Http\Controllers\UploadBookController;
use App\Http\Controllers\DynamicQRController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\InstituteAuthController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $crousel_img = \App\Models\GalleryAsset::where(['category' => 'MAINCOURSEL'])->get();
    return view('wps.welcome')->with(['crousel_img'=> $crousel_img]);
})->name('welcome');

Route::post('/paypal/create-order', [PaymentController::class, 'createOrder']);
Route::post('/paypal/capture-order/{orderId}', [PaymentController::class, 'captureOrder']);
Route::get('/payments', function () {
    return view('payments');
});
Route::get('docs', function () {

    return view('docs');
})->name('docs');

Auth::routes();

Route::get('home', [HomeController::class, 'index'])->name('home');
Route::post('transfer-certificate', 'HomeController@getTC')->name('tc');

Route::resource('myebook', DynamicQRController::class);

Route::get('qr-codes-by-company/{id}', [QRCodeController::class, 'showGuest']);
Route::get('qr-codes-by-company', [QRCodeController::class, 'allComp']);
Route::get('all-companies', [QRCodeController::class, 'allComp']);
Route::resource('qr-codes', QRCodeController::class);

Route::group(['middleware' => ['auth']], function() {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    
    Route::resource('attendances', AttendanceController::class);
    
    Route::resource('companies', CompanyController::class);
    
    Route::resource('departments', DepartmentController::class);
    Route::resource('incharges', InchargeController::class);
    Route::resource('complaints', ComplaintController::class);
    Route::resource('allot-departments', AllotDepartmentController::class);
    Route::resource('employees', EmployeeController::class);

    Route::resource('circulars', CircularController::class);
    Route::resource('upload-books', UploadBookController::class);
    Route::resource('human-resources', HumanResourceController::class);
    Route::resource('transfer-certificates', TCController::class);
    Route::resource('sticky-notices', NoticeController::class);
    Route::resource('news', NewsController::class);
    Route::resource('gallery-assets', GalleryController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('calendars', CalendarController::class);

    Route::resource('events', EventController::class);
    Route::resource('sub-categories', SubCategoryController::class);
    Route::resource('import', ImportExportController::class);
    Route::resource('enquiries', EnquiryController::class);
    Route::resource('followups', EnquiryFollowupController::class);
    Route::resource('blog', BlogController::class);
});

Route::get("bulk-url", function(){
                $i = 736;
                $j = '1';
                $sql ="";
                $url = "https://amitpublishing.acetechnoid.com/myebook/35?ch=";
                $where = "`id` = 736";
                do{
                    $sql .= "UPDATE `qrcodes` SET `url` = '".$url.$j."' WHERE `id`=".$i.",<br>";
                    $i++;$j++;
                    
                }while($i <= 746);

                return $sql;
                
            })->name('bulk-url');


// Route::get('enquiries-status/{id}/{val}', 'EnquiryController@status');
// Route::get('about-us', 'HomeController@about')->name('about-us');
// Route::get('management-desk', 'HomeController@management')->name('management-desk');
// Route::get('the-founders', 'HomeController@management')->name('founders');
// Route::get('academic-calendar', 'HomeController@calendar')->name('calendar');
// Route::get('school-gallery', 'HomeController@gallery')->name('gallery');
// Route::get('mandatory-public-disclosure', function(){
//                 return view('wps.mandatory-public-disclosure');
//             })->name('mandatory-public-disclosure');

// Route::get('the-principal', function(){
//                 return view('wps.principal');
//             })->name('the-principal');

// Route::get('the-principle-of-wps', function(){
//                 $pics = \App\Category::with('imagess')->where('name', 'DISCIPLINE')->first(); 
//                 return view('wps.principle')->with('discipline_pics', $pics);
//             })->name('the-principle');

// Route::get('the-founders', function(){
//                 return view('wps.founders');
//             })->name('the-founders');

// Route::get('mission-vision', function(){
//                 return view('wps.mission');
//             })->name('mission-vision');
            
// Route::get('the-founder-dir', function(){
//                 return view('wps.nitinji');
//             })->name('the-lost-founder');

// Route::get('curriculum', function(){
//                 return view('wps.curriculum');
//             })->name('curriculum');

// Route::get('contact', function(){
//                 return view('wps.contact');
//             })->name('contact');

// Route::get('transfer-certificate', function(){
//                 return view('wps.tc');
//             })->name('tc');

// Route::get('book-list', function(){
//                 return view('wps.downloads.book-list');
//             })->name('book-list');

// Route::get('datesheet', function(){
//                 return view('wps.downloads.datesheet');
//             })->name('datesheet');

// Route::get('syllabus', function(){
//                 return view('wps.downloads.syllabus');
//             })->name('syllabus');

// Route::get('the-houses', function(){
//                 return view('wps.houses');
//             })->name('the-houses');
            
// Route::get('career', function(){
//                 return view('wps.career');
//             })->name('career');



//Ajanta Books Website
// Route::get('/', function(){
//                 return view('acetechnoid.home');
//             })->name('home');

Route::get('/', [AcetechnoidController::class, 'home']);
Route::get('works', [AcetechnoidController::class, 'works'])->name('works');
Route::get('services', [AcetechnoidController::class, 'services'])->name('services');
Route::get('vision', [AcetechnoidController::class, 'vision'])->name('vision');
Route::get('careers', [AcetechnoidController::class, 'careers'])->name('careers');
Route::get('contact', [AcetechnoidController::class, 'contact'])->name('contact');
Route::get('about', [AcetechnoidController::class, 'about'])->name('about');
Route::get('user/delete', [UserController::class, 'userDelete'])->name('user.delete');

   
Route::get('docs/privacy', function(){
    return view('layouts.privacy');
})->name('privacy');

Route::get('connect/mobile', function(){
    return '9837729870';
})->name('privacy');

Route::get('littlelearnersbooks.in/{var}', [DynamicQRController::class, 'littlelearner']);

Route::get('/test-dd', function () {
    dd('HELLO FROM API');
});


    
    
