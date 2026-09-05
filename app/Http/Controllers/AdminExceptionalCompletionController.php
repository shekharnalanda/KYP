<?php

namespace App\Http\Controllers;

use App\Models\ActivityRecord;
use App\Models\AdminOverrideLog;
use App\Models\AttendanceRecord;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Result;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminExceptionalCompletionController extends Controller
{
    public function index(): View
    {
        return view('admin.exceptional.index', [
            'students' => User::where('role','student')
                ->where('status','active')
                ->orderBy('name')
                ->get(),

            'courses' => Course::where('is_active',true)
                ->orderBy('position')
                ->get(),

            'logs' => AdminOverrideLog::with([
                    'student','admin','course','exam'
                ])
                ->latest('performed_at')
                ->paginate(30),
        ]);
    }

    public function eligibility(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required','exists:users,id'],
            'course_id' => ['required','exists:courses,id'],
            'reason' => ['required','string','min:10','max:1500'],
        ]);

        $student = User::whereKey($data['student_id'])
            ->where('role','student')
            ->where('status','active')
            ->firstOrFail();

        $course = Course::with('sessions')
            ->whereKey($data['course_id'])
            ->where('is_active',true)
            ->firstOrFail();

        DB::transaction(function () use (
            $request,$student,$course,$data
        ) {
            Enrollment::updateOrCreate(
                [
                    'user_id'=>$student->id,
                    'course_id'=>$course->id,
                ],
                [
                    'status'=>'active',
                    'enrolled_at'=>now(),
                    'completed_at'=>null,
                ]
            );

            $activityCount = 0;
            $attendanceCount = 0;

            foreach ($course->sessions as $session) {

                ActivityRecord::updateOrCreate(
                    [
                        'user_id'=>$student->id,
                        'learning_session_id'=>$session->id,
                    ],
                    [
                        'status'=>'completed',
                        'score'=>100,
                        'started_at'=>now(),
                        'completed_at'=>now(),
                        'metadata'=>[
                            'source'=>'admin_exceptional_completion',
                            'admin_id'=>$request->user()->id,
                            'reason'=>$data['reason'],
                        ],
                    ]
                );

                $activityCount++;

                foreach (['lab','classroom'] as $mode) {
                    AttendanceRecord::updateOrCreate(
                        [
                            'user_id'=>$student->id,
                            'learning_session_id'=>$session->id,
                            'mode'=>$mode,
                        ],
                        [
                            'course_id'=>$course->id,
                            'recorded_by'=>$request->user()->id,
                            'attendance_date'=>today(),
                            'status'=>'completed',
                            'biometric_reference'=>'ADMIN-EXCEPTION',
                            'minutes_completed'=>$session->duration_minutes,
                        ]
                    );

                    $attendanceCount++;
                }
            }

            AdminOverrideLog::create([
                'student_id'=>$student->id,
                'admin_id'=>$request->user()->id,
                'course_id'=>$course->id,
                'exam_id'=>null,
                'action'=>'complete_eligibility',
                'reason'=>$data['reason'],
                'details'=>[
                    'activity_records'=>$activityCount,
                    'attendance_records'=>$attendanceCount,
                    'sessions'=>$course->sessions->count(),
                    'source'=>'admin_exceptional_completion',
                ],
                'performed_at'=>now(),
            ]);
        });

        return back()->with(
            'status',
            'Exceptional eligibility completion recorded with audit trail.'
        );
    }

    public function pass(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required','exists:users,id'],
            'course_id' => ['required','exists:courses,id'],

            'exam_score' => ['required','numeric','min:0','max:40'],
            'lab_score' => ['required','numeric','min:0','max:40'],
            'classroom_score' => ['required','numeric','min:0','max:20'],

            'reason' => ['required','string','min:10','max:1500'],
        ]);

        $student = User::whereKey($data['student_id'])
            ->where('role','student')
            ->where('status','active')
            ->firstOrFail();

        $course = Course::whereKey($data['course_id'])
            ->where('is_active',true)
            ->firstOrFail();

        $exam = Exam::where('course_id',$course->id)
            ->where('status','published')
            ->firstOrFail();

        $finalScore =
            (float)$data['exam_score'] +
            (float)$data['lab_score'] +
            (float)$data['classroom_score'];

        abort_if(
            $finalScore < 40,
            422,
            'Administrative pass requires minimum final score 40/100.'
        );

        $result = DB::transaction(function () use (
            $request,$student,$course,$exam,$data,$finalScore
        ) {
            Enrollment::updateOrCreate(
                [
                    'user_id'=>$student->id,
                    'course_id'=>$course->id,
                ],
                [
                    'status'=>'active',
                    'enrolled_at'=>now(),
                ]
            );

            $attemptNumber =
                (int) ExamAttempt::where('exam_id',$exam->id)
                    ->where('user_id',$student->id)
                    ->max('attempt_number') + 1;

            $examRaw = round(
                ((float)$data['exam_score'] / 40) *
                (float)$exam->max_marks,
                2
            );

            $labRaw = round((float)$data['lab_score'] * 5, 2);
            $classroomRaw =
                round((float)$data['classroom_score'] * 5, 2);

            $attempt = ExamAttempt::create([
                'exam_id'=>$exam->id,
                'user_id'=>$student->id,
                'attempt_number'=>$attemptNumber,
                'status'=>'submitted',
                'started_at'=>now(),
                'submitted_at'=>now(),
                'raw_exam_score'=>$examRaw,
                'max_raw_score'=>$exam->max_marks,
            ]);

            $result = Result::create([
                'user_id'=>$student->id,
                'exam_attempt_id'=>$attempt->id,

                'exam_raw'=>$examRaw,
                'lab_raw'=>$labRaw,
                'classroom_raw'=>$classroomRaw,

                'total_raw'=>$examRaw+$labRaw+$classroomRaw,

                'exam_final'=>$data['exam_score'],
                'lab_final'=>$data['lab_score'],
                'classroom_final'=>$data['classroom_score'],

                'final_score'=>$finalScore,
                'result_status'=>'pass',
                'published_at'=>now(),
            ]);

            $certificate = Certificate::create([
                'user_id'=>$student->id,
                'result_id'=>$result->id,
                'serial_number'=>
                    'KYP-'.now()->format('Y').'-'.
                    str_pad(
                        (string)$result->id,
                        7,
                        '0',
                        STR_PAD_LEFT
                    ),
                'qr_token'=>(string)Str::uuid(),
                'issued_at'=>now(),
                'status'=>'issued',
            ]);

            AdminOverrideLog::create([
                'student_id'=>$student->id,
                'admin_id'=>$request->user()->id,
                'course_id'=>$course->id,
                'exam_id'=>$exam->id,
                'action'=>'administrative_pass_certificate',
                'reason'=>$data['reason'],
                'details'=>[
                    'exam_attempt_id'=>$attempt->id,
                    'result_id'=>$result->id,
                    'certificate_id'=>$certificate->id,
                    'exam_score'=>(float)$data['exam_score'],
                    'lab_score'=>(float)$data['lab_score'],
                    'classroom_score'=>(float)$data['classroom_score'],
                    'final_score'=>$finalScore,
                    'source'=>'admin_exceptional_completion',
                ],
                'performed_at'=>now(),
            ]);

            return $result;
        });

        return redirect()
            ->route('admin.result.marksheet',$result)
            ->with(
                'status',
                'Administrative result and certificate created with audit trail.'
            );
    }
}
