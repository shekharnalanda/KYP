@extends('layouts.app')
@section('title','Online Admission | KYP')
@section('body')

<style>
.public-form-shell{padding:45px 0;background:#f3f7fc;min-height:100vh}
.public-form{width:min(1050px,94%);margin:auto;background:#fff;border:1px solid #dce7f4;border-radius:24px;padding:30px;box-shadow:0 15px 45px rgba(6,43,99,.08)}
.public-form-head{text-align:center;margin-bottom:28px}
.public-form-head img{width:80px;height:80px;object-fit:contain;border-radius:50%}
.public-form-head h1{color:#062b63;margin:10px 0 5px}
.public-form-head p{color:#718198}
.form-section{margin-top:25px;padding-top:20px;border-top:1px solid #e5edf6}
.form-section h2{color:#062b63;font-size:18px}
.form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
.form-grid .full{grid-column:1/-1}
.public-form label{display:block;font-weight:750;margin-bottom:6px;color:#26364f}
.public-form input,.public-form select,.public-form textarea{width:100%;padding:12px;border:1px solid #cbd8e8;border-radius:11px}
.public-form textarea{min-height:95px}
.public-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:25px}
.error-list{background:#fff0ee;color:#a12a20;padding:15px;border-radius:12px;margin-bottom:18px}
@media(max-width:650px){.form-grid{grid-template-columns:1fr}.form-grid .full{grid-column:auto}}
</style>

<div class="public-form-shell">
<form class="public-form" method="POST"
      action="{{ route('admission.store') }}"
      enctype="multipart/form-data">
@csrf

<div class="public-form-head">
<img src="{{ asset('images/kyp-logo.webp') }}" alt="KYP">
<h1>Online Admission Form</h1>
<p>Kushal Youth Program</p>
</div>

@if($errors->any())
<div class="error-list">
@foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
</div>
@endif

<div class="form-section">
<h2>Program Selection</h2>
<div class="form-grid">
<div>
<label>Branch *</label>
<select name="branch_id" required>
<option value="">Select Branch</option>
@foreach($branches as $branch)
<option value="{{ $branch->id }}" @selected(old('branch_id')==$branch->id)>
{{ $branch->name }}
</option>
@endforeach
</select>
</div>

<div>
<label>Course *</label>
<select name="course_id" required>
<option value="">Select Course</option>
@foreach($courses as $course)
<option value="{{ $course->id }}" @selected(old('course_id')==$course->id)>
{{ $course->code }} — {{ $course->name }}
</option>
@endforeach
</select>
</div>
</div>
</div>

<div class="form-section">
<h2>Student Details</h2>
<div class="form-grid">
<div><label>Full Name *</label><input name="name" value="{{ old('name') }}" required></div>
<div><label>Date of Birth *</label><input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required></div>

<div>
<label>Gender *</label>
<select name="gender" required>
<option value="">Select</option>
@foreach(['Male','Female','Other'] as $g)
<option @selected(old('gender')===$g)>{{ $g }}</option>
@endforeach
</select>
</div>

<div><label>Mobile *</label><input name="mobile" value="{{ old('mobile') }}" required></div>
<div><label>Email</label><input type="email" name="email" value="{{ old('email') }}"></div>
<div><label>Qualification *</label><input name="qualification" value="{{ old('qualification') }}" required></div>

<div>
<label>Student Photo * (JPG/PNG/WebP, max 2MB)</label>
<input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required>
</div>
</div>
</div>

<div class="form-section">
<h2>Parent / Guardian</h2>
<div class="form-grid">
<div><label>Father Name</label><input name="father_name" value="{{ old('father_name') }}"></div>
<div><label>Mother Name</label><input name="mother_name" value="{{ old('mother_name') }}"></div>
<div><label>Guardian Name</label><input name="guardian_name" value="{{ old('guardian_name') }}"></div>
<div><label>Guardian Mobile</label><input name="guardian_mobile" value="{{ old('guardian_mobile') }}"></div>
</div>
</div>

<div class="form-section">
<h2>Address</h2>
<div class="form-grid">
<div class="full"><label>Full Address *</label><textarea name="address" required>{{ old('address') }}</textarea></div>
<div><label>City</label><input name="city" value="{{ old('city') }}"></div>
<div><label>District *</label><input name="district" value="{{ old('district','Nalanda') }}" required></div>
<div><label>State *</label><input name="state" value="{{ old('state','Bihar') }}" required></div>
<div><label>PIN *</label><input name="pin" value="{{ old('pin') }}" required></div>
</div>
</div>

<div class="form-section">
<h2>Identity & Declaration</h2>
<div class="form-grid">
<div>
<label>Identity Type</label>
<select name="identity_type">
<option value="">Select</option>
@foreach(['Aadhaar','Voter ID','PAN','Other'] as $i)
<option @selected(old('identity_type')===$i)>{{ $i }}</option>
@endforeach
</select>
</div>
<div><label>Identity Number</label><input name="identity_number" value="{{ old('identity_number') }}"></div>
<div class="full"><label>Remarks</label><textarea name="remarks">{{ old('remarks') }}</textarea></div>
<div class="full">
<label style="display:flex;gap:10px;align-items:flex-start">
<input style="width:auto;margin-top:5px" type="checkbox" name="consent" value="1" required>
<span>I confirm that the submitted information is correct and may be used for admission and student records.</span>
</label>
</div>
</div>
</div>

<div class="public-actions">
<button class="btn btn-primary" type="submit">Submit Admission Form</button>
<a class="btn btn-light" href="{{ route('home') }}">Back to Home</a>
</div>

</form>
</div>
@endsection
