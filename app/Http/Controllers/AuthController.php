<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['username' => 'Invalid username or password.']);
        }

        if ($user->isStudent() && $user->status !== 'approved') {
            return back()->withErrors(['username' => 'Your account is pending approval.']);
        }

        Auth::login($user, $request->boolean('remember'));

        return $this->redirectByRole($user);
    }

    public function showRegister()
    {
        $sections = Section::orderBy('name')->get(['id', 'name']);
        $teachers = User::where('role', 'teacher')->get(['id', 'full_name']);
        return Inertia::render('Auth/Register', compact('sections', 'teachers'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username'  => 'required|string|max:100|unique:users',
            'password'  => 'required|string|min:8|confirmed',
            'section_id' => 'required|exists:sections,id',
        ]);

        User::create([
            'full_name'  => $request->full_name,
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
            'role'       => 'student',
            'section_id' => $request->section_id,
            'status'     => 'pending',
        ]);

        return redirect()->route('login')->with('success', 'Account created. Wait for teacher approval.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByRole(User $user)
    {
        return $user->isTeacher()
            ? redirect()->route('teacher.dashboard')
            : redirect()->route('student.dashboard');
    }
}
