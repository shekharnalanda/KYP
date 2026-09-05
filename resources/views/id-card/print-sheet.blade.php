@extends('layouts.app')
@section('title','KYP ID Cards')
@section('body')

@include('id-card.styles')

<div class="bulk-toolbar">
<button class="btn btn-primary" onclick="window.print()">
Print / Save PDF
</button>

@if($adminMode ?? false)
<a class="btn btn-light" href="{{ route('admin.users') }}">
Back to Students
</a>
@endif
</div>

@foreach($students->chunk(2) as $pair)

<div class="id-a4-sheet">

@foreach($pair as $student)
@include('id-card.card-pair',['student'=>$student])
@endforeach

</div>

@endforeach

@endsection
