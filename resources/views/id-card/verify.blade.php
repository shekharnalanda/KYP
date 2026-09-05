@extends('layouts.app')
@section('title', 'Verify Student ID | KYP')
@section('body')

<div class="panel-shell">
<div class="container">
<div class="panel" style="max-width:720px;margin:auto;text-align:center">

<img src="{{ asset('images/kyp-logo.webp') }}"
 alt="KYP"
 style="width:90px;height:90px;border-radius:50%">

<h1>Student ID Verification</h1>

@if($valid)

<div style="padding:18px;border-radius:16px;background:#e8fff4;color:#087443;margin:20px 0">
<strong>✓ VALID KYP STUDENT</strong>
</div>

<h2>{{ $student->name }}</h2>

<p><strong>Student ID:</strong> {{ $student->student_id }}</p>

<p>
<strong>Course:</strong>
{{ $student->enrollments->pluck('course.code')->filter()->implode(', ') ?: 'KYP' }}
</p>

<p><strong>Status:</strong> {{ strtoupper($student->status) }}</p>

@else

<div style="padding:18px;border-radius:16px;background:#fff0ee;color:#a12a20;margin:20px 0">
<strong>INVALID / INACTIVE STUDENT ID</strong>
</div>

@endif

</div>
</div>
</div>

@endsection
