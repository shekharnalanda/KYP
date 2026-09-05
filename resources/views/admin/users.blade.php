@extends('layouts.app')
@section('title', 'Users & Enrolments | KYP')
@section('body')

<x-admin-shell
    active="users"
    eyebrow="ADMIN OPERATIONS"
    title="Users & Enrolments"
    subtitle="Student और Teacher accounts, access status तथा course enrolments manage करें।"
>
<button class="btn btn-primary">Create Account</button>
</form>
</div>

<h2 style="margin-top:28px">Student Documents</h2>

<form method="POST"
      action="{{ route('admin.id-cards.bulk') }}"
      target="_blank"
      id="bulk-id-card-form"
      style="padding:16px;border:1px solid #dce7f4;border-radius:16px;background:#f8fbff;margin-bottom:20px">
@csrf

<div style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap">
<div>
<strong>Bulk ID Cards</strong>
<div style="color:var(--muted);font-size:12px;margin-top:4px">
नीचे students select करें। A4 print layout में 2 students per page आएँगे।
</div>
</div>

<button class="btn btn-primary" type="submit">
Print Selected ID Cards
</button>
</div>
</form>

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

<div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:14px">

<label style="padding:8px 12px;border:1px solid #c9dddb;border-radius:999px;background:#f8fbff">
<input
 type="checkbox"
 name="student_ids[]"
 value="{{ $user->id }}"
 form="bulk-id-card-form">
 Select for Bulk ID
</label>

@if($user->id_card_token)
<a class="btn btn-light"
   target="_blank"
   href="{{ route('admin.student.id-card',$user) }}">
View / Print ID Card
</a>
@endif

</div>

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

</x-admin-shell>

@endsection
