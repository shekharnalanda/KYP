<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentIdCardController extends Controller
{
    private function loadStudent(User $student): User
    {
        $student->load([
            'enrollments' => fn ($q) => $q
                ->with('course')
                ->where('status', 'active')
        ]);

        $student->setRelation(
            'approvedAdmission',
            Admission::with('branch')
                ->where('user_id', $student->id)
                ->where('status', 'approved')
                ->latest('approved_at')
                ->first()
        );

        return $student;
    }

    public function show(Request $request): View
    {
        $student = $request->user();

        abort_unless($student->hasRole('student'), 403);
        abort_unless($student->id_card_token, 404);

        $student = $this->loadStudent($student);

        return view('student.id-card', compact('student'));
    }

    public function adminShow(User $student): View
    {
        abort_unless($student->hasRole('student'), 404);
        abort_unless($student->id_card_token, 404);

        $students = collect([$this->loadStudent($student)]);

        return view('id-card.print-sheet', [
            'students' => $students,
            'adminMode' => true,
        ]);
    }

    public function adminBulk(Request $request): View
    {
        $data = $request->validate([
            'student_ids' => ['required','array','min:1','max:100'],
            'student_ids.*' => ['integer','distinct','exists:users,id'],
        ]);

        $students = User::query()
            ->where('role', 'student')
            ->whereIn('id', $data['student_ids'])
            ->orderBy('name')
            ->get()
            ->map(fn (User $student) => $this->loadStudent($student));

        abort_if($students->isEmpty(), 404);

        return view('id-card.print-sheet', [
            'students' => $students,
            'adminMode' => true,
        ]);
    }

    public function verify(string $token): View
    {
        $student = User::query()
            ->where('role', 'student')
            ->where('id_card_token', $token)
            ->with([
                'enrollments' => fn ($q) => $q
                    ->with('course')
                    ->where('status', 'active')
            ])
            ->first();

        return view('id-card.verify', [
            'student' => $student,
            'valid' => (bool) ($student && $student->status === 'active'),
        ]);
    }
}
