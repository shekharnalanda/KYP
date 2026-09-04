@extends('layouts.app')
@section('title', 'Student Progress | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel">
<div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap">
<div><span class="eyebrow" style="color:#057d78;background:#e8fffd">ELIGIBILITY CONTROL</span><h1>Student Progress</h1><p style="color:var(--muted)">Course-wise lab, classroom progress और final exam eligibility की live directory।</p></div>
<a class="btn btn-light" href="{{ route('admin.dashboard') }}">← Dashboard</a>
</div>

<form method="GET" action="{{ route('admin.progress') }}" style="display:flex;gap:10px;flex-wrap:wrap;margin:22px 0">
<input name="search" value="{{ $search }}" placeholder="Name, email या Student ID" style="flex:1;min-width:230px;padding:12px 14px;border:1px solid #bdd3d1;border-radius:14px">
<button class="btn btn-primary">Search</button>
@if($search)<a class="btn btn-light" href="{{ route('admin.progress') }}">Clear</a>@endif
</form>

<div style="display:grid;gap:18px">
@forelse($students as $student)
<section style="padding:20px;border:1px solid #dce8e7;border-radius:22px;background:white">
<div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap">
<div><h2 style="margin:0 0 5px">{{ $student->name }}</h2><div style="color:var(--muted)">{{ $student->student_id ?: 'No Student ID' }} · {{ $student->email }}</div></div>
<span style="align-self:flex-start;padding:8px 13px;border-radius:999px;font-weight:800;background:{{ $student->status === 'active' ? '#e8fff3' : '#fff0f0' }};color:{{ $student->status === 'active' ? '#087443' : '#a52020' }}">{{ ucfirst($student->status) }}</span>
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:12px;margin-top:16px">
@foreach($progress->get($student->id, collect()) as $item)
<article style="padding:15px;border-radius:17px;background:#f7fbfb;border:1px solid #e0eceb">
<div style="display:flex;justify-content:space-between;gap:8px"><strong>{{ $item['course']->code }}</strong><span>{{ $item['progress'] }}%</span></div>
<div style="height:8px;background:#dfe9e8;border-radius:8px;overflow:hidden;margin:10px 0"><div style="height:100%;width:{{ $item['progress'] }}%;background:{{ $item['eligible'] ? '#087443' : '#057d78' }}"></div></div>
<div style="font-size:14px;color:var(--muted)">Lab {{ $item['lab_sessions'] }}/{{ $item['course']->total_sessions }} · Classroom {{ $item['classroom_sessions'] }}</div>
<div style="font-weight:800;margin-top:8px;color:{{ $item['eligible'] ? '#087443' : '#8a5a00' }}">
@if($item['required_sessions'] === null) Advanced module — threshold pending
@elseif($item['eligible']) Exam Eligible
@else {{ max(0, $item['required_sessions'] - $item['lab_sessions']) }} more lab sessions required
@endif
</div>
</article>
@endforeach
</div>
</section>
@empty <div class="panel-card">No student found.</div> @endforelse
</div>
<div style="margin-top:20px">{{ $students->links() }}</div>
</div></div></div>
@endsection
