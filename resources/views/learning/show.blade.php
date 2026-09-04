@extends('layouts.app')
@section('title', $session->title_hi.' | KYP')
@section('body')
@php
    $steps = $courseware['steps'] ?? [];
    $savedSteps = $progress?->completed_steps ?? [];
    $savedAnswers = $progress?->quiz_answers ?? [];
    $activeSeconds = (int) ($progress?->active_seconds ?? 0);
    $requiredSeconds = ((int) $session->required_active_minutes) * 60;
    $initialStep = (int) ($progress?->current_step ?? 0);
@endphp
<div class="panel-shell"><div class="container">
<div class="panel courseware-shell" data-student="{{ auth()->user()->hasRole('student') ? '1' : '0' }}">
    <div class="courseware-head">
        <div>
            <span class="eyebrow" style="color:#057d78;background:#e8fffd">{{ $session->course->code }} • SESSION {{ $session->session_number }}</span>
            <h1>{{ $session->title_hi }}</h1>
            <p style="color:var(--muted)">{{ $session->title_en }} • 120-minute interactive lab courseware</p>
        </div>
        <a class="btn btn-light" href="{{ route('learning.index') }}">All Sessions</a>
    </div>

    @if(session('status'))<div class="notice success">{{ session('status') }}</div>@endif
    @error('session')<div class="notice error-box">{{ $message }}</div>@enderror

    <div class="learning-status">
        <div><strong>Active Learning</strong><span id="active-time">{{ intdiv($activeSeconds, 60) }} / {{ $session->required_active_minutes }} मिनट</span></div>
        <div class="status-grow"><strong>Session Progress</strong><div class="progress-track"><div id="progress-fill" style="width:{{ count($steps) ? round((count($savedSteps) / count($steps)) * 100) : 0 }}%"></div></div></div>
        <div><strong>Quiz Score</strong><span id="quiz-score">{{ (int) ($progress?->quiz_score ?? 0) }} / {{ $session->passing_score }}+</span></div>
        <div id="save-state" class="save-state">Auto-save ready</div>
    </div>

    <div class="courseware-grid">
        <aside class="step-nav" aria-label="Session steps">
            @foreach($steps as $index => $step)
                <button type="button" class="step-link {{ in_array($step['id'], $savedSteps, true) ? 'done' : '' }}" data-step-target="{{ $index }}">
                    <span>{{ $index + 1 }}</span>
                    <b>{{ $step['title'] }}</b>
                    <small>{{ $step['minutes'] }} मिनट</small>
                </button>
            @endforeach
        </aside>

        <main class="step-stage">
            @foreach($steps as $index => $step)
                <section class="learning-step" data-step="{{ $index }}" data-step-id="{{ $step['id'] }}" hidden>
                    <div class="step-meta"><span>{{ strtoupper($step['type']) }}</span><span>⏱ {{ $step['minutes'] }} मिनट</span></div>
                    <h2>{{ $step['title'] }}</h2>
                    <div class="concept-visual" aria-hidden="true">
                        <span></span><span></span><span></span><b>{{ $session->course->code }}</b>
                    </div>
                    <div class="lesson-copy">{!! nl2br(e($step['content'] ?? '')) !!}</div>

                    @if(isset($step['interaction']))
                        @php($interaction = $step['interaction'])
                        <div class="interaction-card">
                            <h3>🧠 Interactive Question</h3>
                            <p>{{ $interaction['prompt'] }}</p>
                            @foreach($interaction['options'] as $optionIndex => $option)
                                <label class="choice">
                                    <input type="radio" name="{{ $interaction['id'] }}" value="{{ $optionIndex }}" @checked((string)($savedAnswers[$interaction['id']] ?? '') === (string)$optionIndex)>
                                    <span>{{ $option }}</span>
                                </label>
                            @endforeach
                            <div class="answer-feedback" data-feedback="{{ $interaction['id'] }}"></div>
                        </div>
                    @endif

                    @if($step['type'] === 'practical')
                        <div class="interaction-card">
                            <h3>💻 Practical Activity Record</h3>
                            <p>आपने क्या किया, कौन-से steps अपनाए और क्या परिणाम मिला—स्पष्ट रूप से लिखें।</p>
                            <textarea id="practical-response" rows="7" placeholder="अपना practical work और learning note यहाँ लिखें...">{{ $progress?->practical_response }}</textarea>
                        </div>
                    @endif

                    <div class="step-actions">
                        <button type="button" class="btn btn-light" data-prev>← पिछला चरण</button>
                        <button type="button" class="btn" data-next>चरण पूरा करें और आगे बढ़ें →</button>
                    </div>
                </section>
            @endforeach

            @if(auth()->user()->hasRole('student'))
                @if($completed)
                    <div class="notice success">यह interactive session complete है। आप इसे revision के लिए देख रहे हैं।</div>
                @else
                    <form method="POST" action="{{ route('learning.complete', $session) }}" id="complete-form">
                        @csrf
                        <button class="btn full" type="submit">Final Check करके Session Complete करें</button>
                        <p class="completion-note">सभी {{ count($steps) }} चरण, 120 active minutes, practical response और minimum {{ $session->passing_score }}% quiz score आवश्यक है।</p>
                    </form>
                @endif
            @endif
        </main>
    </div>
