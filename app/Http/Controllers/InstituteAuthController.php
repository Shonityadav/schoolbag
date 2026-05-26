<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\School;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Auth;
use App\Models\ClassModel;

class InstituteAuthController extends Controller
{
    private function authInstitute(Request $request)
{
    $token = $request->bearerToken();

    if (!$token) {
        abort(401, 'Unauthorized');
    }

    $user = User::where('api_token', hash('sha256', $token))->first();

    if (!$user) {
        abort(401, 'Unauthorized');
    }

    return $user;
}

    /* ==========================
       INSTITUTE SIGNUP
    =========================== */
    public function institutesignup(Request $request)
    {
    // ✅ Validation
    $validator = Validator::make($request->all(), [
        'school_name' => 'required|string|max:255',
        'admin_name'  => 'required|string|max:255',
        'email'       => 'required|email|unique:users,email',
        'password'    => 'required|min:6|confirmed',

        // optional user fields
        'mobile'      => 'nullable|string|max:20',
        'dob'         => 'nullable|string|max:50',
        'state'       => 'nullable|string|max:100',
        'city'        => 'nullable|string|max:100',
        'standard'    => 'nullable|string|max:100',

        // optional school fields
        'school_address' => 'nullable|string',
        'school_number'  => 'nullable|string|max:20',
    ], [
        'email.unique'  => 'Email already exists, please use another email',
        'mobile.unique' => 'Mobile number already exists, please use another number',
    ]);
   if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed, Duplicate email or number found',
            'errors'  => $validator->errors()
        ], 422);
    }

    DB::beginTransaction();

    try {
        /* ======================
           CREATE SCHOOL
        ====================== */
        $school = School::create([
            'name'    => $request->school_name,
            'address' => $request->school_address ?? null,
            'number'  => $request->school_number ?? null,
        ]);

        /* ======================
           PREPARE USER DATA
        ====================== */
        $userData = $request->only([
            'mobile',
            'dob',
            'state',
            'city',
            'standard',
        ]);

        $userData['institute_id'] = $school->id;
        $userData['name']         = $request->admin_name;
        $userData['email']        = $request->email;
        $userData['password']     = Hash::make($request->password);
        $userData['user_type']    = 1; // institute admin
        $userData['created_by']   = null;
        $userData['api_token']    = Str::random(60);

        /* ======================
           CREATE USER
        ====================== */
        $user = User::create($userData);

        DB::commit();

        return response()->json([
            'message'   => 'Institution registered successfully',
            'school_id' => $school->id,
            'user_id'   => $user->id,
            'api_token' => $user->api_token,
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Registration failed',
            'error'   => $e->getMessage(),
        ], 500);
    }
}
       
    /* ==========================
       LOGIN (TOKEN)
    =========================== */
     public function institutelogin(Request $request)
{
    $request->validate([
        'login'    => 'required',
        'password' => 'required',
    ]);

    $login = $request->login;

    // Detect email or mobile
    if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
        $user = User::where('email', $login)->first();
    } else {
        $user = User::where('mobile', $login)->first();
    }

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid email/mobile or password'
        ], 401);
    }

    // Generate token
    $token = Str::random(60);

    // Store hashed token
    $user->api_token = hash('sha256', $token);
    $user->save();

    return response()->json([
        'message' => 'Login successful',
        'token'   => $token,
        'user' => [
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'mobile'    => $user->mobile,
            'user_type' => $user->user_type,
        ]
    ], 200);
}

    /* ==========================
       ADD STAFF OR STUDENT
    =========================== */
    public function addUser(Request $request)
   {
   // 1️⃣ Get Bearer token
   $token = $request->bearerToken();
   if (!$token) {
       return response()->json(['message' => 'Unauthorized'], 401);
    }
    
    // 2️⃣ Find user by hashed token
    $authUser = User::where('api_token', hash('sha256', $token))->first();
    
    if (!$authUser) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    
    // 3️⃣ Check if institute admin
    if ($authUser->user_type !== 1) {
        return response()->json(['message' => 'Only institute admin can add users'], 403);
    }
     // 4️⃣ Validate request
    $validator = Validator::make($request->all(), [
        'name'     => 'required',
        'email'    => 'required|email|unique:users,email',
        'mobile'   => 'required|digits:10|unique:users,mobile',
        'password' => 'required|min:6',
        'role'     => 'required|in:staff,student',
        'designation' => 'required_if:role,staff',
        'department'  => 'required_if:role,staff',
        'class_id' => 'required_if:role,student|integer',
        'roll_no'  => 'required_if:role,student',
        'fee' => 'required_if:role,student|numeric',
        'fee_period' => 'required_if:role,student|in:MONTHLY,QUATERLY,HALF YEARLY,YEARLY',
    ], [
        'email.unique'  => 'Email already exists, please use another email',
        'mobile.unique' => 'Mobile number already exists, please use another number',
    ]);
   if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed, Duplicate email or mobile number found',
            'errors'  => $validator->errors()
        ], 422);
    }
    
 try {
        DB::beginTransaction();

        $user = User::create([
            'institute_id' => $authUser->institute_id,
            'name'         => $request->name,
            'email'        => $request->email,
            'mobile'       => $request->mobile,
            'password'     => Hash::make($request->password),
            'user_type'    => $request->role === 'staff' ? 2 : 3,
            'created_by'   => $authUser->id,
        ]);

        if ($request->role === 'student') {
            Student::create([
                'created_for'  => $user->id,
                'institute_id' => $authUser->institute_id,
                'class_id'     => $request->class_id,
                'roll_no'      => $request->roll_no,
                'fee'      => $request->fee,
                'fee_period'      => $request->fee_period,
                'admission_date' => now(),
            ]);
        }
        else{
            Staff::create([
                'created_for'  => $user->id,
                'institute_id' => $authUser->institute_id,
                'designation'     => $request->designation,
                'department'      => $request->department,
                'joining_date' => now(),
            ]);
        }

        DB::commit();

        return response()->json([
            'message' => 'User added successfully'
        ], 201);

    } catch (QueryException $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Duplicate entry',
            'error'   => 'Email or mobile already exists'
        ], 409);
    }
}
   

    /* ==========================
       LOGOUT
    =========================== */
    public function institutelogout(Request $request)
    {
        $token = $request->bearerToken();

        if ($token) {
            User::where('api_token', hash('sha256', $token))
                ->update(['api_token' => null]);
        }

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function instituteUsers(Request $request)
{
    $authUser = $this->authInstitute($request);

    $staffs = Staff::where('institute_id', $authUser->institute_id)->get();
    $students = Student::where('institute_id', $authUser->institute_id)->get();

    return response()->json([
        'staffs'   => $staffs,
        'students' => $students,
    ]);
}

public function instituteStudents(Request $request)
{
	$authUser = $this->authInstitute($request);
	 // Only institute admin
    	if ($authUser->user_type !== 1) {
        return response()->json(['message' => 'Forbidden'], 403);
    	}
    	
    	$query = Student::where('institute_id', $authUser->institute_id)
        ->with(['user', 'schoolClass']);
        
        
 	//Filter by class_id
 	if ($request->filled('class_id')) {
 	$query->where('class_id',$request->class_id);
 	}
 	
 	// Filter by Standard 
 	if($request->filled('standard')) {
 	$query->whereHas('schoolClass', function ($q) use ($request,$authUser) {
 	$q->where('standard',$request->standard)
 	   ->where('institute_id',$authUser->institute_id);
 	});
	}
	// Filter by stream
	if($request->filled('stream')) {
	$query->whereHas('schoolClass', function ($q) use ($request, $authUser) {
	$q->where('stream',$request->stream)
	  ->where('institute_id', $authUser->institute_id);
	  });
	 } 
	  $students = $query->get();
	  
	  return response()->json([
	  'count' =>  $students->count(),
	  'data' => $students
	  ]);
}


public function instituteStaffs(Request $request)
{
    // 1️⃣ Get token
    $token = $request->bearerToken();
    if (!$token) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // 2️⃣ Find logged-in user
    $authUser = User::where('api_token', hash('sha256', $token))->first();
    if (!$authUser) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // 3️⃣ Only institute admin
    if ($authUser->user_type !== 1) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    // 4️⃣ Build query
    $query = Staff::where('institute_id', $authUser->institute_id)
        ->with('user');

    // 5️⃣ Optional role filter
    if ($request->filled('designation')) {
        $query->where('designation', $request->designation);
    }

    // 6️⃣ Get result
    $staffs = $query->get();

    return response()->json([
        'count' => $staffs->count(),
        'data'  => $staffs
    ]);
}

public function markAttendance(Request $request)
{
    // 1️⃣ Authenticate using token
    $token = $request->bearerToken();
    if (!$token) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $authUser = User::where('api_token', hash('sha256', $token))->first();
    if (!$authUser) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // 2️⃣ Only admin or staff can mark attendance
    if (!in_array($authUser->user_type, [1, 2])) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    // 3️⃣ Validate request
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'user_type' => 'required|in:2,3',
        'attendance_date' => 'required|date',
        'status' => 'required|in:present,absent,late',
        'remark' => 'nullable|string',
    ]);

    // 4️⃣ Ensure same institute
    $targetUser = User::where('id', $request->user_id)
        ->where('institute_id', $authUser->institute_id)
        ->first();

    if (!$targetUser) {
        return response()->json(['message' => 'User not found in your institute'], 404);
    }

    // 5️⃣ Prevent duplicate attendance
    $attendance = Attendance::updateOrCreate(
        [
            'institute_id' => $authUser->institute_id,
            'user_id' => $request->user_id,
            'attendance_date' => $request->attendance_date,
        ],
        [
            'user_type' => $request->user_type,
            'status' => $request->status,
            'remark' => $request->remark,
            'marked_by' => $authUser->id,
        ]
    );

    return response()->json([
        'message' => 'Attendance marked successfully',
        'data' => $attendance
    ], 201);
}

