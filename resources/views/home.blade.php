@extends('layouts.app')
@section('title', 'कुशल यूथ प्रोग्राम (KYP) | पाठ्यक्रम, सेशन और प्रवेश')
@push('head')
<meta name="description" content="KYP में CIT, CLS, CSS और AI-Digital Marketing की पढ़ाई, विषय, अभ्यास, सेशन और प्रवेश प्रक्रिया हिंदी में जानें।">
<link rel="stylesheet" href="{{ asset('css/program-guide.css') }}?v=20261016-1">
@endpush
@section('body')
@include('components.public-site-header')
<main id="main-content">
<section class="hero program-hero">
    <div class="container hero-grid">
        <div>
            <span class="eyebrow">MCI Educational Group • कुशल यूथ प्रोग्राम</span>
            <h1>कंप्यूटर चलाना सीखें।<br><span>आत्मविश्वास से आगे बढ़ें।</span></h1>
            <p>Kushal Youth Program (KYP) में कंप्यूटर के व्यावहारिक काम, Hindi-English में बातचीत, कार्यस्थल का व्यवहार और AI व Digital Marketing की बुनियाद साथ सीखें। शिक्षक की समझाइश और अपने हाथों से अभ्यास—दोनों एक ही पोर्टल पर।</p>
            <div class="actions">
                <a class="btn btn-accent" href="#courses">चारों मॉड्यूल देखें <span aria-hidden="true">↓</span></a>
                <a class="btn btn-light" href="{{ route('admission.form') }}">प्रवेश के लिए आवेदन</a>
            </div>
            <p class="hero-note">शुरुआती विद्यार्थियों के लिए • मुख्यतः हिंदी में समझाइश • Classroom + Lab</p>
        </div>
        <aside class="hero-card program-at-a-glance" aria-label="कार्यक्रम एक नज़र में">
            <div class="program-logo"><img src="{{ asset('images/kyp-logo.webp') }}" alt="KYP Logo"><div><strong>सीखें। अभ्यास करें। आगे बढ़ें।</strong><span>पूरा KYP कार्यक्रम एक साथ</span></div></div>
            <div class="mini-stats"><div class="mini-stat"><b>{{ $totalHours }}</b><span>कुल सीखने के घंटे</span></div><div class="mini-stat"><b>{{ $totalSessions }}</b><span>कुल Learning Sessions</span></div><div class="mini-stat"><b>{{ $modules->count() }}</b><span>कौशल मॉड्यूल</span></div><div class="mini-stat"><b>2 घंटे</b><span>हर सेशन की अवधि</span></div></div>
            <div class="study-note">कंप्यूटर + भाषा + व्यवहार + AI<br>एक ही कार्यक्रम में चारों तरह के कौशल।</div>
        </aside>
    </div>
</section>

<section class="program-section" id="about">
    <div class="container about-grid">
        <div><span class="section-label">KYP क्या है?</span><h2>पढ़ाई और कामकाज के बीच<br>कौशल का एक मज़बूत आधार</h2></div>
        <div class="body-copy"><p>यह MCI Educational Group का कौशल विकास और Learning Portal है। यहाँ विद्यार्थी केवल वीडियो देखकर नहीं रुकते—वे विषय समझते हैं, निर्देशित गतिविधियाँ करते हैं, अपने काम का अभ्यास करते हैं और अपनी प्रगति देखते हैं।</p><p>स्कूल या कॉलेज के विद्यार्थी, पहली नौकरी की तैयारी कर रहे युवा और अपने दैनिक डिजिटल काम स्वयं सीखना चाहने वाले लोग इससे उपयोगी आधार बना सकते हैं। कंप्यूटर का पिछला अनुभव कम हो, तब भी शुरुआत मूल बातों से होती है।</p><p><strong>पूरा कार्यक्रम एक साथ:</strong> CIT, CLS, CSS और AI-DM, KYP के चार मॉड्यूल हैं। प्रवेश पूरे KYP में होता है; नीचे हर मॉड्यूल की पढ़ाई अलग से समझाई गई है।</p></div>
    </div>
</section>

<section class="soft program-section" id="courses">
    <div class="container">
        <div class="section-head"><span class="section-label">क्या-क्या सीखेंगे?</span><h2>चार मॉड्यूल। रोज़मर्रा में काम आने वाली सीख।</h2><p>हर कार्ड में मुख्य जानकारी देखें। “पूरा विवरण” खोलने पर विषयवार पाठ्यक्रम, अभ्यास, उपयोगिता और सेशन सूची मिलेगी।</p></div>
        <div class="program-course-grid">
            @foreach($modules as $module)
                @include('components.public-course-card', ['module' => $module])
            @endforeach
        </div>
        <p class="program-footnote">{{ $totalSessions }} सेशन × 2 घंटे = {{ $totalHours }} घंटे। चारों मॉड्यूल पूरे KYP कार्यक्रम का हिस्सा हैं।</p>
    </div>
