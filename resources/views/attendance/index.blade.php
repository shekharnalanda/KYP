@extends('layouts.app')
@section('title', 'Attendance Management & Reporting | KYP')
@section('body')

<x-admin-shell
    active="attendance"
    eyebrow="ATTENDANCE MANAGEMENT"
    title="Attendance Management & Reporting"
    subtitle="Centre Iris, Online Lab और Classroom attendance का central reporting center."
>

<style>
.kar-grid{
display:grid;
grid-template-columns:repeat(4,minmax(0,1fr));
gap:12px;
margin-bottom:18px
}
.kar-stat,.kar-card{
background:#fff;
border:1px solid #dce7f4;
border-radius:16px;
}
.kar-stat{padding:16px}
.kar-stat small{
display:block;color:#718096;font-weight:800;font-size:11px
}
.kar-stat strong{
display:block;font-size:25px;color:#062b63;margin-top:5px
}
.kar-card{padding:18px;margin-bottom:18px}
.kar-card h3{margin:0 0 14px;color:#062b63}
.kar-filter{
display:grid;
grid-template-columns:repeat(4,minmax(0,1fr));
gap:10px
}
.kar-filter label{
font-size:11px;font-weight:800;color:#53657d
}
.kar-filter select,.kar-filter input{
width:100%;
box-sizing:border-box;
padding:10px;
border:1px solid #cfdbea;
border-radius:9px;
margin-top:4px;
background:#fff
}
.kar-actions{
display:flex;gap:8px;flex-wrap:wrap;margin-top:14px
}
.kar-btn{
display:inline-flex;
align-items:center;
padding:10px 14px;
border-radius:9px;
border:0;
background:#0755a5;
color:#fff;
text-decoration:none;
font-weight:800;
cursor:pointer
}
.kar-btn.alt{background:#087f7a}
.kar-btn.light{background:#edf4fc;color:#062b63}
.kar-table-wrap{overflow:auto}
.kar-table{
width:100%;
border-collapse:collapse;
font-size:12px
}
.kar-table th,.kar-table td{
padding:10px;
border-bottom:1px solid #e5edf6;
text-align:left;
vertical-align:top;
white-space:nowrap
}
.kar-table th{
background:#f4f8fc;
color:#53657d;
font-size:10px;
text-transform:uppercase
}
.kar-student strong,.kar-session strong{
display:block;color:#102a43
}
.kar-muted{display:block;color:#718096;font-size:10px;margin-top:2px}
.kar-badge{
display:inline-block;
padding:5px 8px;
border-radius:999px;
background:#edf4fc;
color:#0755a5;
font-weight:800;
font-size:10px
}
.kar-badge.online{background:#e8f8f5;color:#087f7a}
.kar-badge.auto{background:#fff3d9;color:#9a6100}
.kar-badge.classroom{background:#f3ecff;color:#6741a5}
.kar-badge.completed{background:#e8f7ee;color:#18753c}
.kar-badge.open{background:#fff3d9;color:#8a5b00}
.kar-badge.absent{background:#fdecec;color:#a92a2a}
.kar-empty{
padding:35px;text-align:center;color:#718096
}
.kar-note{
padding:12px 14px;
background:#f5f9ff;
border-left:4px solid #0755a5;
border-radius:8px;
font-size:12px;
color:#44546a;
margin-bottom:16px
}
.kar-pagination{margin-top:16px}
@media(max-width:950px){
.kar-grid,.kar-filter{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:600px){
.kar-grid,.kar-filter{grid-template-columns:1fr}
}
</style>

<div class="kar-note">
<strong>Attendance modes:</strong>
Centre Iris Lab = MIS100V2 biometric attendance,
Online Lab = verified interactive courseware completion,
Classroom = staff-recorded classroom attendance.
Classroom attendance reporting में रहेगी लेकिन exam eligibility को block नहीं करेगी।
</div>

<div class="kar-grid">
<div class="kar-stat">
<small>FILTERED RECORDS</small>
<strong>{{ number_format($summary['records']) }}</strong>
</div>

<div class="kar-stat">
<small>COMPLETED</small>
<strong>{{ number_format($summary['completed']) }}</strong>
</div>

<div class="kar-stat">
<small>CENTRE IRIS</small>
<strong>{{ number_format($summary['centre_iris']) }}</strong>
</div>

<div class="kar-stat">
<small>ONLINE LAB</small>
<strong>{{ number_format($summary['online_lab']) }}</strong>
</div>

<div class="kar-stat">
<small>CLASSROOM</small>
<strong>{{ number_format($summary['classroom']) }}</strong>
</div>

<div class="kar-stat">
<small>AUTO MARK-OUT</small>
<strong>{{ number_format($summary['auto_checkout']) }}</strong>
</div>

<div class="kar-stat">
<small>TOTAL MINUTES</small>
<strong>{{ number_format($summary['minutes']) }}</strong>
</div>
</div>

<div class="kar-card">
<h3>Attendance Report Filters</h3>

<form method="GET" action="{{ route('attendance.index') }}">
<div class="kar-filter">

<label>
Student
<select name="student_id">
<option value="">All Students</option>
@foreach($students as $student)
<option
value="{{ $student->id }}"
@selected((string)request('student_id') === (string)$student->id)
>
{{ $student->student_id }} — {{ $student->name }}
</option>
@endforeach
</select>
</label>

<label>
Course
<select name="course_id" id="report-course">
<option value="">All Courses</option>
@foreach($courses as $course)
<option
value="{{ $course->id }}"
@selected((string)request('course_id') === (string)$course->id)
>
{{ $course->code }} — {{ $course->name }}
</option>
@endforeach
</select>
</label>

<label>
Session
<select name="learning_session_id" id="report-session">
<option value="">All Sessions</option>
@foreach($courses as $course)
@foreach($course->sessions as $session)
<option
value="{{ $session->id }}"
data-course="{{ $course->id }}"
@selected((string)request('learning_session_id') === (string)$session->id)
>
{{ $course->code }} / Session {{ $session->session_number }} — {{ $session->title_en }}
</option>
@endforeach
@endforeach
</select>
</label>

<label>
Attendance Mode
<select name="mode">
<option value="">All Modes</option>
<option value="centre_iris" @selected(request('mode') === 'centre_iris')>
Centre Iris Lab
</option>
<option value="online_lab" @selected(request('mode') === 'online_lab')>
Online Lab
</option>
<option value="classroom" @selected(request('mode') === 'classroom')>
Classroom
</option>
</select>
</label>

<label>
Status
<select name="status">
<option value="">All Status</option>
<option value="completed" @selected(request('status') === 'completed')>
Completed
</option>
<option value="present" @selected(request('status') === 'present')>
Present / Open
</option>
<option value="absent" @selected(request('status') === 'absent')>
Absent
</option>
</select>
</label>

<label>
Mark-Out Type
<select name="checkout_source">
<option value="">All</option>
<option value="iris" @selected(request('checkout_source') === 'iris')>
Iris Mark-Out
</option>
<option value="system_auto" @selected(request('checkout_source') === 'system_auto')>
System Auto Mark-Out
</option>
<option
value="courseware_completion"
@selected(request('checkout_source') === 'courseware_completion')
>
Online Completion
</option>
</select>
</label>

<label>
From Date
<input
type="date"
name="date_from"
value="{{ request('date_from') }}"
>
</label>

<label>
To Date
<input
type="date"
name="date_to"
value="{{ request('date_to') }}"
>
</label>

</div>

<div class="kar-actions">
<button class="kar-btn" type="submit">Apply Filters</button>

<a class="kar-btn light" href="{{ route('attendance.index') }}">
Reset
</a>

<a
class="kar-btn alt"
href="{{ route('attendance.export.csv', request()->query()) }}"
>
Export Excel / CSV
</a>

<a
class="kar-btn"
target="_blank"
rel="noopener"
href="{{ route('attendance.report.print', request()->query()) }}"
>
Print / Save PDF
</a>
</div>
</form>
</div>

<div class="kar-card">
<h3>Attendance Records</h3>

<div class="kar-table-wrap">
<table class="kar-table">
<thead>
<tr>
<th>Date</th>
<th>Student</th>
<th>Course / Session</th>
<th>Mode</th>
<th>Source</th>
<th>Mark-In</th>
<th>Mark-Out</th>
<th>Mark-Out Type</th>
<th>Minutes</th>
<th>Status</th>
</tr>
</thead>

<tbody>
@forelse($records as $record)
<tr>

<td>
{{ optional($record->attendance_date)->format('d-m-Y') ?: '-' }}
</td>

<td class="kar-student">
<strong>{{ $record->user?->name ?: 'Unknown Student' }}</strong>
<span class="kar-muted">{{ $record->user?->student_id ?: '-' }}</span>
</td>

<td class="kar-session">
<strong>
{{ $record->course?->code ?: '-' }}
@if($record->learningSession)
 / Session {{ $record->learningSession->session_number }}
@endif
</strong>
<span class="kar-muted">
{{ $record->learningSession?->title_en ?: $record->course?->name }}
</span>
</td>

<td>
@if($record->mode === 'online_lab')
<span class="kar-badge online">Online Lab</span>
@elseif($record->mode === 'classroom')
<span class="kar-badge classroom">Classroom</span>
@elseif($record->mode === 'lab' && $record->source === 'centre_iris')
<span class="kar-badge">Centre Iris Lab</span>
@else
<span class="kar-badge">{{ ucfirst((string)$record->mode) }}</span>
@endif
</td>

<td>
@if($record->source === 'centre_iris')
MIS100V2 Iris
@elseif($record->source === 'online_portal')
Online Portal
@elseif($record->recorded_by)
Manual Staff Entry
@else
System
@endif
</td>

<td>
{{ optional($record->checked_in_at)->format('d-m-Y h:i A') ?: '-' }}
</td>

<td>
{{ optional($record->checked_out_at)->format('d-m-Y h:i A') ?: '-' }}
</td>

<td>
@if($record->checkout_source === 'system_auto')
<span class="kar-badge auto">System Auto</span>
@elseif($record->checkout_source === 'iris')
<span class="kar-badge">Iris Mark-Out</span>
@elseif($record->checkout_source === 'courseware_completion')
<span class="kar-badge online">Online Completion</span>
@else
-
@endif
</td>

<td>{{ (int)$record->minutes_completed }}</td>

<td>
@if($record->status === 'completed')
<span class="kar-badge completed">Completed</span>
@elseif($record->status === 'present')
<span class="kar-badge open">Present / Open</span>
@elseif($record->status === 'absent')
<span class="kar-badge absent">Absent</span>
@else
<span class="kar-badge">{{ ucfirst((string)$record->status) }}</span>
@endif
</td>

</tr>
@empty
<tr>
<td colspan="10" class="kar-empty">
Selected filters के लिए कोई attendance record उपलब्ध नहीं है।
</td>
</tr>
@endforelse
</tbody>
</table>
</div>

<div class="kar-pagination">
{{ $records->links() }}
</div>
</div>

<script>
(() => {
    const course = document.getElementById('report-course');
    const session = document.getElementById('report-session');

    if (!course || !session) return;

    function filterSessions() {
        const courseId = course.value;

        [...session.options].forEach(option => {
            if (!option.dataset.course) {
                option.hidden = false;
                return;
            }

            option.hidden = courseId !== '' &&
                option.dataset.course !== courseId;
        });

        const selected = session.options[session.selectedIndex];

        if (selected && selected.hidden) {
            session.value = '';
        }
    }

    course.addEventListener('change', filterSessions);
    filterSessions();
})();
</script>

</x-admin-shell>

@endsection
