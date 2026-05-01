<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ActivityManagementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $modules = Module::with(['activities' => fn($q) => $q->orderBy('order')])->orderBy('order')->get();

        return Inertia::render('Teacher/ActivityManagement', [
            'user' => $user,
            'modules' => $modules,
        ]);
    }

    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'is_locked' => 'required|boolean',
            'deadline_enabled' => 'required|boolean',
            'deadline_at' => 'nullable|date',
        ]);

        $activity->update([
            'is_locked' => $request->is_locked,
            'deadline_enabled' => $request->deadline_enabled,
            'deadline_at' => $request->deadline_enabled ? $request->deadline_at : null,
        ]);

        return response()->json(['success' => true, 'activity' => $activity]);
    }
}
