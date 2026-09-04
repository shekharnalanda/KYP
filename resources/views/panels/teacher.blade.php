@extends('layouts.app')
@section('title', 'Teacher Dashboard | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel">
<div style="display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap"><div><span class="eyebrow" style="color:#057d78;background:#e8fffd">TEACHER PANEL</span><h1>Teacher Dashboard</h1><p style="color:var(--muted)">Classroom sessions, learner progress और attendance overview।</p></div><div style="display:flex;gap:10px;flex-wrap:wrap"><a class="btn btn-primary" href="{{ route('learning.index') }}">Open Sessions</a><a class="btn btn-primary" href="{{ route('attendance.index') }}">Mark Attendance</a><a class="btn btn-light" href="{{ route('profile.password.edit') }}">Change Password</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-light">Logout</button></form></div></div>
<div class="panel-grid"><div class="panel-card"><h3>Active Students</h3><div class="metric">{{ $studentCount }}</div></div><div class="panel-card"><h3>Today’s Attendance</h3><div class="metric">{{ $attendanceToday }}</div></div><div class="panel-card"><h3>Course Sessions</h3><div class="metric">{{ $courses->sum('sessions_count') }}</div></div></div>
<div class="cards" style="margin-top:22px">@foreach($courses as $course)<article class="card"><div class="icon">{{ $course->code }}</div><h3>{{ $course->name }}</h3><p>{{ $course->sessions_count }} sequential classroom/lab sessions</p><div class="metric">{{ $course->total_hours }} Hours</div></article>@endforeach</div>
</div></div></div>
@endsection
