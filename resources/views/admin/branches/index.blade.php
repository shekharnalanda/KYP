@extends('layouts.app')
@section('title','Branches | KYP')
@section('body')

<x-admin-shell
 active="branches"
 eyebrow="CENTRE MANAGEMENT"
 title="Branches"
 subtitle="Admission और enquiry के लिए KYP branches manage करें।"
>

@if(session('success'))
<div class="notice success">{{ session('success') }}</div>
@endif

@if(isset($errors) && $errors->any())
<div class="notice error-box">
@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

<h2>Add Branch</h2>

<form method="POST" action="{{ route('admin.branches.store') }}">
@csrf

<div class="form-grid">
<div class="field"><label>Branch Name</label><input name="name" required></div>
<div class="field"><label>Code</label><input name="code" required></div>
<div class="field"><label>Address</label><input name="address"></div>
<div class="field"><label>City</label><input name="city"></div>
<div class="field"><label>District</label><input name="district" value="Nalanda"></div>
<div class="field"><label>State</label><input name="state" value="Bihar" required></div>
<div class="field"><label>PIN</label><input name="pin"></div>
<div class="field"><label>Phone</label><input name="phone"></div>
<div class="field"><label>Email</label><input type="email" name="email"></div>
<div class="field"><label>Position</label><input type="number" name="position" value="0"></div>
</div>

<button class="btn btn-primary">Add Branch</button>
</form>

<h2 style="margin-top:30px">Existing Branches</h2>

<div style="display:grid;gap:15px">
@foreach($branches as $branch)
<form method="POST" action="{{ route('admin.branches.update',$branch) }}"
      style="padding:17px;border:1px solid #dce7f4;border-radius:16px">
@csrf
@method('PUT')

<div class="form-grid">
<div class="field"><label>Name</label><input name="name" value="{{ $branch->name }}" required></div>
<div class="field"><label>Code</label><input name="code" value="{{ $branch->code }}" required></div>
<div class="field"><label>Address</label><input name="address" value="{{ $branch->address }}"></div>
<div class="field"><label>City</label><input name="city" value="{{ $branch->city }}"></div>
<div class="field"><label>District</label><input name="district" value="{{ $branch->district }}"></div>
<div class="field"><label>State</label><input name="state" value="{{ $branch->state }}" required></div>
<div class="field"><label>PIN</label><input name="pin" value="{{ $branch->pin }}"></div>
<div class="field"><label>Phone</label><input name="phone" value="{{ $branch->phone }}"></div>
<div class="field"><label>Email</label><input name="email" value="{{ $branch->email }}"></div>
<div class="field"><label>Position</label><input type="number" name="position" value="{{ $branch->position }}"></div>
<div class="field">
<label>Status</label>
<select name="is_active">
<option value="1" @selected($branch->is_active)>Active</option>
<option value="0" @selected(!$branch->is_active)>Inactive</option>
</select>
</div>
</div>

<button class="btn btn-primary">Save Branch</button>
</form>
@endforeach
</div>

</x-admin-shell>
@endsection