</div></div></div>

<style>
.courseware-head{display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap}.learning-status{display:flex;gap:18px;align-items:center;flex-wrap:wrap;margin:24px 0;padding:18px;border:1px solid #cfe0f4;border-radius:18px;background:#f7fbff}.learning-status>div{display:grid;gap:5px}.learning-status span{color:var(--muted);font-size:14px}.status-grow{flex:1;min-width:220px}.progress-track{height:10px;border-radius:99px;background:#dce8f6;overflow:hidden}.progress-track div{height:100%;background:linear-gradient(90deg,var(--teal),#0b86d7);transition:width .35s}.save-state{font-size:13px;color:#057d78}.courseware-grid{display:grid;grid-template-columns:270px 1fr;gap:24px}.step-nav{display:grid;gap:9px;align-content:start;position:sticky;top:18px}.step-link{display:grid;grid-template-columns:36px 1fr auto;align-items:center;gap:10px;text-align:left;padding:12px;border:1px solid #d8e5f3;background:#fff;border-radius:13px;color:var(--ink);cursor:pointer}.step-link span{width:32px;height:32px;border-radius:50%;display:grid;place-items:center;background:#eaf2fb;color:var(--navy);font-weight:800}.step-link b{font-size:13px}.step-link small{color:var(--muted);font-size:11px}.step-link.active{border-color:var(--teal);box-shadow:0 0 0 3px rgba(17,197,189,.13)}.step-link.done span{background:#d9fbef;color:#087443}.step-stage{min-width:0}.learning-step{min-height:550px;padding:30px;border:1px solid #dce8f6;border-radius:22px;background:#fff;box-shadow:0 14px 38px rgba(7,39,83,.07)}.step-meta{display:flex;justify-content:space-between;color:#057d78;font-weight:800;font-size:12px}.learning-step h2{font-size:clamp(26px,4vw,38px);color:var(--navy)}.lesson-copy{font-size:17px;line-height:1.85;white-space:normal}.concept-visual{height:110px;border-radius:18px;margin:18px 0 24px;background:linear-gradient(130deg,#062b63,#0b6b8c);display:flex;align-items:center;justify-content:center;gap:14px;overflow:hidden}.concept-visual span{width:22px;height:22px;border-radius:50%;background:var(--teal);animation:pulse 1.8s infinite alternate}.concept-visual span:nth-child(2){animation-delay:.3s}.concept-visual span:nth-child(3){animation-delay:.6s}.concept-visual b{font-size:32px;color:#fff;margin-left:12px}@keyframes pulse{to{transform:translateY(-15px) scale(1.15);background:var(--orange)}}.interaction-card{margin-top:26px;padding:22px;border-radius:17px;background:#f2f9ff;border:1px solid #cfe0f4}.choice{display:flex;gap:10px;align-items:flex-start;padding:12px;margin:9px 0;background:#fff;border:1px solid #dce8f6;border-radius:11px;cursor:pointer}.choice input{margin-top:4px}.answer-feedback{font-weight:700;margin-top:10px}.interaction-card textarea{width:100%;padding:14px;border-radius:12px;border:1px solid #bfd1e5;font:inherit;line-height:1.6}.step-actions{display:flex;justify-content:space-between;gap:12px;margin-top:28px}.notice{margin:18px 0;padding:14px;border-radius:12px}.success{background:#e8fff4;color:#087443;border:1px solid #a9e8cc}.error-box{background:#fff0ee;color:#a12a20;border:1px solid #f1bab4}.completion-note{text-align:center;color:var(--muted);font-size:13px}.full{width:100%;margin-top:22px}
@media(max-width:850px){.courseware-grid{grid-template-columns:1fr}.step-nav{position:static;grid-template-columns:repeat(2,1fr)}.learning-step{padding:22px}.step-link{grid-template-columns:32px 1fr}.step-link small{display:none}}@media(max-width:520px){.step-nav{grid-template-columns:1fr}.step-actions{flex-direction:column-reverse}.step-actions .btn{width:100%}}
</style>

@if(auth()->user()->hasRole('student') && !$completed)
<script>
(() => {
    const steps = [...document.querySelectorAll('.learning-step')];
    const nav = [...document.querySelectorAll('.step-link')];
    const token = document.querySelector('meta[name="csrf-token"]')?.content || @json(csrf_token());
    const saveUrl = @json(route('learning.progress', $session));
    const correctAnswers = @json(collect($steps)->pluck('interaction')->filter()->mapWithKeys(fn($q) => [$q['id'] => (string)$q['correct']]));
    let current = Math.min(@json($initialStep), Math.max(0, steps.length - 1));
    let completed = new Set(@json($savedSteps));
    let answers = @json($savedAnswers);
    let activeSeconds = @json($activeSeconds);
    let lastInteraction = Date.now();
    let saveTimer;

    function render() {
        steps.forEach((step, i) => step.hidden = i !== current);
        nav.forEach((item, i) => item.classList.toggle('active', i === current));
        document.getElementById('progress-fill').style.width = (completed.size / Math.max(1, steps.length) * 100) + '%';
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    async function save(extra = {}) {
        const state = document.getElementById('save-state');
        state.textContent = 'Saving…';
        const practical = document.getElementById('practical-response')?.value ?? null;
        try {
            const response = await fetch(saveUrl, {
                method: 'POST',
                headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':token},
                body: JSON.stringify({
                    current_step: current,
                    completed_steps: [...completed],
                    quiz_answers: answers,
                    practical_response: practical,
                    ...extra
                })
            });
            if (!response.ok) throw new Error('save failed');
            const data = await response.json();
            activeSeconds = data.active_seconds;
            document.getElementById('active-time').textContent = Math.floor(activeSeconds / 60) + ' / ' + @json((int)$session->required_active_minutes) + ' मिनट';
            document.getElementById('quiz-score').textContent = Math.round(data.quiz_score) + ' / ' + @json((int)$session->passing_score) + '+';
            state.textContent = 'Progress saved';
        } catch (error) {
            state.textContent = 'Connection interrupted—retrying';
        }
    }

    nav.forEach((item, i) => item.addEventListener('click', () => { current = i; render(); save(); }));
    document.querySelectorAll('[data-next]').forEach(button => button.addEventListener('click', () => {
        completed.add(steps[current].dataset.stepId);
        nav[current].classList.add('done');
        if (current < steps.length - 1) current++;
        render();
        save();
    }));
    document.querySelectorAll('[data-prev]').forEach(button => button.addEventListener('click', () => {
        current = Math.max(0, current - 1); render(); save();
    }));
    document.querySelectorAll('input[type="radio"]').forEach(input => input.addEventListener('change', event => {
        answers[event.target.name] = event.target.value;
        const feedback = document.querySelector('[data-feedback="'+event.target.name+'"]');
        feedback.textContent = correctAnswers[event.target.name] === event.target.value ? 'सही उत्तर ✓' : 'फिर से सोचें और lesson के practical भाग को दोहराएँ।';
        feedback.style.color = correctAnswers[event.target.name] === event.target.value ? '#087443' : '#a12a20';
        save();
    }));
    document.getElementById('practical-response')?.addEventListener('input', () => {
        clearTimeout(saveTimer); saveTimer = setTimeout(() => save(), 900);
    });
    ['click','keydown','scroll','touchstart'].forEach(name => document.addEventListener(name, () => lastInteraction = Date.now(), {passive:true}));
    setInterval(() => {
        if (document.visibilityState === 'visible' && Date.now() - lastInteraction < @json((int)config('kyp.courseware.idle_after_seconds', 90) * 1000)) {
            save({active_seconds:@json((int)config('kyp.courseware.heartbeat_seconds', 15))});
        }
    }, @json((int)config('kyp.courseware.heartbeat_seconds', 15) * 1000));
    window.addEventListener('beforeunload', () => save());
    render();
})();
</script>
@else
<script>
(() => {
    const steps=[...document.querySelectorAll('.learning-step')],nav=[...document.querySelectorAll('.step-link')];let current=0;
    const render=()=>{steps.forEach((s,i)=>s.hidden=i!==current);nav.forEach((n,i)=>n.classList.toggle('active',i===current));};
    nav.forEach((n,i)=>n.addEventListener('click',()=>{current=i;render()}));
    document.querySelectorAll('[data-next]').forEach(b=>b.addEventListener('click',()=>{current=Math.min(steps.length-1,current+1);render()}));
    document.querySelectorAll('[data-prev]').forEach(b=>b.addEventListener('click',()=>{current=Math.max(0,current-1);render()}));render();
})();
</script>
@endif
@endsection
