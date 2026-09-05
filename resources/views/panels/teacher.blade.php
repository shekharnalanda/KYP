@extends('layouts.app')
@section('title', 'Teacher Dashboard | KYP')
@section('body')

<x-portal-shell
 active="dashboard"
 eyebrow="TEACHER CONTROL CENTRE"
 title="Teacher Dashboard"
 subtitle="Classroom sessions, learner activity और attendance operations का professional overview।"
>

<style>
.teacher-courses{display:grid;grid-template-columns:repeat(2,1fr);gap:15px;margin-top:20px}
.teacher-course{padding:20px;border:1px solid #dce7f4;border-radius:17px;background:#f9fcff;border-left:4px solid #11c5bd}
.teacher-course b{color:#057d78}
.teacher-course h3{color:#062b63;margin:7px 0}
.teacher-course p{color:#718198;font-size:13px}
.teacher-actions{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px}
@media(max-width:650px){.teacher-courses{grid-template-columns:1fr}}
</style>

<div class="teacher-actions">
<a class="btn btn-primary" href="{{ route('learning.index') }}">Open Sessions</a>
<a class="btn btn-light" href="{{ route('attendance.index') }}">Mark Attendance</a>
</div>

<div class="kps-metrics">
<div class="kps-metric"><small>Active Students</small><strong>{{ $studentCount }}</strong></div>
<div class="kps-metric"><small>Today's Attendance</small><strong>{{ $attendanceToday }}</strong></div>
<div class="kps-metric"><small>Course Sessions</small><strong>{{ $courses->sum('sessions_count') }}</strong></div>
</div>

<div class="teacher-courses">
@foreach($courses as $course)
<article class="teacher-course">
<b>{{ $course->code }}</b>
<h3>{{ $course->name }}</h3>
<p>{{ $course->sessions_count }} sequential classroom/lab sessions</p>
<strong>{{ $course->total_hours }} Hours</strong>
</article>
@endforeach
</div>

</x-portal-shell>
@endsection
