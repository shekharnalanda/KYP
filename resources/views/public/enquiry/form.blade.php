@extends('layouts.app')
@section('title','Enquiry | KYP')
@section('body')

<div class="panel-shell">
<div class="container">
<div class="panel" style="max-width:850px;margin:auto">

<span class="eyebrow" style="color:#057d78;background:#e8fffd">KYP ENQUIRY</span>
<h1>Course Enquiry Form</h1>
<p style="color:var(--muted)">अपनी पसंदीदा branch और course चुनकर enquiry भेजें।</p>

@if($errors->any())
<div class="notice error-box">
@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('enquiry.store') }}">
@csrf

<div class="form-grid">
<div class="field">
<label>Name *</label>
<input name="name" value="{{ old('name') }}" required>
</div>

<div class="field">
<label>Mobile *</label>
<input name="mobile" value="{{ old('mobile') }}" required>
</div>

<div class="field">
<label>Email</label>
<input type="email" name="email" value="{{ old('email') }}">
</div>

<div class="field">
<label>Qualification</label>
<input name="qualification" value="{{ old('qualification') }}">
</div>

<div class="field">
<label>Preferred Branch *</label>
<select name="branch_id" required>
<option value="">Select Branch</option>
@foreach($branches as $branch)
<option value="{{ $branch->id }}">{{ $branch->name }}</option>
@endforeach
</select>
</div>

<div class="field">
<label>Interested Course</label>
<select name="course_id">
<option value="">Select Course</option>
@foreach($courses as $course)
<option value="{{ $course->id }}">{{ $course->code }} — {{ $course->name }}</option>
@endforeach
</select>
</div>
</div>

<div class="field">
<label>Message</label>
<textarea name="message" rows="4">{{ old('message') }}</textarea>
</div>

<button class="btn btn-primary" type="submit">Send Enquiry</button>
<a class="btn btn-light" href="{{ route('home') }}">Home</a>

</form>
</div>
</div>
</div>
@endsection
