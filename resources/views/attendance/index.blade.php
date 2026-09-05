@extends('layouts.app')
@section('title', 'Attendance Operations | KYP')
@section('body')

<x-admin-shell
    active="attendance"
    eyebrow="ATTENDANCE OPERATIONS"
    title="Classroom एवं Lab Attendance"
    subtitle="Individual और bulk session-wise verified attendance operations."
>
</div>
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
</x-admin-shell>

@endsection
