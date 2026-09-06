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

<!-- KYP-DOWNLOAD-CENTER-V32 -->

<section class="kyp-download-center">
<div class="container">

<div class="kyp-download-head">

<span>OFFICIAL RESOURCES</span>

<h2>Downloads & Resources</h2>

<p>
Official software and resources for
Kushal Youth Programme learning and
secure biometric attendance.
</p>

</div>

<div class="kyp-download-grid">

<div class="kyp-download-card kyp-featured-download">

<div class="kyp-download-icon">◉</div>

<h3>KYP Iris Connector V3.2</h3>

<p>
Final secure biometric attendance connector
for Mantra MIS100V2.
</p>

<div class="kyp-download-meta">
Windows 64-bit • Secure Edition • Final / Locked
</div>

<a
class="kyp-download-btn"
href="/downloads/kyp-iris/KYP-Iris-Connector-V3.2-Final.zip">
Download KYP Iris Connector
</a>

</div>


<div class="kyp-download-card">

<div class="kyp-download-icon">◎</div>

<h3>Mantra MIS100V2</h3>

<p>
Required Mantra iris device driver and
support software.
</p>

<a
class="kyp-resource-btn"
href="https://www.mantratec.com/"
target="_blank"
rel="noopener">
Mantra Official Resources
</a>

</div>


<div class="kyp-download-card">

<div class="kyp-download-icon">▣</div>

<h3>Microsoft .NET Framework 4.8</h3>

<p>
Required Windows runtime for the
KYP Iris Connector.
</p>

<a
class="kyp-resource-btn"
href="https://dotnet.microsoft.com/download/dotnet-framework/net48"
target="_blank"
rel="noopener">
Microsoft Official Download
</a>

</div>

</div>


<div class="kyp-download-security">

<strong>Secure Biometric Attendance</strong>

<span>
Verified iris identification is required
for every Check-In and Check-Out.
</span>

</div>

</div>
</section>

<style>

.kyp-download-center{
 padding:70px 20px;
 background:#f3f8ff;
}

.kyp-download-center .container{
 max-width:1180px;
 margin:auto;
}

.kyp-download-head{
 text-align:center;
 max-width:760px;
 margin:0 auto 38px;
}

.kyp-download-head span{
 font-weight:800;
 letter-spacing:1.4px;
 color:#0874d1;
 font-size:12px;
}

.kyp-download-head h2{
 margin:8px 0 10px;
 font-size:36px;
 color:#073b78;
}

.kyp-download-head p{
 color:#607086;
 font-size:16px;
}

.kyp-download-grid{
 display:grid;
 grid-template-columns:repeat(3,1fr);
 gap:22px;
}

.kyp-download-card{
 background:#fff;
 border:1px solid #dce8f6;
 border-radius:20px;
 padding:28px;
 box-shadow:0 12px 34px rgba(18,68,120,.08);
}

.kyp-featured-download{
 border:2px solid #0c83d8;
}

.kyp-download-icon{
 font-size:34px;
 color:#0874d1;
 margin-bottom:14px;
}

.kyp-download-card h3{
 color:#073b78;
 margin:0 0 10px;
}

.kyp-download-card p{
 color:#607086;
 min-height:70px;
}

.kyp-download-meta{
 font-size:12px;
 font-weight:700;
 color:#52708f;
 margin:12px 0 18px;
}

.kyp-download-btn,
.kyp-resource-btn{
 display:inline-block;
 padding:12px 20px;
 border-radius:10px;
 font-weight:800;
 text-decoration:none;
}

.kyp-download-btn{
 background:#078c5a;
 color:#fff;
}

.kyp-resource-btn{
 background:#eaf3ff;
 color:#075aa9;
}

.kyp-download-security{
 margin-top:25px;
 padding:17px 22px;
 background:#e8f8f1;
 border-radius:14px;
 color:#126444;
 display:flex;
 gap:12px;
 flex-wrap:wrap;
}

@media(max-width:850px){

 .kyp-download-grid{
  grid-template-columns:1fr;
 }

 .kyp-download-card p{
  min-height:auto;
 }

}

</style>


<!-- KYP-MOBILE-INSTALL-V1 -->

<section class="kyp-mobile-install">

<div>

<strong>
Kushal Youth Programme Mobile App
</strong>

<span>
Student learning, attendance, progress,
results and KYP account access on mobile.
</span>

</div>

<span class="kyp-mobile-coming">
Mobile App • Coming Soon
</span>

</section>

<style>

.kyp-mobile-install{
 max-width:1180px;
 margin:22px auto 32px;
 padding:20px 24px;
 border-radius:18px;
 background:#073b78;
 color:#fff;
 display:flex;
 align-items:center;
 justify-content:space-between;
 gap:20px;
}

.kyp-mobile-install strong{
 display:block;
 font-size:19px;
 margin-bottom:5px;
}

.kyp-mobile-install span{
 color:#d7e9ff;
}

.kyp-mobile-coming{
 white-space:nowrap;
 padding:13px 22px;
 background:#fff;
 color:#073b78 !important;
 border-radius:10px;
 font-weight:900;
}

@media(max-width:700px){

 .kyp-mobile-install{
  flex-direction:column;
  align-items:flex-start;
 }

}

</style>

@endsection
