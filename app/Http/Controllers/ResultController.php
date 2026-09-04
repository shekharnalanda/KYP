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

    public function bulkPrint(Request $request): View
    {
        $validated = $request->validate([
            'result_ids' => ['required', 'array', 'min:1', 'max:100'],
            'result_ids.*' => ['integer', 'exists:results,id'],
            'document_type' => ['required', Rule::in(['marksheet', 'certificate'])],
        ]);

        $results = Result::query()
            ->whereIn('id', $validated['result_ids'])
            ->whereNotNull('published_at')
            ->with(['user', 'examAttempt.exam.course', 'certificate'])
            ->orderBy('id')
            ->get();

        abort_if($results->isEmpty(), 422, 'No published result selected.');

        if ($validated['document_type'] === 'certificate') {
            $results = $results->filter(fn (Result $result) => $result->certificate?->status === 'issued')->values();
            abort_if($results->isEmpty(), 422, 'No issued certificate selected.');
        }

        return view('admin.bulk-documents', [
            'results' => $results,
            'documentType' => $validated['document_type'],
        ]);
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

    public function verify(string $token): View
    {
        $certificate = Certificate::query()
            ->where('qr_token', $token)
            ->with(['user', 'result.examAttempt.exam.course'])
            ->first();

        return view('certificate.verify', [
            'certificate' => $certificate,
            'valid' => $certificate?->status === 'issued',
        ]);
    }
}
