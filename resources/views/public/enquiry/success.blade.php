@extends('layouts.app')
@section('title','Enquiry Submitted | KYP')
@section('body')
<div class="panel-shell"><div class="container">
<div class="panel" style="max-width:720px;margin:auto;text-align:center">
<h1>Enquiry Received</h1>
<div class="notice success">
Enquiry Number: <strong>{{ $enquiry->enquiry_number }}</strong>
</div>
<p>हमारी team आपकी enquiry को संबंधित branch के अनुसार process करेगी।</p>
<a class="btn btn-primary" href="{{ route('home') }}">Back to Home</a>
</div></div></div>
@endsection
