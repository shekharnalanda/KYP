@extends('layouts.app')
@section('title', 'Result Publishing | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel">
<div style="display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap"><div><span class="eyebrow" style="color:#057d78;background:#e8fffd">RESULT CONTROL</span><h1>Results, Marksheets & Certificates</h1><p style="color:var(--muted)">Exam result review तथा pass/fail publication.</p></div><a class="btn btn-light" href="{{ route('admin.dashboard') }}">Dashboard</a></div>
@if(session('status'))<div style="margin-top:18px;padding:14px;border-radius:12px;background:#e8fffd;color:#057d78">{{ session('status') }}</div>@endif
<div style="overflow:auto;margin-top:24px"><table style="width:100%;border-collapse:collapse"><thead><tr><th>Student</th><th>Exam</th><th>Raw</th><th>Final</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($results as $result)<tr>
<td>{{ $result->user->student_id ?: 'KYP' }} — {{ $result->user->name }}</td>
<td>{{ $result->examAttempt?->exam?->course?->code }} {{ $result->examAttempt?->exam?->title_en }}</td>
<td>{{ number_format($result->total_raw, 2) }}/500</td><td>{{ number_format($result->final_score, 2) }}/100</td><td>{{ strtoupper($result->result_status) }}</td>
<td><form method="POST" action="{{ route('admin.results.publish', $result) }}">@csrf<select name="result_status" style="padding:8px;border-radius:8px"><option value="pass">Pass</option><option value="fail">Fail</option></select><button class="btn btn-primary" style="padding:8px 12px">Publish</button></form></td>
</tr>@empty<tr><td colspan="6">No submitted results yet.</td></tr>@endforelse
</tbody></table></div>
<div style="margin-top:20px">{{ $results->links() }}</div>
</div></div></div>
@endsection
