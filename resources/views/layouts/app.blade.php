<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#062b63">
    <title>@yield('title', 'Kushal Youth Program')</title>
    <style>
        :root{--navy:#062b63;--navy2:#031b3f;--teal:#11c5bd;--orange:#ff9418;--ink:#17243c;--muted:#60708b;--surface:#f5f9ff}
        *{box-sizing:border-box}body{margin:0;font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;color:var(--ink);background:#fff}
        a{text-decoration:none;color:inherit}.container{width:min(1180px,92%);margin:auto}
        .topbar{background:var(--navy2);color:#dbeafe;font-size:13px}.topbar .container{display:flex;justify-content:space-between;padding:8px 0}
        header{position:sticky;top:0;z-index:20;background:rgba(255,255,255,.96);backdrop-filter:blur(14px);border-bottom:1px solid #e6eef8}
        nav{min-height:76px;display:flex;align-items:center;justify-content:space-between;gap:24px}.brand{display:flex;align-items:center;gap:12px}.brand img,.brand-fallback{width:58px;height:58px;border-radius:50%;object-fit:contain}.brand-fallback{display:none;place-items:center;background:var(--navy);color:#fff;font-weight:900;border:4px solid var(--teal)}
        .brand strong{display:block;color:var(--navy);font-size:18px;letter-spacing:.2px}.brand small{color:var(--muted)}
        .links{display:flex;align-items:center;gap:24px;font-weight:650;font-size:14px}.btn{display:inline-flex;align-items:center;justify-content:center;padding:12px 20px;border-radius:12px;background:var(--navy);color:#fff;font-weight:750;border:1px solid var(--navy)}.btn:hover{background:#0b3d82}.btn-accent{background:var(--orange);border-color:var(--orange);color:#1e293b}.btn-light{background:#fff;color:var(--navy);border-color:#cbdcf2}
        .hero{overflow:hidden;background:radial-gradient(circle at 85% 20%,rgba(17,197,189,.22),transparent 34%),linear-gradient(135deg,var(--navy2),var(--navy));color:#fff;padding:78px 0}.hero-grid{display:grid;grid-template-columns:1.2fr .8fr;gap:54px;align-items:center}.eyebrow{display:inline-flex;padding:7px 12px;border-radius:999px;background:rgba(17,197,189,.16);border:1px solid rgba(17,197,189,.45);color:#86fff5;font-weight:750;font-size:13px}.hero h1{font-size:clamp(38px,5vw,66px);line-height:1.06;margin:20px 0}.hero h1 span{color:var(--teal)}.hero p{font-size:18px;line-height:1.7;color:#d9e8ff;max-width:700px}.actions{display:flex;gap:14px;flex-wrap:wrap;margin-top:28px}
        .hero-card{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:28px;padding:26px;box-shadow:0 30px 70px rgba(0,0,0,.25)}.big-logo{width:190px;height:190px;margin:auto;display:block;object-fit:contain}.logo-fallback{display:none;width:190px;height:190px;margin:auto;border-radius:50%;place-items:center;background:var(--navy2);border:10px solid var(--teal);font-size:48px;font-weight:900;color:#fff}.mini-stats{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-top:22px}.mini-stat{background:rgba(255,255,255,.09);padding:15px;border-radius:14px;text-align:center}.mini-stat b{display:block;font-size:24px;color:#fff}.mini-stat span{font-size:12px;color:#cfe1fb}
        section{padding:72px 0}.section-head{text-align:center;max-width:760px;margin:0 auto 38px}.section-head h2{font-size:clamp(28px,4vw,42px);color:var(--navy);margin:8px 0 12px}.section-head p{color:var(--muted);line-height:1.7}.cards{display:grid;grid-template-columns:repeat(4,1fr);gap:18px}.card{border:1px solid #dce8f6;border-radius:20px;padding:24px;background:#fff;box-shadow:0 12px 35px rgba(7,39,83,.07)}.card .icon{width:48px;height:48px;border-radius:14px;display:grid;place-items:center;background:#e8fffd;color:#057d78;font-weight:900}.card h3{color:var(--navy);margin:18px 0 8px}.card p{color:var(--muted);line-height:1.6;font-size:14px}.metric{font-weight:850;color:var(--orange);margin-top:16px}.soft{background:var(--surface)}
        .panel-shell{min-height:100vh;background:var(--surface);padding:40px 0}.panel{background:#fff;border:1px solid #dce8f6;border-radius:22px;padding:28px;box-shadow:0 15px 45px rgba(7,39,83,.08)}.panel-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}.panel-card{padding:22px;border-radius:16px;background:#f7fbff;border:1px solid #dce8f6}
        .form-wrap{min-height:78vh;display:grid;place-items:center;background:linear-gradient(140deg,#edf8ff,#f8fbff)}.form-card{width:min(440px,92%);background:#fff;padding:34px;border-radius:24px;box-shadow:0 24px 70px rgba(7,39,83,.14);border:1px solid #dce8f6}.form-card h1{color:var(--navy);margin:0 0 8px}.field{margin-top:18px}.field label{display:block;font-weight:700;margin-bottom:7px}.field input{width:100%;padding:13px 14px;border:1px solid #cbd8e8;border-radius:11px;font:inherit}.field input:focus{outline:3px solid rgba(17,197,189,.2);border-color:var(--teal)}.error{color:#b42318;font-size:13px;margin-top:6px}.full{width:100%;margin-top:22px}
        footer{background:var(--navy2);color:#bcd0ea;padding:28px 0;text-align:center}
        @media(max-width:900px){.links a:not(.btn){display:none}.hero-grid{grid-template-columns:1fr}.cards{grid-template-columns:repeat(2,1fr)}.panel-grid{grid-template-columns:1fr}.hero-card{max-width:520px}.topbar .container{justify-content:center}.topbar span:last-child{display:none}}
        @media(max-width:560px){nav{min-height:68px}.brand strong{font-size:15px}.brand small{font-size:11px}.brand img,.brand-fallback{width:48px;height:48px}.cards{grid-template-columns:1fr}.hero{padding:54px 0}.hero h1{font-size:40px}.btn{padding:11px 15px}}
    </style>

<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<meta name="theme-color" content="#073b78">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="apple-touch-icon" href="{{ asset('images/app-icons/kyp-192.png') }}">

<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker
            .register('/service-worker.js')
            .catch(function () {});
    });
}
</script>

</head>
<body>
@yield('body')
</body>
</html>
