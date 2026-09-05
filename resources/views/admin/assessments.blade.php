@extends('layouts.app')
@section('title', 'Assessment CMS | KYP')
@section('body')

<x-admin-shell
    active="assessments"
    eyebrow="ASSESSMENT CMS"
    title="Exams & Bilingual Question Bank"
    subtitle="Exam creation, publishing और Hindi-English questions का central control."
>
</div>
<script>document.getElementById('q-course').addEventListener('change',function(){const id=this.value;document.querySelectorAll('#q-session option[data-course]').forEach(o=>o.hidden=id&&o.dataset.course!==id);document.getElementById('q-session').value='';});</script>
</x-admin-shell>

@endsection
