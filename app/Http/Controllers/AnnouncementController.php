<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Notification;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sections = Section::orderBy('name')->get(['id', 'name']);
        $announcements = Announcement::with('section')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Teacher/Announcements', [
            'user' => $user,
            'sections' => $sections,
            'announcements' => $announcements,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $announcement = Announcement::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'section_id' => $request->section_id ?: null,
        ]);

        // Notify relevant students
        $query = User::where('role', 'student')->where('status', 'approved');
        if ($announcement->section_id) {
            $query->where('section_id', $announcement->section_id);
        }
        $students = $query->pluck('id');

        $notifications = $students->map(fn($id) => [
            'user_id' => $id,
            'message' => $request->message,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        Notification::insert($notifications);

        return back()->with('success', 'Announcement posted.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back();
    }

    public function destroyAll()
    {
        Announcement::where('user_id', Auth::id())->delete();
        return back();
    }
}
