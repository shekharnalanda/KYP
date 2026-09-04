<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Result;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(): View
    {
        $results = Result::with(['user', 'examAttempt.exam.course', 'certificate'])->latest()->paginate(30);

        return view('admin.results', compact('results'));
    }

    public function publish(Request $request, Result $result): RedirectResponse
    {
        $validated = $request->validate([
            'result_status' => ['required', Rule::in(['pass', 'fail'])],
        ]);

        $result->update([
            'result_status' => $validated['result_status'],
            'published_at' => now(),
        ]);

        if ($validated['result_status'] === 'pass') {
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

        return back()->with('status', 'Result publish कर दिया गया है।');
    }

    public function marksheet(Request $request, Result $result): View
    {
        abort_unless($result->user_id === $request->user()->id && $result->published_at, 403);
        $result->load(['user', 'examAttempt.exam.course', 'certificate']);

        return view('student.marksheet', compact('result'));
    }

    public function certificate(Request $request, Certificate $certificate): View
    {
        abort_unless($certificate->user_id === $request->user()->id && $certificate->status === 'issued', 403);
        $certificate->load(['user', 'result.examAttempt.exam.course']);

        return view('student.certificate', compact('certificate'));
    }
}
