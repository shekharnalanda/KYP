<?php

namespace App\Http\Controllers;

use App\Models\AttemptAnswer;
use App\Models\Certificate;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Result;
use App\Services\EligibilityService;
use App\Services\InternalAssessmentService;
use App\Services\ResultScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function index(Request $request, EligibilityService $eligibility): View
    {
        $eligibilityByCourse = $eligibility->summaryFor($request->user())->keyBy(fn (array $item) => $item['course']->id);

        $exams = Exam::query()
            ->where('status', 'published')
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->with(['course', 'attempts' => fn ($query) => $query->where('user_id', $request->user()->id)->latest()])
            ->orderByDesc('published_at')
            ->get()
            ->map(function (Exam $exam) use ($eligibilityByCourse): array {
                $courseEligibility = $eligibilityByCourse->get($exam->course_id);
                return [
                    'exam' => $exam,
                    'attempt' => $exam->attempts->first(),
                    'eligible' => (bool) ($courseEligibility['eligible'] ?? false),
                    'lab_sessions' => (int) ($courseEligibility['lab_sessions'] ?? 0),
                    'required_sessions' => $courseEligibility['required_sessions'] ?? null,
                ];
            });

        return view('student.exams', compact('exams'));
    }

    public function start(Request $request, Exam $exam, EligibilityService $eligibility): RedirectResponse
    {
        abort_unless($exam->status === 'published', 404);
        abort_if($exam->starts_at && $exam->starts_at->isFuture(), 403);
        abort_if($exam->ends_at && $exam->ends_at->isPast(), 403);

        $courseEligibility = $eligibility->summaryFor($request->user())
            ->first(fn (array $item) => $item['course']->id === $exam->course_id);

        abort_unless((bool) ($courseEligibility['eligible'] ?? false), 403, 'Required lab sessions are not complete.');

        $attempt = ExamAttempt::query()
            ->where('exam_id', $exam->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'in_progress')
            ->first();

        if (! $attempt) {
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => $request->user()->id,
                'attempt_number' => ((int) ExamAttempt::where('exam_id', $exam->id)->where('user_id', $request->user()->id)->max('attempt_number')) + 1,
                'status' => 'in_progress',
                'started_at' => now(),
                'max_raw_score' => $exam->max_marks,
            ]);
        }

        return redirect()->route('student.exam.attempt', $attempt);
    }

    public function attempt(Request $request, ExamAttempt $attempt): View
    {
        $this->authorizeAttempt($request, $attempt);
        abort_unless($attempt->status === 'in_progress', 409);

        $attempt->load(['exam.course', 'exam.questions.options']);

        return view('student.exam-attempt', compact('attempt'));
    }

    public function submit(Request $request, ExamAttempt $attempt, ResultScoringService $scoring, InternalAssessmentService $internal): RedirectResponse
    {
        $this->authorizeAttempt($request, $attempt);
        abort_unless($attempt->status === 'in_progress', 409);

        $attempt->load(['user', 'exam.course', 'exam.questions.options']);
        $validated = $request->validate(['answers' => ['nullable', 'array'], 'answers.*' => ['nullable', 'integer']]);
        $submitted = collect($validated['answers'] ?? []);

        $result = DB::transaction(function () use ($attempt, $submitted, $scoring, $internal): Result {
            $earnedExamScore = 0.0;

            foreach ($attempt->exam->questions as $question) {
                $optionId = $submitted->get((string) $question->id);
                $selected = $question->options->firstWhere('id', (int) $optionId);
                $correct = $selected?->is_correct === true;
                $awarded = $correct ? (float) $question->marks : -1 * (float) $question->negative_marks;
                $earnedExamScore += $awarded;

                AttemptAnswer::updateOrCreate(
                    ['exam_attempt_id' => $attempt->id, 'question_id' => $question->id],
                    ['selected_option_id' => $selected?->id, 'is_correct' => $selected ? $correct : null, 'marks_awarded' => $awarded, 'answered_at' => now()]
                );
            }

            $examMaximum = max(1, (float) $attempt->exam->max_marks);
            $earnedExamScore = max(0, min($earnedExamScore, $examMaximum));
            $examRaw = round(($earnedExamScore / $examMaximum) * (float) config('kyp.scoring.exam.raw_max'), 2);
            $internalRaw = $internal->rawScores($attempt->user, $attempt->exam->course);
            $scores = $scoring->calculate($examRaw, $internalRaw['lab_raw'], $internalRaw['classroom_raw']);
            $status = $scores['final_score'] >= (float) config('kyp.scoring.pass_mark') ? 'pass' : 'fail';

            $attempt->update(['status' => 'submitted', 'submitted_at' => now(), 'raw_exam_score' => $earnedExamScore]);

            $result = Result::updateOrCreate(
                ['exam_attempt_id' => $attempt->id],
                $scores + ['user_id' => $attempt->user_id, 'result_status' => $status, 'published_at' => now()]
            );

            if ($status === 'pass') {
                Certificate::firstOrCreate(
                    ['result_id' => $result->id],
                    [
                        'user_id' => $result->user_id,
                        'serial_number' => 'KYP-'.now()->format('Y').'-'.str_pad((string) $result->id, 7, '0', STR_PAD_LEFT),
                        'qr_token' => (string) Str::uuid(),
                        'issued_at' => now(),
                        'status' => 'issued',
                    ]
                );
            }

            return $result;
        });

        return redirect()->route('student.exam.result', $result);
    }

    public function result(Request $request, Result $result): View
    {
        abort_unless($result->user_id === $request->user()->id, 403);
        $result->load('examAttempt.exam.course');

        return view('student.exam-result', compact('result'));
    }

    private function authorizeAttempt(Request $request, ExamAttempt $attempt): void
    {
        abort_unless($attempt->user_id === $request->user()->id, 403);
    }
}
