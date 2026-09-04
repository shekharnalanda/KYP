@extends('layouts.app')
@section('title', 'Exam Result | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel">
<span class="eyebrow" style="color:#057d78;background:#e8fffd">AUTO EVALUATED</span>
<h1>परीक्षा परिणाम</h1>
<p>{{ $result->examAttempt->exam->title_hi }} • {{ $result->examAttempt->exam->title_en }}</p>
<div class="panel-grid">
<div class="panel-card"><h3>Exam Raw</h3><div class="metric">{{ number_format($result->exam_raw, 2) }}/200</div></div>
<div class="panel-card"><h3>Exam Converted</h3><div class="metric">{{ number_format($result->exam_final, 2) }}/40</div></div>
<div class="panel-card"><h3>Lab Component</h3><div class="metric">{{ number_format($result->lab_final, 2) }}/40</div></div>
<div class="panel-card"><h3>Classroom Component</h3><div class="metric">{{ number_format($result->classroom_final, 2) }}/20</div></div>
<div class="panel-card"><h3>Current Final Score</h3><div class="metric">{{ number_format($result->final_score, 2) }}/100</div></div>
<div class="panel-card"><h3>Status</h3><div class="metric">{{ strtoupper($result->result_status) }}</div></div>
</div>
<p style="color:var(--muted);margin-top:20px">Lab और classroom marks जुड़ने के बाद final result, marksheet तथा certificate publish होंगे।</p>
<a class="btn btn-primary" href="{{ route('student.exams') }}">परीक्षाओं पर लौटें</a>
</div></div></div>
@endsection
