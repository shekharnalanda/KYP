@extends('layouts.app')

@section('content')
<div style="max-width:1180px;margin:30px auto;padding:0 20px;">

    <div style="margin-bottom:24px;">
        <h1 style="margin:0 0 8px;font-size:30px;">
            Iris / Biometric Setup
        </h1>

        <p style="margin:0;color:#64748b;">
            Administrator-only installation resources for the
            KYP biometric attendance system.
        </p>
    </div>

    <div style="
        padding:18px 20px;
        margin-bottom:24px;
        background:#eff6ff;
        border:1px solid #bfdbfe;
        border-radius:12px;
    ">
        <strong>Mantra MIS100V2 Setup</strong><br>
        Install these resources only on authorized KYP attendance computers.
    </div>

    <div style="
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
        gap:20px;
    ">

        <div style="background:white;border:1px solid #e2e8f0;border-radius:14px;padding:24px;">
            <h3>KYP Iris Connector</h3>
            <p>
                KYP Windows biometric attendance connector for
                Mantra MIS100V2.
            </p>

            <a
                href="/downloads/kyp-iris/KYP-Iris-Connector-V3.2-Final.zip"
                style="
                    display:inline-block;
                    padding:11px 18px;
                    background:#0755a5;
                    color:white;
                    text-decoration:none;
                    border-radius:8px;
                    font-weight:700;
                "
            >
                Download KYP Iris Connector
            </a>
        </div>

        <div style="background:white;border:1px solid #e2e8f0;border-radius:14px;padding:24px;">
            <h3>Mantra MIS100V2 Driver</h3>
            <p>
                Device driver and required Mantra support resources
                for MIS100V2.
            </p>

            <a
                href="https://www.mantratec.com/"
                target="_blank"
                rel="noopener"
                style="
                    display:inline-block;
                    padding:11px 18px;
                    background:#0755a5;
                    color:white;
                    text-decoration:none;
                    border-radius:8px;
                    font-weight:700;
                "
            >
                Mantra Official Resources
            </a>
        </div>

        <div style="background:white;border:1px solid #e2e8f0;border-radius:14px;padding:24px;">
            <h3>Iris Setup & Support</h3>
            <p>
                Installation/support resources required for the
                KYP Iris Connector and attendance workstation.
            </p>

            <a
                href="/downloads/kyp-iris/"
                style="
                    display:inline-block;
                    padding:11px 18px;
                    background:#0755a5;
                    color:white;
                    text-decoration:none;
                    border-radius:8px;
                    font-weight:700;
                "
            >
                Open Iris Resources
            </a>
        </div>

    </div>

    <div style="
        margin-top:24px;
        padding:16px 18px;
        border:1px solid #e2e8f0;
        border-radius:10px;
        color:#475569;
    ">
        <strong>Security:</strong>
        Connector token, biometric templates and student biometric
        information must never be placed in public downloads.
    </div>

</div>
@endsection
