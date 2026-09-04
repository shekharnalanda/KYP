<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Exam;
use App\Models\LearningSession;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminAssessmentController extends Controller
{
    public function index(): View
    {
        return view('admin.assessments', [
            'courses' => Course::where('is_active', true)->with('sessions')->orderBy('position')->get(),
            'exams' => Exam::with('course')->withCount('questions')->latest()->get(),
            'questions' => Question::with(['course', 'learningSession', 'exams'])->latest()->limit(50)->get(),
        ]);
    }

    public function storeExam(Request $request): RedirectResponse
    {
        $data = $request->validate($this->examRules());
        $data['published_at'] = $data['status'] === 'published' ? now() : null;
        Exam::create($data);

        return back()->with('status', 'Exam created successfully.');
    }

    public function updateExam(Request $request, Exam $exam): RedirectResponse
    {
        $data = $request->validate($this->examRules());
        $data['published_at'] = $data['status'] === 'published' ? ($exam->published_at ?? now()) : null;

        if ($data['status'] === 'published') {
            abort_if($exam->questions()->count() < 1, 422, 'Add at least one question before publishing.');
            $data['total_questions'] = $exam->questions()->count();
            $data['max_marks'] = $exam->questions()->sum('marks');
        }

        $exam->update($data);

        return back()->with('status', 'Exam settings updated.');
    }

    public function storeQuestion(Request $request): RedirectResponse
    {
        $data = $request->validate($this->questionRules());
        $this->validateRelationships($data);
        $this->saveQuestion(new Question(), $data);

        return back()->with('status', 'Bilingual question added.');
    }

    public function editQuestion(Question $question): View
    {
        $question->load(['options', 'exams']);
        return view('admin.question-edit', [
            'question' => $question,
            'courses' => Course::where('is_active', true)->with('sessions')->orderBy('position')->get(),
            'exams' => Exam::with('course')->latest()->get(),
        ]);
    }

    public function updateQuestion(Request $request, Question $question): RedirectResponse
    {
        $data = $request->validate($this->questionRules());
        $this->validateRelationships($data);
        $this->saveQuestion($question, $data);

        return redirect()->route('admin.assessments')->with('status', 'Question updated successfully.');
    }

    private function saveQuestion(Question $question, array $data): void
    {
        DB::transaction(function () use ($question, $data): void {
            $question->fill([
                'course_id' => $data['course_id'],
                'learning_session_id' => $data['learning_session_id'],
                'text_hi' => $data['text_hi'],
                'text_en' => $data['text_en'],
                'type' => 'single_choice',
                'marks' => $data['marks'],
                'negative_marks' => $data['negative_marks'],
                'explanation_hi' => $data['explanation_hi'] ?? null,
                'explanation_en' => $data['explanation_en'] ?? null,
                'difficulty' => $data['difficulty'],
                'status' => $data['status'],
            ])->save();

            foreach (['A', 'B', 'C', 'D'] as $key) {
                $question->options()->updateOrCreate(
                    ['option_key' => $key],
                    [
                        'text_hi' => $data['options_hi'][$key],
                        'text_en' => $data['options_en'][$key],
                        'is_correct' => $data['correct_option'] === $key,
                    ]
                );
            }

            $question->exams()->sync(isset($data['exam_id']) ? [$data['exam_id']] : []);
            if (isset($data['exam_id'])) {
                $exam = Exam::findOrFail($data['exam_id']);
                $exam->update([
                    'total_questions' => $exam->questions()->count(),
                    'max_marks' => $exam->questions()->sum('marks'),
                ]);
            }
        });
    }

    private function validateRelationships(array $data): void
    {
        $sessionValid = LearningSession::whereKey($data['learning_session_id'])->where('course_id', $data['course_id'])->exists();
        abort_unless($sessionValid, 422, 'Selected session does not belong to the course.');

        if (isset($data['exam_id'])) {
            abort_unless(Exam::whereKey($data['exam_id'])->where('course_id', $data['course_id'])->exists(), 422, 'Selected exam does not belong to the course.');
        }
    }

    private function examRules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'title_hi' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:240'],
            'total_questions' => ['required', 'integer', 'min:0', 'max:500'],
            'max_marks' => ['required', 'numeric', 'min:1', 'max:500'],
            'status' => ['required', Rule::in(['draft', 'published', 'closed'])],
        ];
    }

    private function questionRules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'learning_session_id' => ['required', 'exists:learning_sessions,id'],
            'exam_id' => ['nullable', 'exists:exams,id'],
            'text_hi' => ['required', 'string', 'max:2000'],
            'text_en' => ['required', 'string', 'max:2000'],
            'options_hi' => ['required', 'array'],
            'options_en' => ['required', 'array'],
            'options_hi.A' => ['required', 'string', 'max:1000'],
            'options_hi.B' => ['required', 'string', 'max:1000'],
            'options_hi.C' => ['required', 'string', 'max:1000'],
            'options_hi.D' => ['required', 'string', 'max:1000'],
            'options_en.A' => ['required', 'string', 'max:1000'],
            'options_en.B' => ['required', 'string', 'max:1000'],
            'options_en.C' => ['required', 'string', 'max:1000'],
            'options_en.D' => ['required', 'string', 'max:1000'],
            'correct_option' => ['required', Rule::in(['A', 'B', 'C', 'D'])],
            'marks' => ['required', 'numeric', 'min:0.25', 'max:200'],
            'negative_marks' => ['required', 'numeric', 'min:0', 'max:50'],
            'difficulty' => ['required', Rule::in(['foundation', 'intermediate', 'advanced'])],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'explanation_hi' => ['nullable', 'string', 'max:3000'],
            'explanation_en' => ['nullable', 'string', 'max:3000'],
        ];
    }
}
