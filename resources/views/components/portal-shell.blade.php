@props([
 'active' => 'dashboard',
 'eyebrow' => 'KYP PORTAL',
 'title' => 'Kushal Youth Program',
 'subtitle' => ''
])

@php
$isStudent = auth()->user()->hasRole('student');
$isTeacher = auth()->user()->hasRole('teacher');
$dashboardRoute = $isStudent ? 'student.dashboard' : 'teacher.dashboard';
$roleLabel = $isStudent ? 'Student' : 'Teacher';
@endphp

<style>
.kps{min-height:100vh;background:#f3f7fc;color:#17243c}
.kps-top{background:linear-gradient(135deg,#031b3f,#073b78);color:#fff}
.kps-topin{width:min(1380px,94%);height:76px;margin:auto;display:flex;align-items:center;justify-content:space-between;gap:18px}
.kps-brand,.kps-user{display:flex;align-items:center;gap:12px}
.kps-portal-nav{display:flex;align-items:center;justify-content:center;gap:5px;flex:1;min-width:0}
.kps-toplink{display:flex;align-items:center;gap:7px;padding:10px 11px;border-radius:10px;color:#e5effc;font-size:12px;font-weight:800;white-space:nowrap;transition:.18s}
.kps-toplink:hover{background:rgba(255,255,255,.11);color:#fff}
.kps-toplink.on{background:#0b65b8;color:#fff;box-shadow:0 5px 14px rgba(0,0,0,.14)}
.kps-toplink b{font-size:13px}
.kps-toplogout{
border:0;
font:inherit;
cursor:pointer;
background:transparent;
color:#e5effc;
width:auto;
margin:0;
appearance:none;
-webkit-appearance:none;
}
.kps-toplogout:hover{
background:rgba(255,255,255,.11);
color:#fff;
}
.kps-toplogout b,
.kps-toplogout span{
color:inherit;
}
.kps-topform{margin:0}
.kps-full-layout{width:min(1480px,96%);margin:auto;padding:25px 0 45px}
.kps-full-layout .kps-main{width:100%}

.kps-brand img{width:54px;height:54px;border-radius:50%;background:#fff;padding:3px;object-fit:contain}
.kps-brand strong,.kps-brand small,.kps-user strong,.kps-user small{display:block}
.kps-brand small,.kps-user small{color:#c8d9ee;font-size:11px;margin-top:3px}
.kps-user{text-align:right}
.kps-mci{width:58px;height:58px;border-radius:50%;background:#fff;padding:4px;display:flex;align-items:center;justify-content:center;overflow:hidden}
.kps-mci img{width:100%;height:100%;object-fit:contain}

.kps-layout{width:min(1380px,94%);margin:auto;display:grid;grid-template-columns:245px minmax(0,1fr);gap:24px;padding:25px 0 45px}
.kps-side{background:#fff;border:1px solid #dce7f4;border-radius:19px;padding:14px;height:max-content;position:sticky;top:18px;box-shadow:0 10px 30px rgba(7,39,83,.06)}
.kps-label{padding:8px 10px;color:#8794a8;font-size:10px;font-weight:900;letter-spacing:1.2px}
.kps-link{display:flex;align-items:center;gap:10px;padding:11px;border-radius:11px;margin:3px 0;color:#42536b;font-size:13px;font-weight:750}
.kps-link:hover{background:#eef6ff;color:#062b63}
.kps-link.on{background:#062b63;color:#fff}
.kps-link b{width:27px;height:27px;border-radius:8px;background:#edf4fc;color:#062b63;display:grid;place-items:center}
.kps-link.on b{background:rgba(255,255,255,.16);color:#fff}
.kps-sep{height:1px;background:#e7edf5;margin:12px 5px}
.kps-logout{width:100%;border:0;background:transparent;font:inherit;cursor:pointer;text-align:left}

.kps-main{min-width:0}
.kps-head{background:linear-gradient(135deg,#062b63,#0b478c 65%,#087f7a);color:#fff;border-radius:23px;padding:27px 29px;margin-bottom:18px;box-shadow:0 16px 42px rgba(6,43,99,.14)}
.kps-eye{display:inline-block;border:1px solid rgba(255,255,255,.25);background:rgba(255,255,255,.09);padding:6px 10px;border-radius:999px;font-size:10px;font-weight:900;letter-spacing:.8px}
.kps-head h1{font-size:clamp(25px,3vw,34px);margin:11px 0 6px}
.kps-head p{margin:0;color:#dcecff;line-height:1.6}
.kps-content{background:#fff;border:1px solid #dce7f4;border-radius:20px;padding:24px;box-shadow:0 9px 28px rgba(7,39,83,.05)}
.kps-content input,.kps-content select,.kps-content textarea{font:inherit}

.kps-metrics{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.kps-metric{padding:20px;border:1px solid #dce7f4;border-radius:17px;background:#f8fbff}
.kps-metric strong{display:block;font-size:28px;color:#062b63;margin-top:8px}
.kps-metric small{color:#718198}

@media(max-width:1100px){
.kps-portal-nav{gap:2px}
.kps-toplink{padding:9px 7px;font-size:11px}
.kps-toplink b{display:none}
}
@media(max-width:850px){
.kps-layout{grid-template-columns:1fr}
.kps-side{position:static;display:grid;grid-template-columns:repeat(3,1fr)}
.kps-label,.kps-sep{display:none}
.kps-topin.student-top{height:auto;min-height:76px;flex-wrap:wrap;padding:10px 0}
.kps-topin.student-top .kps-brand{order:1}
.kps-topin.student-top .kps-user{order:2}
.kps-portal-nav{order:3;flex-basis:100%;justify-content:flex-start;overflow-x:auto;padding:5px 0 2px;scrollbar-width:thin}
.kps-toplink{flex:0 0 auto}
}
@media(max-width:600px){
.kps-user>div{display:none}
.kps-mci{width:48px;height:48px}
.kps-layout{width:94%}
.kps-full-layout{width:94%;padding-top:18px}
.kps-side{grid-template-columns:1fr 1fr}
.kps-link b{display:none}
.kps-content{padding:16px}
.kps-metrics{grid-template-columns:1fr}
}
</style>

<div class="kps">

<header class="kps-top">
<div class="kps-topin student-top">

<div class="kps-brand">
<img src="{{ asset('images/kyp-logo.webp') }}" alt="KYP Logo">
<div>
<strong>Kushal Youth Program</strong>
<small>{{ $roleLabel }} Learning Portal</small>
</div>
</div>

<nav class="kps-portal-nav" aria-label="{{ $roleLabel }} navigation">

<a class="kps-toplink {{ $active==='dashboard'?'on':'' }}"
   href="{{ route($dashboardRoute) }}">
<b>⌂</b><span>Dashboard</span>
</a>

<a class="kps-toplink {{ $active==='learning'?'on':'' }}"
   href="{{ route('learning.index') }}">
<b>L</b><span>Learning Sessions</span>
</a>

@if($isStudent)
<a class="kps-toplink {{ $active==='exams'?'on':'' }}"
   href="{{ route('student.exams') }}">
<b>X</b><span>Online Exams</span>
</a>
@else
<a class="kps-toplink {{ $active==='attendance'?'on':'' }}"
   href="{{ route('attendance.index') }}">
<b>A</b><span>Attendance</span>
</a>
@endif

<a class="kps-toplink {{ $active==='password'?'on':'' }}"
   href="{{ route('profile.password.edit') }}">
<b>⚙</b><span>Change Password</span>
</a>

<form method="POST"
      action="{{ route('logout') }}"
      class="kps-topform">
@csrf
<button type="submit" class="kps-toplink kps-toplogout">
<b>↪</b><span>Logout</span>
</button>
</form>

</nav>

<div class="kps-user">
<div>
<strong>{{ auth()->user()->name }}</strong>
<small>{{ $roleLabel }}</small>
</div>
<span class="kps-mci">
<img src="{{ asset('images/mci-circle-logo.png') }}" alt="MCI Logo">
</span>
</div>

</div>
</header>

<div class="kps-full-layout">



<main class="kps-main">

<section class="kps-head">
<span class="kps-eye">{{ $eyebrow }}</span>
<h1>{{ $title }}</h1>
@if($subtitle)<p>{{ $subtitle }}</p>@endif
</section>

<div class="kps-content">
{{ $slot }}
</div>

</main>
</div>
</div>
