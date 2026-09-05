<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Branch;
use App\Models\Enrollment;
use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminApplicationController extends Controller
{
    public function branches(): View
    {
        return view('admin.branches.index', [
            'branches' => Branch::orderBy('position')->orderBy('name')->get(),
        ]);
    }

    public function storeBranch(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'code' => ['required','string','max:30','unique:branches,code'],
            'address' => ['nullable','string','max:255'],
            'city' => ['nullable','string','max:100'],
            'district' => ['nullable','string','max:100'],
            'state' => ['required','string','max:100'],
            'pin' => ['nullable','string','max:10'],
            'phone' => ['nullable','string','max:20'],
            'email' => ['nullable','email','max:255'],
            'position' => ['nullable','integer','min:0'],
        ]);

        $data['code'] = Str::upper($data['code']);
        $data['is_active'] = true;
        Branch::create($data);

        return back()->with('success', 'Branch created successfully.');
    }

    public function updateBranch(Request $request, Branch $branch): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'code' => ['required','string','max:30',Rule::unique('branches','code')->ignore($branch->id)],
            'address' => ['nullable','string','max:255'],
            'city' => ['nullable','string','max:100'],
            'district' => ['nullable','string','max:100'],
            'state' => ['required','string','max:100'],
            'pin' => ['nullable','string','max:10'],
            'phone' => ['nullable','string','max:20'],
            'email' => ['nullable','email','max:255'],
            'position' => ['nullable','integer','min:0'],
            'is_active' => ['required','boolean'],
        ]);

        $data['code'] = Str::upper($data['code']);
        $branch->update($data);

        return back()->with('success', 'Branch updated.');
    }

    public function admissions(Request $request): View
    {
        $query = Admission::with(['branch','course','user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->integer('branch_id'));
        }

        return view('admin.admissions.index', [
            'admissions' => $query->paginate(20)->withQueryString(),
            'branches' => Branch::orderBy('position')->get(),
        ]);
    }

    public function approveAdmission(Request $request, Admission $admission): RedirectResponse
    {
        abort_if($admission->status === 'approved' && $admission->user_id, 422, 'Admission already approved.');

        $data = $request->validate([
            'student_id' => ['required','string','max:50','unique:users,student_id'],
            'password' => ['required','string','min:8','confirmed'],
            'admin_note' => ['nullable','string','max:1000'],
        ]);

        $user = DB::transaction(function () use ($admission, $data, $request) {
            $email = $admission->email
                ? Str::lower($admission->email)
                : Str::lower($data['student_id']).'@student.kyp.local';

            if (User::where('email', $email)->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'student_id' => 'This admission email is already linked with another account.'
                ]);
            }

            $user = User::create([
                'name' => $admission->name,
                'email' => $email,
                'phone' => $admission->mobile,
                'student_id' => Str::upper($data['student_id']),
                'role' => 'student',
                'status' => 'active',
                'password' => $data['password'],
            ]);

            // id_card_token is not fillable, so forceFill intentionally.
            $user->forceFill([
                'id_card_token' => (string) Str::uuid(),
            ])->save();

            Enrollment::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $admission->course_id,
                ],
                [
                    'status' => 'active',
                    'enrolled_at' => now(),
                    'completed_at' => null,
                ]
            );

            $admission->update([
                'status' => 'approved',
                'admin_note' => $data['admin_note'] ?? null,
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
                'user_id' => $user->id,
            ]);

            return $user;
        });

        return back()->with(
            'success',
            'Admission approved. Student account created: '.$user->student_id
        );
    }

    public function admissionStatus(Request $request, Admission $admission): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['submitted','under_review','rejected'])],
            'admin_note' => ['nullable','string','max:1000'],
        ]);

        abort_if($admission->status === 'approved', 422, 'Approved admission cannot be changed here.');

        $admission->update($data);

        return back()->with('success', 'Admission status updated.');
    }

    public function enquiries(Request $request): View
    {
        $query = Enquiry::with(['branch','course','handler'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->integer('branch_id'));
        }

        return view('admin.enquiries.index', [
            'enquiries' => $query->paginate(20)->withQueryString(),
            'branches' => Branch::orderBy('position')->get(),
        ]);
    }

    public function updateEnquiry(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['new','contacted','follow_up','closed'])],
            'admin_note' => ['nullable','string','max:1000'],
        ]);

        $enquiry->update([
            'status' => $data['status'],
            'admin_note' => $data['admin_note'] ?? null,
            'handled_by' => $request->user()->id,
            'contacted_at' => $data['status'] === 'new'
                ? null
                : ($enquiry->contacted_at ?: now()),
        ]);

        return back()->with('success', 'Enquiry updated.');
    }
}
