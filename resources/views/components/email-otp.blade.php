@props([
    'purpose',
    'inputId' => 'verified-email'
])

<div class="kyp-otp-box"
     data-purpose="{{ $purpose }}"
     data-send="{{ route('public.otp.send') }}"
     data-verify="{{ route('public.otp.verify') }}">

<label>
Email *
<span style="color:#057d78">
(OTP verification required)
</span>
</label>

<div class="kyp-otp-row">
<input
 class="kyp-otp-email"
 id="{{ $inputId }}"
 type="email"
 name="email"
 value="{{ old('email') }}"
 required
 autocomplete="email">

<button type="button"
        class="btn btn-light kyp-send-otp">
Send OTP
</button>
</div>

<div class="kyp-otp-row kyp-code-area" hidden>
<input class="kyp-otp-code"
       type="text"
       inputmode="numeric"
       maxlength="6"
       pattern="[0-9]{6}"
       placeholder="6-digit OTP">

<button type="button"
        class="btn btn-light kyp-verify-otp">
Verify OTP
</button>
</div>

<div class="kyp-otp-status">
Email verification required.
</div>

</div>
