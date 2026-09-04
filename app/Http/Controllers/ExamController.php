<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Services\EligibilityService;
use Illuminate\Http\Request;
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
            ->with('course')
            ->orderByDesc('published_at')
            ->get()
            ->map(function (Exam $exam) use ($eligibilityByCourse): array {
                $courseEligibility = $eligibilityByCourse->get($exam->course_id);
                return [
                    'exam' => $exam,
                    'eligible' => (bool) ($courseEligibility['eligible'] ?? false),
                    'lab_sessions' => (int) ($courseEligibility['lab_sessions'] ?? 0),
                    'required_sessions' => $courseEligibility['required_sessions'] ?? null,
                ];
            });

        return view('student.exams', compact('exams'));
    }
}
