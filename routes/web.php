<?php

use App\Http\Controllers\StudentIdCardController;
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
use App\Http\Controllers\PublicApplicationController;
use App\Http\Controllers\AdminApplicationController;
use App\Http\Controllers\AdminExceptionalCompletionController;


Route::post('/email-otp/send', [PublicApplicationController::class, 'sendOtp'])->middleware('throttle:3,10')->name('public.otp.send');
Route::post('/email-otp/verify', [PublicApplicationController::class, 'verifyOtp'])->middleware('throttle:10,10')->name('public.otp.verify');

Route::get('/admission', [PublicApplicationController::class, 'admissionForm'])->name('admission.form');
Route::post('/admission', [PublicApplicationController::class, 'admissionStore'])->name('admission.store');
Route::get('/admission/success/{number}', [PublicApplicationController::class, 'admissionSuccess'])->name('admission.success');

Route::get('/enquiry', [PublicApplicationController::class, 'enquiryForm'])->name('enquiry.form');
Route::post('/enquiry', [PublicApplicationController::class, 'enquiryStore'])->name('enquiry.store');
Route::get('/enquiry/success/{number}', [PublicApplicationController::class, 'enquirySuccess'])->name('enquiry.success');


Route::view('/', 'home')->name('home');
Route::get('/verify-certificate/{token}', [ResultController::class, 'verify'])->middleware('throttle:60,1')->name('certificate.verify');

Route::prefix('api/iris')->group(function (): void {
    Route::get('/health', [IrisConnectorController::class, 'health'])->middleware('throttle:30,1');
    Route::get('/catalog', [IrisConnectorController::class, 'catalog'])->middleware('throttle:30,1');
    Route::get('/students', [IrisConnectorController::class, 'students'])->middleware('throttle:30,1');
    Route::get('/candidates', [IrisConnectorController::class, 'candidates'])->middleware('throttle:12,1');
    Route::post('/enroll', [IrisConnectorController::class, 'enroll'])->middleware('throttle:20,1');
    Route::post('/attendance', [IrisConnectorController::class, 'attendance'])->middleware('throttle:120,1');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1');
});

Route::get('/verify-student/{token}', [StudentIdCardController::class, 'verify'])
    ->name('student.id-card.verify');

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
        Route::get('/student/id-card', [StudentIdCardController::class, 'show'])->name('student.id-card');
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
        Route::view('/admin/iris-software', 'admin.iris-software')
            ->name('admin.iris-software');

        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->middleware('throttle:60,1')->name('attendance.store');
        Route::post('/attendance/bulk', [AttendanceController::class, 'bulkStore'])->middleware('throttle:20,1')->name('attendance.bulk');
    });

    Route::get('/teacher', [DashboardController::class, 'teacher'])->middleware('role:teacher')->name('teacher.dashboard');

    Route::middleware('role:admin,master_admin')->group(function (): void {
        Route::get('/admin', [DashboardController::class, 'admin'])->name('admin.dashboard');
        Route::get('/admin/branches', [AdminApplicationController::class, 'branches'])->name('admin.branches');
        Route::post('/admin/branches', [AdminApplicationController::class, 'storeBranch'])->middleware('throttle:20,1')->name('admin.branches.store');
        Route::put('/admin/branches/{branch}', [AdminApplicationController::class, 'updateBranch'])->middleware('throttle:30,1')->name('admin.branches.update');

        Route::get('/admin/admissions', [AdminApplicationController::class, 'admissions'])->name('admin.admissions');
        Route::post('/admin/admissions/{admission}/approve', [AdminApplicationController::class, 'approveAdmission'])->middleware('throttle:20,1')->name('admin.admissions.approve');
        Route::post('/admin/admissions/{admission}/status', [AdminApplicationController::class, 'admissionStatus'])->middleware('throttle:30,1')->name('admin.admissions.status');

        Route::get('/admin/enquiries', [AdminApplicationController::class, 'enquiries'])->name('admin.enquiries');
        Route::put('/admin/enquiries/{enquiry}', [AdminApplicationController::class, 'updateEnquiry'])->middleware('throttle:30,1')->name('admin.enquiries.update');

        Route::get('/admin/students/{student}/id-card', [StudentIdCardController::class, 'adminShow'])->name('admin.student.id-card');
        Route::post('/admin/id-cards/bulk', [StudentIdCardController::class, 'adminBulk'])->middleware('throttle:10,1')->name('admin.id-cards.bulk');
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
        Route::get('/admin/exceptional-completion', [AdminExceptionalCompletionController::class, 'index'])->name('admin.exceptional');
        Route::post('/admin/exceptional-completion/eligibility', [AdminExceptionalCompletionController::class, 'eligibility'])->middleware('throttle:5,1')->name('admin.exceptional.eligibility');
        Route::post('/admin/exceptional-completion/pass', [AdminExceptionalCompletionController::class, 'pass'])->middleware('throttle:5,1')->name('admin.exceptional.pass');
        Route::get('/admin/results', [ResultController::class, 'index'])->name('admin.results');
        Route::get('/admin/results/{result}/marksheet', [ResultController::class, 'adminMarksheet'])->name('admin.result.marksheet');
        Route::get('/admin/certificates/{certificate}', [ResultController::class, 'adminCertificate'])->name('admin.result.certificate');
        
        Route::post('/admin/results/bulk-print', [ResultController::class, 'bulkPrint'])->middleware('throttle:10,1')->name('admin.results.bulk');
        Route::post('/admin/results/{result}/publish', [ResultController::class, 'publish'])->middleware('throttle:30,1')->name('admin.results.publish');
    });
});
