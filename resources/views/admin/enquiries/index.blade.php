@extends('layouts.app')
@section('title','Enquiries | KYP')
@section('body')

<x-admin-shell
 active="enquiries"
 eyebrow="ENQUIRY CONTROL"
 title="Enquiries"
 subtitle="Branch-wise enquiries, follow-up और status management."
>

@if(session('success'))
<div class="notice success">{{ session('success') }}</div>
@endif

@if(isset($errors) && $errors->any())
<div class="notice error-box">
@foreach($errors->all() as $error)
<div>{{ $error }}</div>
@endforeach
</div>
@endif

<form method="GET"
      style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:22px">

<select name="branch_id">
<option value="">All Branches</option>
@foreach($branches as $branch)
<option value="{{ $branch->id }}"
        @selected(request('branch_id')==$branch->id)>
{{ $branch->name }}
</option>
@endforeach
</select>

<select name="status">
<option value="">All Status</option>
@foreach(['new','contacted','follow_up','closed'] as $status)
<option value="{{ $status }}"
        @selected(request('status')===$status)>
{{ ucfirst(str_replace('_',' ',$status)) }}
</option>
@endforeach
</select>

<button class="btn btn-light">Filter</button>
</form>

<div style="display:grid;gap:16px">

@forelse($enquiries as $e)

<form method="POST"
      action="{{ route('admin.enquiries.update',$e) }}"
      style="border:1px solid #dce7f4;border-radius:17px;padding:18px">
@csrf
@method('PUT')

<div style="display:flex;justify-content:space-between;gap:15px;flex-wrap:wrap">

<div>
<h3 style="margin:0 0 6px">{{ $e->name }}</h3>

<div style="color:var(--muted)">
{{ $e->enquiry_number }}
</div>

<p style="margin:8px 0 3px">
<strong>Mobile:</strong> {{ $e->mobile }}
@if($e->email)
• {{ $e->email }}
@endif
</p>

<p style="margin:3px 0">
<strong>Branch:</strong>
{{ $e->branch?->name ?: '—' }}
</p>

<p style="margin:3px 0">
<strong>Course:</strong>
{{ $e->course?->code ?: 'Not selected' }}
</p>

@if($e->qualification)
<p style="margin:3px 0">
<strong>Qualification:</strong>
{{ $e->qualification }}
</p>
@endif

@if($e->message)
<div style="margin-top:12px;padding:12px;background:#f7faff;border-radius:11px">
{{ $e->message }}
</div>
@endif
</div>

<div>
<span class="eyebrow"
      style="color:#057d78;background:#e8fffd">
{{ strtoupper(str_replace('_',' ',$e->status)) }}
</span>
</div>

</div>

<div style="display:grid;grid-template-columns:220px 1fr auto;gap:12px;align-items:end;margin-top:17px">

<div class="field" style="margin:0">
<label>Status</label>
<select name="status">
@foreach(['new','contacted','follow_up','closed'] as $status)
<option value="{{ $status }}"
        @selected($e->status===$status)>
{{ ucfirst(str_replace('_',' ',$status)) }}
</option>
@endforeach
</select>
</div>

<div class="field" style="margin:0">
<label>Admin Note</label>
<textarea name="admin_note"
          rows="2">{{ $e->admin_note }}</textarea>
</div>

<button class="btn btn-primary">
Save
</button>

</div>

@if($e->handler)
<small style="display:block;color:var(--muted);margin-top:10px">
Handled by {{ $e->handler->name }}
@if($e->contacted_at)
• {{ $e->contacted_at->format('d M Y h:i A') }}
@endif
</small>
@endif

</form>

@empty

<div class="panel-card">
No enquiries found.
</div>

@endforelse

</div>

<div style="margin-top:20px">
{{ $enquiries->links() }}
</div>

</x-admin-shell>

@endsection