public function markBulkAttendance(Request $request)
{
    // 1️⃣ Authenticate
    $token = $request->bearerToken();
    if (!$token) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $authUser = User::where('api_token', hash('sha256', $token))->first();
    if (!$authUser) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // 2️⃣ Role check
    if (!in_array($authUser->user_type, [1, 2])) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    // 3️⃣ Validate
    $request->validate([
        'attendance_date' => 'required|date',
        'records' => 'required|array|min:1',
        'records.*.created_for' => 'required|exists:users,id',
        'records.*.status' => 'required|in:present,absent,late',
        'records.*.remark' => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        $saved = [];

        foreach ($request->records as $row) {

            // 4️⃣ Ensure same institute
            $user = User::where('id', $row['created_for'])
                ->where('institute_id', $authUser->institute_id)
                ->first();

            if (!$user) {
                throw new \Exception("User ID {$row['user_id']} not in your institute");
            }

            // 5️⃣ Insert / Update
            $attendance = Attendance::updateOrCreate(
                [
                    'institute_id' => $authUser->institute_id,
                    'created_for' => $row['created_for'],
                    'attendance_date' => $request->attendance_date,
                    'created_by' => $authUser->id,
                ],
                [
                    'status' => $row['status'],
                    'remark' => $row['remark'] ?? null,
                    'marked_by' => $authUser->id,
                ]
            );

            $saved[] = $attendance;
        }

        DB::commit();

        return response()->json([
            'message' => 'Bulk attendance marked successfully',
            'count' => count($saved),
            'data' => $saved
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Attendance failed',
            'error' => $e->getMessage()
        ], 422);
    }
}
/*============================
FETCH ATTENDANCE
============================*/
public function fetchAttendance(Request $request)
{
    // 1️⃣ Authenticate admin
    $token = $request->bearerToken();
    abort_if(!$token, 401, 'Unauthorized');

    $authUser = User::where('api_token', hash('sha256', $token))->first();
    abort_if(!$authUser, 401, 'Unauthorized');

    // Only institute admin allowed
    if ($authUser->user_type !== 1) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    // 2️⃣ Validate input
    $request->validate([
        'month' => 'required|in:jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dec',
        'year'  => 'required|integer',
        'created_for' => 'required|exists:users,id'
    ]);

    // 3️⃣ Ensure user belongs to same institute
    $targetUser = User::where('id', $request->created_for)
        ->where('institute_id', $authUser->institute_id)
        ->firstOrFail();

    // 4️⃣ Month mapping
    $monthMap = [
        'jan'=>1,'feb'=>2,'mar'=>3,'apr'=>4,'may'=>5,'jun'=>6,
        'jul'=>7,'aug'=>8,'sep'=>9,'oct'=>10,'nov'=>11,'dec'=>12
    ];

    $monthNumber = $monthMap[$request->month];

    // 5️⃣ Fetch attendance
    $records = Attendance::where('created_For', $targetUser->id)
        ->whereMonth('attendance_date', $monthNumber)
        ->whereYear('attendance_date', $request->year)
        ->orderBy('attendance_date')
        ->get();

    // 6️⃣ Summary calculation
    $summary = [
        'total_days'   => $records->count(),
        'present_days' => $records->where('status', 'present')->count(),
        'absent_days'  => $records->where('status', 'absent')->count(),
        'late_days'    => $records->where('status', 'late')->count(),
    ];

    return response()->json([
        'user' => [
            'id'   => $targetUser->id,
            'name' => $targetUser->name,
        ],
        'month'   => $request->month,
        'year'    => $request->year,
        'summary' => $summary,
        'records' => $records
    ]);
}

/* ==========================
       ADD CLASS
    =========================== */

public function addClass(Request $request)
{
    $authUser = $this->authInstitute($request);

    // Only institute admin
    if ($authUser->user_type !== 1) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

   $request->validate([
    'standard' => 'required|string',
    'description' => 'nullable|string',
]);

    $class = ClassModel::create([
        'institute_id' => $authUser->institute_id,
        'standard'     => $request->standard,
        'description'       => $request->description,
        'created_by'   => $authUser->id,
    ]);

    return response()->json([
        'message' => 'Class added successfully',
        'data'    => $class
    ], 201);
}

/* ==========================
       show CLASS
    =========================== */

public function classes(Request $request, $id = null)
{
    $authUser = $this->authInstitute($request);

    // Only institute admin
    if ($authUser->user_type !== 1) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    $query = ClassModel::where('institute_id', $authUser->institute_id);

    // If ID is provided → fetch single class
    if ($id) {
        $class = $query->where('id', $id)->first();

        if (!$class) {
            return response()->json([
                'message' => 'Class not found for this institute'
            ], 404);
        }

        return response()->json([
            'message' => 'Class details fetched successfully',
            'data'    => $class
        ]);
    }

    // Optional filters (for list)
    if ($request->filled('standard')) {
        $query->where('standard', $request->standard);
    }

    if ($request->filled('stream')) {
        $query->where('stream', $request->stream);
    }

    $classes = $query->orderBy('standard')->get();

    return response()->json([
        'count' => $classes->count(),
        'data'  => $classes
    ]);
}

// Forgot password

// -------------
public function forgotPassword(Request $request)
{
    $request->validate([
        'login' => 'required'
    ]);
    // Find user by email or mobile
    if (filter_var($request->login, FILTER_VALIDATE_EMAIL)) {
        $user = User::where('email', $request->login)->first();
    } else {
        $user = User::where('mobile', $request->login)->first();
    }
     if (!$user) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }
   // Generate 4-digit OTP
   $token = rand(1000,9999);
   
   $user->reset_token = $token;
   $user->reset_token_expires_at = now()->addMinute(2);
   $user->save();
   
   return response()->json([
   	'message' => 'OTP generated successfully',
   	'otp' => $token,
   	'expires_in_seconds' => 120 
   ]);
}

