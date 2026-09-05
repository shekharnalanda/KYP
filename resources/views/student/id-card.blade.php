@extends('layouts.app')
@section('title','Student ID Card | KYP')
@section('body')

<x-portal-shell
 active="dashboard"
 eyebrow="DIGITAL STUDENT ID"
 title="Student Identity Card"
 subtitle="Front & Back QR-verifiable KYP identity card."
>

@include('id-card.styles')

<div class="id-screen-actions">
<button class="btn btn-primary" onclick="window.print()">
Print / Save PDF
</button>

<a class="btn btn-light"
   href="{{ route('student.dashboard') }}">
Dashboard
</a>
</div>

<div class="id-a4-sheet single-sheet">
@include('id-card.card-pair',['student'=>$student])
</div>

</x-portal-shell>
@endsection
