@extends('layouts.app')
@section('title', 'Learning Sessions | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel">
<div style="display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap"><div><span class="eyebrow" style="color:#057d78;background:#e8fffd">270-HOUR INTERACTIVE LEARNING PATH</span><h1>क्रमबद्ध Learning Sessions</h1><p style="color:var(--muted)">हर session में active timer, interactive steps, knowledge check और practical work।</p></div><a class="btn btn-light" href="{{ route('dashboard') }}">Dashboard</a></div>
@if(session('status'))<div style="margin-top:18px;padding:14px;border-radius:12px;background:#e8fffd;color:#057d78">{{ session('status') }}</div>@endif
@foreach($courses as $course)
<section style="margin-top:28px"><div style="display:flex;justify-content:space-between;align-items:end;gap:12px"><div><h2>{{ $course->code }} — {{ $course->name }}</h2><p>{{ $course->total_sessions }} sessions • {{ $course->total_hours }} hours</p></div></div>
<div class="cards">
@foreach($course->sessions as $session)
@php
$done = $completedIds->contains($session->id);
$record = $progressMap->get($session->id);
$percent = $done ? 100 : ($record ? min(99, round(($record->active_seconds / max(1, $session->required_active_minutes * 60)) * 100)) : 0);
@endphp
<article class="card"><div class="icon">{{ $session->session_number }}</div><h3>{{ $session->title_hi }}</h3><p>{{ $session->duration_minutes }} minutes • 8 interactive phases</p>
@if(auth()->user()->hasRole('student'))<div style="height:8px;background:#e6eef8;border-radius:9px;overflow:hidden;margin:12px 0"><div style="width:{{ $percent }}%;height:100%;background:var(--teal)"></div></div><small style="color:var(--muted)">{{ $percent }}% active progress</small>@endif
<div style="margin-top:14px"><a class="btn {{ $done ? 'btn-light' : 'btn-primary' }}" href="{{ route('learning.show', $session) }}">{{ $done ? 'Completed — Review' : ($record ? 'Resume Session' : 'Start Session') }}</a></div></article>
@endforeach
</div></section>
@endforeach
</div></div></div>
@endsection
