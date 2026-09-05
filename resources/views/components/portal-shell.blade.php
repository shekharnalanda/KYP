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

@media(max-width:850px){
.kps-layout{grid-template-columns:1fr}
.kps-side{position:static;display:grid;grid-template-columns:repeat(3,1fr)}
.kps-label,.kps-sep{display:none}
}
@media(max-width:600px){
.kps-user>div{display:none}
.kps-mci{width:48px;height:48px}
.kps-layout{width:94%}
.kps-side{grid-template-columns:1fr 1fr}
.kps-link b{display:none}
.kps-content{padding:16px}
.kps-metrics{grid-template-columns:1fr}
}
</style>

<div class="kps">

<header class="kps-top">
<div class="kps-topin">

<div class="kps-brand">
<img src="{{ asset('images/kyp-logo.webp') }}" alt="KYP Logo">
<div>
<strong>Kushal Youth Program</strong>
<small>{{ $roleLabel }} Learning Portal</small>
</div>
</div>

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

<div class="kps-layout">

<aside class="kps-side">
<div class="kps-label">{{ strtoupper($roleLabel) }} PORTAL</div>

<a class="kps-link {{ $active==='dashboard'?'on':'' }}"
href="{{ route($dashboardRoute) }}">
<b>⌂</b> Dashboard
</a>

<a class="kps-link {{ $active==='learning'?'on':'' }}"
href="{{ route('learning.index') }}">
<b>L</b> Learning Sessions
</a>

@if($isStudent)
<a class="kps-link {{ $active==='exams'?'on':'' }}"
href="{{ route('student.exams') }}">
<b>X</b> Online Exams
</a>
@else
<a class="kps-link {{ $active==='attendance'?'on':'' }}"
href="{{ route('attendance.index') }}">
<b>A</b> Attendance
</a>
@endif

<div class="kps-sep"></div>

<a class="kps-link {{ $active==='password'?'on':'' }}"
href="{{ route('profile.password.edit') }}">
<b>⚙</b> Change Password
</a>

<form method="POST" action="{{ route('logout') }}">
@csrf
<button type="submit" class="kps-link kps-logout">
<b>↪</b> Logout
</button>
</form>
</aside>

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
