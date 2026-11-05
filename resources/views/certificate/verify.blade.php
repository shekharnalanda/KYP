@extends('layouts.app')
@section('title', 'Certificate Verification | YKK')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel" style="max-width:760px;margin:auto;text-align:center">
<img src="{{ asset('images/ykk-logo.webp') }}" alt="YKK" style="width:110px;height:110px;border-radius:50%">
@if($valid)
<div style="margin:18px auto;padding:12px;border-radius:999px;background:#e8fff3;color:#087443;font-weight:900;max-width:300px">✓ VERIFIED CERTIFICATE</div>
<h1>{{ $certificate->user->name }}</h1><p>Certificate record is authentic and currently valid.</p>
<div class="panel-grid" style="text-align:left;margin-top:24px"><div><strong>Certificate No.</strong><br>{{ $certificate->serial_number }}</div><div><strong>Course</strong><br>{{ $certificate->result->examAttempt->exam->course->name }}</div><div><strong>Final Score</strong><br>{{ $certificate->result->final_score }}/100</div><div><strong>Issued</strong><br>{{ $certificate->issued_at->format('d F Y') }}</div></div>
@else
<div style="margin:18px auto;padding:12px;border-radius:999px;background:#fff0f0;color:#a52020;font-weight:900;max-width:300px">NOT VERIFIED</div><h1>Certificate not found</h1><p>This verification link does not match an active YKK certificate.</p>
@endif
<a class="btn btn-light" style="margin-top:24px" href="{{ route('home') }}">YKK Home</a>
</div></div></div>
@endsection
