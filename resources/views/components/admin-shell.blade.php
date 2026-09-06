@props([
    'active' => 'dashboard',
    'eyebrow' => 'ADMIN OPERATIONS',
    'title' => 'Administration',
    'subtitle' => ''
])

<style>
.kas{min-height:100vh;background:#f3f7fc;color:#17243c}
.kas-top{background:linear-gradient(135deg,#031b3f,#073b78);color:#fff}
.kas-topin{width:min(1380px,94%);height:76px;margin:auto;display:flex;align-items:center;justify-content:space-between;gap:18px}
.kas-brand,.kas-user{display:flex;align-items:center;gap:12px}
.kas-brand img{width:54px;height:54px;object-fit:contain;border-radius:50%;background:#fff;padding:3px}
.kas-brand strong,.kas-brand small,.kas-user strong,.kas-user small{display:block}
.kas-brand small,.kas-user small{color:#c8d9ee;font-size:11px;margin-top:3px}
.kas-user{text-align:right}
.kas-user-logo{width:58px;height:58px;border-radius:50%;background:#fff;padding:4px;display:flex;align-items:center;justify-content:center;overflow:hidden}
.kas-user-logo img{width:100%;height:100%;object-fit:contain;display:block}

.kas-layout{width:min(1380px,94%);margin:auto;display:grid;grid-template-columns:245px minmax(0,1fr);gap:24px;padding:25px 0 45px}
.kas-side{background:#fff;border:1px solid #dce7f4;border-radius:19px;padding:14px;height:max-content;position:sticky;top:18px;box-shadow:0 10px 30px rgba(7,39,83,.06)}
.kas-label{padding:8px 10px;color:#8794a8;font-size:10px;font-weight:900;letter-spacing:1.2px}
.kas-link{display:flex;align-items:center;gap:10px;padding:11px;border-radius:11px;margin:3px 0;color:#42536b;font-size:13px;font-weight:750}
.kas-link:hover{background:#eef6ff;color:#062b63}
.kas-link.on{background:#062b63;color:#fff}
.kas-link b{width:27px;height:27px;border-radius:8px;background:#edf4fc;color:#062b63;display:grid;place-items:center}
.kas-link.on b{background:rgba(255,255,255,.16);color:#fff}
.kas-sep{height:1px;background:#e7edf5;margin:12px 5px}
.kas-logout{width:100%;border:0;background:transparent;font:inherit;cursor:pointer;text-align:left}

.kas-main{min-width:0}
.kas-head{background:linear-gradient(135deg,#062b63,#0b478c 65%,#087f7a);color:#fff;border-radius:23px;padding:27px 29px;box-shadow:0 16px 42px rgba(6,43,99,.14);margin-bottom:18px}
.kas-eye{display:inline-block;border:1px solid rgba(255,255,255,.25);background:rgba(255,255,255,.09);padding:6px 10px;border-radius:999px;font-size:10px;font-weight:900;letter-spacing:.8px}
.kas-head h1{font-size:clamp(25px,3vw,34px);margin:11px 0 6px}
.kas-head p{margin:0;color:#dcecff;line-height:1.6}

.kas-content{background:#fff;border:1px solid #dce7f4;border-radius:20px;padding:24px;box-shadow:0 9px 28px rgba(7,39,83,.05)}
.kas-content input,.kas-content select,.kas-content textarea{font:inherit}
.kas-content table{width:100%;border-collapse:collapse}
.kas-content th,.kas-content td{padding:12px;border-bottom:1px solid #e8eef5;text-align:left;vertical-align:top}
.kas-content th{font-size:12px;color:#52627a;background:#f7faff}
.kas-content details.card,.kas-content .card{box-shadow:none}
.kas-content label{font-weight:650;color:#26364f}
.kas-content input,.kas-content select,.kas-content textarea{border:1px solid #cbd8e8!important;border-radius:11px!important}
.kas-content input:focus,.kas-content select:focus,.kas-content textarea:focus{outline:3px solid rgba(17,197,189,.15);border-color:#11a9a3!important}

@media(max-width:850px){
.kas-layout{grid-template-columns:1fr}
.kas-side{position:static;display:grid;grid-template-columns:repeat(3,1fr)}
.kas-label,.kas-sep{display:none}
}
@media(max-width:600px){
.kas-user>div{display:none}
.kas-user-logo{width:48px;height:48px}
.kas-layout{width:94%}
.kas-side{grid-template-columns:1fr 1fr}
.kas-link b{display:none}
.kas-content{padding:16px}
}
</style>

<div class="kas">
<header class="kas-top">
<div class="kas-topin">

<div class="kas-brand">
<img src="{{ asset('images/kyp-logo.webp') }}" alt="KYP Logo">
<div>
<strong>Kushal Youth Program</strong>
<small>Central Administration</small>
</div>
</div>

<div class="kas-user">
<div>
<strong>{{ auth()->user()->name }}</strong>
<small>Administrator</small>
</div>
<span class="kas-user-logo">
<img src="{{ asset('images/mci-circle-logo.png') }}" alt="MCI Logo">
</span>
</div>

</div>
</header>

<div class="kas-layout">

<aside class="kas-side">
<div class="kas-label">ADMINISTRATION</div>

<a class="kas-link {{ $active==='dashboard'?'on':'' }}" href="{{ route('admin.dashboard') }}">
<b>⌂</b> Dashboard
</a>

<a class="kas-link {{ $active==='users'?'on':'' }}" href="{{ route('admin.users') }}">
<b>U</b> Users & Enrolments
</a>

<a class="kas-link {{ $active==='branches'?'on':'' }}" href="{{ route('admin.branches') }}">
<b>B</b> Branches
</a>

<a class="kas-link {{ $active==='admissions'?'on':'' }}" href="{{ route('admin.admissions') }}">
<b>D</b> Admissions
</a>

<a class="kas-link {{ $active==='enquiries'?'on':'' }}" href="{{ route('admin.enquiries') }}">
<b>E</b> Enquiries
</a>



<a class="kas-link {{ $active==='progress'?'on':'' }}" href="{{ route('admin.progress') }}">
<b>P</b> Student Progress
</a>

<a class="kas-link {{ $active==='assessments'?'on':'' }}" href="{{ route('admin.assessments') }}">
<b>Q</b> Exams & Questions
</a>

<a class="kas-link {{ $active==='attendance'?'on':'' }}" href="{{ route('attendance.index') }}">
<b>A</b> Attendance
</a>

<a class="kas-link {{ $active==='iris-software'?'on':'' }}"
   href="{{ route('admin.iris-software') }}">
<b>◉</b> Iris / Biometric Setup
</a>

<a class="kas-link {{ $active==='results'?'on':'' }}" href="{{ route('admin.results') }}">
<b>R</b> Results & Certificates
</a>

<a class="kas-link {{ $active==='exceptional'?'on':'' }}" href="{{ route('admin.exceptional') }}">
<b>!</b> Exceptional Completion
</a>

<div class="kas-sep"></div>

<a class="kas-link {{ $active==='password'?'on':'' }}" href="{{ route('profile.password.edit') }}">
<b>⚙</b> Change Password
</a>

<form method="POST" action="{{ route('logout') }}">
@csrf
<button type="submit" class="kas-link kas-logout">
<b>↪</b> Logout
</button>
</form>
</aside>

<main class="kas-main">

<section class="kas-head">
<span class="kas-eye">{{ $eyebrow }}</span>
<h1>{{ $title }}</h1>
@if($subtitle)
<p>{{ $subtitle }}</p>
@endif
</section>

<div class="kas-content">
{{ $slot }}
</div>

</main>
</div>
</div>
