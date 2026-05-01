<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class TeacherManagementController extends Controller
{
    public function index()
    {
        $teachers = User::where('role', 'teacher')->orderBy('id')->get(['id', 'full_name', 'username', 'created_at']);

        return Inertia::render('Teacher/TeacherManagement', [
            'user'     => Auth::user(),
            'teachers' => $teachers,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username'  => 'required|string|max:100|unique:users',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'full_name' => $request->full_name,
            'username'  => $request->username,
            'password'  => Hash::make($request->password),
            'role'      => 'teacher',
            'status'    => 'approved',
        ]);

        return back()->with('success', 'Teacher account created.');
    }

    public function update(Request $request, User $teacher)
    {
        // Only teacher #1 (super admin) cannot be edited by others
        if ($teacher->id === 1 && Auth::id() !== 1) {
            abort(403, 'You cannot edit the primary teacher account.');
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'username'  => 'required|string|max:100|unique:users,username,' . $teacher->id,
            'password'  => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'full_name' => $request->full_name,
            'username'  => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $teacher->update($data);

        return back()->with('success', 'Teacher updated.');
    }

    public function destroy(User $teacher)
    {
        if ($teacher->id === 1) {
            return back()->withErrors(['error' => 'The primary teacher account cannot be deleted.']);
        }

        if ($teacher->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $teacher->delete();
        return back()->with('success', 'Teacher deleted.');
    }
}
