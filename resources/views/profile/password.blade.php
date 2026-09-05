@extends('layouts.app')
@section('title', 'Change Password | KYP')
@section('body')

<x-admin-shell
    active="password"
    eyebrow="ACCOUNT SECURITY"
    title="Change Password"
    subtitle="अपने administrator account की सुरक्षा के लिए मजबूत और अलग password रखें।"
>

@if(session('status'))
<div style="padding:13px 15px;border-radius:12px;background:#e8fff4;color:#087443;border:1px solid #a9e8cc;font-weight:700;margin-bottom:18px">
{{ session('status') }}
</div>
@endif

<form method="POST" action="{{ route('profile.password.update') }}" style="max-width:620px">
@csrf
@method('PUT')

<div class="field">
<label for="current_password">Current Password</label>
<input id="current_password" name="current_password" type="password" required autocomplete="current-password">
@error('current_password')<div class="error">{{ $message }}</div>@enderror
</div>

<div class="field">
<label for="password">New Password</label>
<input id="password" name="password" type="password" required autocomplete="new-password">
<small style="display:block;color:var(--muted);margin-top:7px">
कम-से-कम 10 characters; uppercase, lowercase, number और symbol आवश्यक।
</small>
@error('password')<div class="error">{{ $message }}</div>@enderror
</div>

<div class="field">
<label for="password_confirmation">Confirm New Password</label>
<input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
</div>

<button class="btn btn-primary" style="margin-top:22px" type="submit">
Update Password Securely
</button>
</form>

</x-admin-shell>
@endsection
