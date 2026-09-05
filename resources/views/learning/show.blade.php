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
    $allQuestions = collect($steps)->flatMap(fn(array $step) => $step['interactions'] ?? (isset($step['interaction']) ? [$step['interaction']] : []));
    $correctAnswers = $allQuestions->mapWithKeys(fn(array $question) => [$question['id'] => (string) $question['correct']]);
    $flowMap = [
        'CIT' => [['इनपुट', 'Input'], ['प्रोसेसिंग', 'Processing'], ['आउटपुट', 'Output'], ['संग्रह', 'Storage']],
        'CLS' => [['सुनें', 'Listen'], ['समझें', 'Understand'], ['बोलें', 'Speak'], ['समीक्षा', 'Review']],
        'CSS' => [['स्थिति', 'Situation'], ['विकल्प', 'Choice'], ['कार्रवाई', 'Action'], ['परिणाम', 'Result']],
        'AI-DM' => [['प्रॉम्प्ट', 'Prompt'], ['AI प्रारूप', 'AI Draft'], ['सत्यापन', 'Verify'], ['सुधार', 'Improve']],
    ];
    $flow = $flowMap[$session->course->code] ?? $flowMap['CIT'];
    $studentMode = auth()->user()->hasRole('student');
    $currentStepNumber = (int) ($progress?->current_step ?? 0);
    $furthestStepNumber = max($currentStepNumber, (int) ($progress?->furthest_step ?? 0));
    $completedStepIds = collect($savedSteps);
@endphp
<x-portal-shell
 active="learning"
 eyebrow="{{ $session->course->code }} • SESSION {{ $session->session_number }}"
 title="{{ $session->title_hi }}"
 subtitle="{{ $session->title_en }} • 120-minute interactive {{ auth()->user()->hasRole('teacher') ? 'teaching' : 'lab' }} courseware"
>

@if(auth()->user()->hasRole('teacher'))
<div class="courseware-teacher-mode">
    <strong>Teacher Presentation Mode</strong>
    <span>यह session teaching/revision के लिए स्वतंत्र रूप से खुला है। Student progress, timer credit या completion record Teacher account पर लागू नहीं होगा।</span>
    <a class="btn btn-light" href="{{ route('learning.index') }}">← Choose Another Session</a>
</div>
@else
<div class="courseware-student-mode">
    <strong>Student Interactive Mode</strong>
    <span>Active learning time, activities, quiz और practical progress automatically save होंगे।</span>
</div>
@endif

