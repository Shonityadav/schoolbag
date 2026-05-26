<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentClass;
use App\Models\User;
use App\Services\AutoRuleEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('student.dashboard');
        $classes = StudentClass::orderBy('level')->get();
        return view('student.auth.login', compact('classes'));
    }

    public function showRegister()
    {
        if (Auth::check()) return redirect()->route('student.dashboard');
        $classes = StudentClass::orderBy('level')->get();
        return view('student.auth.register', compact('classes'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:4',
        ]);

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        $user = Auth::user();

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
        ]);

        Auth::login($user);

        return redirect()->route('student.dashboard')->with('success', "Welcome, {$user->name}! 🎉");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('student.welcome');
    }
}
