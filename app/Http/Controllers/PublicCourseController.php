<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PublicCourseController extends Controller
{
    public function index(): View
    {
        $modules = $this->modules();

        return view('home', [
            'modules' => $modules,
            'totalSessions' => $modules->sum(fn (array $module) => $module['course']->total_sessions),
            'totalHours' => $modules->sum(fn (array $module) => $module['course']->total_hours),
        ]);
    }

    public function show(string $slug): View
    {
        $modules = $this->modules();
        $module = $modules->firstWhere('slug', $slug);
        abort_unless($module, 404);
        $course = $module['course'];
        $sessions = $course->sessions()
            ->where('content_status', 'published')
            ->whereNotNull('published_at')->where('published_at', '<=', now())
            ->get(['session_number', 'title_hi', 'duration_minutes']);

        return view('public.courses.show', compact('module', 'modules', 'course', 'sessions'));
    }

    private function modules(): Collection
    {
        $definitions = config('kyp-public.modules');

        return Course::query()->where('is_active', true)
            ->whereIn('code', array_keys($definitions))->orderBy('position')->get()
            ->map(fn (Course $course): array => $definitions[$course->code] + ['course' => $course]);
    }
}
