@extends('layouts.app')
@section('title', 'Kushal Youth Program | Skills for the Digital Future')
@section('body')
<div class="topbar"><div class="container"><span>270 घंटे का आधुनिक रोजगारोन्मुख कौशल कार्यक्रम</span><span>Hindi-first Learning • Online + Classroom</span></div></div>
<header>
    <nav class="container">
        <a class="brand" href="{{ route('home') }}">
            <img src="/images/kyp-logo.webp" alt="Kushal Youth Program Logo" onerror="this.style.display='none';this.nextElementSibling.style.display='grid'">
            <span class="brand-fallback">KYP</span>
            <span><strong>KUSHAL YOUTH PROGRAM</strong><small>Skills for the Digital Future</small></span>
        </a>
        <div class="links">
<a href="#courses">Courses</a>
<a href="#system">Learning System</a>
<a href="#eligibility">Eligibility</a>
<a href="{{ route('enquiry.form') }}">Enquiry</a>
<a class="btn btn-accent" href="{{ route('admission.form') }}">Online Admission</a>
<a class="btn" href="{{ route('login') }}">Portal Login</a>
</div>
    </nav>
</header>
<main>
<section class="hero">
    <div class="container hero-grid">
        <div>
            <span class="eyebrow">आधुनिक IT • Communication • Soft Skills • AI</span>
            <h1>कौशल से आत्मविश्वास,<br><span>आत्मविश्वास से भविष्य</span></h1>
            <p>Kushal Youth Program युवाओं को practical computer knowledge, language confidence, professional behaviour और latest AI तथा Digital Marketing skills के साथ भविष्य के लिए तैयार करता है।</p>
            <div class="actions">
<a class="btn btn-accent" href="{{ route('admission.form') }}">Online Admission</a>
<a class="btn btn-light" href="{{ route('enquiry.form') }}">Enquiry Now</a>
<a class="btn btn-light" href="{{ route('login') }}">Student / Teacher Login</a>
</div>
        </div>
        <div class="hero-card">
            <img class="big-logo" src="/images/kyp-logo.webp" alt="KYP Logo" onerror="this.style.display='none';this.nextElementSibling.style.display='grid'">
            <span class="logo-fallback">KYP</span>
            <div class="mini-stats"><div class="mini-stat"><b>270</b><span>Total Learning Hours</span></div><div class="mini-stat"><b>135</b><span>Two-Hour Sessions</span></div><div class="mini-stat"><b>4</b><span>Skill Modules</span></div><div class="mini-stat"><b>3</b><span>Integrated Panels</span></div></div>
        </div>
    </div>
</section>

<section class="soft" id="admission-enquiry">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow" style="color:#057d78;background:#e8fffd">
                JOIN KUSHAL YOUTH PROGRAM
            </span>
            <h2>Admission एवं Enquiry</h2>
            <p>
                अपनी पसंदीदा branch और course चुनें। Online admission में
                student photo एवं आवश्यक details submit करें या पहले enquiry भेजें।
            </p>
        </div>

        <div class="cards" style="grid-template-columns:repeat(2,1fr)">
            <article class="card">
                <div class="icon">A</div>
                <h3>Online Admission</h3>
                <p>
                    Complete admission application, branch/course selection,
                    student photograph, personal एवं guardian details के साथ।
                </p>
                <div style="margin-top:18px">
                    <a class="btn btn-primary"
                       href="{{ route('admission.form') }}">
                        Apply for Admission
                    </a>
                </div>
            </article>

            <article class="card">
                <div class="icon">E</div>
                <h3>Course Enquiry</h3>
                <p>
                    Course, preferred branch और contact details भेजें।
                    संबंधित branch आपकी enquiry को process करेगी।
                </p>
                <div style="margin-top:18px">
                    <a class="btn btn-light"
                       href="{{ route('enquiry.form') }}">
                        Send Enquiry
                    </a>
                </div>
            </article>
        </div>
    </div>
</section>

<section id="courses">
    <div class="container">
        <div class="section-head"><span class="eyebrow" style="color:#057d78;background:#e8fffd">PROGRAM STRUCTURE</span><h2>एक संपूर्ण कौशल विकास यात्रा</h2><p>Topic-wise sequential sessions, classroom explanation और guided lab practice के साथ व्यवस्थित learning journey।</p></div>
        <div class="cards">
            <article class="card"><div class="icon">CIT</div><h3>Information Technology</h3><p>Digital fundamentals, office productivity, internet safety और practical computer skills।</p><div class="metric">60 Sessions • 120 Hours</div></article>
            <article class="card"><div class="icon">CLS</div><h3>Language Skills</h3><p>Workplace Hindi-English communication, listening, speaking और confidence building।</p><div class="metric">40 Sessions • 80 Hours</div></article>
            <article class="card"><div class="icon">CSS</div><h3>Soft Skills</h3><p>Professional behaviour, teamwork, interview readiness और personal effectiveness।</p><div class="metric">20 Sessions • 40 Hours</div></article>
            <article class="card"><div class="icon">AI</div><h3>AI & Digital Marketing</h3><p>Latest AI tools, responsible usage, digital presence और modern marketing essentials।</p><div class="metric">15 Sessions • 30 Hours</div></article>
        </div>
    </div>
</section>
<section class="soft" id="system">
    <div class="container">
        <div class="section-head"><h2>Classroom और Lab का एकीकृत system</h2><p>Teacher वही session classroom में समझाएंगे जिसे student अपने login से lab में पूरा करेगा। Attendance, activity और progress administrator को एक ही स्थान पर उपलब्ध होंगे।</p></div>
        <div class="cards" style="grid-template-columns:repeat(3,1fr)"><article class="card"><div class="icon">S</div><h3>Student Panel</h3><p>Hindi-first lessons, lab activities, session progress, examination, result, marksheet और certificate।</p></article><article class="card"><div class="icon">T</div><h3>Teacher Panel</h3><p>Classroom lesson view, topic guidance, session completion और student support tools।</p></article><article class="card"><div class="icon">A</div><h3>Admin Panel</h3><p>Biometric attendance, classroom/lab monitoring, eligibility, examinations और bulk certificates।</p></article></div>
    </div>
</section>
<section id="eligibility">
    <div class="container">
        <div class="section-head"><h2>पारदर्शी examination eligibility</h2><p>Final online examination केवल निर्धारित minimum learning sessions पूरे होने के बाद उपलब्ध होगी।</p></div>
        <div class="cards" style="grid-template-columns:repeat(3,1fr)"><article class="card"><h3>CIT Eligibility</h3><div class="metric">48 of 60 Sessions</div></article><article class="card"><h3>CLS Eligibility</h3><div class="metric">32 of 40 Sessions</div></article><article class="card"><h3>CSS Eligibility</h3><div class="metric">16 of 20 Sessions</div></article></div>
    </div>
</section>
</main>
<footer><div class="container">© {{ date('Y') }} Kushal Youth Program • Skills for the Digital Future</div></footer>
@endsection
