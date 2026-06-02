<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Institute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle admin login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->login;

        // Determine if login is email or phone
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (Auth::attempt([$fieldType => $login, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Optional: You can check if the user is an admin here
            // if (Auth::user()->user_type != 1 && Auth::user()->role !== 'admin') {
            //     Auth::logout();
            //     return back()->withErrors(['login' => 'Unauthorized access.']);
            // }

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    /**
     * Show the admin registration form.
     */
    public function showRegister()
    {
        return view('admin.auth.register');
    }

    /**
     * Handle admin registration (Institute Signup).
     */
    public function register(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'admin_name'  => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|min:6|confirmed',
            'phone'       => 'nullable|string|max:20|unique:users,phone',
            'dob'         => 'nullable|string|max:50',
            'state'       => 'nullable|string|max:100',
            'city'        => 'nullable|string|max:100',
            'standard'    => 'nullable|string|max:100',
            'school_address' => 'nullable|string',
            'school_number'  => 'nullable|string|max:20',
        ], [
            'email.unique'  => 'Email already exists, please use another email.',
            'phone.unique'  => 'Phone number already exists, please use another number.',
        ]);

        DB::beginTransaction();

        try {
            // 1. Create School (Institute)
            $school = Institute::create([
                'name'    => $request->school_name,
                'address' => $request->school_address,
                'number'  => $request->school_number,
            ]);

            // 2. Create User (Institute Admin)
            $user = User::create([
                'institute_id' => $school->id,
                'name'         => $request->admin_name,
                'email'        => $request->email,
                'phone'        => $request->phone,
                'password'     => Hash::make($request->password),
                'dob'          => $request->dob,
                'state'        => $request->state,
                'city'         => $request->city,
                // Assuming standard maps to something, or keeping it in user table if it exists
                'user_type'    => 1, // 1 = Institute admin
                'role'         => 'admin', // Kept for compatibility with existing admin views
                'api_token'    => Str::random(60),
            ]);

            DB::commit();

            // Log the user in
            Auth::login($user);

            return redirect()->route('admin.dashboard')->with('success', 'Registration successful. Welcome!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle admin logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