<div class="courseware-shell" data-student="{{ auth()->user()->hasRole('student') ? '1' : '0' }}">

    @if(session('status'))<div class="notice success">{{ session('status') }}</div>@endif
    @error('session')<div class="notice error-box">{{ $message }}</div>@enderror

    <div class="learning-status">
        <div><strong>Active Learning</strong><span id="active-time">{{ intdiv($activeSeconds, 60) }} / {{ $session->required_active_minutes }} मिनट</span></div>
        <div class="status-grow"><strong>Session Progress</strong><div class="progress-track"><div id="progress-fill" style="width:{{ count($steps) ? round((count($savedSteps) / count($steps)) * 100) : 0 }}%"></div></div></div>
        <div><strong>Quiz Score</strong><span id="quiz-score">{{ (int) ($progress?->quiz_score ?? 0) }} / {{ $session->passing_score }}+</span></div>
        <div id="save-state" class="save-state">Auto-save ready</div>
    </div>

    <button type="button" class="course-menu-toggle" id="course-menu-toggle" aria-expanded="false" aria-controls="course-tree">☰ सभी Sessions और Topics</button>
    <div class="courseware-grid">
        <aside class="course-tree" id="course-tree" aria-label="{{ $session->course->code }} course sessions">
            <div class="tree-head">
                <strong>{{ $session->course->code }} Course Flow</strong>
                <small>{{ $courseSessions->count() }} sessions • एक ही learning window</small>
            </div>
            <div class="status-legend" aria-label="Status colour guide">
                <span class="legend-pending">● नया</span>
                <span class="legend-active">● चालू</span>
                <span class="legend-complete">● पूरा</span>
                <span class="legend-skipped">● छूटा</span>
                <span class="legend-locked">● लॉक</span>
            </div>
            <div class="session-tree-list">
                @foreach($courseSessions as $courseSession)
                    @php
                        $isCurrentSession = $courseSession->id === $session->id;
                        $isSessionDone = $completedSessionIds->contains($courseSession->id);
                        $sessionProgress = $courseProgressMap->get($courseSession->id);
                        $previousSession = $courseSessions->firstWhere('session_number', $courseSession->session_number - 1);
                        $isLocked = $studentMode && $previousSession && ! $completedSessionIds->contains($previousSession->id);
                        $wasLeftIncomplete = $studentMode && $sessionProgress && ! $isSessionDone && ! $isCurrentSession;
                        $sessionState = $isSessionDone ? 'complete' : ($isCurrentSession ? 'active' : ($isLocked ? 'locked' : ($wasLeftIncomplete ? 'skipped' : 'pending')));
                    @endphp
                    <div class="session-branch state-{{ $sessionState }}">
                        @if($isLocked)
                            <div class="session-row" aria-disabled="true">
                                <span class="state-dot"></span>
                                <span><b>Session {{ str_pad($courseSession->session_number, 2, '0', STR_PAD_LEFT) }}</b><small>{{ $courseSession->title_hi }}</small></span>
                                <em>🔒</em>
                            </div>
                        @else
                            <a class="session-row" href="{{ route('learning.show', $courseSession) }}" @if($isCurrentSession) aria-current="page" @endif>
                                <span class="state-dot"></span>
                                <span><b>Session {{ str_pad($courseSession->session_number, 2, '0', STR_PAD_LEFT) }}</b><small>{{ $courseSession->title_hi }}</small></span>
                                <em>{{ $isSessionDone ? '✓' : ($wasLeftIncomplete ? '!' : '›') }}</em>
                            </a>
                        @endif

                        @if($isCurrentSession)
                            <div class="topic-flow" aria-label="Current session topic flow">
                                @foreach($steps as $index => $step)
                                    @php
                                        $stepDone = $completed || $completedStepIds->contains($step['id']);
                                        $stepSkipped = ! $stepDone && $index < $furthestStepNumber;
                                        $stepState = $stepDone ? 'complete' : ($index === $currentStepNumber ? 'active' : ($stepSkipped ? 'skipped' : 'pending'));
                                    @endphp
                                    <button type="button" class="step-link state-{{ $stepState }}" data-step-target="{{ $index }}">
                                        <span class="state-dot"></span>
                                        <b>{{ $index + 1 }}. {{ $step['title'] }}</b>
                                        <small>{{ $step['minutes'] }} मिनट</small>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </aside>

        <main class="step-stage">
            @foreach($steps as $index => $step)
                <section class="learning-step" data-step="{{ $index }}" data-step-id="{{ $step['id'] }}" hidden>
                    <div class="step-meta"><span>{{ strtoupper($step['type']) }}</span><span>⏱ {{ $step['minutes'] }} मिनट</span></div>
                    <h2>{{ $step['title'] }}</h2>
                    <div class="lesson-copy">{!! nl2br(e($step['content'] ?? '')) !!}</div>

                    @if(isset($step['activity']))
                        @php($activity = $step['activity'])
                        @php($activityComplete = (bool)(($progress?->activity_states ?? [])[$activity['id']] ?? false))
                        <section class="skill-activity {{ $activityComplete ? 'is-complete' : '' }}" data-activity-id="{{ $activity['id'] }}" data-activity-type="{{ $activity['type'] }}" data-complete="{{ $activityComplete ? '1' : '0' }}">
                            <div class="activity-heading">
                                <div><span>ACTIVE LAB</span><h3>{{ $activity['title_hi'] }}</h3><p lang="en">{{ $activity['title_en'] }}</p></div>
                                <strong class="activity-badge">{{ $activityComplete ? 'पूरा • Complete' : 'करना बाकी • Action required' }}</strong>
                            </div>
                            <p class="activity-instruction">{{ $activity['instruction_hi'] }} <small lang="en">{{ $activity['instruction_en'] }}</small></p>

                            @if($activity['type'] === 'reveal')
                                <div class="reveal-grid">
                                    @foreach($activity['items'] as $itemIndex => $item)
                                        <button type="button" class="reveal-card {{ $activityComplete ? 'is-open' : '' }}" data-reveal-item>
                                            <span class="reveal-front"><b>{{ $item['hi'] }}</b><small lang="en">{{ $item['en'] }}</small></span>
                                            <span class="reveal-detail"><b>{{ $item['detail_hi'] }}</b><small lang="en">{{ $item['detail_en'] }}</small></span>
                                        </button>
                                    @endforeach
                                </div>
                            @elseif($activity['type'] === 'sequence')
                                @php($sequenceItems = collect($activity['items'])->sortBy(fn($item) => (($item['order'] + $session->session_number) % count($activity['items'])))->values())
                                <div class="sequence-board">
                                    @foreach($sequenceItems as $item)
                                        <button type="button" class="sequence-item" data-sequence-order="{{ $item['order'] }}">
                                            <span>{{ $item['order'] }}</span><b>{{ $item['hi'] }}</b><small lang="en">{{ $item['en'] }}</small>
                                        </button>
                                    @endforeach
                                </div>
                            @elseif($activity['type'] === 'observe')
                                <div class="activity-flow" data-observe-flow>
                                    <div class="activity-flow-track"><i></i></div>
                                    @foreach($activity['items'] as $item)
                                        <div class="activity-flow-node"><span>{{ $item['order'] }}</span><b>{{ $item['hi'] }}</b><small lang="en">{{ $item['en'] }}</small></div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn activity-play" data-observe-play>▶ Animation चलाएँ • Play animation</button>
                            @elseif($activity['type'] === 'checklist')
                                <div class="activity-checklist">
                                    @foreach($activity['items'] as $itemIndex => $item)
                                        <label><input type="checkbox" data-check-item @checked($activityComplete)><span><b>{{ $item['hi'] }}</b><small lang="en">{{ $item['en'] }}</small></span></label>
                                    @endforeach
                                </div>
                            @endif
                            <div class="activity-feedback" aria-live="polite">{{ $activityComplete ? 'Activity सुरक्षित रूप से पूरी है। • Activity completed and saved.' : '' }}</div>
                        </section>
                    @endif

                    @foreach(($step['interactions'] ?? (isset($step['interaction']) ? [$step['interaction']] : [])) as $interaction)
                        <div class="interaction-card">
                            <h3>🧠 Interactive Question • द्विभाषी प्रश्न</h3>
                            <p class="question-hi">{{ $interaction['prompt_hi'] ?? $interaction['prompt'] }}</p>
                            <p class="question-en" lang="en">{{ $interaction['prompt_en'] ?? '' }}</p>
                            @foreach($interaction['options'] as $optionIndex => $option)
                                @php($optionHi = is_array($option) ? $option['hi'] : $option)
                                @php($optionEn = is_array($option) ? ($option['en'] ?? '') : '')
                                <label class="choice">
                                    <input type="radio" name="{{ $interaction['id'] }}" value="{{ $optionIndex }}" @checked((string)($savedAnswers[$interaction['id']] ?? '') === (string)$optionIndex)>
                                    <span><b>{{ $optionHi }}</b><small class="option-en" lang="en">{{ $optionEn }}</small></span>
                                </label>
                            @endforeach
                            <div class="answer-feedback" data-feedback="{{ $interaction['id'] }}"></div>
                        </div>
                    @endforeach

                    @if($step['type'] === 'practical')
                        <div class="interaction-card">
                            <h3>💻 Practical Activity Record</h3>
                            <p>आपने क्या किया, कौन-से steps अपनाए और क्या परिणाम मिला—स्पष्ट रूप से लिखें।</p>
                            <textarea id="practical-response" rows="7" placeholder="अपना practical work और learning note यहाँ लिखें...">{{ $progress?->practical_response }}</textarea>
                        </div>
                    @endif

                    <div class="step-gate-message" data-step-gate aria-live="polite"></div>
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
</div>

