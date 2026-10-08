<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\User;
use App\Services\AutoRuleEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class StudentAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('student')->check()) return redirect()->route('student.dashboard');
        $classes = ClassModel::orderBy('standard')->get();
        return view('student.auth.login', compact('classes'));
    }

    public function showRegister()
    {
        if (Auth::guard('student')->check()) return redirect()->route('student.dashboard');
        $classes = ClassModel::orderBy('standard')->get();
        return view('student.auth.register', compact('classes'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:4',
        ]);

        if (!Auth::guard('student')->attempt(['email' => $request->email, 'password' => $request->password], true)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        $user = Auth::guard('student')->user();

        return redirect()->route('student.dashboard');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:4|confirmed',
            'class_id' => 'required|exists:classes,id',
            'phone'    => 'nullable|string|max:15',
        ]);

        $class = \App\Models\ClassModel::findOrFail($request->class_id);

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'class_id'     => $request->class_id,
            'institute_id' => $class->institute_id,
            'phone'        => $request->phone,
            'role'         => 'student',
            'user_type'    => 3, 
        ]);

        \App\Models\StudentDetails::create([
            'created_for'  => $user->id,
            'institute_id' => $class->institute_id,
            'class_id'     => $request->class_id,
        ]);

        Auth::guard('student')->login($user, true);

        return redirect()->route('student.dashboard')->with('success', "Welcome, {$user->name}! 🎉");
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        // Since session is shared (with different keys), regenerating might affect other guards. 
        // We can just invalidate the guard specific data or just redirect.
        // It's safe to skip session invalidate to keep admin session alive if present.
        return redirect()->route('student.welcome');
    }

    public function sendWhatsappOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15'
        ]);

        $phone = $request->phone;
        // Clean phone number (remove non-digits)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        // Default to India country code if length is 10
        if (strlen($phone) == 10) {
            $phone = '91' . $phone;
        }

        $otp = rand(100000, 999999);
        Session::put('whatsapp_otp_' . $phone, $otp);
        Session::put('whatsapp_otp_time_' . $phone, now());

        $phoneId = env('WHATSAPP_PHONE_ID');
        $token = env('WHATSAPP_TOKEN');
        $templateName = env('WHATSAPP_OTP_TEMPLATE', 'otp_login');

        $url = "https://graph.facebook.com/v22.0/{$phoneId}/messages";
        
        try {
            $response = Http::withToken($token)->post($url, [
                'messaging_product' => 'whatsapp',
                'to' => $phone,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => ['code' => 'en'],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => (string) $otp
                                ]
                            ]
                        ],
                        [
                            'type' => 'button',
                            'sub_type' => 'url',
                            'index' => '0',
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => (string) $otp
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'OTP sent successfully']);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Failed to send OTP: ' . $response->body()
                ], 400); // Changed to 400 so fetch doesn't treat it as a hard server crash in some browsers
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()
            ], 400);
        }
    }

    public function verifyWhatsappOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string|size:6'
        ]);

        $phone = $request->phone;
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) == 10) {
            $phone = '91' . $phone;
        }

        $sessionOtp = Session::get('whatsapp_otp_' . $phone);
        $sessionTime = Session::get('whatsapp_otp_time_' . $phone);

        if (!$sessionOtp || $sessionOtp != $request->otp) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP'], 400);
        }

        if (now()->diffInMinutes($sessionTime) > 10) {
            return response()->json(['success' => false, 'message' => 'OTP expired'], 400);
        }

        Session::forget('whatsapp_otp_' . $phone);
        Session::forget('whatsapp_otp_time_' . $phone);

        $user = User::where('phone', $phone)->orWhere('phone', substr($phone, 2))->first();

        if ($user) {
            Auth::guard('student')->login($user, true);
            Session::flash('success', "Welcome back, {$user->name}! ");
            return response()->json(['success' => true, 'is_new_user' => false, 'redirect' => route('student.dashboard')]);
        } else {
            Session::put('verified_whatsapp_phone', $phone);
            return response()->json(['success' => true, 'is_new_user' => true]);
        }
    }

    public function registerWhatsappUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100'
        ]);

        $phone = Session::get('verified_whatsapp_phone');
        if (!$phone) {
            return response()->json(['success' => false, 'message' => 'Phone verification session expired'], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $phone,
            'email' => uniqid() . '@whatsapp.local',
            'password' => Hash::make(Str::random(16)),
            'role' => 'student',
            'user_type' => 3,
            'class_id' => null,
            'institute_id' => null,
        ]);

        \App\Models\StudentDetails::create([
            'created_for' => $user->id,
            'institute_id' => null,
            'class_id' => null,
        ]);

        Session::forget('verified_whatsapp_phone');
        Auth::guard('student')->login($user, true);
        
        Session::flash('success', "Welcome, {$user->name}! ");
        return response()->json(['success' => true, 'redirect' => route('student.dashboard')]);
    }
}
