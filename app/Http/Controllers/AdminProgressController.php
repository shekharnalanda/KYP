<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\EligibilityService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProgressController extends Controller
{
    public function index(Request $request, EligibilityService $eligibility): View
    {
        $search = trim((string) $request->query('search'));

        $students = User::query()
            ->where('role', 'student')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('student_id', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $progress = $students->getCollection()->mapWithKeys(
            fn (User $student) => [$student->id => $eligibility->summaryFor($student)]
        );

        return view('admin.progress', compact('students', 'progress', 'search'));
    }
}
