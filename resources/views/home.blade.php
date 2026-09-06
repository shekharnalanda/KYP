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
