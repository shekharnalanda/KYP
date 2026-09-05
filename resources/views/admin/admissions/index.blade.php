@extends('layouts.app')
@section('title','Admissions | KYP')
@section('body')

<x-admin-shell
 active="admissions"
 eyebrow="ADMISSION CONTROL"
 title="Online Admissions"
 subtitle="Applications review करें और approved applicant का Student account बनाएं।"
>

@if(session('success'))
<div class="notice success">{{ session('success') }}</div>
@endif

@if(isset($errors) && $errors->any())
<div class="notice error-box">
@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

<form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:22px">
<select name="branch_id">
<option value="">All Branches</option>
@foreach($branches as $branch)
<option value="{{ $branch->id }}" @selected(request('branch_id')==$branch->id)>
{{ $branch->name }}
</option>
@endforeach
</select>

<select name="status">
<option value="">All Status</option>
@foreach(['submitted','under_review','approved','rejected'] as $status)
<option value="{{ $status }}" @selected(request('status')===$status)>
{{ ucfirst(str_replace('_',' ',$status)) }}
</option>
@endforeach
</select>

<button class="btn btn-light">Filter</button>
</form>

<div style="display:grid;gap:18px">
@forelse($admissions as $a)
<article style="border:1px solid #dce7f4;border-radius:18px;padding:18px">

<div style="display:flex;gap:18px;align-items:flex-start;flex-wrap:wrap">
@if($a->photo_path)
<img src="{{ asset('storage/'.$a->photo_path) }}"
     alt="{{ $a->name }}"
     style="width:90px;height:110px;object-fit:cover;border-radius:12px">
@endif

<div style="flex:1;min-width:240px">
<h3 style="margin:0 0 5px">{{ $a->name }}</h3>
<div>{{ $a->application_number }}</div>
<div>{{ $a->mobile }} @if($a->email) • {{ $a->email }} @endif</div>
<div>{{ $a->branch->name }} • {{ $a->course->code }}</div>
<div><strong>Status:</strong> {{ strtoupper($a->status) }}</div>
</div>
</div>

@if($a->status !== 'approved')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-top:18px">

<form method="POST" action="{{ route('admin.admissions.status',$a) }}">
@csrf
<select name="status">
<option value="under_review">Under Review</option>
<option value="rejected">Rejected</option>
<option value="submitted">Submitted</option>
</select>
<textarea name="admin_note" placeholder="Admin note">{{ $a->admin_note }}</textarea>
<button class="btn btn-light">Update Status</button>
</form>

<form method="POST" action="{{ route('admin.admissions.approve',$a) }}">
@csrf
<input name="student_id" placeholder="Student ID e.g. KYP2026001" required>
<input type="password" name="password" placeholder="Temporary Password" required>
<input type="password" name="password_confirmation" placeholder="Confirm Password" required>
<textarea name="admin_note" placeholder="Approval note"></textarea>
<button class="btn btn-primary">Approve & Create Student</button>
</form>

</div>
@else
<div class="notice success" style="margin-top:15px">
Approved Student:
<strong>{{ $a->user?->student_id }}</strong>
</div>
@endif

</article>
@empty
<div class="panel-card">No admission applications found.</div>
@endforelse
</div>

<div style="margin-top:20px">{{ $admissions->links() }}</div>

</x-admin-shell>
@endsection
