<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::middleware('role:student')->group(function (): void {
        Route::get('/student', [DashboardController::class, 'student'])->name('student.dashboard');
        Route::get('/student/exams', [ExamController::class, 'index'])->name('student.exams');
    });

    Route::get('/teacher', [DashboardController::class, 'teacher'])->middleware('role:teacher')->name('teacher.dashboard');
    Route::get('/admin', [DashboardController::class, 'admin'])->middleware('role:admin,master_admin')->name('admin.dashboard');
});
