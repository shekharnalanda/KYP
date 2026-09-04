<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::view('/student', 'panels.student')->middleware('role:student')->name('student.dashboard');
    Route::view('/teacher', 'panels.teacher')->middleware('role:teacher')->name('teacher.dashboard');
    Route::view('/admin', 'panels.admin')->middleware('role:admin,master_admin')->name('admin.dashboard');
});
