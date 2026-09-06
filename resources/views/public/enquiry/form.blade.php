@extends('layouts.app')
@section('title','Enquiry | KYP')
@section('body')

<x-public-program-style />

<div class="kpv-page">
<div class="kpv-shell">

<x-public-program-head />

<div class="kpv-body">

@if($errors->any())
<div class="kpv-errors">
@foreach($errors->all() as $error)
<div>• {{ $error }}</div>
@endforeach
</div>
@endif

<form method="POST"
      action="{{ route('enquiry.store') }}">
@csrf

<section class="kpv-section">

<h2>KYP Enquiry</h2>

<p>
Kushal Youth Program के बारे में जानकारी प्राप्त करने के लिए
अपनी details और preferred branch दर्ज करें।
</p>

<div class="kpv-grid">

<div class="kpv-field">
<label>Selected Program</label>

<div class="kpv-selected">
<strong>Kushal Youth Program (KYP)</strong><br>
135 Sessions • 270 Hours • All 4 Modules Included
</div>
</div>

<div class="kpv-field">
<label>Preferred Branch *</label>

<select name="branch_id" required>

<option value="">
Select Branch
</option>

@foreach($branches as $branch)

<option
 value="{{ $branch->id }}"
 @selected(old('branch_id')==$branch->id)>

{{ $branch->name }}

</option>

@endforeach

</select>
</div>

</div>
</section>


<section class="kpv-section">

<h2>Contact Details</h2>

<p>
Email OTP verification के बाद ही enquiry submit होगी।
</p>

<div class="kpv-grid">

<div class="kpv-field">
<label>Full Name *</label>

<input
 name="name"
 value="{{ old('name') }}"
 required>
</div>


<div class="kpv-field">
<label>Mobile Number *</label>

<input
 name="mobile"
 value="{{ old('mobile') }}"
 required>
</div>


<div class="kpv-field full">

<x-email-otp
 purpose="enquiry"
 input-id="enquiry-email"
/>

</div>


<div class="kpv-field">
<label>Qualification</label>

<input
 name="qualification"
 value="{{ old('qualification') }}">
</div>


<div class="kpv-field full">
<label>Your Message</label>

<textarea
 name="message"
 placeholder="KYP admission, course structure, branch या अन्य जानकारी के बारे में अपना प्रश्न लिखें।">{{ old('message') }}</textarea>

</div>

</div>
</section>


<div class="kpv-actions">

<button
 class="btn btn-primary"
 type="submit">

Submit KYP Enquiry

</button>

<a
 class="btn btn-light"
 href="{{ route('home') }}">

Back to Home

</a>

</div>

</form>

</div>
</div>
</div>

<x-email-otp-script />

@endsection
