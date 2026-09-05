@extends('layouts.app')
@section('title', 'KYP Administration')

@section('body')

<style>
.kyp-admin-v2{min-height:100vh;background:#f3f7fc;color:#17243c}
.kyp-admin-header{background:linear-gradient(135deg,#031b3f,#073b78);color:#fff}
.kyp-admin-header-inner{width:min(1380px,94%);height:76px;margin:auto;display:flex;align-items:center;justify-content:space-between}
.kyp-admin-brand{display:flex;align-items:center;gap:12px}
.kyp-admin-mark{width:48px;height:48px;border-radius:14px;background:#11c5bd;color:#032653;display:grid;place-items:center;font-weight:900}
.kyp-admin-brand strong,.kyp-admin-brand small{display:block}
.kyp-admin-brand small{color:#c8d9ee;font-size:12px;margin-top:3px}
.kyp-admin-user{display:flex;align-items:center;gap:12px;text-align:right}
.kyp-admin-user strong,.kyp-admin-user small{display:block}
.kyp-admin-user small{font-size:11px;color:#c8d9ee}
.kyp-admin-user>span{width:40px;height:40px;border-radius:50%;background:#fff;color:#062b63;display:grid;place-items:center;font-weight:900}

.kyp-admin-layout{width:min(1380px,94%);margin:auto;display:grid;grid-template-columns:245px 1fr;gap:24px;padding:25px 0 45px}
.kyp-admin-sidebar{background:#fff;border:1px solid #dce7f4;border-radius:19px;padding:14px;height:max-content;box-shadow:0 10px 30px rgba(7,39,83,.06)}
.kyp-side-label{padding:8px 10px;color:#8794a8;font-size:10px;font-weight:900;letter-spacing:1.2px}
.kyp-side-link{display:flex;align-items:center;gap:10px;padding:11px;border-radius:11px;margin:3px 0;color:#42536b;font-size:13px;font-weight:750}
.kyp-side-link:hover{background:#eef6ff;color:#062b63}
.kyp-side-link.active{background:#062b63;color:#fff}
.kyp-side-link b{width:27px;height:27px;border-radius:8px;background:#edf4fc;color:#062b63;display:grid;place-items:center}
.kyp-side-link.active b{background:rgba(255,255,255,.16);color:#fff}
.kyp-side-separator{height:1px;background:#e7edf5;margin:12px 5px}
.kyp-logout{border:0;background:transparent;width:100%;font:inherit;cursor:pointer;text-align:left}

.kyp-admin-main{min-width:0}
.kyp-welcome{background:linear-gradient(135deg,#062b63,#0b478c 65%,#087f7a);color:#fff;border-radius:23px;padding:29px;box-shadow:0 16px 42px rgba(6,43,99,.16)}
.kyp-eyebrow{display:inline-block;border:1px solid rgba(255,255,255,.25);background:rgba(255,255,255,.09);padding:6px 10px;border-radius:999px;font-size:10px;font-weight:900;letter-spacing:.9px}
.kyp-welcome h1{font-size:34px;margin:12px 0 7px}
.kyp-welcome p{color:#dcecff;margin:0;line-height:1.6}
.kyp-live{margin-top:16px;color:#cffff8;font-size:12px;font-weight:750}
.kyp-live i{display:inline-block;width:8px;height:8px;border-radius:50%;background:#42f1c6;margin-right:7px}

.kyp-section-heading{margin:25px 2px 13px}
.kyp-section-heading h2{margin:0;color:#17243c;font-size:18px}
.kyp-section-heading p{margin:4px 0 0;color:#718198;font-size:12px}

.kyp-stats{display:grid;grid-template-columns:repeat(5,1fr);gap:13px}
.kyp-stat-card{background:#fff;border:1px solid #dce7f4;border-radius:17px;padding:17px;box-shadow:0 7px 23px rgba(7,39,83,.045)}
.kyp-stat-icon{width:36px;height:36px;border-radius:10px;background:#edf5ff;color:#062b63;display:grid;place-items:center;font-weight:900}
.kyp-stat-card.teal .kyp-stat-icon{background:#e6fffb;color:#057d78}
.kyp-stat-card.orange .kyp-stat-icon{background:#fff2e3;color:#c86600}
.kyp-stat-card strong{display:block;font-size:28px;color:#062b63;margin-top:13px}
.kyp-stat-card small{display:block;color:#718198;font-size:11px;font-weight:700;margin-top:5px}

.kyp-courses{display:grid;grid-template-columns:repeat(4,1fr);gap:13px}
.kyp-course{background:#fff;border:1px solid #dce7f4;border-radius:17px;padding:18px;border-left:4px solid #11c5bd}
.kyp-course>b{font-size:11px;color:#057d78;letter-spacing:1px}
.kyp-course h3{font-size:16px;color:#062b63;margin:7px 0}
.kyp-course p{font-size:12px;color:#718198;line-height:1.5;min-height:36px}
.kyp-course footer{background:transparent;color:#52627a;padding:11px 0 0;margin-top:11px;border-top:1px solid #edf1f6;display:flex;justify-content:space-between;font-size:10px;font-weight:800}

.kyp-quick{display:grid;grid-template-columns:repeat(5,1fr);gap:11px}
.kyp-quick a{background:#fff;border:1px solid #dce7f4;border-radius:14px;padding:14px;display:flex;align-items:center;gap:9px;font-size:12px;font-weight:800}
.kyp-quick a:hover{border-color:#9bbfe7;box-shadow:0 8px 22px rgba(7,39,83,.07)}
.kyp-quick b{width:31px;height:31px;border-radius:9px;background:#edf5ff;color:#062b63;display:grid;place-items:center}

.kyp-system{margin-top:23px;background:#fff;border:1px solid #dce7f4;border-radius:17px;padding:17px;display:flex;align-items:center;justify-content:space-between}
.kyp-system>div{display:flex;align-items:center;gap:11px}
.kyp-system>div>b{width:37px;height:37px;border-radius:50%;background:#e6fff4;color:#08764e;display:grid;place-items:center}
.kyp-system strong,.kyp-system small{display:block}
.kyp-system small{color:#718198;margin-top:3px}
.kyp-system em{font-style:normal;background:#e6fff4;color:#08764e;border-radius:999px;padding:7px 10px;font-size:10px;font-weight:900}

@media(max-width:1100px){
.kyp-stats{grid-template-columns:repeat(3,1fr)}
.kyp-quick{grid-template-columns:repeat(3,1fr)}
}
@media(max-width:850px){
.kyp-admin-layout{grid-template-columns:1fr}
.kyp-admin-sidebar{display:grid;grid-template-columns:repeat(3,1fr)}
.kyp-side-label,.kyp-side-separator{display:none}
.kyp-courses{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:600px){
.kyp-admin-user>div{display:none}
.kyp-admin-layout{width:94%}
.kyp-admin-sidebar{grid-template-columns:1fr 1fr}
.kyp-side-link b{display:none}
.kyp-stats,.kyp-courses,.kyp-quick{grid-template-columns:1fr 1fr}
.kyp-welcome h1{font-size:27px}
}
@media(max-width:390px){
.kyp-stats,.kyp-courses,.kyp-quick{grid-template-columns:1fr}
}
</style>

<div class="kyp-admin-v2">

<header class="kyp-admin-header">
<div class="kyp-admin-header-inner">
    <div class="kyp-admin-brand">
        <div class="kyp-admin-mark">KYP</div>
        <div>
            <strong>Kushal Youth Program</strong>
            <small>Central Administration</small>
        </div>
    </div>

    <div class="kyp-admin-user">
        <div>
            <strong>{{ auth()->user()->name }}</strong>
            <small>Administrator</small>
        </div>
        <span>{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
    </div>
</div>
</header>

<div class="kyp-admin-layout">

<aside class="kyp-admin-sidebar">
    <div class="kyp-side-label">ADMINISTRATION</div>

    <a class="kyp-side-link active" href="{{ route('admin.dashboard') }}">
        <b>⌂</b><span>Dashboard</span>
    </a>

    <a class="kyp-side-link" href="{{ route('admin.users') }}">
        <b>U</b><span>Users & Enrolments</span>
    </a>

    <a class="kyp-side-link" href="{{ route('admin.progress') }}">
        <b>P</b><span>Student Progress</span>
    </a>

    <a class="kyp-side-link" href="{{ route('admin.assessments') }}">
        <b>Q</b><span>Exams & Questions</span>
    </a>

    <a class="kyp-side-link" href="{{ route('attendance.index') }}">
        <b>A</b><span>Attendance</span>
    </a>

    <a class="kyp-side-link" href="{{ route('admin.results') }}">
        <b>R</b><span>Results & Certificates</span>
    </a>

    <div class="kyp-side-separator"></div>

    <a class="kyp-side-link" href="{{ route('profile.password.edit') }}">
        <b>⚙</b><span>Change Password</span>
    </a>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="kyp-side-link kyp-logout" type="submit">
            <b>↪</b><span>Logout</span>
        </button>
    </form>
</aside>

<main class="kyp-admin-main">

<section class="kyp-welcome">
    <div>
        <span class="kyp-eyebrow">ADMIN CONTROL CENTRE</span>
        <h1>KYP Administration</h1>
        <p>Learning, attendance, assessment, examination और certification का unified operational dashboard.</p>
        <div class="kyp-live"><i></i> Production system operational</div>
    </div>
</section>

<div class="kyp-section-heading">
    <div>
        <h2>Live Overview</h2>
        <p>Current platform statistics</p>
    </div>
</div>

<div class="kyp-stats">
@php
$cards = [
 ['Students',$stats['students'],'S','blue'],
 ['Teachers',$stats['teachers'],'T','blue'],
 ['Active Courses',$stats['courses'],'C','teal'],
 ['Learning Sessions',$stats['sessions'],'L','teal'],
 ['Active Enrolments',$stats['enrollments'],'E','blue'],
 ['Attendance Today',$stats['attendance_today'],'A','orange'],
 ['Online Exams',$stats['exams'],'X','blue'],
 ['Question Bank',$stats['questions'],'Q','blue'],
 ['Results',$stats['results'],'R','orange'],
 ['Certificates',$stats['certificates'],'✓','orange'],
];
@endphp

@foreach($cards as $card)
<div class="kyp-stat-card {{ $card[3] }}">
    <span class="kyp-stat-icon">{{ $card[2] }}</span>
    <strong>{{ $card[1] }}</strong>
    <small>{{ $card[0] }}</small>
</div>
@endforeach
</div>

<div class="kyp-section-heading">
    <div>
        <h2>Program Structure</h2>
        <p>135 sessions · 270 hours</p>
    </div>
</div>

<div class="kyp-courses">
    <div class="kyp-course">
        <b>CIT</b>
        <h3>Information Technology</h3>
        <p>Digital and practical computer skills</p>
        <footer><span>60 Sessions</span><span>120 Hours</span></footer>
    </div>

    <div class="kyp-course">
        <b>CLS</b>
        <h3>Language Skills</h3>
        <p>Communication and professional language skills</p>
        <footer><span>40 Sessions</span><span>80 Hours</span></footer>
    </div>

    <div class="kyp-course">
        <b>CSS</b>
        <h3>Soft Skills</h3>
        <p>Personality, workplace and employability skills</p>
        <footer><span>20 Sessions</span><span>40 Hours</span></footer>
    </div>

    <div class="kyp-course">
        <b>AI-DM</b>
        <h3>AI & Digital Marketing</h3>
        <p>Modern AI literacy and digital marketing</p>
        <footer><span>15 Sessions</span><span>30 Hours</span></footer>
    </div>
</div>

<div class="kyp-section-heading">
    <div>
        <h2>Quick Operations</h2>
        <p>Frequently used administration tools</p>
    </div>
</div>

<div class="kyp-quick">
    <a href="{{ route('admin.users') }}"><b>U</b>Manage Users</a>
    <a href="{{ route('admin.progress') }}"><b>P</b>Student Progress</a>
    <a href="{{ route('attendance.index') }}"><b>A</b>Attendance</a>
    <a href="{{ route('admin.assessments') }}"><b>Q</b>Assessments</a>
    <a href="{{ route('admin.results') }}"><b>R</b>Results</a>
</div>

<div class="kyp-system">
    <div>
        <b>✓</b>
        <span>
            <strong>KYP Production System</strong>
            <small>Learning and assessment services are operational.</small>
        </span>
    </div>
    <em>OPERATIONAL</em>
</div>

</main>
</div>
</div>

@endsection