</section>

<section class="program-section" id="system">
    <div class="container">
        <div class="section-head"><span class="section-label">पढ़ाई कैसे होगी?</span><h2>समझें, खुद करें और अपनी प्रगति देखें</h2><p>Classroom में शिक्षक का मार्गदर्शन और Student Login में पाठ, गतिविधियाँ व अभ्यास एक साथ जुड़े हैं।</p></div>
        <ol class="learning-steps">
            <li><span>01</span><h3>विषय समझें</h3><p>शिक्षक उदाहरण और Demonstration से विषय समझाएँगे। उपलब्ध Lesson और Video से दोहराएँ।</p></li>
            <li><span>02</span><h3>Lab में स्वयं करें</h3><p>Student Login से संबंधित सेशन खोलें। Activity, Quiz और Practical कार्य पूरा करें।</p></li>
            <li><span>03</span><h3>अभ्यास और समीक्षा</h3><p>जहाँ कठिनाई हो, शिक्षक से पूछें। अपनी Progress देखें और पिछले विषय पर दोबारा अभ्यास करें।</p></li>
        </ol>
        @include('components.public-study-plan')
    </div>
</section>

<section class="soft program-section" id="benefits">
    <div class="container">
        <div class="section-head"><span class="section-label">सीखने के बाद</span><h2>वे काम, जिन्हें करने का आत्मविश्वास बढ़ेगा</h2></div>
        <div class="benefit-grid">
            <article class="card"><h3>Office और पढ़ाई के काम</h3><p>पत्र, Report, PDF, Budget Sheet, Attendance Sheet और Presentation बनाना; Email व ऑनलाइन सेवाएँ सँभालना।</p></article>
            <article class="card"><h3>बातचीत और नौकरी की तैयारी</h3><p>अपना परिचय देना, साफ़ Email लिखना, Resume तैयार करना, Interview का अभ्यास और टीम में सहयोग करना।</p></article>
            <article class="card"><h3>डिजिटल काम की नई समझ</h3><p>AI से उपयोगी Draft बनवाना, उसके उत्तर जाँचना और छोटे व्यवसाय के लिए Content व Social Media की योजना बनाना।</p></article>
        </div>
        <p class="program-footnote">ये कौशल आगे की पढ़ाई, शुरुआती Office/Customer Support कार्यों और स्वरोज़गार की तैयारी में सहायक हैं। अवसर विद्यार्थी के अभ्यास, योग्यता और उपलब्ध भूमिका पर निर्भर करते हैं।</p>
    </div>
</section>

<section class="program-section" id="eligibility">
    <div class="container">
        <div class="section-head"><span class="section-label">Progress और मूल्यांकन</span><h2>सीखने की प्रगति से परीक्षा की तैयारी तक</h2><p>सेशन के सक्रिय अभ्यास, गतिविधियों, Quiz और Practical submission से प्रगति दर्ज होती है। Final Exam की पात्रता के लिए संबंधित मॉड्यूल के न्यूनतम पूरे Lab Sessions आवश्यक हैं।</p></div>
        <div class="eligibility-grid">
        @foreach($modules as $module)
            <article class="eligibility-card"><strong>{{ $module['course']->code }}</strong><p><b>{{ $module['course']->minimum_exam_sessions ?? '—' }}</b> / {{ $module['course']->total_sessions }} सेशन</p><span>न्यूनतम पूरे Lab Sessions</span></article>
        @endforeach
        </div>
        <p class="program-footnote">केवल लॉगिन या वीडियो खोलने से सेशन पूरा नहीं होता। पात्रता के साथ Admin द्वारा उपलब्ध कराई गई परीक्षा, मूल्यांकन और Result Publication की प्रक्रिया भी लागू होती है। प्रकाशित परिणाम के बाद Marksheet और Certificate पोर्टल में उपलब्ध होते हैं।</p>
    </div>
</section>

