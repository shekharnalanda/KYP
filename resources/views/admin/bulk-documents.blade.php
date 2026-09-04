@extends('layouts.app')
@section('title', 'Bulk KYP Documents')
@section('body')
<style>
@media print {.no-print{display:none!important}.document-page{break-after:page;box-shadow:none!important;margin:0!important}.document-page:last-child{break-after:auto}}
.document-page{max-width:950px;margin:22px auto;padding:34px;border:7px double #0f766e;background:white;box-shadow:0 10px 35px #1232}
</style>
<div class="panel-shell"><div class="container">
<div class="no-print" style="display:flex;justify-content:center;gap:12px"><button class="btn btn-primary" onclick="window.print()">Print / Save PDF</button><a class="btn btn-light" href="{{ route('admin.results') }}">Back</a></div>
@foreach($results as $result)
<section class="document-page">
<div style="text-align:center"><img src="{{ asset('images/kyp-logo.webp') }}" alt="KYP" style="width:95px;height:95px;border-radius:50%"><h1>Kushal Youth Program</h1>
@if($documentType === 'certificate')
<p>Certificate of Achievement / उपलब्धि प्रमाणपत्र</p><h2 style="font-size:32px;color:#0f766e">{{ $result->user->name }}</h2><p>has successfully completed the assessment requirements of</p><h2>{{ $result->examAttempt->exam->course->name }}</h2><p><strong>Final Score:</strong> {{ $result->final_score }}/100</p><p><strong>Certificate No:</strong> {{ $result->certificate->serial_number }}</p><img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('certificate.verify', $result->certificate->qr_token)) }}" alt="Verification QR" width="150" height="150"><p style="font-size:12px">{{ route('certificate.verify', $result->certificate->qr_token) }}</p>
@else
<p>Statement of Marks / अंक विवरण</p><h2>{{ $result->user->name }}</h2><p>Student ID: {{ $result->user->student_id }} · Course: {{ $result->examAttempt->exam->course->name }}</p><div class="panel-grid"><div class="panel-card"><h3>Exam</h3><div class="metric">{{ $result->exam_final }}/40</div></div><div class="panel-card"><h3>Lab</h3><div class="metric">{{ $result->lab_final }}/40</div></div><div class="panel-card"><h3>Classroom</h3><div class="metric">{{ $result->classroom_final }}/20</div></div><div class="panel-card"><h3>Total</h3><div class="metric">{{ $result->final_score }}/100</div></div></div><h3>Status: {{ strtoupper($result->result_status) }}</h3>
@endif
</div></section>
@endforeach
</div></div>
@endsection
