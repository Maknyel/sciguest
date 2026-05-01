<?php

use App\Http\Controllers\AccountManagementController;
use App\Http\Controllers\ActivityManagementController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
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
    Route::put('/activity-management/{activity}', [ActivityManagementController::class, 'update'])->name('activity-management.update');

    // Account Management
    Route::get('/account-management', [AccountManagementController::class, 'index'])->name('account-management');
    Route::post('/account-management/{student}/approve', [AccountManagementController::class, 'approve'])->name('account-management.approve');
    Route::delete('/account-management/{student}/decline', [AccountManagementController::class, 'decline'])->name('account-management.decline');
    Route::delete('/account-management/{student}', [AccountManagementController::class, 'destroy'])->name('account-management.destroy');
    Route::delete('/account-management', [AccountManagementController::class, 'destroyAll'])->name('account-management.destroyAll');
});
