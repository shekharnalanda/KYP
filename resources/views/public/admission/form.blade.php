@extends('layouts.app')
@section('title','Online Admission | KYP')
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
      action="{{ route('admission.store') }}"
      enctype="multipart/form-data">
@csrf

<section class="kpv-section">
<h2>Admission & Branch Selection</h2>
<p>
Program पहले से Kushal Youth Program (KYP) selected है।
केवल अपनी preferred branch चुनें।
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
<option value="">Select Branch</option>
@foreach($branches as $branch)
<option value="{{ $branch->id }}"
 @selected(old('branch_id')==$branch->id)>
{{ $branch->name }}
</option>
@endforeach
</select>
</div>

</div>
</section>

<section class="kpv-section">
<h2>Student Details</h2>
<p>Student की मूल जानकारी एवं verified email दर्ज करें।</p>

<div class="kpv-grid">

<div class="kpv-field">
<label>Full Name *</label>
<input name="name" value="{{ old('name') }}" required>
</div>

<div class="kpv-field">
<label>Date of Birth *</label>
<input type="date"
       name="date_of_birth"
       value="{{ old('date_of_birth') }}"
       required>
</div>

<div class="kpv-field">
<label>Gender *</label>
<select name="gender" required>
<option value="">Select Gender</option>
@foreach(['Male','Female','Other'] as $gender)
<option value="{{ $gender }}"
 @selected(old('gender')===$gender)>
{{ $gender }}
</option>
@endforeach
</select>
</div>

<div class="kpv-field">
<label>Mobile Number *</label>
<input name="mobile"
       value="{{ old('mobile') }}"
       required>
</div>

<div class="kpv-field full">
<x-email-otp
 purpose="admission"
 input-id="admission-email"
/>
</div>

<div class="kpv-field">
<label>Qualification *</label>
<input name="qualification"
       value="{{ old('qualification') }}"
       required>
</div>

<div class="kpv-field">
<label>Student Photo * (max 2 MB)</label>
<input type="file"
       name="photo"
       accept="image/jpeg,image/png,image/webp"
       required>
</div>

</div>
</section>

<section class="kpv-section">
<h2>Parent / Guardian Details</h2>

<div class="kpv-grid">

<div class="kpv-field">
<label>Father Name</label>
<input name="father_name"
       value="{{ old('father_name') }}">
</div>

<div class="kpv-field">
<label>Mother Name</label>
<input name="mother_name"
       value="{{ old('mother_name') }}">
</div>

<div class="kpv-field">
<label>Guardian Name</label>
<input name="guardian_name"
       value="{{ old('guardian_name') }}">
</div>

<div class="kpv-field">
<label>Guardian Mobile</label>
<input name="guardian_mobile"
       value="{{ old('guardian_mobile') }}">
</div>

</div>
</section>

<section class="kpv-section">
<h2>Address</h2>

<div class="kpv-grid">

<div class="kpv-field full">
<label>Full Address *</label>
<textarea name="address"
          required>{{ old('address') }}</textarea>
</div>

<div class="kpv-field">
<label>City</label>
<input name="city" value="{{ old('city') }}">
</div>

<div class="kpv-field">
<label>District *</label>
<input name="district"
       value="{{ old('district','Nalanda') }}"
       required>
</div>

<div class="kpv-field">
<label>State *</label>
<input name="state"
       value="{{ old('state','Bihar') }}"
       required>
</div>

<div class="kpv-field">
<label>PIN *</label>
<input name="pin"
       value="{{ old('pin') }}"
       required>
</div>

</div>
</section>

<section class="kpv-section">
<h2>Identity & Declaration</h2>

<div class="kpv-grid">

<div class="kpv-field">
<label>Identity Type</label>
<select name="identity_type">
<option value="">Select Identity</option>
@foreach(['Aadhaar','Voter ID','PAN','Other'] as $type)
<option value="{{ $type }}"
 @selected(old('identity_type')===$type)>
{{ $type }}
</option>
@endforeach
</select>
</div>

<div class="kpv-field">
<label>Identity Number</label>
<input name="identity_number"
       value="{{ old('identity_number') }}">
</div>

<div class="kpv-field full">
<label>Remarks</label>
<textarea name="remarks">{{ old('remarks') }}</textarea>
</div>

<div class="kpv-field full">
<label style="display:flex;gap:10px;align-items:flex-start">
<input style="width:auto;margin-top:4px"
       type="checkbox"
       name="consent"
       value="1"
       required>
<span>
I confirm that the submitted information is correct and
may be used for KYP admission and official student records.
</span>
</label>
</div>

</div>
</section>

<div class="kpv-actions">
<button class="btn btn-primary" type="submit">
Submit KYP Admission
</button>

<a class="btn btn-light"
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
