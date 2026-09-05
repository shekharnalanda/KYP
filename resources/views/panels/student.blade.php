@extends('layouts.app')
@section('title', 'Student Dashboard | KYP')
@section('body')

<x-portal-shell
 active="dashboard"
 eyebrow="STUDENT LEARNING PORTAL"
 title="नमस्ते, {{ auth()->user()->name }}"
 subtitle="आपकी classroom तथा lab progress, learning sessions और exam eligibility का live overview।"
>

<style>
.student-course-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
.student-course{border:1px solid #dce7f4;border-radius:18px;padding:20px;background:#f9fcff}
.student-course-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}
.student-course-code{width:46px;height:46px;border-radius:13px;background:#e7fffb;color:#057d78;display:grid;place-items:center;font-weight:900}
.student-course h3{color:#062b63;margin:12px 0 6px}
.student-course p{color:#718198;margin:0;font-size:13px}
.student-progress{height:9px;background:#e3edf7;border-radius:99px;overflow:hidden;margin:16px 0 9px}
.student-progress div{height:100%;background:linear-gradient(90deg,#11c5bd,#0b86d7)}
.student-meta{display:flex;justify-content:space-between;gap:10px;font-size:12px;color:#52627a}
.student-status{margin-top:13px;font-weight:850;color:#057d78}
.student-actions{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px}
@media(max-width:700px){.student-course-grid{grid-template-columns:1fr}}
</style>

@php
$myLatestResult = \App\Models\Result::where('user_id',auth()->id())
    ->whereNotNull('published_at')
    ->with('certificate')
    ->latest()
    ->first();
@endphp

<div class="student-actions">

<a class="btn btn-primary"
   href="{{ route('learning.index') }}">
Open Learning Sessions
</a>

<a class="btn btn-light"
   href="{{ route('student.exams') }}">
Online Exams
</a>

@if(auth()->user()->id_card_token)
<a class="btn btn-light"
   href="{{ route('student.id-card') }}">
My ID Card
</a>
@endif

@if($myLatestResult)
<a class="btn btn-light"
   href="{{ route('student.marksheet',$myLatestResult) }}">
My Marksheet
</a>

@if($myLatestResult->certificate)
<a class="btn btn-light"
   href="{{ route('student.certificate',$myLatestResult->certificate) }}">
My Certificate
</a>
@endif
@endif

</div>

<div class="student-course-grid">
@foreach($summary as $item)
<article class="student-course">
<div class="student-course-head">
<div>
<div class="student-course-code">{{ $item['course']->code }}</div>
<h3>{{ $item['course']->name }}</h3>
</div>
<strong>{{ $item['progress'] }}%</strong>
</div>

<p>{{ $item['course']->total_sessions }} sessions · {{ $item['course']->total_hours }} hours</p>

<div class="student-progress">
<div style="width:{{ $item['progress'] }}%"></div>
</div>

<div class="student-meta">
<span>Lab {{ $item['lab_sessions'] }}/{{ $item['course']->total_sessions }}</span>
<span>Classroom {{ $item['classroom_sessions'] }}</span>
</div>

<div class="student-status">
@if($item['required_sessions'] === null)
Advanced Module
@elseif($item['eligible'])
✓ Exam Eligible
@else
{{ max(0,$item['required_sessions']-$item['lab_sessions']) }} more lab sessions required
@endif
</div>
</article>
@endforeach
</div>

</x-portal-shell>
@endsection
