@extends('layouts.app')
@section('title', 'Change Password | KYP')
@section('body')
<header>
    <nav class="container">
        <a class="brand" href="{{ route('dashboard') }}">
            <img src="{{ asset('images/kyp-logo.webp') }}" alt="KYP Logo" onerror="this.style.display='none';this.nextElementSibling.style.display='grid'">
            <span class="brand-fallback">KYP</span>
            <span><strong>KUSHAL YOUTH PROGRAM</strong><small>Secure Account Settings</small></span>
        </a>
        <a class="btn btn-light" href="{{ route('dashboard') }}">Dashboard</a>
    </nav>
</header>

<div class="form-wrap">
    <form class="form-card" method="POST" action="{{ route('profile.password.update') }}">
        @csrf
        @method('PUT')

        <span class="eyebrow" style="color:#057d78;background:#e8fffd">ACCOUNT SECURITY</span>
        <h1 style="margin-top:14px">Change Password</h1>
        <p style="color:var(--muted);line-height:1.6">अपने account की सुरक्षा के लिए मजबूत और अलग password रखें।</p>

        @if(session('status'))
            <div style="margin-top:18px;padding:13px 15px;border-radius:12px;background:#e8fff4;color:#087443;border:1px solid #a9e8cc;font-weight:700">{{ session('status') }}</div>
        @endif

        <div class="field">
            <label for="current_password">Current Password</label>
            <input id="current_password" name="current_password" type="password" required autocomplete="current-password">
            @error('current_password')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="password">New Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password">
            <small style="display:block;color:var(--muted);margin-top:7px;line-height:1.5">कम-से-कम 10 characters; uppercase, lowercase, number और symbol आवश्यक।</small>
            @error('password')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm New Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
        </div>

        <button class="btn full" type="submit">Update Password Securely</button>
    </form>
</div>
@endsection
