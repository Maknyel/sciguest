<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Module;
use App\Models\Section;
use App\Models\StudentActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $sections = Section::orderBy('name')->get();
        $modules = Module::orderBy('order')->withCount('activities')->get();

        $sectionProgress = [];
        foreach ($sections as $section) {
            $students = User::where('section_id', $section->id)->where('role', 'student')->pluck('id');
            $moduleProgress = [];
            foreach ($modules as $module) {
                $totalActivities = $module->activities_count;
                if ($totalActivities === 0 || $students->isEmpty()) {
                    $moduleProgress[$module->id] = 0;
                    continue;
                }
                $totalPossible = $totalActivities * $students->count();
                $completed = StudentActivity::whereIn('user_id', $students)
                    ->whereHas('activity', fn($q) => $q->where('module_id', $module->id))
                    ->where('quiz_completed', true)
                    ->count();
                $moduleProgress[$module->id] = $totalPossible > 0 ? round(($completed / $totalPossible) * 100) : 0;
            }
            $sectionProgress[$section->id] = $moduleProgress;
        }

        $completedStudents = User::where('role', 'student')
            ->whereHas('studentActivities', fn($q) => $q->where('quiz_completed', true))
            ->with('section')
            ->get();

        return Inertia::render('Teacher/Dashboard', [
            'user' => $user,
            'sections' => $sections,
            'modules' => $modules,
            'sectionProgress' => $sectionProgress,
            'completedStudents' => $completedStudents,
        ]);
    }

    public function notifications()
    {
        $user = Auth::user();
        $completedStudents = User::where('role', 'student')
            ->whereHas('studentActivities', fn($q) => $q->where('quiz_completed', true))
            ->with(['section', 'studentActivities' => fn($q) => $q->where('quiz_completed', true)])
            ->get();

        return Inertia::render('Teacher/Notifications', [
            'user' => $user,
            'completedStudents' => $completedStudents,
        ]);
    }
}
