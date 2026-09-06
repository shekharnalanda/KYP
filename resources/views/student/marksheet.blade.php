@extends('layouts.app')
@section('title','KYP Marksheet')
@section('body')

<x-kyp-document-style />

@php
$cit = $document['moduleResults']['CIT'] ?? null;
$cls = $document['moduleResults']['CLS'] ?? null;
$css = $document['moduleResults']['CSS'] ?? null;

$ready = $document['allCoreReady'];

$verifyUrl = $result->certificate
 ? route(
     'certificate.verify',
     $result->certificate->qr_token
   )
 : null;
@endphp

@if(!$ready)

<div class="container" style="padding:30px">
<div class="panel" style="max-width:760px;margin:auto">

<h2 style="color:#b42318">
Integrated Marksheet not ready
</h2>

<p>
CIT, CLS और CSS के published results आवश्यक हैं।
</p>

</div>
</div>

@else

<div class="kdoc-page">
<div class="kdoc-frame">
<div class="kdoc-inner">

<div class="kdoc-head">

<img class="kdoc-logo"
 src="{{ asset('images/mci-circle-logo.png') }}"
 alt="MCI">

<div class="kdoc-title">
<h1>STATEMENT OF MARKS</h1>
<h2>KUSHAL YOUTH PROGRAM</h2>
<p>VOCATIONAL • EMPLOYABILITY • DIGITAL SKILLS</p>
</div>

<img class="kdoc-logo"
 src="{{ asset('images/kyp-logo.webp') }}"
 alt="KYP">

</div>

<div class="kmark-title">
<h1>MARKSHEET</h1>
<h2>Integrated Program Performance Record</h2>
</div>

<div class="kmark-info">

<div>
<strong>Student Name:</strong>
{{ $result->user->name }}
</div>

<div>
<strong>Student ID:</strong>
{{ $result->user->student_id }}
</div>

<div>
<strong>Parent / Guardian:</strong>
{{ $document['parentName'] }}
</div>

<div>
<strong>Program:</strong>
Kushal Youth Program (KYP)
</div>

<div>
<strong>Total Duration:</strong>
270 Hours
</div>

<div>
<strong>Total Sessions:</strong>
135
</div>

</div>

<table class="kmark-table">

<thead>
<tr>
<th>Module</th>
<th>Sessions</th>
<th>Hours</th>
<th>Performance</th>
<th>Status</th>
</tr>
</thead>

<tbody>

<tr>
<td>
<strong>CIT</strong><br>
Information Technology
</td>
<td>60</td>
<td>120</td>
<td>{{ number_format($cit->final_score,2) }}%</td>
<td>PASS</td>
</tr>

<tr>
<td>
<strong>CLS</strong><br>
Language Skills
</td>
<td>40</td>
<td>80</td>
<td>{{ number_format($cls->final_score,2) }}%</td>
<td>PASS</td>
</tr>

<tr>
<td>
<strong>CSS</strong><br>
Soft Skills
</td>
<td>20</td>
<td>40</td>
<td>{{ number_format($css->final_score,2) }}%</td>
<td>PASS</td>
</tr>

<tr>
<td>
<strong>AI-DM</strong><br>
AI Technology & Digital Marketing
</td>
<td>15</td>
<td>30</td>
<td>
{{ $document['aiCompleted']
   ? 'Successfully Completed'
   : 'Pending' }}
</td>
<td>
{{ $document['aiCompleted'] ? 'COMPLETE' : 'PENDING' }}
</td>
</tr>

</tbody>
</table>

<div class="kmark-total">

<div>
<span>OVERALL</span>
<strong>{{ number_format($document['overall'],2) }}%</strong>
</div>

<div>
<span>GRADE</span>
<strong>{{ $document['grade'] }}</strong>
</div>

<div>
<span>RESULT</span>
<strong>{{ $document['division'] }}</strong>
</div>

</div>

<div class="kdoc-ai">
AI & DIGITAL SKILLS • 15 SESSIONS • 30 HOURS •
{{ $document['aiCompleted']
    ? 'SUCCESSFULLY COMPLETED'
    : 'COMPLETION PENDING' }}
</div>

<div class="kdoc-signatures">

<div class="kdoc-sign">
<strong>AUTHORIZED SIGNATORY</strong><br>
C&N Educational Trust
</div>

<div class="kdoc-trust-logo">
<img
 src="{{ asset('images/trust-logo.jpeg') }}"
 alt="Chandrashekhar & Narayan Educational Trust Logo">
</div>

<div class="kdoc-sign">
<strong>PROGRAM DIRECTOR</strong><br>
MCI Educational Group
</div>

</div>

@if($verifyUrl)

<div class="kdoc-qr">

<img
 src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($verifyUrl) }}"
 alt="Verification QR">

<b>SCAN TO VERIFY AUTHENTICITY</b>

<div class="kdoc-verify">
{{ $verifyUrl }}
</div>

</div>

@endif

<div class="kdoc-note">
This statement of marks is digitally generated from the
Kushal Youth Program portal of MCI Educational Group.
Overall performance is calculated from CIT, CLS and CSS assessments.
</div>

</div>
</div>
</div>

<div class="kdoc-actions">

<button
 class="btn btn-primary"
 onclick="window.print()">
Print / Save PDF
</button>

@if($result->certificate)

<a
 class="btn btn-light"
 href="{{ route('student.certificate',$result->certificate) }}">
Certificate
</a>

@endif

</div>

@endif

@endsection
