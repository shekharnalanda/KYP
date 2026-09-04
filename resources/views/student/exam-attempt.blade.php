@extends('layouts.app')
@section('title', 'Exam Attempt | KYP')
@section('body')
<div class="panel-shell"><div class="container"><div class="panel">
<div style="display:flex;justify-content:space-between;gap:18px;flex-wrap:wrap">
<div><span class="eyebrow" style="color:#057d78;background:#e8fffd">{{ $attempt->exam->course->code }} EXAM</span><h1>{{ $attempt->exam->title_hi }}</h1><p>{{ $attempt->exam->title_en }}</p></div>
<div class="panel-card"><strong id="timer">{{ $attempt->exam->duration_minutes }}:00</strong><div style="color:var(--muted)">समय शेष</div></div>
</div>
<form method="POST" action="{{ route('student.exam.submit', $attempt) }}" id="exam-form">@csrf
@foreach($attempt->exam->questions as $index => $question)
<article class="card" style="margin-top:22px">
<h3>{{ $index + 1 }}. {{ $question->text_hi }}</h3>
<p style="color:var(--muted)">{{ $question->text_en }}</p>
<div style="display:grid;gap:10px;margin-top:16px">
@foreach($question->options as $option)
<label style="display:flex;gap:10px;align-items:flex-start;padding:12px;border:1px solid #dbe5f0;border-radius:12px;cursor:pointer">
<input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}">
<span><strong>{{ $option->option_key }}.</strong> {{ $option->text_hi }} <small style="display:block;color:var(--muted)">{{ $option->text_en }}</small></span>
</label>
@endforeach
</div>
</article>
@endforeach
<div style="display:flex;justify-content:flex-end;margin-top:24px"><button class="btn btn-primary" type="submit">Final Submit</button></div>
</form>
</div></div></div>
<script>
(() => {
 const form = document.getElementById('exam-form');
 const timer = document.getElementById('timer');
 const started = {{ $attempt->started_at->timestamp * 1000 }};
 const limit = {{ $attempt->exam->duration_minutes * 60 * 1000 }};
 const tick = () => {
   const left = Math.max(0, limit - (Date.now() - started));
   const minutes = Math.floor(left / 60000);
   const seconds = Math.floor((left % 60000) / 1000);
   timer.textContent = minutes + ':' + String(seconds).padStart(2, '0');
   if (left === 0) form.submit();
 };
 tick(); setInterval(tick, 1000);
})();
</script>
@endsection
