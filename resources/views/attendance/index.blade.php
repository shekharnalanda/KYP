@extends('layouts.app')
@section('title', 'Attendance Operations | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel">
<div style="display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap">
<div><span class="eyebrow" style="color:#057d78;background:#e8fffd">ATTENDANCE OPERATIONS</span><h1>Classroom एवं Lab Attendance</h1><p style="color:var(--muted)">एक विद्यार्थी या पूरी enrolled class की session-wise verified attendance।</p></div>
<a class="btn btn-light" href="{{ auth()->user()->hasRole('teacher') ? route('teacher.dashboard') : route('admin.dashboard') }}">Dashboard</a>
</div>
@if(session('status'))<div style="margin-top:18px;padding:14px;border-radius:12px;background:#e8fffd;color:#057d78">{{ session('status') }}</div>@endif
@if($errors->any())<div style="margin-top:18px;padding:14px;border-radius:12px;background:#fff1f2;color:#be123c">{{ $errors->first() }}</div>@endif

<details open class="card" style="margin-top:22px"><summary style="font-size:20px;font-weight:800;cursor:pointer">Bulk Class Register</summary>
<form method="POST" action="{{ route('attendance.bulk') }}" style="margin-top:18px">@csrf
<div class="panel-grid">
<label>Course<select name="course_id" id="bulk-course" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"><option value="">Select course</option>@foreach($courses as $course)<option value="{{ $course->id }}">{{ $course->code }} — {{ $course->name }}</option>@endforeach</select></label>
<label>Session<select name="learning_session_id" id="bulk-session" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"><option value="">Select session</option>@foreach($courses as $course)@foreach($course->sessions as $session)<option value="{{ $session->id }}" data-course="{{ $course->id }}">{{ $course->code }} {{ $session->session_number }} — {{ $session->title_hi }}</option>@endforeach@endforeach</select></label>
<label>Date<input type="date" name="attendance_date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"></label>
<label>Mode<select name="mode" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"><option value="classroom">Classroom</option><option value="lab">Lab</option></select></label>
</div>
<div id="bulk-help" style="padding:14px;margin-top:16px;background:#f7fbfb;border-radius:14px">Course चुनने पर enrolled students दिखाई देंगे।</div>
<div style="overflow:auto"><table id="bulk-table" style="display:none;width:100%;border-collapse:collapse;margin-top:14px"><thead><tr><th>Student</th><th>Status</th><th>Minutes</th></tr></thead><tbody>
@foreach($students as $index => $student)
@php($courseIds = $student->enrollments->pluck('course_id')->implode(','))
<tr class="bulk-student" data-courses=",{{ $courseIds }},">
<td><input disabled type="hidden" name="records[{{ $index }}][user_id]" value="{{ $student->id }}">{{ $student->student_id ?: 'KYP' }} — {{ $student->name }}</td>
<td><select disabled name="records[{{ $index }}][status]" style="padding:9px;border-radius:9px"><option value="completed">Completed</option><option value="present">Present</option><option value="absent">Absent</option></select></td>
<td><input disabled type="number" name="records[{{ $index }}][minutes_completed]" value="120" min="0" max="120" style="width:90px;padding:9px;border-radius:9px;border:1px solid #dbe5f0"></td>
</tr>
@endforeach
</tbody></table></div>
<button id="bulk-submit" disabled class="btn btn-primary" style="margin-top:18px">Save Bulk Attendance</button>
</form></details>

<details class="card" style="margin-top:18px"><summary style="font-size:20px;font-weight:800;cursor:pointer">Single Student Entry</summary>
<form method="POST" action="{{ route('attendance.store') }}" style="margin-top:18px">@csrf
<div class="panel-grid">
<label>Student<select name="user_id" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"><option value="">Select student</option>@foreach($students as $student)<option value="{{ $student->id }}">{{ $student->student_id ?: 'KYP' }} — {{ $student->name }}</option>@endforeach</select></label>
<label>Course<select name="course_id" id="course" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"><option value="">Select course</option>@foreach($courses as $course)<option value="{{ $course->id }}">{{ $course->code }} — {{ $course->name }}</option>@endforeach</select></label>
<label>Session<select name="learning_session_id" id="session" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"><option value="">Select session</option>@foreach($courses as $course)@foreach($course->sessions as $session)<option value="{{ $session->id }}" data-course="{{ $course->id }}">{{ $course->code }} {{ $session->session_number }} — {{ $session->title_hi }}</option>@endforeach@endforeach</select></label>
<label>Date<input type="date" name="attendance_date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"></label>
<label>Mode<select name="mode" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"><option value="classroom">Classroom</option><option value="lab">Lab</option></select></label>
<label>Status<select name="status" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"><option value="completed">Completed</option><option value="present">Present</option><option value="absent">Absent</option></select></label>
<label>Completed minutes<input type="number" name="minutes_completed" value="120" min="0" max="120" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"></label>
<label>Biometric reference<input name="biometric_reference" maxlength="255" placeholder="Optional device/reference ID" style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"></label>
</div><button class="btn btn-primary" style="margin-top:18px">Attendance Save करें</button>
</form></details>

<h2 style="margin-top:28px">Recent Records</h2>
<div style="overflow:auto"><table style="width:100%;border-collapse:collapse"><thead><tr><th>Student</th><th>Course/Session</th><th>Mode</th><th>Status</th><th>Date</th><th>Recorded by</th></tr></thead><tbody>
@forelse($recent as $record)<tr><td>{{ $record->user->name }}</td><td>{{ $record->course->code }}-{{ $record->learningSession->session_number }}</td><td>{{ ucfirst($record->mode) }}</td><td>{{ ucfirst($record->status) }}</td><td>{{ $record->attendance_date->format('d M Y') }}</td><td>{{ $record->recorder?->name ?: 'System' }}</td></tr>@empty<tr><td colspan="6">No attendance records yet.</td></tr>@endforelse
</tbody></table></div>
</div></div></div>
<script>
function filterSessions(courseId, selector) {
 document.querySelectorAll(selector + ' option[data-course]').forEach(option => option.hidden = courseId && option.dataset.course !== courseId);
 document.querySelector(selector).value = '';
}
document.getElementById('course').addEventListener('change', function(){ filterSessions(this.value, '#session'); });
document.getElementById('bulk-course').addEventListener('change', function(){
 const id = this.value; filterSessions(id, '#bulk-session'); let visible = 0;
 document.querySelectorAll('.bulk-student').forEach(row => {
   const show = id && row.dataset.courses.includes(',' + id + ','); row.hidden = !show;
   row.querySelectorAll('input,select').forEach(field => field.disabled = !show); if(show) visible++;
 });
 document.getElementById('bulk-table').style.display = visible ? 'table' : 'none';
 document.getElementById('bulk-help').textContent = visible ? visible + ' enrolled students found.' : (id ? 'No active enrolments found.' : 'Course चुनने पर enrolled students दिखाई देंगे।');
 document.getElementById('bulk-submit').disabled = !visible;
});
</script>
@endsection
