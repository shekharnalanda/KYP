@php
$admission = $student->approvedAdmission ?? null;
$branch = $admission?->branch;

$courseNames = $student->enrollments
    ->pluck('course.code')
    ->filter()
    ->implode(', ');

$verifyUrl = route('student.id-card.verify', $student->id_card_token);

$photoUrl = $admission?->photo_path
    ? asset('storage/'.$admission->photo_path)
    : null;

$dob = $admission?->date_of_birth
    ? $admission->date_of_birth->format('d-m-Y')
    : '—';

$addressParts = array_filter([
    $branch?->address,
    $branch?->city,
    $branch?->district,
    $branch?->state,
    $branch?->pin,
]);

$branchAddress = implode(', ', $addressParts);
@endphp

<div class="id-student-unit">

<div class="id-side id-front">

<div class="id-topbar">
<div class="id-brand">
<img src="{{ asset('images/ykk-logo.webp') }}" alt="YKK">
<div>
<strong>युवा करियर कौशल कार्यक्रम</strong>
<small>Student Identity Card</small>
</div>
</div>

<img class="id-mci"
     src="{{ asset('images/mci-circle-logo.png') }}"
     alt="MCI">
</div>

<div class="id-front-body">

<div class="id-photo">
@if($photoUrl)
<img src="{{ $photoUrl }}" alt="{{ $student->name }}">
@else
<span>{{ strtoupper(substr($student->name,0,1)) }}</span>
@endif
</div>

<div class="id-main-data">
<h3>{{ $student->name }}</h3>
<div class="id-badge">STUDENT</div>

<div class="id-line">
<span>Student ID</span>
<strong>{{ $student->student_id }}</strong>
</div>

<div class="id-line">
<span>Course</span>
<strong>{{ $courseNames ?: 'YKK' }}</strong>
</div>

<div class="id-line">
<span>Branch</span>
<strong>{{ $branch?->name ?: 'YKK Centre' }}</strong>
</div>

<div class="id-line">
<span>Status</span>
<strong>{{ strtoupper($student->status) }}</strong>
</div>
</div>

<div class="id-qr">
<img
 src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($verifyUrl) }}"
 alt="Verification QR">
<small>SCAN TO VERIFY</small>
</div>

</div>

<div class="id-bottom">
MCI Educational Group • युवा करियर कौशल कार्यक्रम
</div>

</div>


<div class="id-side id-back">

<div class="id-back-head">
<img src="{{ asset('images/ykk-logo.webp') }}" alt="YKK">
<div>
<strong>युवा करियर कौशल कार्यक्रम</strong>
<small>Official Student Identity</small>
</div>
</div>

<div class="id-back-body">

<div class="id-back-data">

<div class="id-line">
<span>Name</span>
<strong>{{ $student->name }}</strong>
</div>

<div class="id-line">
<span>Date of Birth</span>
<strong>{{ $dob }}</strong>
</div>

<div class="id-line">
<span>Mobile</span>
<strong>{{ $student->phone ?: '—' }}</strong>
</div>

<div class="id-line">
<span>Student ID</span>
<strong>{{ $student->student_id }}</strong>
</div>

<div class="id-line">
<span>Centre</span>
<strong>{{ $branch?->name ?: 'YKK Centre' }}</strong>
</div>

</div>

<div class="id-rules">
<strong>Instructions</strong>
<p>This card is issued for YKK academic and training identification.</p>
<p>Card validity depends on active student status.</p>
<p>If found, please return to the issuing YKK centre.</p>
</div>

@if($branchAddress)
<div class="id-address">
<strong>Centre Address:</strong>
{{ $branchAddress }}
</div>
@endif

<div class="id-verification">
QR verification:
<strong>{{ $student->student_id }}</strong>
</div>

</div>

<div class="id-bottom">
Authorized YKK Student Record • QR Verifiable
</div>

</div>

</div>
