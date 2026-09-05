@extends('layouts.app')
@section('title','Exceptional Completion | KYP')
@section('body')

<x-admin-shell
 active="exceptional"
 eyebrow="AUTHORIZED ADMIN OVERRIDE"
 title="Exceptional Completion"
 subtitle="विशेष परिस्थिति में academic completion करें। प्रत्येक action permanent audit trail में दर्ज होगा।"
>

@if(session('status'))
<div class="notice success">{{ session('status') }}</div>
@endif

@if(isset($errors) && $errors->any())
<div class="notice error-box">
@foreach($errors->all() as $error)
<div>{{ $error }}</div>
@endforeach
</div>
@endif

<style>
.exc-warning{
 padding:16px 18px;
 border:1px solid #f0c889;
 background:#fff8e8;
 color:#744b0b;
 border-radius:15px;
 margin-bottom:22px
}
.exc-grid{
 display:grid;
 grid-template-columns:1fr 1fr;
 gap:18px
}
.exc-box{
 border:1px solid #dce7f4;
 border-radius:18px;
 padding:20px;
 background:#f9fcff
}
.exc-box h2{
 margin-top:0;
 color:#062b63
}
.exc-score-grid{
 display:grid;
 grid-template-columns:repeat(3,1fr);
 gap:10px
}
.exc-history{
 margin-top:26px
}
.exc-log{
 padding:15px;
 border:1px solid #dce7f4;
 border-radius:14px;
 margin-bottom:10px
}
@media(max-width:800px){
 .exc-grid{grid-template-columns:1fr}
 .exc-score-grid{grid-template-columns:1fr}
}
</style>

<div class="exc-warning">
<strong>Controlled Administrative Function</strong><br>
यह normal student workflow का replacement नहीं है।
Mandatory reason के बिना कोई exceptional action नहीं किया जा सकता।
</div>

<div class="exc-grid">

<div class="exc-box">
<h2>1. Complete Eligibility</h2>

<p>
Selected course के learning sessions तथा Lab/Classroom attendance
administratively complete करेगा।
</p>

<form method="POST"
      action="{{ route('admin.exceptional.eligibility') }}">
@csrf

<div class="field">
<label>Student</label>
<select name="student_id" required>
<option value="">Select Student</option>
@foreach($students as $student)
<option value="{{ $student->id }}">
{{ $student->student_id }} — {{ $student->name }}
</option>
@endforeach
</select>
</div>

<div class="field">
<label>Course</label>
<select name="course_id" required>
<option value="">Select Course</option>
@foreach($courses as $course)
<option value="{{ $course->id }}">
{{ $course->code }} — {{ $course->name }}
</option>
@endforeach
</select>
</div>

<div class="field">
<label>Mandatory Reason</label>
<textarea name="reason"
          rows="5"
          minlength="10"
          required
          placeholder="Exceptional completion का स्पष्ट कारण लिखें।"></textarea>
</div>

<button class="btn btn-primary"
        onclick="return confirm('क्या आप eligibility administratively complete करना चाहते हैं? यह action audit log में permanently दर्ज होगा।')">
Complete Eligibility
</button>

</form>
</div>


<div class="exc-box">
<h2>2. Administrative Pass & Certificate</h2>

<p>
Authorized scores के आधार पर published result तथा QR certificate
generate करेगा।
</p>

<form method="POST"
      action="{{ route('admin.exceptional.pass') }}">
@csrf

<div class="field">
<label>Student</label>
<select name="student_id" required>
<option value="">Select Student</option>
@foreach($students as $student)
<option value="{{ $student->id }}">
{{ $student->student_id }} — {{ $student->name }}
</option>
@endforeach
</select>
</div>

<div class="field">
<label>Course</label>
<select name="course_id" required>
<option value="">Select Course</option>
@foreach($courses as $course)
<option value="{{ $course->id }}">
{{ $course->code }} — {{ $course->name }}
</option>
@endforeach
</select>
</div>

<div class="exc-score-grid">

<div class="field">
<label>Exam /40</label>
<input type="number"
       name="exam_score"
       min="0" max="40" step=".01"
       required>
</div>

<div class="field">
<label>Lab /40</label>
<input type="number"
       name="lab_score"
       min="0" max="40" step=".01"
       required>
</div>

<div class="field">
<label>Classroom /20</label>
<input type="number"
       name="classroom_score"
       min="0" max="20" step=".01"
       required>
</div>

</div>

<div class="field">
<label>Mandatory Reason</label>
<textarea name="reason"
          rows="5"
          minlength="10"
          required
          placeholder="Administrative result/certificate का स्पष्ट कारण लिखें।"></textarea>
</div>

<button class="btn btn-primary"
        onclick="return confirm('यह administrative PASS और certificate generate करेगा तथा permanent audit log बनाएगा। जारी रखें?')">
Create Pass Result & Certificate
</button>

</form>
</div>

</div>


<div class="exc-history">

<h2>Exceptional Action History</h2>

@forelse($logs as $log)

<div class="exc-log">

<div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap">

<div>
<strong>
{{ strtoupper(str_replace('_',' ',$log->action)) }}
</strong>

<div style="margin-top:5px">
{{ $log->student?->student_id }}
— {{ $log->student?->name }}
@if($log->course)
• {{ $log->course->code }}
@endif
</div>
</div>

<div style="text-align:right;color:var(--muted);font-size:12px">
{{ $log->performed_at?->format('d M Y h:i A') }}<br>
Admin: {{ $log->admin?->name }}
</div>

</div>

<div style="margin-top:9px">
<strong>Reason:</strong>
{{ $log->reason }}
</div>

</div>

@empty
<div class="panel-card">
No exceptional actions recorded.
</div>
@endforelse

{{ $logs->links() }}

</div>

</x-admin-shell>
@endsection