// Reset Password
public function resetPassword(Request $request)
{
    $request->validate(
        [
            'login' => 'required',
            'reset_token' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ],
        [
            'new_password.confirmed' => 'Password is not correct, please try again',
        ]
    );
    
    if (filter_var($request->login, FILTER_VALIDATE_EMAIL)) {
        $user = User::where('email', $request->login)->first();
    } else {
        $user = User::where('mobile', $request->login)->first();
    }

    if (!$user) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }
    
    if (
        $user->reset_token !== $request->reset_token ||
        now()->gt($user->reset_token_expires_at)
    ) {
        return response()->json([
            'message' => 'Invalid or expired reset token'
        ], 422);
    }

    // Update password
    $user->password = Hash::make($request->password);
    $user->reset_token = null;
    $user->reset_token_expires_at = null;
    $user->api_token = null; // logout all sessions
    $user->save();

    return response()->json([
        'message' => 'Password reset successfully'
    ]);
}
/*============================
 ATTENDANCE REPORT
==============================*/
public function AttendanceReport(Request $request)
{
    /* ==========================
       1️⃣ AUTHENTICATE ADMIN
    =========================== */
    $token = $request->bearerToken();
    abort_if(!$token, 401, 'Unauthorized');

    $authUser = User::where('api_token', hash('sha256', $token))->first();
    abort_if(!$authUser, 401, 'Unauthorized');

    if ($authUser->user_type !== 1) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    /* ==========================
       2️⃣ VALIDATION
    =========================== */
    $request->validate([
        'created_for'   => 'required|array|min:1',
        'created_for.*' => 'exists:users,id',
        'duration_type' => 'required|in:single,range',

        'date'       => 'required_if:duration_type,single|date',
        'from_date'  => 'required_if:duration_type,range|date',
        'to_date'    => 'required_if:duration_type,range|date|after_or_equal:from_date',
    ]);

    /* ==========================
       3️⃣ FETCH USERS (SAME INSTITUTE)
    =========================== */
    $users = User::whereIn('id', $request->created_for)
        ->where('institute_id', $authUser->institute_id)
        ->get();

    if ($users->count() === 0) {
        return response()->json(['message' => 'No valid users found'], 404);
    }

    $responseData = [];

    /* ==========================
       4️⃣ LOOP EACH USER
    =========================== */
    foreach ($users as $user) {

        $query = Attendance::where('created_for', $user->id)
            ->orderBy('attendance_date');

        if ($request->duration_type === 'single') {
            $query->whereDate('attendance_date', $request->date);
        }

        if ($request->duration_type === 'range') {
            $query->whereBetween('attendance_date', [
                $request->from_date,
                $request->to_date
            ]);
        }

        $records = $query->get();

        /* ==========================
           5️⃣ USER INFO
        =========================== */
        $userInfo = [
            'id'   => $user->id,
            'name' => $user->name,
            'type' => $user->user_type === 3 ? 'student' : 'staff',
        ];

        if ($user->user_type === 3) {
            $student = Student::where('created_for', $user->id)->first();
            $userInfo['roll_no'] = $student->roll_no ?? null;
        }

        /* ==========================
           6️⃣ FORMAT RECORDS
        =========================== */
        $formattedRecords = $records->map(function ($row) {
            $creator = User::find($row->created_by);

            return [
                'attendance_date' => $row->attendance_date,
                'attendance_time' => $row->created_at->format('h:i A'),
                'status' => $row->status,
                'created_by' => $creator ? [
                    'id'   => $creator->id,
                    'name' => $creator->name,
                ] : null
            ];
        });

        $responseData[] = [
            'user'    => $userInfo,
            'count'   => $formattedRecords->count(),
            'records' => $formattedRecords
        ];
    }

    /* ==========================
       7️⃣ FINAL RESPONSE
    =========================== */
    return response()->json([
        'duration' => $request->duration_type === 'single'
            ? [
                'type' => 'single',
                'date' => $request->date
            ]
            : [
                'type'      => 'range',
                'from_date' => $request->from_date,
                'to_date'   => $request->to_date
            ],
        'total_users' => count($responseData),
        'data'        => $responseData
    ]);
}

}
