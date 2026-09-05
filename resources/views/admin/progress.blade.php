@extends('layouts.app')
@section('title', 'Student Progress | KYP')
@section('body')

<x-admin-shell
    active="progress"
    eyebrow="ELIGIBILITY CONTROL"
    title="Student Progress"
    subtitle="Course-wise lab, classroom progress और final exam eligibility की live directory।"
>
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

</x-admin-shell>

@endsection
