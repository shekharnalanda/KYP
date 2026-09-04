@extends('layouts.app')
@section('title', 'Online Exams | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel">
<div style="display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap">
<div><span class="eyebrow" style="color:#057d78;background:#e8fffd">ONLINE EXAM</span><h1>ऑनलाइन परीक्षा</h1><p style="color:var(--muted)">Syllabus-linked bilingual examinations और eligibility status।</p></div>
<a class="btn btn-light" href="{{ route('student.dashboard') }}">Dashboard</a>
</div>
<div class="cards" style="margin-top:28px">
@forelse($exams as $item)
<article class="card">
<div class="icon">{{ $item['exam']->course->code }}</div>
<h3>{{ $item['exam']->title_hi }}</h3>
<p>{{ $item['exam']->title_en }}</p>
<p>{{ $item['exam']->duration_minutes }} minutes • {{ $item['exam']->total_questions }} questions • {{ number_format($item['exam']->max_marks, 0) }} marks</p>
<div class="metric" style="color:{{ $item['eligible'] ? '#057d78' : '#b45309' }}">
@if($item['eligible']) Eligible — परीक्षा उपलब्ध @elseif($item['required_sessions']) {{ $item['lab_sessions'] }}/{{ $item['required_sessions'] }} lab sessions; अभी locked @else Eligibility policy pending @endif
</div>
</article>
@empty
<article class="card"><h3>Exam schedule जल्द उपलब्ध होगा</h3><p>Published examinations यहाँ दिखाई देंगे। आपकी attendance eligibility अपने-आप लागू होगी।</p></article>
@endforelse
</div>
</div></div></div>
@endsection
