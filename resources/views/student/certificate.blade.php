@extends('layouts.app')
@section('title', 'KYP Certificate')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel" style="max-width:950px;margin:auto;text-align:center;border:10px double #0f766e">
<img src="{{ asset('images/kyp-logo.webp') }}" alt="KYP" style="width:110px;height:110px;border-radius:50%">
<h1>Certificate of Achievement</h1><p>यह प्रमाणित किया जाता है कि / This is to certify that</p>
<h2 style="font-size:34px;color:#0f766e">{{ $certificate->user->name }}</h2>
<p>has successfully completed the assessment requirements of</p>
<h2>{{ $certificate->result->examAttempt->exam->course->name }}</h2>
<p><strong>Final Score:</strong> {{ $certificate->result->final_score }}/100</p>
<p><strong>Certificate No:</strong> {{ $certificate->serial_number }}</p>
<p><strong>Verification Token:</strong> {{ $certificate->qr_token }}</p>
<p>Issued on {{ $certificate->issued_at->format('d F Y') }}</p>
<button class="btn btn-primary" onclick="window.print()">Print / Save PDF</button>
</div></div></div>
@endsection
