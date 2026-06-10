<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\User;
use App\Services\AutoRuleEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        if (!Auth::guard('student')->attempt(['email' => $request->email, 'password' => $request->password])) {
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

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'class_id' => $request->class_id,
            'phone'    => $request->phone,
            'role'     => 'student',
            'user_type' => 3, // Make sure user_type is set for consistency!
        ]);

        Auth::guard('student')->login($user);

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
}