<style>

.courseware-teacher-mode,
.courseware-student-mode{
 display:flex;align-items:center;gap:14px;
 padding:15px 17px;margin-bottom:17px;
 border-radius:15px;border:1px solid #cbddec;
 background:#f7fbff
}
.courseware-teacher-mode{
 border-color:#9eddd6;
 background:linear-gradient(135deg,#effffd,#f5fbff)
}
.courseware-teacher-mode strong,
.courseware-student-mode strong{
 color:#062b63;white-space:nowrap
}
.courseware-teacher-mode span,
.courseware-student-mode span{
 flex:1;color:#52627a;font-size:12px;line-height:1.5
}
.courseware-shell{
 padding:0;
 background:transparent
}
@media(max-width:700px){
 .courseware-teacher-mode,
 .courseware-student-mode{
   align-items:flex-start;
   flex-direction:column
 }
}

.courseware-head{display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap}.learning-status{display:flex;gap:18px;align-items:center;flex-wrap:wrap;margin:24px 0;padding:18px;border:1px solid #cfe0f4;border-radius:18px;background:#f7fbff}.learning-status>div{display:grid;gap:5px}.learning-status span{color:var(--muted);font-size:14px}.status-grow{flex:1;min-width:220px}.progress-track{height:10px;border-radius:99px;background:#dce8f6;overflow:hidden}.progress-track div{height:100%;background:linear-gradient(90deg,var(--teal),#0b86d7);transition:width .35s}.save-state{font-size:13px;color:#057d78}.course-menu-toggle{display:none;width:100%;margin:0 0 14px;padding:13px 16px;border:1px solid #b9cee5;border-radius:12px;background:#fff;color:#082c5c;font-weight:800;text-align:left}.courseware-grid{display:grid;grid-template-columns:minmax(300px,350px) minmax(0,1fr);gap:24px}.course-tree{position:sticky;top:14px;align-self:start;max-height:calc(100vh - 28px);overflow:auto;border:1px solid #cfe0f4;border-radius:18px;background:#f8fbff;box-shadow:0 10px 28px rgba(7,39,83,.07)}.tree-head{position:sticky;top:0;z-index:3;display:grid;gap:4px;padding:16px;background:#062b63;color:#fff}.tree-head small{color:#cce7ff}.status-legend{display:flex;flex-wrap:wrap;gap:7px 11px;padding:11px 14px;border-bottom:1px solid #dbe8f5;background:#fff;font-size:11px;font-weight:800}.legend-pending{color:#151c27}.legend-active{color:#086acb}.legend-complete{color:#078143}.legend-skipped{color:#c22b2b}.legend-locked{color:#8995a5}.session-tree-list{padding:9px}.session-branch{margin-bottom:7px}.session-row{display:grid;grid-template-columns:14px minmax(0,1fr) 22px;align-items:center;gap:9px;width:100%;padding:10px;border:1px solid transparent;border-radius:11px;color:#151c27;text-decoration:none;background:#fff}.session-row:hover{border-color:#a9c8e8}.session-row>span:nth-child(2){display:grid;gap:2px;min-width:0}.session-row b{font-size:12px}.session-row small{overflow:hidden;color:inherit;font-size:11px;line-height:1.3;text-overflow:ellipsis;white-space:nowrap}.session-row em{font-style:normal;font-weight:900;text-align:center}.state-dot{display:block;width:10px!important;height:10px!important;border-radius:50%;background:#151c27!important}.session-branch.state-active>.session-row{border-color:#1782d4;background:#eaf5ff;color:#075fa7}.session-branch.state-active>.session-row .state-dot,.step-link.state-active .state-dot{background:#0878cf!important;box-shadow:0 0 0 4px rgba(8,120,207,.13)}.session-branch.state-complete>.session-row{color:#087443}.session-branch.state-complete>.session-row .state-dot,.step-link.state-complete .state-dot{background:#0a9b55!important}.session-branch.state-skipped>.session-row{border-color:#f0c0c0;background:#fff5f5;color:#b42323}.session-branch.state-skipped>.session-row .state-dot,.step-link.state-skipped .state-dot{background:#cf3030!important}.session-branch.state-locked>.session-row{background:#f0f3f6;color:#8995a5;cursor:not-allowed}.session-branch.state-locked>.session-row .state-dot{background:#a5afbb!important}.topic-flow{display:grid;gap:5px;margin:7px 0 12px;padding:7px 0 7px 19px;border-left:2px solid #bfd6ec}.step-link{display:grid;grid-template-columns:14px minmax(0,1fr) auto;align-items:center;gap:8px;width:100%;padding:9px;border:1px solid #d8e5f3;background:#fff;border-radius:10px;color:#151c27;cursor:pointer;text-align:left}.step-link b{font-size:11px;line-height:1.3}.step-link small{color:inherit;font-size:10px}.step-link.state-active{border-color:#0878cf;background:#edf7ff;color:#075fa7}.step-link.state-complete{border-color:#a7dfc3;background:#effcf5;color:#087443}.step-link.state-skipped{border-color:#efb4b4;background:#fff2f2;color:#b42323}.step-link.state-pending{color:#151c27}.step-stage{min-width:0}.learning-step{min-height:550px;padding:30px;border:1px solid #dce8f6;border-radius:22px;background:#fff;box-shadow:0 14px 38px rgba(7,39,83,.07)}.step-meta{display:flex;justify-content:space-between;color:#057d78;font-weight:800;font-size:12px}.learning-step h2{font-size:clamp(26px,4vw,38px);color:var(--navy)}.lesson-copy{font-size:17px;line-height:1.85;white-space:normal}.process-animation{position:relative;display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin:18px 0 24px;padding:25px 18px;border-radius:18px;background:linear-gradient(130deg,#062b63,#0b6b8c);overflow:hidden;color:#fff}.flow-line{position:absolute;left:11%;right:11%;top:45px;height:4px;border-radius:9px;background:rgba(255,255,255,.35)}.flow-pulse{position:absolute;left:0;display:block;width:16px;height:16px;margin-top:-6px;border-radius:50%;background:#ffd166;box-shadow:0 0 18px #ffd166;animation:flowMove 4s linear infinite}.flow-node{position:relative;z-index:1;text-align:center}.flow-number{display:grid;place-items:center;width:42px;height:42px;margin:0 auto 10px;border-radius:50%;background:#fff;color:#06366b;font-weight:900}.flow-node strong,.flow-node small{display:block}.flow-node small{margin-top:3px;color:#d9f7ff}@keyframes flowMove{from{left:0}to{left:calc(100% - 16px)}}.skill-activity{margin:28px 0;padding:22px;border:2px solid #cbddec;border-radius:18px;background:linear-gradient(180deg,#f9fcff,#f1f8ff)}.skill-activity.is-complete{border-color:#92d7b5;background:linear-gradient(180deg,#f7fffb,#eafaf2)}.activity-heading{display:flex;justify-content:space-between;align-items:flex-start;gap:14px}.activity-heading>div>span{display:inline-block;padding:4px 8px;border-radius:99px;background:#0b6b8c;color:#fff;font-size:10px;font-weight:900;letter-spacing:.08em}.activity-heading h3{margin:8px 0 2px;color:#092e60;font-size:21px}.activity-heading p{margin:0;color:#526a82;font-size:13px}.activity-badge{padding:7px 10px;border-radius:9px;background:#fff3cf;color:#8a5a00;font-size:11px;white-space:nowrap}.is-complete .activity-badge{background:#dff7ea;color:#087443}.activity-instruction{padding:12px 14px;border-left:4px solid #0b86d7;background:#fff;line-height:1.55}.activity-instruction small,.reveal-card small,.sequence-item small,.activity-checklist small,.activity-flow-node small{display:block;margin-top:4px;color:#526a82;font-weight:500}.reveal-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}.reveal-card{min-height:105px;padding:15px;border:1px solid #bad1e8;border-radius:14px;background:#fff;color:#12375f;text-align:left;cursor:pointer;transition:.2s}.reveal-card:hover{transform:translateY(-2px);border-color:#0b86d7}.reveal-front,.reveal-detail{display:block}.reveal-front b,.reveal-detail b{display:block}.reveal-detail{display:none;margin-top:10px;padding-top:10px;border-top:1px dashed #b8cbdf;color:#087443}.reveal-card.is-open{border-color:#37a874;background:#f1fff8}.reveal-card.is-open .reveal-detail{display:block}.sequence-board{display:grid;gap:9px}.sequence-item{display:grid;grid-template-columns:34px minmax(0,1fr);gap:3px 10px;align-items:center;padding:12px;border:1px solid #c8daeb;border-radius:12px;background:#fff;color:#173b61;text-align:left;cursor:pointer}.sequence-item>span{grid-row:1/3;display:grid;place-items:center;width:32px;height:32px;border-radius:50%;background:#edf3fa;color:#637a92;font-weight:900}.sequence-item small{grid-column:2}.sequence-item.is-selected{border-color:#39ad76;background:#edfff6;color:#087443}.sequence-item.is-selected>span{background:#0a9b55;color:#fff}.activity-checklist{display:grid;gap:9px}.activity-checklist label{display:flex;gap:11px;align-items:flex-start;padding:12px;border:1px solid #c8daeb;border-radius:12px;background:#fff;cursor:pointer}.activity-checklist input{width:19px;height:19px;accent-color:#0a9b55;flex:0 0 auto}.activity-checklist span,.activity-checklist b{display:block}.activity-flow{position:relative;display:grid;grid-template-columns:repeat(4,1fr);gap:12px;padding:24px 12px;margin-bottom:14px;border-radius:15px;background:#062b63;color:#fff;overflow:hidden}.activity-flow-track{position:absolute;left:10%;right:10%;top:43px;height:4px;background:rgba(255,255,255,.3)}.activity-flow-track i{position:absolute;left:0;top:-6px;width:16px;height:16px;border-radius:50%;background:#ffd166;box-shadow:0 0 16px #ffd166}.activity-flow.is-playing .activity-flow-track i{animation:activityTravel 4.5s linear forwards}.activity-flow-node{position:relative;z-index:1;text-align:center}.activity-flow-node>span{display:grid;place-items:center;width:40px;height:40px;margin:0 auto 9px;border-radius:50%;background:#fff;color:#06366b;font-weight:900}.activity-flow-node b{display:block}.activity-flow-node small{color:#cfefff}@keyframes activityTravel{from{left:0}to{left:calc(100% - 16px)}}.activity-feedback{min-height:22px;margin-top:12px;color:#087443;font-weight:800}.step-gate-message{display:none;margin-top:18px;padding:12px;border:1px solid #efb4b4;border-radius:11px;background:#fff2f2;color:#b42323;font-weight:800}.step-gate-message.show{display:block}.interaction-card{margin-top:26px;padding:22px;border-radius:17px;background:#f2f9ff;border:1px solid #cfe0f4}.question-hi{font-size:18px;font-weight:800;margin-bottom:3px}.question-en{margin-top:0;color:#38536f;font-size:15px}.choice{display:flex;gap:10px;align-items:flex-start;padding:12px;margin:9px 0;background:#fff;border:1px solid #dce8f6;border-radius:11px;cursor:pointer}.choice input{margin-top:5px;flex:0 0 auto}.choice span{display:block}.option-en{display:block;margin-top:5px;color:#526a82;font-weight:500;line-height:1.45}.answer-feedback{font-weight:700;margin-top:10px}.interaction-card textarea{width:100%;padding:14px;border-radius:12px;border:1px solid #bfd1e5;font:inherit;line-height:1.6}.step-actions{display:flex;justify-content:space-between;gap:12px;margin-top:28px}.notice{margin:18px 0;padding:14px;border-radius:12px}.success{background:#e8fff4;color:#087443;border:1px solid #a9e8cc}.error-box{background:#fff0ee;color:#a12a20;border:1px solid #f1bab4}.completion-note{text-align:center;color:var(--muted);font-size:13px}.full{width:100%;margin-top:22px}
@media(max-width:900px){.course-menu-toggle{display:block}.courseware-grid{grid-template-columns:1fr}.course-tree{display:none;position:static;max-height:65vh}.course-tree.mobile-open{display:block}.learning-step{padding:22px}}@media(prefers-reduced-motion:reduce){.flow-pulse,.activity-flow-track i{animation:none!important}}@media(max-width:650px){.process-animation,.activity-flow{grid-template-columns:repeat(2,1fr)}.flow-line,.activity-flow-track{display:none}.reveal-grid{grid-template-columns:1fr}.activity-heading{display:grid}.activity-badge{justify-self:start}}@media(max-width:520px){.step-actions{flex-direction:column-reverse}.step-actions .btn{width:100%}}
</style>

</x-portal-shell>

@if(auth()->user()->hasRole('student') && !$completed)
<script>
(() => {
    const steps = [...document.querySelectorAll('.learning-step')];
    const nav = [...document.querySelectorAll('.step-link')];
    const token = document.querySelector('meta[name="csrf-token"]')?.content || @json(csrf_token());
    const saveUrl = @json(route('learning.progress', $session));
    const correctAnswers = @json($correctAnswers);
    let current = Math.min(@json($initialStep), Math.max(0, steps.length - 1));
    let completed = new Set(@json($savedSteps));
    let answers = @json($savedAnswers);
    let activityStates = @json($progress?->activity_states ?? []);
    let activeSeconds = @json($activeSeconds);
    let lastInteraction = Date.now();
    let saveTimer;

    function render() {
        steps.forEach((step, i) => step.hidden = i !== current);
        nav.forEach((item, i) => item.classList.toggle('active', i === current));
        document.getElementById('progress-fill').style.width = (completed.size / Math.max(1, steps.length) * 100) + '%';
        nav.forEach((item, i) => {
            item.classList.remove('state-pending', 'state-active', 'state-complete', 'state-skipped');
            const done = completed.has(steps[i].dataset.stepId);
            item.classList.add(done ? 'state-complete' : (i === current ? 'state-active' : (i < current ? 'state-skipped' : 'state-pending')));
        });
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
                    activity_states: activityStates,
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

    function completeActivity(card, message) {
        const id = card.dataset.activityId;
        activityStates[id] = true;
        card.dataset.complete = '1';
        card.classList.add('is-complete');
        card.querySelector('.activity-badge').textContent = 'पूरा • Complete';
        card.querySelector('.activity-feedback').textContent = message || 'Activity पूरी हुई और save हो गई। • Activity completed and saved.';
        save();
    }

    document.querySelectorAll('[data-activity-type="reveal"]').forEach(card => {
        const items = [...card.querySelectorAll('[data-reveal-item]')];
        items.forEach(item => item.addEventListener('click', () => {
            item.classList.add('is-open');
            if (items.every(entry => entry.classList.contains('is-open'))) completeActivity(card);
        }));
    });

    document.querySelectorAll('[data-activity-type="sequence"]').forEach(card => {
        let nextOrder = 1;
        card.querySelectorAll('[data-sequence-order]').forEach(item => item.addEventListener('click', () => {
            if (Number(item.dataset.sequenceOrder) === nextOrder) {
                item.classList.add('is-selected');
                item.disabled = true;
                nextOrder++;
                card.querySelector('.activity-feedback').textContent = 'सही क्रम • Correct sequence: ' + (nextOrder - 1) + '/4';
                if (nextOrder > card.querySelectorAll('[data-sequence-order]').length) completeActivity(card, 'सही क्रम पूरा हुआ। • Correct sequence completed.');
            } else {
                card.querySelectorAll('[data-sequence-order]').forEach(entry => { entry.classList.remove('is-selected'); entry.disabled = false; });
                nextOrder = 1;
                card.querySelector('.activity-feedback').textContent = 'क्रम सही नहीं है—Step 1 से फिर चुनें। • Try again from Step 1.';
            }
        }));
    });

    document.querySelectorAll('[data-activity-type="checklist"]').forEach(card => {
        const checks = [...card.querySelectorAll('[data-check-item]')];
        checks.forEach(check => check.addEventListener('change', () => {
            if (checks.every(entry => entry.checked)) completeActivity(card, 'सभी checks पूरे हुए। • All checks completed.');
        }));
    });

    document.querySelectorAll('[data-observe-play]').forEach(button => button.addEventListener('click', () => {
        const card = button.closest('[data-activity-id]');
        const flow = card.querySelector('[data-observe-flow]');
        button.disabled = true;
        flow.classList.remove('is-playing');
        void flow.offsetWidth;
        flow.classList.add('is-playing');
        card.querySelector('.activity-feedback').textContent = 'Animation चल रही है—हर stage ध्यान से देखें। • Watch every stage.';
        setTimeout(() => {
            button.disabled = false;
            completeActivity(card, 'पूरा process देखा गया। • Complete process observed.');
        }, 4800);
    }));

    nav.forEach((item, i) => item.addEventListener('click', () => { current = i; render(); save(); }));
    document.querySelectorAll('[data-next]').forEach(button => button.addEventListener('click', () => {
        const step = steps[current];
        const gate = step.querySelector('[data-step-gate]');
        const activity = step.querySelector('[data-activity-id]');
        const question = step.querySelector('input[type="radio"]');
        const practical = step.querySelector('#practical-response');
        const activityReady = !activity || activityStates[activity.dataset.activityId] === true;
        const questionReady = !question || Object.prototype.hasOwnProperty.call(answers, question.name);
        const practicalReady = !practical || practical.value.trim().length >= 20;

        if (!activityReady || !questionReady || !practicalReady) {
            const missing = [];
            if (!activityReady) missing.push('interactive activity');
            if (!questionReady) missing.push('bilingual question');
            if (!practicalReady) missing.push('कम-से-कम 20 अक्षरों का practical record');
            gate.textContent = 'पहले पूरा करें • Complete first: ' + missing.join(', ');
            gate.classList.add('show');
            return;
        }

        gate.textContent = '';
        gate.classList.remove('show');
        completed.add(step.dataset.stepId);
        nav[current].classList.add('state-complete');
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
        feedback.textContent = correctAnswers[event.target.name] === event.target.value ? 'सही उत्तर ✓ • Correct answer' : 'फिर से सोचें • Think again and review the practical step.';
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
    const menuToggle = document.getElementById('course-menu-toggle');
    const courseTree = document.getElementById('course-tree');
    menuToggle?.addEventListener('click', () => {
        const open = courseTree.classList.toggle('mobile-open');
        menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    render();
})();
</script>
@else
<script>
(() => {
    const steps=[...document.querySelectorAll('.learning-step')],nav=[...document.querySelectorAll('.step-link')];let current=0;
    const render=()=>{steps.forEach((s,i)=>s.hidden=i!==current);nav.forEach((n,i)=>n.classList.toggle('active',i===current));};
    nav.forEach((n,i)=>n.addEventListener('click',()=>{current=i;render()}));
    const menuToggle=document.getElementById('course-menu-toggle'),courseTree=document.getElementById('course-tree');
    menuToggle?.addEventListener('click',()=>{const open=courseTree.classList.toggle('mobile-open');menuToggle.setAttribute('aria-expanded',open?'true':'false')});
    document.querySelectorAll('[data-next]').forEach(b=>b.addEventListener('click',()=>{current=Math.min(steps.length-1,current+1);render()}));
    document.querySelectorAll('[data-prev]').forEach(b=>b.addEventListener('click',()=>{current=Math.max(0,current-1);render()}));render();
})();
</script>
@endif
@endsection
