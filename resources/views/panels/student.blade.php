@extends('layouts.app')
@section('title', 'Student Dashboard | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel"><div style="display:flex;justify-content:space-between;align-items:center;gap:18px"><div><span class="eyebrow" style="color:#057d78;background:#e8fffd">STUDENT PANEL</span><h1>नमस्ते, {{ auth()->user()->name }}</h1><p style="color:var(--muted)">आपकी learning journey और daily lab progress यहाँ उपलब्ध होगी।</p></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-light">Logout</button></form></div><div class="panel-grid"><div class="panel-card"><h3>My Sessions</h3><p>Topic-wise CIT, CLS, CSS और AI sessions।</p></div><div class="panel-card"><h3>Attendance & Activity</h3><p>Classroom तथा lab completion status।</p></div><div class="panel-card"><h3>Exam & Certificates</h3><p>Eligibility, result, marksheet और certificate।</p></div></div></div></div></div>
@endsection
