@extends('layouts.app')
@section('title', 'KYP Marksheet')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel" style="max-width:900px;margin:auto">
<div style="text-align:center"><img src="{{ asset('images/kyp-logo.webp') }}" alt="KYP" style="width:100px;height:100px;border-radius:50%"><h1>Kushal Youth Program</h1><p>Statement of Marks / अंक विवरण</p></div>
<div class="panel-grid"><div><strong>Student:</strong> {{ $result->user->name }}</div><div><strong>Student ID:</strong> {{ $result->user->student_id }}</div><div><strong>Course:</strong> {{ $result->examAttempt->exam->course->name }}</div><div><strong>Status:</strong> {{ strtoupper($result->result_status) }}</div></div>
<div class="panel-grid" style="margin-top:22px"><div class="panel-card"><h3>Online Exam</h3><div class="metric">{{ $result->exam_final }}/40</div></div><div class="panel-card"><h3>Lab Activity</h3><div class="metric">{{ $result->lab_final }}/40</div></div><div class="panel-card"><h3>Classroom</h3><div class="metric">{{ $result->classroom_final }}/20</div></div><div class="panel-card"><h3>Total</h3><div class="metric">{{ $result->final_score }}/100</div></div></div>
<div style="display:flex;gap:10px;justify-content:center;margin-top:24px"><button class="btn btn-primary" onclick="window.print()">Print / PDF</button>@if($result->certificate)<a class="btn btn-light" href="{{ route('student.certificate', $result->certificate) }}">Certificate</a>@endif</div>
</div></div></div>
@endsection
