@extends('layouts.app')
@section('title', 'Exam Result | KYP')
@section('body')

<x-portal-shell
 active="exams"
 eyebrow="AUTO EVALUATED RESULT"
 title="परीक्षा परिणाम"
 subtitle="Online exam, lab और classroom components का consolidated result।"
>
<span class="eyebrow" style="color:#057d78;background:#e8fffd">AUTO EVALUATED</span><h1>परीक्षा परिणाम</h1>
<p>{{ $result->examAttempt->exam->title_hi }} • {{ $result->examAttempt->exam->title_en }}</p>
<div class="panel-grid"><div class="panel-card"><h3>Exam Raw</h3><div class="metric">{{ number_format($result->exam_raw, 2) }}/200</div></div><div class="panel-card"><h3>Exam Converted</h3><div class="metric">{{ number_format($result->exam_final, 2) }}/40</div></div><div class="panel-card"><h3>Lab Component</h3><div class="metric">{{ number_format($result->lab_final, 2) }}/40</div></div><div class="panel-card"><h3>Classroom Component</h3><div class="metric">{{ number_format($result->classroom_final, 2) }}/20</div></div><div class="panel-card"><h3>Current Final Score</h3><div class="metric">{{ number_format($result->final_score, 2) }}/100</div></div><div class="panel-card"><h3>Status</h3><div class="metric">{{ strtoupper($result->result_status) }}</div></div></div>
<p style="color:var(--muted);margin-top:20px">@if($result->published_at) Final result publish हो चुका है। @else Lab और classroom marks जुड़ने के बाद admin final result publish करेगा। @endif</p>
<div style="display:flex;gap:10px;flex-wrap:wrap"><a class="btn btn-light" href="{{ route('student.exams') }}">परीक्षाओं पर लौटें</a>@if($result->published_at)<a class="btn btn-primary" href="{{ route('student.marksheet', $result) }}">Marksheet</a>@if($result->certificate)<a class="btn btn-primary" href="{{ route('student.certificate', $result->certificate) }}">Certificate</a>@endif @endif</div>

</x-portal-shell>

@endsection
