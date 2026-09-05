@extends('layouts.app')
@section('title','Documents & Results | KYP')
@section('body')

<x-admin-shell
 active="results"
 eyebrow="DOCUMENT & RESULT CONTROL"
 title="Results, Marksheets & Certificates"
 subtitle="Single और bulk academic documents का central administration."
>

@if(session('status'))
<div class="notice success">{{ session('status') }}</div>
@endif

@if(isset($errors) && $errors->any())
<div class="notice error-box">
@foreach($errors->all() as $error)
<div>{{ $error }}</div>
@endforeach
</div>
@endif

<style>
.doc-summary{
 display:grid;
 grid-template-columns:repeat(3,1fr);
 gap:14px;
 margin-bottom:22px
}
.doc-summary>div{
 padding:17px;
 border:1px solid #dce7f4;
 border-radius:15px;
 background:#f8fbff
}
.doc-summary strong{
 display:block;
 color:#062b63;
 font-size:25px
}
.doc-summary span{
 color:#718198;
 font-size:11px
}
.doc-result{
 border:1px solid #dce7f4;
 border-radius:17px;
 padding:18px;
 margin-bottom:15px
}
.doc-head{
 display:flex;
 justify-content:space-between;
 gap:15px;
 align-items:flex-start;
 flex-wrap:wrap
}
.doc-actions{
 display:flex;
 gap:8px;
 flex-wrap:wrap;
 margin-top:13px
}
@media(max-width:650px){
 .doc-summary{grid-template-columns:1fr}
}
</style>

@php
$publishedCount = \App\Models\Result::whereNotNull('published_at')->count();
$passedCount = \App\Models\Result::where('result_status','pass')
    ->whereNotNull('published_at')->count();
$certificateCount = \App\Models\Certificate::where('status','issued')->count();
@endphp

<div class="doc-summary">
<div>
<strong>{{ $publishedCount }}</strong>
<span>Published Results</span>
</div>
<div>
<strong>{{ $passedCount }}</strong>
<span>Passed Students</span>
</div>
<div>
<strong>{{ $certificateCount }}</strong>
<span>Issued Certificates</span>
</div>
</div>

<form method="POST"
      action="{{ route('admin.results.bulk') }}"
      target="_blank"
      id="bulk-document-form"
      style="padding:17px;border:1px solid #cbddec;border-radius:16px;background:#f8fbff;margin-bottom:22px">
@csrf

<div style="display:flex;gap:12px;align-items:end;flex-wrap:wrap">

<div class="field" style="margin:0">
<label>Bulk Document Type</label>
<select name="document_type" required>
<option value="marksheet">Marksheets</option>
<option value="certificate">Certificates</option>
</select>
</div>

<button class="btn btn-primary">
Print Selected Documents
</button>

<div style="color:var(--muted);font-size:12px">
नीचे published results select करें। अधिकतम 100 records।
</div>

</div>
</form>

<div>

@forelse($results as $result)

<div class="doc-result">

<div class="doc-head">

<div style="display:flex;gap:12px;align-items:flex-start">

@if($result->published_at)
<input type="checkbox"
       name="result_ids[]"
       value="{{ $result->id }}"
       form="bulk-document-form"
       style="margin-top:6px">
@endif

<div>
<h3 style="margin:0 0 5px">
{{ $result->user->name }}
</h3>

<div style="color:var(--muted);font-size:12px">
{{ $result->user->student_id }}
•
{{ $result->examAttempt->exam->course->code }}
•
Score {{ $result->final_score }}/100
</div>
</div>

</div>

<div>
<strong>{{ strtoupper($result->result_status) }}</strong>
</div>

</div>

<div class="doc-actions">

@if(!$result->published_at)

<form method="POST"
      action="{{ route('admin.results.publish',$result) }}">
@csrf

<input type="hidden"
       name="result_status"
       value="{{ $result->result_status }}">

<button class="btn btn-primary">
Publish Result
</button>
</form>

@else

<a class="btn btn-light"
   target="_blank"
   href="{{ route('admin.result.marksheet',$result) }}">
Marksheet
</a>

@if($result->certificate)
<a class="btn btn-light"
   target="_blank"
   href="{{ route('admin.result.certificate',$result->certificate) }}">
Certificate
</a>
@endif

@if($result->user->id_card_token)
<a class="btn btn-light"
   target="_blank"
   href="{{ route('admin.student.id-card',$result->user) }}">
ID Card
</a>
@endif

@endif

</div>

</div>

@empty
<div class="panel-card">
No results found.
</div>
@endforelse

</div>

<div style="margin-top:20px">
{{ $results->links() }}
</div>

</x-admin-shell>
@endsection
