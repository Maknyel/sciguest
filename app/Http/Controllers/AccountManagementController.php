<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AccountManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $sections = Section::orderBy('name')->get(['id', 'name']);

        $query = User::where('role', 'student')->where('status', 'approved')->with('section');

        if ($request->filled('search')) {
            $query->where('full_name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $students = $query->orderBy('full_name')->get();
        $pending = User::where('role', 'student')->where('status', 'pending')->with('section')->get();

        return Inertia::render('Teacher/AccountManagement', [
            'user' => $user,
            'students' => $students,
            'pending' => $pending,
            'sections' => $sections,
            'filters' => $request->only(['search', 'section_id']),
        ]);
    }

    public function approve(User $student)
    {
        $student->update(['status' => 'approved']);
        return back();
    }

    public function decline(User $student)
    {
        $student->delete();
        return back();
    }

    public function destroy(User $student)
    {
        $student->delete();
        return back();
    }

    public function destroyAll(Request $request)
    {
        $query = User::where('role', 'student')->where('status', 'approved');
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        $query->delete();
        return back();
    }
}
