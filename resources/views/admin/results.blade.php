@extends('layouts.app')
@section('title', 'Result Publishing | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel">
<div style="display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap"><div><span class="eyebrow" style="color:#057d78;background:#e8fffd">RESULT CONTROL</span><h1>Results, Marksheets & Certificates</h1><p style="color:var(--muted)">Result publication तथा अधिकतम 100 documents का bulk print/PDF.</p></div><a class="btn btn-light" href="{{ route('admin.dashboard') }}">Dashboard</a></div>
@if(session('status'))<div style="margin-top:18px;padding:14px;border-radius:12px;background:#e8fffd;color:#057d78">{{ session('status') }}</div>@endif
@if($errors->any())<div style="margin-top:18px;padding:14px;border-radius:12px;background:#fff0f0;color:#a52020">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('admin.results.bulk') }}">@csrf
<div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:20px">
<select name="document_type" style="padding:10px;border-radius:10px"><option value="marksheet">Bulk Marksheets</option><option value="certificate">Bulk Certificates</option></select>
<button class="btn btn-primary">Open Print/PDF View</button><span style="color:var(--muted)">Published records चुनें</span>
</div>
<div style="overflow:auto;margin-top:18px"><table style="width:100%;border-collapse:collapse"><thead><tr><th><input type="checkbox" onclick="document.querySelectorAll('.result-check').forEach(c=>c.checked=this.checked)"></th><th>Student</th><th>Exam</th><th>Raw</th><th>Final</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($results as $result)<tr>
<td><input class="result-check" type="checkbox" name="result_ids[]" value="{{ $result->id }}" @disabled(!$result->published_at)></td>
<td>{{ $result->user->student_id ?: 'KYP' }} — {{ $result->user->name }}</td>
<td>{{ $result->examAttempt?->exam?->course?->code }} {{ $result->examAttempt?->exam?->title_en }}</td>
<td>{{ number_format($result->total_raw, 2) }}/500</td><td>{{ number_format($result->final_score, 2) }}/100</td><td>{{ strtoupper($result->result_status) }}</td>
<td><form method="POST" action="{{ route('admin.results.publish', $result) }}">@csrf<select name="result_status" style="padding:8px;border-radius:8px"><option value="pass">Pass</option><option value="fail">Fail</option></select><button class="btn btn-primary" style="padding:8px 12px">Publish</button></form></td>
</tr>@empty<tr><td colspan="7">No submitted results yet.</td></tr>@endforelse
</tbody></table></div>
</form>
<div style="margin-top:20px">{{ $results->links() }}</div>
</div></div></div>
@endsection
