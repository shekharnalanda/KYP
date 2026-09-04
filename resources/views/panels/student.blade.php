@extends('layouts.app')
@section('title', 'Student Dashboard | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel">
<div style="display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap"><div><span class="eyebrow" style="color:#057d78;background:#e8fffd">STUDENT PANEL</span><h1>नमस्ते, {{ auth()->user()->name }}</h1><p style="color:var(--muted)">आपकी classroom तथा lab progress और exam eligibility।</p></div><div style="display:flex;gap:10px"><a class="btn btn-primary" href="{{ route('student.exams') }}">Online Exams</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-light">Logout</button></form></div></div>
<div class="cards" style="margin-top:28px">
@foreach($summary as $item)
<article class="card"><div class="icon">{{ $item['course']->code }}</div><h3>{{ $item['course']->name }}</h3><p>Lab: {{ $item['lab_sessions'] }}/{{ $item['course']->total_sessions }} • Classroom: {{ $item['classroom_sessions'] }}</p><div style="height:9px;background:#e6eef8;border-radius:9px;overflow:hidden;margin:14px 0"><div style="width:{{ $item['progress'] }}%;height:100%;background:var(--teal)"></div></div><div class="metric">{{ $item['required_sessions'] ? ($item['eligible'] ? 'Exam Eligible' : $item['required_sessions'].' lab sessions required') : 'Advanced Module' }}</div></article>
@endforeach
</div></div></div></div>
@endsection
