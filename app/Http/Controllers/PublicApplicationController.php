<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Enquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PublicApplicationController extends Controller
{
    private const OTP_TTL_MINUTES = 10;
    private const OTP_MAX_ATTEMPTS = 5;
    private const ADMIN_EMAIL = 'mcibiharsharif@gmail.com';

    private function catalog(): array
    {
        return [
            'branches' => Branch::where('is_active', true)
                ->orderBy('position')->orderBy('name')->get(),

            'courses' => Course::where('is_active', true)
                ->orderBy('position')->get(),
        ];
    }

    private function otpKey(string $purpose): string
    {
        return 'kyp_email_otp_'.$purpose;
    }

    private function normalizeEmail(string $email): string
    {
        return Str::lower(trim($email));
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required','email:rfc','max:255'],
            'purpose' => ['required','in:admission,enquiry'],
        ]);

        $email = $this->normalizeEmail($data['email']);
        $otp = (string) random_int(100000, 999999);

        $request->session()->put(
            $this->otpKey($data['purpose']),
            [
                'email' => $email,
                'hash' => Hash::make($otp),
                'expires_at' => now()
                    ->addMinutes(self::OTP_TTL_MINUTES)
                    ->timestamp,
                'attempts' => 0,
                'verified' => false,
            ]
        );

        try {
            Mail::raw(
                "Your Kushal Youth Program verification OTP is: {$otp}\n\n".
                "This OTP is valid for ".self::OTP_TTL_MINUTES." minutes.\n".
                "Do not share this OTP or your email password with anyone.\n\n".
                "Kushal Youth Program",
                function ($message) use ($email) {
                    $message->to($email)
                        ->subject('KYP Email Verification OTP');
                }
            );
        } catch (\Throwable $e) {
            $request->session()->forget(
                $this->otpKey($data['purpose'])
            );

            report($e);

            return response()->json([
                'message' => 'OTP email भेजा नहीं जा सका। कृपया कुछ समय बाद पुनः प्रयास करें।'
            ], 503);
        }

        return response()->json([
            'message' => 'OTP आपके email पर भेज दिया गया है।',
            'expires_in_minutes' => self::OTP_TTL_MINUTES,
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required','email:rfc','max:255'],
            'purpose' => ['required','in:admission,enquiry'],
            'otp' => ['required','digits:6'],
        ]);

        $key = $this->otpKey($data['purpose']);
        $record = $request->session()->get($key);
        $email = $this->normalizeEmail($data['email']);

        if (!is_array($record) ||
            ($record['email'] ?? null) !== $email) {
            return response()->json([
                'message' => 'पहले इसी email के लिए नया OTP भेजें।'
            ], 422);
        }

        if (($record['expires_at'] ?? 0) < now()->timestamp) {
            $request->session()->forget($key);

            return response()->json([
                'message' => 'OTP expire हो चुका है। नया OTP भेजें।'
            ], 422);
        }

        $attempts = (int)($record['attempts'] ?? 0);

        if ($attempts >= self::OTP_MAX_ATTEMPTS) {
            $request->session()->forget($key);

            return response()->json([
                'message' => 'OTP attempts समाप्त हो गए। नया OTP भेजें।'
            ], 429);
        }

        if (!Hash::check($data['otp'], $record['hash'])) {
            $record['attempts'] = $attempts + 1;
            $request->session()->put($key, $record);

            return response()->json([
                'message' => 'OTP सही नहीं है।'
            ], 422);
        }

        $record['verified'] = true;
        $record['verified_at'] = now()->timestamp;
        $request->session()->put($key, $record);

        return response()->json([
            'message' => 'Email successfully verified.',
        ]);
    }

    private function requireVerifiedEmail(
        Request $request,
        string $purpose,
        string $email
    ): void {
        $record = $request->session()->get(
            $this->otpKey($purpose)
        );

        $email = $this->normalizeEmail($email);

        $valid =
            is_array($record) &&
            ($record['email'] ?? null) === $email &&
            ($record['verified'] ?? false) === true &&
            ($record['expires_at'] ?? 0) >= now()->timestamp;

        if (!$valid) {
            throw ValidationException::withMessages([
                'email' => 'Email OTP verification आवश्यक है।',
            ]);
        }
    }

    private function clearOtp(
        Request $request,
        string $purpose
    ): void {
        $request->session()->forget(
            $this->otpKey($purpose)
        );
    }

    private function sendApplicantAndAdminMail(
        string $type,
        string $name,
        string $email,
        string $number,
        string $branch,
        ?string $course,
        string $mobile
    ): void {
        try {
            Mail::raw(
                "Dear {$name},\n\n".
                "Your KYP {$type} has been received successfully.\n\n".
                "Reference Number: {$number}\n".
                "Branch: {$branch}\n".
                ($course ? "Course: {$course}\n" : '').
                "\nPlease keep this reference number for future communication.\n\n".
                "Kushal Youth Program",
                function ($message) use ($email, $type, $number) {
                    $message->to($email)
                        ->subject("KYP {$type} Received — {$number}");
                }
            );

            Mail::raw(
                "New KYP {$type} received.\n\n".
                "Reference: {$number}\n".
                "Applicant: {$name}\n".
                "Email: {$email}\n".
                "Mobile: {$mobile}\n".
                "Branch: {$branch}\n".
                ($course ? "Course: {$course}\n" : ''),
                function ($message) use ($type, $number) {
                    $message->to(self::ADMIN_EMAIL)
                        ->subject("New KYP {$type} — {$number}");
                }
            );
        } catch (\Throwable $e) {
            // Submission must remain saved even if a later
            // acknowledgement email temporarily fails.
            report($e);
        }
    }

    public function admissionForm(): View
    {
        return view(
            'public.admission.form',
            $this->catalog()
        );
    }

    public function admissionStore(
        Request $request
    ): RedirectResponse {
        $data = $request->validate([
            'branch_id' => ['required','exists:branches,id'],
            'name' => ['required','string','max:150'],
            'date_of_birth' => ['required','date','before:today'],
            'gender' => ['required','in:Male,Female,Other'],

            'mobile' => ['required','string','max:20'],
            'email' => ['required','email:rfc','max:255'],

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

            'identity_type' => [
                'nullable',
                'in:Aadhaar,Voter ID,PAN,Other'
            ],
            'identity_number' => [
                'nullable','string','max:100'
            ],

            'photo' => [
                'required','image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],

            'remarks' => ['nullable','string','max:1000'],
            'consent' => ['accepted'],
        ]);

        $data['email'] =
            $this->normalizeEmail($data['email']);

        $this->requireVerifiedEmail(
            $request,
            'admission',
            $data['email']
        );

        $branch = Branch::whereKey($data['branch_id'])
            ->where('is_active', true)
            ->firstOrFail();

        // Public admission is for the complete Kushal Youth Program.
        // CIT remains the compatibility anchor because admissions.course_id
        // is currently NOT NULL. It is not presented as a separate program.
        $course = Course::where('code', 'CIT')
            ->where('is_active', true)
            ->firstOrFail();

        $data['course_id'] = $course->id;

        $path = $request->file('photo')
            ->store('admissions/photos', 'public');

        $data['application_number'] =
            'KYP-ADM-'.now()->format('Ymd').'-'.
            strtoupper(Str::random(6));

        $data['photo_path'] = $path;
        $data['consent'] = true;
        $data['status'] = 'submitted';

        unset($data['photo']);

        $admission = Admission::create($data);

        $this->clearOtp($request, 'admission');

        $this->sendApplicantAndAdminMail(
            'Admission Application',
            $admission->name,
            $admission->email,
            $admission->application_number,
            $branch->name,
            'Kushal Youth Program (KYP) — 270 Hours',
            $admission->mobile
        );

        return redirect()->route(
            'admission.success',
            $admission->application_number
        );
    }

    public function admissionSuccess(
        string $number
    ): View {
        $admission = Admission::where(
            'application_number',
            $number
        )->with(['branch','course'])->firstOrFail();

        return view(
            'public.admission.success',
            compact('admission')
        );
    }

    public function enquiryForm(): View
    {
        return view(
            'public.enquiry.form',
            $this->catalog()
        );
    }

    public function enquiryStore(
        Request $request
    ): RedirectResponse {
        $data = $request->validate([
            'branch_id' => ['required','exists:branches,id'],
            'name' => ['required','string','max:150'],
            'mobile' => ['required','string','max:20'],
            'email' => ['required','email:rfc','max:255'],
            'qualification' => ['nullable','string','max:120'],
            'message' => ['nullable','string','max:1500'],
        ]);

        $data['email'] =
            $this->normalizeEmail($data['email']);

        $this->requireVerifiedEmail(
            $request,
            'enquiry',
            $data['email']
        );

        $branch = Branch::whereKey($data['branch_id'])
            ->where('is_active', true)
            ->firstOrFail();

        // Enquiry is for the complete KYP program, not an individual module.
        $course = null;
        $data['course_id'] = null;

        $data['enquiry_number'] =
            'KYP-ENQ-'.now()->format('Ymd').'-'.
            strtoupper(Str::random(6));

        $data['status'] = 'new';

        $enquiry = Enquiry::create($data);

        $this->clearOtp($request, 'enquiry');

        $this->sendApplicantAndAdminMail(
            'Enquiry',
            $enquiry->name,
            $enquiry->email,
            $enquiry->enquiry_number,
            $branch->name,
            $course
                ? $course->code.' — '.$course->name
                : null,
            $enquiry->mobile
        );

        return redirect()->route(
            'enquiry.success',
            $enquiry->enquiry_number
        );
    }

    public function enquirySuccess(
        string $number
    ): View {
        $enquiry = Enquiry::where(
            'enquiry_number',
            $number
        )->with(['branch','course'])->firstOrFail();

        return view(
            'public.enquiry.success',
            compact('enquiry')
        );
    }
}
