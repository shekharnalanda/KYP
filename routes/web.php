<?php

use App\Http\Controllers\AdminAssessmentController;
use App\Http\Controllers\AdminProgressController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\IrisConnectorController;
use App\Http\Controllers\LearningSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResultController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::get('/verify-certificate/{token}', [ResultController::class, 'verify'])->middleware('throttle:60,1')->name('certificate.verify');

Route::prefix('api/iris')->group(function (): void {
    Route::get('/health', [IrisConnectorController::class, 'health'])->middleware('throttle:30,1');
    Route::get('/candidates', [IrisConnectorController::class, 'candidates'])->middleware('throttle:12,1');
    Route::post('/enroll', [IrisConnectorController::class, 'enroll'])->middleware('throttle:20,1');
    Route::post('/attendance', [IrisConnectorController::class, 'attendance'])->middleware('throttle:120,1');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1');
});

Route::middleware(['auth', 'auth.session'])->group(function (): void {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->middleware('throttle:5,1')->name('profile.password.update');

    Route::middleware('role:student,teacher,admin,master_admin')->group(function (): void {
        Route::get('/learning', [LearningSessionController::class, 'index'])->name('learning.index');
        Route::get('/learning/{session}', [LearningSessionController::class, 'show'])->name('learning.show');
    });

    Route::middleware('role:student')->group(function (): void {
        Route::get('/student', [DashboardController::class, 'student'])->name('student.dashboard');
        Route::post('/learning/{session}/progress', [LearningSessionController::class, 'progress'])->middleware('throttle:120,1')->name('learning.progress');
        Route::post('/learning/{session}/complete', [LearningSessionController::class, 'complete'])->middleware('throttle:20,1')->name('learning.complete');
        Route::get('/student/exams', [ExamController::class, 'index'])->name('student.exams');
        Route::post('/student/exams/{exam}/start', [ExamController::class, 'start'])->middleware('throttle:10,1')->name('student.exam.start');
        Route::get('/student/attempts/{attempt}', [ExamController::class, 'attempt'])->name('student.exam.attempt');
        Route::post('/student/attempts/{attempt}/submit', [ExamController::class, 'submit'])->middleware('throttle:5,1')->name('student.exam.submit');
        Route::get('/student/results/{result}', [ExamController::class, 'result'])->name('student.exam.result');
        Route::get('/student/marksheets/{result}', [ResultController::class, 'marksheet'])->name('student.marksheet');
        Route::get('/student/certificates/{certificate}', [ResultController::class, 'certificate'])->name('student.certificate');
    });

    Route::middleware('role:teacher,admin,master_admin')->group(function (): void {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->middleware('throttle:60,1')->name('attendance.store');
        Route::post('/attendance/bulk', [AttendanceController::class, 'bulkStore'])->middleware('throttle:20,1')->name('attendance.bulk');
    });

    Route::get('/teacher', [DashboardController::class, 'teacher'])->middleware('role:teacher')->name('teacher.dashboard');

    Route::middleware('role:admin,master_admin')->group(function (): void {
        Route::get('/admin', [DashboardController::class, 'admin'])->name('admin.dashboard');
        Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
        Route::post('/admin/users', [AdminUserController::class, 'store'])->middleware('throttle:20,1')->name('admin.users.store');
        Route::post('/admin/users/{user}/status', [AdminUserController::class, 'status'])->middleware('throttle:30,1')->name('admin.users.status');
        Route::post('/admin/users/{user}/enrollments', [AdminUserController::class, 'enrollments'])->middleware('throttle:30,1')->name('admin.users.enrollments');
        Route::get('/admin/progress', [AdminProgressController::class, 'index'])->name('admin.progress');
        Route::get('/admin/assessments', [AdminAssessmentController::class, 'index'])->name('admin.assessments');
        Route::post('/admin/exams', [AdminAssessmentController::class, 'storeExam'])->middleware('throttle:20,1')->name('admin.exams.store');
        Route::put('/admin/exams/{exam}', [AdminAssessmentController::class, 'updateExam'])->middleware('throttle:30,1')->name('admin.exams.update');
        Route::post('/admin/questions', [AdminAssessmentController::class, 'storeQuestion'])->middleware('throttle:30,1')->name('admin.questions.store');
        Route::get('/admin/questions/{question}/edit', [AdminAssessmentController::class, 'editQuestion'])->name('admin.questions.edit');
        Route::put('/admin/questions/{question}', [AdminAssessmentController::class, 'updateQuestion'])->middleware('throttle:30,1')->name('admin.questions.update');
        Route::get('/admin/results', [ResultController::class, 'index'])->name('admin.results');
        Route::post('/admin/results/bulk-print', [ResultController::class, 'bulkPrint'])->middleware('throttle:10,1')->name('admin.results.bulk');
        Route::post('/admin/results/{result}/publish', [ResultController::class, 'publish'])->middleware('throttle:30,1')->name('admin.results.publish');
    });
});
