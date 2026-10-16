<article class="program-course-card">
    <div class="course-card-top"><span class="course-code">{{ $module['course']->code }}</span><span>{{ $module['course']->total_sessions }} सेशन · {{ $module['course']->total_hours }} घंटे</span></div>
    <h3>{{ $module['label'] }}</h3><p class="course-english">{{ $module['course']->name }}</p>
    <p>{{ $module['short'] }}</p>
    <ul>@foreach($module['highlights'] as $highlight)<li>{{ $highlight }}</li>@endforeach</ul>
    <a class="course-detail-link" href="{{ route('public.courses.show', $module['slug']) }}">{{ $module['course']->code }} का पूरा विवरण <span aria-hidden="true">→</span></a>
</article>
