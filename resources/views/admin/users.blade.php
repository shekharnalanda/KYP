@extends('layouts.app')
@section('title', 'Users & Enrolments | KYP')
@section('body')
<div class="panel-shell"><div class="container">
<div class="panel">
<div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap">
<div><span class="eyebrow" style="color:#057d78;background:#e8fffd">ADMIN OPERATIONS</span><h1>Users & Enrolments</h1><p style="color:var(--muted)">Student और Teacher accounts, access status तथा course enrolments manage करें।</p></div>
<a class="btn btn-light" href="{{ route('admin.dashboard') }}">← Dashboard</a>
</div>
@if(session('success'))<div style="margin-top:18px;padding:13px 16px;border-radius:14px;background:#e8fff3;color:#087443;font-weight:700">{{ session('success') }}</div>@endif
@if($errors->any())<div style="margin-top:18px;padding:13px 16px;border-radius:14px;background:#fff0f0;color:#a52020"><strong>Please correct:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

<div style="margin-top:22px;padding:22px;border:1px solid #dce8e7;border-radius:22px;background:#faffff">
<h2 style="margin-top:0">Create Account</h2>
<form method="POST" action="{{ route('admin.users.store') }}">@csrf
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px">
<label>Name<input required name="name" value="{{ old('name') }}" style="width:100%;padding:12px;border:1px solid #bdd3d1;border-radius:12px"></label>
<label>Email<input required type="email" name="email" value="{{ old('email') }}" style="width:100%;padding:12px;border:1px solid #bdd3d1;border-radius:12px"></label>
<label>Phone<input name="phone" value="{{ old('phone') }}" style="width:100%;padding:12px;border:1px solid #bdd3d1;border-radius:12px"></label>
<label>Role<select required name="role" style="width:100%;padding:12px;border:1px solid #bdd3d1;border-radius:12px"><option value="student">Student</option><option value="teacher" @selected(old('role') === 'teacher')>Teacher</option></select></label>
<label>Student ID<input name="student_id" value="{{ old('student_id') }}" placeholder="Required for student" style="width:100%;padding:12px;border:1px solid #bdd3d1;border-radius:12px"></label>
<label>Password<input required type="password" name="password" minlength="8" style="width:100%;padding:12px;border:1px solid #bdd3d1;border-radius:12px"></label>
<label>Confirm Password<input required type="password" name="password_confirmation" minlength="8" style="width:100%;padding:12px;border:1px solid #bdd3d1;border-radius:12px"></label>
</div>
<div style="margin:16px 0"><strong>Initial courses (students)</strong><div style="display:flex;gap:14px;flex-wrap:wrap;margin-top:8px">@foreach($courses as $course)<label style="padding:9px 13px;border:1px solid #c9dddb;border-radius:999px;background:white"><input type="checkbox" name="course_ids[]" value="{{ $course->id }}"> {{ $course->code }}</label>@endforeach</div></div>
<button class="btn btn-primary">Create Account</button>
</form>
</div>

<h2 style="margin-top:28px">Learner & Teacher Directory</h2>
<div style="display:grid;gap:16px">
@forelse($users as $user)
<div style="padding:18px;border:1px solid #dce8e7;border-radius:20px;background:white">
<div style="display:flex;justify-content:space-between;gap:15px;align-items:flex-start;flex-wrap:wrap">
<div><h3 style="margin:0 0 5px">{{ $user->name }}</h3><div style="color:var(--muted)">{{ $user->email }} · {{ ucfirst($user->role) }} @if($user->student_id) · ID: {{ $user->student_id }} @endif</div></div>
<form method="POST" action="{{ route('admin.users.status', $user) }}">@csrf
<input type="hidden" name="status" value="{{ $user->status === 'active' ? 'inactive' : 'active' }}">
<button class="btn {{ $user->status === 'active' ? 'btn-light' : 'btn-primary' }}">{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}</button>
</form>
</div>
@if($user->role === 'student')
<form method="POST" action="{{ route('admin.users.enrollments', $user) }}" style="margin-top:14px">@csrf
<div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
@foreach($courses as $course)
<label style="padding:8px 12px;border:1px solid #c9dddb;border-radius:999px">
<input type="checkbox" name="course_ids[]" value="{{ $course->id }}" @checked($user->enrollments->contains(fn($enrolment) => $enrolment->course_id === $course->id && $enrolment->status === 'active'))> {{ $course->code }}
</label>
@endforeach
<button class="btn btn-primary">Save Enrolments</button>
</div>
</form>
@endif
</div>
@empty <div class="panel-card">No student or teacher account found.</div> @endforelse
</div>
<div style="margin-top:20px">{{ $users->links() }}</div>
</div></div></div>
@endsection
