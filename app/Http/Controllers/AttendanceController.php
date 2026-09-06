<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\LearningSession;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $courses = Course::where('is_active', true)->orderBy('position')->with('sessions')->get();
        $students = User::query()
            ->where('role', 'student')
            ->where('status', 'active')
            ->with(['enrollments' => fn ($query) => $query->where('status', 'active')])
            ->orderBy('name')
            ->get(['id', 'name', 'student_id']);
        $query = $this->reportQuery($request);

        $records = (clone $query)->paginate(50)->withQueryString();

        $summaryQuery = $this->reportQuery($request);

        $summary = [
            'records' => (clone $summaryQuery)->count(),
            'completed' => (clone $summaryQuery)->where('status', 'completed')->count(),
            'centre_iris' => (clone $summaryQuery)->where('source', 'centre_iris')->count(),
            'online_lab' => (clone $summaryQuery)->where('mode', 'online_lab')->count(),
            'classroom' => (clone $summaryQuery)->where('mode', 'classroom')->count(),
            'auto_checkout' => (clone $summaryQuery)->where('checkout_source', 'system_auto')->count(),
            'minutes' => (int) (clone $summaryQuery)->sum('minutes_completed'),
        ];

        return view('attendance.index', compact(
            'courses',
            'students',
            'records',
            'summary'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'student')->where('status', 'active'))],
            'course_id' => ['required', 'exists:courses,id'],
            'learning_session_id' => ['required', 'exists:learning_sessions,id'],
            'attendance_date' => ['required', 'date', 'before_or_equal:today'],
            'mode' => ['required', Rule::in(['classroom', 'lab'])],
            'status' => ['required', Rule::in(['present', 'completed', 'absent'])],
            'minutes_completed' => ['required', 'integer', 'min:0', 'max:120'],
            'biometric_reference' => ['nullable', 'string', 'max:255'],
        ]);

        $this->validateSessionCourse((int) $validated['learning_session_id'], (int) $validated['course_id']);

        AttendanceRecord::updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'learning_session_id' => $validated['learning_session_id'],
                'mode' => $validated['mode'],
            ],
            $validated + ['recorded_by' => $request->user()->id]
        );

        return back()->with('status', 'Attendance सुरक्षित कर दी गई है।');
    }

    public function bulkStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'learning_session_id' => ['required', 'exists:learning_sessions,id'],
            'attendance_date' => ['required', 'date', 'before_or_equal:today'],
            'mode' => ['required', Rule::in(['classroom', 'lab'])],
            'records' => ['required', 'array', 'min:1', 'max:500'],
            'records.*.user_id' => ['required', 'integer', 'distinct', 'exists:users,id'],
            'records.*.status' => ['required', Rule::in(['present', 'completed', 'absent'])],
            'records.*.minutes_completed' => ['required', 'integer', 'min:0', 'max:120'],
        ]);

        $courseId = (int) $validated['course_id'];
        $this->validateSessionCourse((int) $validated['learning_session_id'], $courseId);

        $userIds = collect($validated['records'])->pluck('user_id')->map(fn ($id) => (int) $id);
        $validUserIds = User::query()
            ->whereIn('id', $userIds)
            ->where('role', 'student')
            ->where('status', 'active')
            ->whereHas('enrollments', fn ($query) => $query->where('course_id', $courseId)->where('status', 'active'))
            ->pluck('id');

        abort_unless($validUserIds->count() === $userIds->unique()->count(), 422, 'Only active enrolled students can be marked.');

        DB::transaction(function () use ($validated, $request): void {
            foreach ($validated['records'] as $record) {
                AttendanceRecord::updateOrCreate(
                    [
                        'user_id' => $record['user_id'],
                        'learning_session_id' => $validated['learning_session_id'],
                        'mode' => $validated['mode'],
                    ],
                    [
                        'course_id' => $validated['course_id'],
                        'attendance_date' => $validated['attendance_date'],
                        'status' => $record['status'],
                        'minutes_completed' => $record['status'] === 'absent' ? 0 : $record['minutes_completed'],
                        'recorded_by' => $request->user()->id,
                    ]
                );
            }
        });

        return back()->with('status', count($validated['records']).' students की bulk attendance सुरक्षित कर दी गई है।');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $records = $this->reportQuery($request)->get();

        $filename = 'KYP-Attendance-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($records): void {
            $out = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel / Hindi compatibility.
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'Date',
                'Student ID',
                'Student Name',
                'Course',
                'Session',
                'Attendance Mode',
                'Source',
                'Mark-In',
                'Mark-Out',
                'Mark-Out Source',
                'Minutes',
                'Status',
            ]);

            foreach ($records as $record) {
                fputcsv($out, [
                    optional($record->attendance_date)->format('d-m-Y'),
                    $record->user?->student_id,
                    $record->user?->name,
                    $record->course?->code.' - '.$record->course?->name,
                    $record->learningSession
                        ? 'Session '.$record->learningSession->session_number.' - '.$record->learningSession->title_en
                        : '',
                    $this->modeLabel($record),
                    $this->sourceLabel($record),
                    optional($record->checked_in_at)->format('d-m-Y h:i A'),
                    optional($record->checked_out_at)->format('d-m-Y h:i A'),
                    $this->checkoutLabel($record),
                    $record->minutes_completed,
                    ucfirst((string) $record->status),
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function printReport(Request $request): View
    {
        $records = $this->reportQuery($request)->get();

        $summary = [
            'records' => $records->count(),
            'completed' => $records->where('status', 'completed')->count(),
            'centre_iris' => $records->where('source', 'centre_iris')->count(),
            'online_lab' => $records->where('mode', 'online_lab')->count(),
            'classroom' => $records->where('mode', 'classroom')->count(),
            'auto_checkout' => $records->where('checkout_source', 'system_auto')->count(),
            'minutes' => (int) $records->sum('minutes_completed'),
        ];

        return view('attendance.print', compact('records', 'summary'));
    }

    private function reportQuery(Request $request)
    {
        $query = AttendanceRecord::query()
            ->with(['user', 'course', 'learningSession', 'recorder'])
            ->orderByDesc('attendance_date')
            ->orderByDesc('id');

        if ($request->filled('student_id')) {
            $query->where('user_id', (int) $request->input('student_id'));
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', (int) $request->input('course_id'));
        }

        if ($request->filled('learning_session_id')) {
            $query->where('learning_session_id', (int) $request->input('learning_session_id'));
        }

        if ($request->filled('mode')) {
            $mode = (string) $request->input('mode');

            if ($mode === 'centre_iris') {
                $query->where('mode', 'lab')->where('source', 'centre_iris');
            } elseif (in_array($mode, ['online_lab', 'classroom'], true)) {
                $query->where('mode', $mode);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        if ($request->filled('checkout_source')) {
            $query->where('checkout_source', (string) $request->input('checkout_source'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('attendance_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('attendance_date', '<=', $request->input('date_to'));
        }

        return $query;
    }

    private function modeLabel(AttendanceRecord $record): string
    {
        return match ($record->mode) {
            'online_lab' => 'Online Lab',
            'classroom' => 'Classroom',
            'lab' => 'Centre Iris Lab',
            default => ucfirst((string) $record->mode),
        };
    }

    private function sourceLabel(AttendanceRecord $record): string
    {
        return match ($record->source) {
            'centre_iris' => 'MIS100V2 Iris',
            'online_portal' => 'Online Portal',
            default => $record->recorded_by ? 'Manual Staff Entry' : 'System',
        };
    }

    private function checkoutLabel(AttendanceRecord $record): string
    {
        return match ($record->checkout_source) {
            'iris' => 'Iris Mark-Out',
            'system_auto' => 'System Auto Mark-Out',
            'courseware_completion' => 'Online Completion',
            default => '-',
        };
    }

    private function validateSessionCourse(int $sessionId, int $courseId): void
    {
        $valid = LearningSession::whereKey($sessionId)->where('course_id', $courseId)->exists();
        abort_unless($valid, 422, 'Selected session does not belong to the course.');
    }
}
