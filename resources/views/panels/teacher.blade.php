@extends('layouts.app')
@section('title', 'Teacher Dashboard | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel"><div style="display:flex;justify-content:space-between;align-items:center;gap:18px"><div><span class="eyebrow" style="color:#057d78;background:#e8fffd">TEACHER PANEL</span><h1>Teacher Dashboard</h1><p style="color:var(--muted)">Classroom sessions खोलें, topic guidance दें और learner progress देखें।</p></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-light">Logout</button></form></div><div class="panel-grid"><div class="panel-card"><h3>Today’s Classroom</h3><p>Assigned topic-wise sessions।</p></div><div class="panel-card"><h3>Attendance</h3><p>Classroom presence और session completion।</p></div><div class="panel-card"><h3>Student Support</h3><p>Lab progress और pending activities।</p></div></div></div></div></div>
@endsection
