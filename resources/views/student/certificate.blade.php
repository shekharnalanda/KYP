@extends('layouts.app')
@section('title','KYP Certificate')
@section('body')

<x-kyp-document-style />

@php
$cit = $document['moduleResults']['CIT'] ?? null;
$cls = $document['moduleResults']['CLS'] ?? null;
$css = $document['moduleResults']['CSS'] ?? null;

$ready = $document['allCoreReady']
    && $document['aiCompleted'];

$verifyUrl = route(
    'certificate.verify',
    $certificate->qr_token
);

$certNo = str_replace(
    'KYP-',
    'KYP/',
    $certificate->serial_number
);
@endphp

@if(!$ready)
<div class="container" style="padding:30px">
<div class="panel" style="max-width:760px;margin:auto">
<h2 style="color:#b42318">Certificate not ready</h2>
<p>
CIT, CLS और CSS के published passing results तथा
AI & Digital Skills completion आवश्यक है।
</p>
</div>
</div>
@else

<div class="kdoc-page">
<div class="kdoc-frame">
<div class="kdoc-inner">

<div class="kdoc-head">

<div>
<img class="kdoc-logo"
 src="{{ asset('images/mci-circle-logo.png') }}"
 alt="MCI">
</div>

<div class="kdoc-title">
<h1>CERTIFICATE</h1>
<h2>OF COURSE COMPLETION</h2>
<p>VOCATIONAL • EMPLOYABILITY • DIGITAL SKILLS</p>
</div>

<div>
<img class="kdoc-logo"
 src="{{ asset('images/kyp-logo.webp') }}"
 alt="KYP">
</div>

</div>

<div class="kdoc-meta">
<div>
CERTIFICATE NO.<br>
{{ $certNo }}
</div>

<div style="text-align:right">
ISSUED: {{ $certificate->issued_at->format('d F Y') }}<br>
ACADEMIC SESSION {{ $certificate->issued_at->format('Y') }}
</div>
</div>

<div class="kdoc-student">

<div class="intro">This is to certify that</div>

<h3>{{ $certificate->user->name }}</h3>

<div class="kdoc-line"></div>

<p>
Student ID:
<strong>{{ $certificate->user->student_id }}</strong>
&nbsp; • &nbsp;
S/o / D/o:
<strong>{{ $document['parentName'] }}</strong>
</p>

<p>
has successfully completed the
<strong>Kushal Youth Program</strong> conducted by
</p>

<p class="kdoc-org">
MCI EDUCATIONAL GROUP
</p>

<p class="kdoc-trust">
Operated under Chandrashekhar & Narayan Educational Trust
</p>

</div>

<div class="kdoc-performance">

<div class="kdoc-performance-title">
CORE COURSE PERFORMANCE
</div>

<div class="kdoc-cards">

<div class="kdoc-card">
<b>CIT</b>
<span>Information Technology</span>
<strong>{{ number_format($cit->final_score,0) }}%</strong>
</div>

<div class="kdoc-card">
<b>CLS</b>
<span>Language Skills</span>
<strong>{{ number_format($cls->final_score,0) }}%</strong>
</div>

<div class="kdoc-card">
<b>CSS</b>
<span>Soft Skills</span>
<strong>{{ number_format($css->final_score,0) }}%</strong>
</div>

</div>
</div>

<div class="kdoc-ai">
AI & DIGITAL SKILLS • 15 SESSIONS • 30 HOURS •
SUCCESSFULLY COMPLETED
</div>

<div class="kdoc-summary">

<small>
Overall performance calculated from CIT, CLS and CSS assessments
</small>

<strong>
OVERALL: {{ number_format($document['overall'],2) }}%
&nbsp; • &nbsp;
GRADE: {{ $document['grade'] }}
&nbsp; • &nbsp;
RESULT: {{ $document['division'] }}
</strong>

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

<div class="kdoc-qr">

<img
 src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($verifyUrl) }}"
 alt="Verification QR">

<b>SCAN TO VERIFY AUTHENTICITY</b>

<div class="kdoc-verify">
{{ $verifyUrl }}
</div>

</div>

<div class="kdoc-note">
Digitally generated from the KYP portal •
This certificate confirms completion of an independent vocational
education program of MCI Educational Group.
</div>

</div>
</div>
</div>

<div class="kdoc-actions">
<button class="btn btn-primary"
 onclick="window.print()">
Print / Save PDF
</button>
</div>

@endif

@endsection
