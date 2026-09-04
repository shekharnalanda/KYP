@extends('layouts.app')
@section('title', 'Admin Dashboard | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel">
<div style="display:flex;justify-content:space-between;align-items:center;gap:18px"><div><span class="eyebrow" style="color:#057d78;background:#e8fffd">ADMIN CONTROL</span><h1>KYP Administration</h1><p style="color:var(--muted)">Learning operations का live central overview।</p></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-light">Logout</button></form></div>
<div class="panel-grid">
<div class="panel-card"><h3>Students</h3><div class="metric">{{ $stats['students'] }}</div></div>
<div class="panel-card"><h3>Teachers</h3><div class="metric">{{ $stats['teachers'] }}</div></div>
<div class="panel-card"><h3>Active Courses</h3><div class="metric">{{ $stats['courses'] }}</div></div>
<div class="panel-card"><h3>Learning Sessions</h3><div class="metric">{{ $stats['sessions'] }}</div></div>
<div class="panel-card"><h3>Active Enrolments</h3><div class="metric">{{ $stats['enrollments'] }}</div></div>
<div class="panel-card"><h3>Attendance Today</h3><div class="metric">{{ $stats['attendance_today'] }}</div></div>
</div></div></div></div>
@endsection
