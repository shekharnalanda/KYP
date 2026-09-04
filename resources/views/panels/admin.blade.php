@extends('layouts.app')
@section('title', 'Admin Dashboard | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel"><div style="display:flex;justify-content:space-between;align-items:center;gap:18px"><div><span class="eyebrow" style="color:#057d78;background:#e8fffd">ADMIN CONTROL</span><h1>KYP Administration</h1><p style="color:var(--muted)">Students, teachers, attendance, courses, exams और certificates का central control।</p></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-light">Logout</button></form></div><div class="panel-grid"><div class="panel-card"><h3>People & Centres</h3><p>Accounts, roles और learning-centre management।</p></div><div class="panel-card"><h3>Learning Operations</h3><p>Sessions, biometric attendance और lab activity।</p></div><div class="panel-card"><h3>Assessment</h3><p>Eligibility, question bank, result और bulk certificates।</p></div></div></div></div></div>
@endsection