<section class="soft program-section" id="admission-enquiry">
    <div class="container">
        <div class="section-head"><span class="section-label">प्रवेश की प्रक्रिया</span><h2>पहले जानकारी लें, फिर आवेदन करें</h2><p>फीस, बैच के दिन व समय, आयु/शैक्षणिक पात्रता और आवश्यक दस्तावेज़ अपनी चुनी हुई शाखा से स्पष्ट कर लें।</p></div>
        <ol class="admission-steps">
            <li><b>1</b><div><h3>कोर्स समझें और शाखा चुनें</h3><p>चारों मॉड्यूल का विवरण पढ़ें। किसी सवाल पर Enquiry भेजकर शाखा से जानकारी लें।</p></div></li>
            <li><b>2</b><div><h3>Online Admission Form भरें</h3><p>नाम, Mobile, Email, शैक्षणिक व अभिभावक विवरण दें और अपनी Photo अपलोड करें। Email को OTP से Verify करें।</p></div></li>
            <li><b>3</b><div><h3>आवेदन संख्या सँभालकर रखें</h3><p>Form जमा होने पर Reference Number मिलता है। शाखा आवेदन व विवरण जाँचकर प्रवेश की प्रक्रिया आगे बढ़ाती है।</p></div></li>
            <li><b>4</b><div><h3>स्वीकृति के बाद पढ़ाई शुरू करें</h3><p>Admin Approval के बाद Student Account बनाया जाता है। शाखा से Login और बैच की समय-सारणी लेकर Classroom व Lab से जुड़ें।</p></div></li>
        </ol>
        <div class="center-actions"><a class="btn btn-accent" href="{{ route('admission.form') }}">प्रवेश के लिए आवेदन करें</a><a class="btn btn-light" href="{{ route('enquiry.form') }}">पहले जानकारी लें</a><a class="text-link" href="{{ route('login') }}">पहले से विद्यार्थी हैं? Login करें →</a></div>
    </div>
</section>
<section class="program-section" id="faq">
    <div class="container faq-wrap"><div class="section-head"><span class="section-label">आपके सवाल</span><h2>शुरू करने से पहले जानें</h2></div>
        <details><summary>क्या चार अलग-अलग कोर्स में Admission लेना है?</summary><p>नहीं। प्रवेश पूरे KYP कार्यक्रम के लिए है। CIT, CLS, CSS और AI-DM उसी के चार मॉड्यूल हैं।</p></details>
        <details><summary>पूरा कार्यक्रम कितने दिन में होगा?</summary><p>कुल {{ $totalSessions }} सेशन और {{ $totalHours }} घंटे हैं। रोज़ 1 सेशन के अध्ययन लक्ष्य पर {{ $totalSessions }} अध्ययन-दिन लगते हैं। सप्ताह की छुट्टी, परीक्षा, दोहराव और बैच की समय-सारणी से कैलेंडर अवधि बदलती है; निश्चित शुरू व समाप्ति तारीख शाखा बताएगी।</p></details>
        <details><summary>क्या पहले से Computer या English आना ज़रूरी है?</summary><p>पढ़ाई मूल बातों से शुरू होती है और मुख्य समझाइश हिंदी में है। अभ्यास के लिए नियमित समय दें। प्रवेश की औपचारिक पात्रता अपनी शाखा से पता करें।</p></details>
        <details><summary>क्या मोबाइल से पढ़ाई हो सकती है?</summary><p>मोबाइल से Lesson, उपलब्ध Video और अपनी Progress देख सकते हैं। Typing, Documents, Spreadsheet और Presentation के सही अभ्यास के लिए Computer/Laptop या शाखा की Lab का उपयोग करें।</p></details>
        <details><summary>फीस, बैच और Admission की जानकारी कहाँ मिलेगी?</summary><p>Enquiry Form में शाखा चुनकर सवाल भेजें। उसी शाखा से फीस, भुगतान, उपलब्ध बैच और दस्तावेज़ों की जानकारी लेकर आवेदन करें।</p></details>
    </div>
</section>
</main>
@include('components.public-site-footer')

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

<button
type="button"
class="kyp-mobile-coming"
id="kyp-install-app"
>
📱 Install Mobile App
</button>

</section>

<script>
(() => {
    const button = document.getElementById('kyp-install-app');
    if (!button) return;

    let installPrompt = null;

    const installed =
        window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true;

    if (installed) {
        button.textContent = '✓ KYP App Installed';
        button.disabled = true;
        return;
    }

    window.addEventListener('beforeinstallprompt', event => {
        event.preventDefault();
        installPrompt = event;
        button.textContent = '📱 Install Mobile App';
    });

    button.addEventListener('click', async () => {

        if (installPrompt) {
            installPrompt.prompt();

            try {
                await installPrompt.userChoice;
            } finally {
                installPrompt = null;
            }

            return;
        }

        if (/iphone|ipad|ipod/i.test(navigator.userAgent)) {
            alert(
                'Safari में Share खोलें और Add to Home Screen चुनें।'
            );
            return;
        }

        alert(
            'Chrome menu में Install app या Add to Home screen चुनें।'
        );
    });

    window.addEventListener('appinstalled', () => {
        installPrompt = null;
        button.textContent = '✓ KYP App Installed';
        button.disabled = true;
    });
})();
</script>


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
 border:0;
 cursor:pointer;
 font:inherit;
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
