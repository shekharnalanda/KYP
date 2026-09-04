@extends('layouts.app')
@section('title', 'Learning Sessions | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel">
<div style="display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap"><div><span class="eyebrow" style="color:#057d78;background:#e8fffd">270-HOUR LEARNING PATH</span><h1>क्रमबद्ध Learning Sessions</h1><p style="color:var(--muted)">Hindi-first classroom तथा practical lab content।</p></div><a class="btn btn-light" href="{{ auth()->user()->hasRole('student') ? route('student.dashboard') : route('teacher.dashboard') }}">Dashboard</a></div>
@if(session('status'))<div style="margin-top:18px;padding:14px;border-radius:12px;background:#e8fffd;color:#057d78">{{ session('status') }}</div>@endif
@foreach($courses as $course)
<section style="margin-top:28px"><div style="display:flex;justify-content:space-between;align-items:end;gap:12px"><div><h2>{{ $course->code }} — {{ $course->name }}</h2><p>{{ $course->total_sessions }} sessions • {{ $course->total_hours }} hours</p></div></div>
<div class="cards">
@foreach($course->sessions as $session)
@php($done = $completedIds->contains($session->id))
<article class="card"><div class="icon">{{ $session->session_number }}</div><h3>{{ $session->title_hi }}</h3><p>{{ $session->duration_minutes }} minutes • {{ ucfirst($session->delivery_mode) }}</p><a class="btn {{ $done ? 'btn-light' : 'btn-primary' }}" href="{{ route('learning.show', $session) }}">{{ $done ? 'Completed — Review' : 'Open Session' }}</a></article>
@endforeach
</div></section>
@endforeach
</div></div></div>
@endsection
