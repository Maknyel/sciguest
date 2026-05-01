<?php

use App\Http\Controllers\AccountManagementController;
use App\Http\Controllers\ActivityManagementController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherManagementController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Student Routes
Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/module/{module}', [StudentController::class, 'module'])->name('module');
    Route::get('/activity/{activity}', [StudentController::class, 'activity'])->name('activity');
    Route::post('/activity/{activity}/complete', [StudentController::class, 'completeActivity'])->name('activity.complete');
    Route::post('/activity/{activity}/quiz', [StudentController::class, 'submitQuiz'])->name('activity.quiz');
    Route::get('/notifications', [StudentController::class, 'notifications'])->name('notifications');
    Route::get('/history', [StudentController::class, 'history'])->name('history');

    // Notification actions
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
});

// Teacher Routes
Route::middleware(['auth'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
    Route::get('/notifications', [TeacherController::class, 'notifications'])->name('notifications');

    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::delete('/announcements', [AnnouncementController::class, 'destroyAll'])->name('announcements.destroyAll');

    // Activity Management
    Route::get('/activity-management', [ActivityManagementController::class, 'index'])->name('activity-management');
    Route::put('/activity-management/activity/{activity}', [ActivityManagementController::class, 'update'])->name('activity-management.update');

    // Modules CRUD
    Route::post('/activity-management/modules', [ActivityManagementController::class, 'storeModule'])->name('activity-management.modules.store');
    Route::delete('/activity-management/modules/{module}', [ActivityManagementController::class, 'destroyModule'])->name('activity-management.modules.destroy');
    Route::post('/activity-management/modules/{module}/image', [ActivityManagementController::class, 'uploadModuleImage'])->name('activity-management.modules.image');

    // Activities CRUD
    Route::post('/activity-management/modules/{module}/activities', [ActivityManagementController::class, 'storeActivity'])->name('activity-management.activities.store');
    Route::delete('/activity-management/activities/{activity}', [ActivityManagementController::class, 'destroyActivity'])->name('activity-management.activities.destroy');
    Route::post('/activity-management/activities/{activity}/image', [ActivityManagementController::class, 'uploadActivityImage'])->name('activity-management.activities.image');

    // Quiz Questions CRUD
    Route::post('/activity-management/activities/{activity}/questions', [ActivityManagementController::class, 'storeQuestion'])->name('activity-management.questions.store');
    Route::delete('/activity-management/questions/{question}', [ActivityManagementController::class, 'destroyQuestion'])->name('activity-management.questions.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Teacher Management
    Route::get('/teacher-management', [TeacherManagementController::class, 'index'])->name('teacher-management');
    Route::post('/teacher-management', [TeacherManagementController::class, 'store'])->name('teacher-management.store');
    Route::put('/teacher-management/{teacher}', [TeacherManagementController::class, 'update'])->name('teacher-management.update');
    Route::delete('/teacher-management/{teacher}', [TeacherManagementController::class, 'destroy'])->name('teacher-management.destroy');

    // Account Management
    Route::get('/account-management', [AccountManagementController::class, 'index'])->name('account-management');
    Route::post('/account-management/{student}/approve', [AccountManagementController::class, 'approve'])->name('account-management.approve');
    Route::delete('/account-management/{student}/decline', [AccountManagementController::class, 'decline'])->name('account-management.decline');
    Route::delete('/account-management/{student}', [AccountManagementController::class, 'destroy'])->name('account-management.destroy');
    Route::delete('/account-management', [AccountManagementController::class, 'destroyAll'])->name('account-management.destroyAll');
});
