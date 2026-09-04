@extends('layouts.app')
@section('title', 'Attendance Operations | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel">
<div style="display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap">
<div><span class="eyebrow" style="color:#057d78;background:#e8fffd">ATTENDANCE OPERATIONS</span><h1>Classroom एवं Lab Attendance</h1><p style="color:var(--muted)">Teacher/Admin द्वारा session-wise verified attendance और biometric reference।</p></div>
<a class="btn btn-light" href="{{ auth()->user()->hasRole('teacher') ? route('teacher.dashboard') : route('admin.dashboard') }}">Dashboard</a>
</div>
@if(session('status'))<div style="margin-top:18px;padding:14px;border-radius:12px;background:#e8fffd;color:#057d78">{{ session('status') }}</div>@endif
@if($errors->any())<div style="margin-top:18px;padding:14px;border-radius:12px;background:#fff1f2;color:#be123c">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('attendance.store') }}" class="card" style="margin-top:22px">@csrf
<div class="panel-grid">
<label>Student<select name="user_id" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"><option value="">Select student</option>@foreach($students as $student)<option value="{{ $student->id }}">{{ $student->student_id ?: 'KYP' }} — {{ $student->name }}</option>@endforeach</select></label>
<label>Course<select name="course_id" id="course" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"><option value="">Select course</option>@foreach($courses as $course)<option value="{{ $course->id }}">{{ $course->code }} — {{ $course->name }}</option>@endforeach</select></label>
<label>Session<select name="learning_session_id" id="session" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"><option value="">Select session</option>@foreach($courses as $course)@foreach($course->sessions as $session)<option value="{{ $session->id }}" data-course="{{ $course->id }}">{{ $course->code }} {{ $session->session_number }} — {{ $session->title_hi }}</option>@endforeach@endforeach</select></label>
<label>Date<input type="date" name="attendance_date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"></label>
<label>Mode<select name="mode" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"><option value="classroom">Classroom</option><option value="lab">Lab</option></select></label>
<label>Status<select name="status" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"><option value="completed">Completed</option><option value="present">Present</option><option value="absent">Absent</option></select></label>
<label>Completed minutes<input type="number" name="minutes_completed" value="120" min="0" max="120" required style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"></label>
<label>Biometric reference<input name="biometric_reference" maxlength="255" placeholder="Optional device/reference ID" style="width:100%;padding:12px;margin-top:7px;border:1px solid #dbe5f0;border-radius:10px"></label>
</div>
<button class="btn btn-primary" style="margin-top:18px">Attendance Save करें</button>
</form>
<h2 style="margin-top:28px">Recent Records</h2>
<div style="overflow:auto"><table style="width:100%;border-collapse:collapse"><thead><tr><th>Student</th><th>Course/Session</th><th>Mode</th><th>Status</th><th>Date</th><th>Recorded by</th></tr></thead><tbody>
@forelse($recent as $record)<tr><td>{{ $record->user->name }}</td><td>{{ $record->course->code }}-{{ $record->learningSession->session_number }}</td><td>{{ ucfirst($record->mode) }}</td><td>{{ ucfirst($record->status) }}</td><td>{{ $record->attendance_date->format('d M Y') }}</td><td>{{ $record->recorder?->name ?: 'System' }}</td></tr>@empty<tr><td colspan="6">No attendance records yet.</td></tr>@endforelse
</tbody></table></div>
</div></div></div>
<script>
document.getElementById('course').addEventListener('change', function () {
 const value = this.value;
 document.querySelectorAll('#session option[data-course]').forEach(option => {
   option.hidden = value && option.dataset.course !== value;
 });
 document.getElementById('session').value = '';
});
</script>
@endsection
