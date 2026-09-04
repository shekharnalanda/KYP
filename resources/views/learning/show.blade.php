@extends('layouts.app')
@section('title', $session->title_hi.' | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel">
<div style="display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap"><div><span class="eyebrow" style="color:#057d78;background:#e8fffd">{{ $session->course->code }} • SESSION {{ $session->session_number }}</span><h1>{{ $session->title_hi }}</h1><p style="color:var(--muted)">{{ $session->title_en }} • {{ $session->duration_minutes }} minutes</p></div><a class="btn btn-light" href="{{ route('learning.index') }}">All Sessions</a></div>
<div class="cards" style="margin-top:24px">
<article class="card"><h3>🎯 सीखने के उद्देश्य</h3><p style="white-space:pre-line">{{ $session->objectives_hi }}</p></article>
<article class="card"><h3>📘 मुख्य पाठ</h3><p style="white-space:pre-line">{{ $session->lesson_content_hi }}</p></article>
<article class="card"><h3>👩‍🏫 Classroom Notes</h3><p style="white-space:pre-line">{{ $session->classroom_notes_hi }}</p></article>
<article class="card"><h3>💻 Lab Activity</h3><p style="white-space:pre-line">{{ $session->lab_activity_hi }}</p></article>
<article class="card"><h3>✅ Session Check</h3><p style="white-space:pre-line">{{ $session->assessment_prompt_hi }}</p></article>
</div>
@if(auth()->user()->hasRole('student'))
@if($completed)<div style="margin-top:22px;padding:14px;border-radius:12px;background:#e8fffd;color:#057d78">यह session complete है।</div>
@else<form method="POST" action="{{ route('learning.complete', $session) }}" style="margin-top:22px">@csrf<button class="btn btn-primary">Lab Activity Complete करें</button></form>@endif
@endif
</div></div></div>
@endsection
