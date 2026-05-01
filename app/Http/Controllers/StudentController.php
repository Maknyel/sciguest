<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Module;
use App\Models\Notification;
use App\Models\StudentActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user()->load('section');
        $modules = Module::orderBy('order')->withCount('activities')->get();

        $progress = [];
        foreach ($modules as $module) {
            $total = $module->activities_count;
            $completed = StudentActivity::whereHas('activity', fn($q) => $q->where('module_id', $module->id))
                ->where('user_id', $user->id)
                ->where('quiz_completed', true)
                ->count();
            $progress[$module->id] = $total > 0 ? round(($completed / $total) * 100) : 0;
        }

        return Inertia::render('Student/Dashboard', [
            'modules' => $modules,
            'progress' => $progress,
            'user' => $user,
        ]);
    }

    public function module(Module $module)
    {
        $user = Auth::user();
        $activities = $module->activities()->get();

        $studentRecords = StudentActivity::where('user_id', $user->id)
            ->whereIn('activity_id', $activities->pluck('id'))
            ->get()
            ->keyBy('activity_id');

        $activitiesData = $activities->map(function ($activity) use ($studentRecords) {
            $record = $studentRecords->get($activity->id);
            return array_merge($activity->toArray(), [
                'activity_completed' => $record?->activity_completed ?? false,
                'quiz_completed' => $record?->quiz_completed ?? false,
                'quiz_score' => $record?->quiz_score,
                'quiz_max_score' => $record?->quiz_max_score,
            ]);
        });

        return Inertia::render('Student/Module', [
            'module' => $module,
            'activities' => $activitiesData,
            'user' => $user,
        ]);
    }

    public function activity(Activity $activity)
    {
        $user = Auth::user();
        $activity->load('module', 'quizQuestions');

        $record = StudentActivity::firstOrCreate(
            ['user_id' => $user->id, 'activity_id' => $activity->id]
        );

        return Inertia::render('Student/Activity', [
            'activity' => $activity,
            'record' => $record,
            'user' => $user,
        ]);
    }

    public function completeActivity(Activity $activity)
    {
        $user = Auth::user();
        StudentActivity::updateOrCreate(
            ['user_id' => $user->id, 'activity_id' => $activity->id],
            ['activity_completed' => true]
        );

        return response()->json(['success' => true]);
    }

    public function submitQuiz(Request $request, Activity $activity)
    {
        $request->validate(['answers' => 'required|array']);

        $user = Auth::user();
        $questions = $activity->quizQuestions;
        $score = 0;

        foreach ($questions as $question) {
            if (isset($request->answers[$question->id]) &&
                $request->answers[$question->id] === $question->correct_answer) {
                $score++;
            }
        }

        $record = StudentActivity::updateOrCreate(
            ['user_id' => $user->id, 'activity_id' => $activity->id],
            [
                'activity_completed' => true,
                'quiz_completed' => true,
                'quiz_score' => $score,
                'quiz_max_score' => $questions->count(),
                'completed_at' => now(),
            ]
        );

        return response()->json([
            'score' => $score,
            'max_score' => $questions->count(),
            'record' => $record,
        ]);
    }

    public function notifications()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Student/Notifications', [
            'notifications' => $notifications,
            'user' => $user,
        ]);
    }

    public function history()
    {
        $user = Auth::user();
        $history = StudentActivity::where('user_id', $user->id)
            ->with('activity.module')
            ->where('quiz_completed', true)
            ->orderByDesc('completed_at')
            ->get();

        return Inertia::render('Student/History', [
            'history' => $history,
            'user' => $user,
        ]);
    }
}
