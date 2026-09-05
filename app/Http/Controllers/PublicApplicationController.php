<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Enquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicApplicationController extends Controller
{
    private function catalog(): array
    {
        return [
            'branches' => Branch::where('is_active', true)
                ->orderBy('position')->orderBy('name')->get(),

            'courses' => Course::where('is_active', true)
                ->orderBy('position')->get(),
        ];
    }

    public function admissionForm(): View
    {
        return view('public.admission.form', $this->catalog());
    }

    public function admissionStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'branch_id' => ['required','exists:branches,id'],
            'course_id' => ['required','exists:courses,id'],

            'name' => ['required','string','max:150'],
            'date_of_birth' => ['required','date','before:today'],
            'gender' => ['required','in:Male,Female,Other'],

            'mobile' => ['required','string','max:20'],
            'email' => ['nullable','email','max:255'],

            'father_name' => ['nullable','string','max:150'],
            'mother_name' => ['nullable','string','max:150'],
            'guardian_name' => ['nullable','string','max:150'],
            'guardian_mobile' => ['nullable','string','max:20'],

            'qualification' => ['required','string','max:120'],

            'address' => ['required','string','max:1000'],
            'city' => ['nullable','string','max:100'],
            'district' => ['required','string','max:100'],
            'state' => ['required','string','max:100'],
            'pin' => ['required','string','max:10'],

            'identity_type' => ['nullable','in:Aadhaar,Voter ID,PAN,Other'],
            'identity_number' => ['nullable','string','max:100'],

            'photo' => ['required','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'remarks' => ['nullable','string','max:1000'],
            'consent' => ['accepted'],
        ]);

        $branch = Branch::whereKey($data['branch_id'])
            ->where('is_active', true)->firstOrFail();

        Course::whereKey($data['course_id'])
            ->where('is_active', true)->firstOrFail();

        $path = $request->file('photo')
            ->store('admissions/photos', 'public');

        $data['application_number'] =
            'KYP-ADM-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));

        $data['photo_path'] = $path;
        $data['consent'] = true;
        $data['status'] = 'submitted';

        unset($data['photo']);

        $admission = Admission::create($data);

        return redirect()
            ->route('admission.success', $admission->application_number);
    }

    public function admissionSuccess(string $number): View
    {
        $admission = Admission::where('application_number', $number)
            ->with(['branch','course'])->firstOrFail();

        return view('public.admission.success', compact('admission'));
    }

    public function enquiryForm(): View
    {
        return view('public.enquiry.form', $this->catalog());
    }

    public function enquiryStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'branch_id' => ['required','exists:branches,id'],
            'course_id' => ['nullable','exists:courses,id'],
            'name' => ['required','string','max:150'],
            'mobile' => ['required','string','max:20'],
            'email' => ['nullable','email','max:255'],
            'qualification' => ['nullable','string','max:120'],
            'message' => ['nullable','string','max:1500'],
        ]);

        Branch::whereKey($data['branch_id'])
            ->where('is_active', true)->firstOrFail();

        $data['enquiry_number'] =
            'KYP-ENQ-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));

        $data['status'] = 'new';

        $enquiry = Enquiry::create($data);

        return redirect()
            ->route('enquiry.success', $enquiry->enquiry_number);
    }

    public function enquirySuccess(string $number): View
    {
        $enquiry = Enquiry::where('enquiry_number', $number)
            ->with(['branch','course'])->firstOrFail();

        return view('public.enquiry.success', compact('enquiry'));
    }
}
