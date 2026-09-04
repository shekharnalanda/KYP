<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        return view('admin.users', [
            'users' => User::query()
                ->whereIn('role', ['student', 'teacher'])
                ->with(['enrollments.course'])
                ->latest()
                ->paginate(20),
            'courses' => Course::query()->where('is_active', true)->orderBy('position')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'student_id' => ['nullable', 'required_if:role,student', 'string', 'max:50', 'unique:users,student_id'],
            'role' => ['required', Rule::in(['student', 'teacher'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'course_ids' => ['nullable', 'array'],
            'course_ids.*' => ['integer', 'exists:courses,id'],
        ]);

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'name' => $validated['name'],
                'email' => Str::lower($validated['email']),
                'phone' => $validated['phone'] ?? null,
                'student_id' => isset($validated['student_id']) ? Str::upper($validated['student_id']) : null,
                'role' => $validated['role'],
                'status' => 'active',
                'password' => $validated['password'],
            ]);

            if ($user->role === 'student') {
                foreach ($validated['course_ids'] ?? [] as $courseId) {
                    Enrollment::updateOrCreate(
                        ['user_id' => $user->id, 'course_id' => $courseId],
                        ['status' => 'active', 'enrolled_at' => now(), 'completed_at' => null]
                    );
                }
            }
        });

        return back()->with('success', 'Account successfully created.');
    }

    public function status(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->hasRole('student', 'teacher'), 404);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $user->update(['status' => $validated['status']]);

        return back()->with('success', 'Account status updated.');
    }

    public function enrollments(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->hasRole('student'), 404);

        $validated = $request->validate([
            'course_ids' => ['nullable', 'array'],
            'course_ids.*' => ['integer', 'exists:courses,id'],
        ]);
        $selected = collect($validated['course_ids'] ?? [])->map(fn ($id) => (int) $id);

        DB::transaction(function () use ($user, $selected): void {
            $user->enrollments()
                ->when($selected->isNotEmpty(), fn ($query) => $query->whereNotIn('course_id', $selected))
                ->update(['status' => 'inactive']);

            foreach ($selected as $courseId) {
                Enrollment::updateOrCreate(
                    ['user_id' => $user->id, 'course_id' => $courseId],
                    ['status' => 'active', 'enrolled_at' => now(), 'completed_at' => null]
                );
            }
        });

        return back()->with('success', 'Student enrolments updated.');
    }
}
