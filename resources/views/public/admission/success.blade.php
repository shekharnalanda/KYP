@extends('layouts.app')
@section('title','Admission Submitted | KYP')
@section('body')
<div class="panel-shell"><div class="container">
<div class="panel" style="max-width:720px;margin:auto;text-align:center">
<img src="{{ asset('images/kyp-logo.webp') }}" style="width:85px" alt="KYP">
<h1>Admission Application Submitted</h1>
<div class="notice success">
Application Number: <strong>{{ $admission->application_number }}</strong>
</div>
<p>{{ $admission->name }}</p>
<p>{{ $admission->branch->name }} • {{ $admission->course->code }}</p>
<p>Admin verification के बाद admission status आगे process किया जाएगा।</p>
<a class="btn btn-primary" href="{{ route('home') }}">Back to Home</a>
</div></div></div>
@endsection
