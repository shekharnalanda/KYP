@extends('layouts.app')
@section('title', 'Learning Sessions | KYP')
@section('body')
<x-portal-shell
 active="learning"
 eyebrow="270-HOUR INTERACTIVE LEARNING PATH"
 title="क्रमबद्ध Learning Sessions"
 subtitle="हर session में active timer, interactive steps, knowledge check और practical work।"
>

<style>
.learning-course{
 margin-top:26px;
 padding-top:4px
}
.learning-course:first-of-type{margin-top:4px}
.learning-course-head{
 display:flex;
 align-items:flex-end;
 justify-content:space-between;
 gap:14px;
 margin-bottom:15px
}
.learning-course-head h2{
 margin:0;
 color:#062b63;
 font-size:22px
}
.learning-course-head p{
 margin:5px 0 0;
 color:#718198;
 font-size:13px
}
.learning-session-grid{
 display:grid;
 grid-template-columns:repeat(3,minmax(0,1fr));
 gap:14px
}
.learning-session-card{
 border:1px solid #dce7f4;
 border-radius:17px;
 padding:18px;
 background:#f9fcff;
 display:flex;
 flex-direction:column;
 min-height:225px;
 transition:.18s
}
.learning-session-card:hover{
 transform:translateY(-2px);
 border-color:#a8c7e7;
 box-shadow:0 10px 25px rgba(7,39,83,.07)
}
.learning-number{
 width:42px;
 height:42px;
 border-radius:12px;
 background:#e7fffb;
 color:#057d78;
 display:grid;
 place-items:center;
 font-weight:900
}
.learning-session-card h3{
 color:#062b63;
 font-size:15px;
 line-height:1.4;
 margin:13px 0 7px
}
.learning-session-card p{
 color:#718198;
 font-size:12px;
 line-height:1.5;
 margin:0
}
.learning-progress{
 height:8px;
 background:#e3edf7;
 border-radius:99px;
 overflow:hidden;
 margin:14px 0 6px
}
.learning-progress div{
 height:100%;
 background:linear-gradient(90deg,#11c5bd,#0b86d7)
}
.learning-progress-text{
 color:#718198;
 font-size:11px
}
.learning-session-action{
 margin-top:auto;
 padding-top:15px
}
.learning-session-action .btn{
 width:100%;
 padding:10px 12px
}
@media(max-width:1050px){
 .learning-session-grid{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:600px){
 .learning-session-grid{grid-template-columns:1fr}
 .learning-course-head{align-items:flex-start;flex-direction:column}
}
</style>
@if(auth()->user()->hasRole('teacher'))
<div class="teacher-freedom">
    <div class="teacher-freedom-icon">T</div>
    <div>
        <strong>Teacher Freedom Mode</strong>
        <p>किसी भी Course का कोई भी Session सीधे खोलें। Multiple batches के लिए session sequence या daily limit लागू नहीं है।</p>
        <small>135 Sessions • 4 Courses • Any Session • Any Batch</small>
    </div>
</div>
@endif

@if(session('status'))<div style="margin-top:18px;padding:14px;border-radius:12px;background:#e8fffd;color:#057d78">{{ session('status') }}</div>@endif
@foreach($courses as $course)
<section class="learning-course">
<div class="learning-course-head">
<div>
<h2>{{ $course->code }} — {{ $course->name }}</h2>
<p>{{ $course->total_sessions }} sessions • {{ $course->total_hours }} hours</p>
</div>
</div>
<div class="learning-session-grid">
@foreach($course->sessions as $session)
@php
$done = $completedIds->contains($session->id);
$record = $progressMap->get($session->id);
$percent = $done ? 100 : ($record ? min(99, round(($record->active_seconds / max(1, $session->required_active_minutes * 60)) * 100)) : 0);
@endphp
<article class="learning-session-card">
<div class="learning-number">{{ $session->session_number }}</div>
<h3>{{ $session->title_hi }}</h3>
<p>{{ $session->duration_minutes }} minutes • 10 interactive topic screens</p>
@if(auth()->user()->hasRole('student'))
<div class="learning-progress"><div style="width:{{ $percent }}%"></div></div>
<small class="learning-progress-text">{{ $percent }}% active progress</small>
@endif
<div class="learning-session-action">
<a class="btn {{ $done ? 'btn-light' : 'btn-primary' }}" href="{{ route('learning.show', $session) }}">
{{ auth()->user()->hasRole('teacher')
    ? 'Open for Teaching'
    : ($done ? 'Completed — Review' : ($record ? 'Resume Session' : 'Start Session')) }}
</a>
</div>
</article>
@endforeach
</div></section>
@endforeach


<style>
.teacher-freedom{
 display:flex;align-items:center;gap:16px;
 padding:18px 20px;margin-bottom:22px;
 border:1px solid #9eddd6;border-radius:17px;
 background:linear-gradient(135deg,#effffd,#f5fbff)
}
.teacher-freedom-icon{
 width:46px;height:46px;min-width:46px;
 border-radius:13px;background:#057d78;color:#fff;
 display:grid;place-items:center;font-weight:900;font-size:18px
}
.teacher-freedom strong{
 display:block;color:#062b63;font-size:16px
}
.teacher-freedom p{
 margin:5px 0;color:#42536b;font-size:13px;line-height:1.5
}
.teacher-freedom small{
 color:#057d78;font-weight:800
}
</style>

</x-portal-shell>
@endsection
