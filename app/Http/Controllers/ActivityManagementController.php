<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Module;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ActivityManagementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $modules = Module::with([
            'activities' => fn($q) => $q->orderBy('order')->with('quizQuestions'),
        ])->orderBy('order')->get();

        return Inertia::render('Teacher/ActivityManagement', [
            'user' => $user,
            'modules' => $modules,
        ]);
    }

    // ---------- Modules ----------

    public function storeModule(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'icon'        => 'nullable|string|max:10',
            'description' => 'nullable|string',
        ]);

        $order = Module::max('order') + 1;

        $module = Module::create([
            'name'        => $request->name,
            'icon'        => $request->icon ?: '⚙',
            'description' => $request->description,
            'order'       => $order,
            'color'       => '#00e5ff',
        ]);

        return back()->with('success', 'Module created.');
    }

    public function destroyModule(Module $module)
    {
        if ($module->image) {
            Storage::disk('public')->delete($module->image);
        }
        $module->delete();
        return back()->with('success', 'Module deleted.');
    }

    public function uploadModuleImage(Request $request, Module $module)
    {
        $request->validate(['image' => 'required|image|max:4096']);

        if ($module->image) {
            Storage::disk('public')->delete($module->image);
        }

        $path = $request->file('image')->store('module-images', 'public');
        $module->update(['image' => $path]);

        return response()->json(['url' => Storage::disk('public')->url($path)]);
    }

    // ---------- Activities ----------

    public function storeActivity(Request $request, Module $module)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'icon'             => 'nullable|string|max:10',
            'description'      => 'nullable|string',
            'procedure'        => 'nullable|string',
            'safety_reminders' => 'nullable|string',
        ]);

        $order = $module->activities()->max('order') + 1;

        $activity = Activity::create([
            'module_id'        => $module->id,
            'name'             => $request->name,
            'icon'             => $request->icon ?: '🔬',
            'description'      => $request->description,
            'procedure'        => $request->procedure,
            'safety_reminders' => $request->safety_reminders,
            'order'            => $order,
            'is_locked'        => true,
        ]);

        return back()->with('success', 'Activity created.');
    }

    public function destroyActivity(Activity $activity)
    {
        if ($activity->image) {
            Storage::disk('public')->delete($activity->image);
        }
        $activity->delete();
        return back()->with('success', 'Activity deleted.');
    }

    public function uploadActivityImage(Request $request, Activity $activity)
    {
        $request->validate(['image' => 'required|image|max:4096']);

        if ($activity->image) {
            Storage::disk('public')->delete($activity->image);
        }

        $path = $request->file('image')->store('activity-images', 'public');
        $activity->update(['image' => $path]);

        return response()->json(['url' => Storage::disk('public')->url($path)]);
    }

    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'is_locked'       => 'required|boolean',
            'deadline_enabled' => 'required|boolean',
            'deadline_at'     => 'nullable|date',
        ]);

        $activity->update([
            'is_locked'       => $request->is_locked,
            'deadline_enabled' => $request->deadline_enabled,
            'deadline_at'     => $request->deadline_enabled ? $request->deadline_at : null,
        ]);

        return response()->json(['success' => true, 'activity' => $activity]);
    }

    // ---------- Quiz Questions ----------

    public function storeQuestion(Request $request, Activity $activity)
    {
        $request->validate([
            'question'       => 'required|string',
            'options'        => 'required|array|min:2',
            'options.*'      => 'required|string',
            'correct_answer' => 'required|string',
        ]);

        $order = $activity->quizQuestions()->max('order') + 1;

        QuizQuestion::create([
            'activity_id'    => $activity->id,
            'order'          => $order,
            'question'       => $request->question,
            'options'        => $request->options,
            'correct_answer' => $request->correct_answer,
        ]);

        return back()->with('success', 'Question added.');
    }

    public function destroyQuestion(QuizQuestion $question)
    {
        $question->delete();
        return back()->with('success', 'Question deleted.');
    }
}
