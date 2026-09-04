@extends('layouts.app')
@section('title', 'Portal Login | Kushal Youth Program')
@section('body')
<header><nav class="container"><a class="brand" href="{{ route('home') }}"><span class="brand-fallback" style="display:grid">KYP</span><span><strong>KUSHAL YOUTH PROGRAM</strong><small>Secure Learning Portal</small></span></a><a class="btn btn-light" href="{{ route('home') }}">Home</a></nav></header>
<div class="form-wrap">
<form class="form-card" method="POST" action="{{ route('login') }}">
@csrf
<h1>Portal Login</h1><p style="color:var(--muted)">Student, Teacher या Administrator account से प्रवेश करें।</p>
<div class="field"><label for="email">Email Address</label><input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email">@error('email')<div class="error">{{ $message }}</div>@enderror</div>
<div class="field"><label for="password">Password</label><input id="password" name="password" type="password" required autocomplete="current-password">@error('password')<div class="error">{{ $message }}</div>@enderror</div>
<div class="field"><label style="font-weight:500"><input style="width:auto" type="checkbox" name="remember" value="1"> मुझे login रखें</label></div>
<button class="btn full" type="submit">Secure Login</button>
</form>
</div>
@endsection
