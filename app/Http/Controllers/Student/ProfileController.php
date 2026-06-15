<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in web.php
    }

    public function index()
    {
        $user    = Auth::guard('student')->user()->load(['studentClass', 'studentBadges.badge']);
        $badges  = $user->studentBadges->map(fn($sb) => $sb->badge);

        $xpHistory = $user->xpTransactions()->latest()->take(20)->get();

        // Build 12-week attendance heatmap
        $attendances = Attendance::where('created_for', $user->id)
            ->where('attendance_date', '>=', now()->subWeeks(12))
            ->pluck('attendance_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
            ->toArray();

        // Get available avatars and banners
        $avatars = [];
        $avatarPath = public_path('uploads/images/banners/Avatar');
        if (File::exists($avatarPath)) {
            $avatars = array_map(function($file) {
                return $file->getFilename();
            }, File::files($avatarPath));
        }

        $banners = [];
        $bannerPath = public_path('uploads/images/banners/frames');
        if (File::exists($bannerPath)) {
            $banners = array_map(function($file) {
                return $file->getFilename();
            }, File::files($bannerPath));
        }

        $unlockedItems = $user->unlocked_items ?? [];
        $unlockedAvatars = $unlockedItems['avatars'] ?? [];
        $unlockedFrames = $user->unlocked_frames;

        // Strip previously applied items if they are no longer unlocked
        $userUpdated = false;
        if ($user->avatar) {
            $avatarFilename = basename($user->avatar);
            if (!in_array($avatarFilename, $unlockedAvatars)) {
                $user->avatar = null;
                $userUpdated = true;
            }
        }

        if ($user->banner) {
            $bannerFilename = basename($user->banner);
            if (!in_array($bannerFilename, $unlockedFrames)) {
                $user->banner = 'uploads/images/banners/frames/Bronze.png';
                $userUpdated = true;
            }
        }

        if ($userUpdated) {
            $user->save();
        }

        return view('student.profile.index', compact(
            'user', 'badges', 'xpHistory', 'attendances', 'avatars', 'banners',
            'unlockedAvatars', 'unlockedFrames'
        ));
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'nullable|string',
            'banner' => 'nullable|string',
        ]);

        $user = Auth::guard('student')->user();
        
        if ($request->filled('avatar')) {
            $user->avatar = $request->avatar;
        }

        if ($request->filled('banner')) {
            $user->banner = $request->banner;
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function changePassword()
    {
        $user = Auth::guard('student')->user();
        
        // Check if OTP already exists
        if (!\Illuminate\Support\Facades\Cache::has('password_reset_otp_' . $user->id)) {
            // Generate a 6 digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Store in cache for 10 minutes
            \Illuminate\Support\Facades\Cache::put('password_reset_otp_' . $user->id, $otp, now()->addMinutes(10));
            \Illuminate\Support\Facades\Cache::put('password_reset_otp_time_' . $user->id, now(), now()->addMinutes(10));
            
            // Clear any previous verified session flag
            session()->forget('otp_verified_time');

            // Send email
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));
            } catch (\Exception $e) {
                // Log or handle email failure if needed
                \Illuminate\Support\Facades\Log::error("Failed to send OTP email: " . $e->getMessage());
            }
        }

        $lastSent = \Illuminate\Support\Facades\Cache::get('password_reset_otp_time_' . $user->id);
        $cooldown = 0;
        if ($lastSent) {
            $secondsSinceLast = $lastSent->diffInSeconds(now());
            if ($secondsSinceLast < 60) {
                $cooldown = 60 - $secondsSinceLast;
            }
        }

        return view('student.profile.change_password', compact('cooldown'));
    }

    public function resendOtp()
    {
        $user = Auth::guard('student')->user();
        $lastSent = \Illuminate\Support\Facades\Cache::get('password_reset_otp_time_' . $user->id);
        
        if ($lastSent) {
            $secondsSinceLast = $lastSent->diffInSeconds(now());
            if ($secondsSinceLast < 60) {
                $cooldown = 60 - $secondsSinceLast;
                return response()->json(['success' => false, 'message' => "Please wait {$cooldown} seconds before resending."], 429);
            }
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        \Illuminate\Support\Facades\Cache::put('password_reset_otp_' . $user->id, $otp, now()->addMinutes(10));
        \Illuminate\Support\Facades\Cache::put('password_reset_otp_time_' . $user->id, now(), now()->addMinutes(10));

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send OTP email: " . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'OTP sent successfully.']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6'
        ]);

        $user = Auth::guard('student')->user();
        $cachedOtp = \Illuminate\Support\Facades\Cache::get('password_reset_otp_' . $user->id);

        if ($cachedOtp && $cachedOtp === $request->otp) {
            // OTP is correct, set a session flag indicating verification
            session(['otp_verified_time' => now()]);
            // Clear the OTP so it can't be reused
            \Illuminate\Support\Facades\Cache::forget('password_reset_otp_' . $user->id);
            
            return response()->json(['success' => true, 'message' => 'OTP verified successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 400);
    }

    public function updatePassword(Request $request)
    {
        // Check if user verified OTP in this session (within last 15 minutes)
        $verifiedTime = session('otp_verified_time');
        if (!$verifiedTime || $verifiedTime->diffInMinutes(now()) > 15) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please request a new OTP.'], 403);
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::guard('student')->user();
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        // Clear the verification flag
        session()->forget('otp_verified_time');

        return response()->json(['success' => true, 'message' => 'Password updated successfully!']);
    }
}
