# KYP Mantra MIS100V2 Iris Connector

## Locked scope

- Iris only; fingerprint/MFS100 is not used.
- Device: Mantra MIS100V2 on a trusted Windows computer.
- Students present their iris directly for attendance. They do not select a Student ID, QR code or roll number during attendance.
- The operator selects the course and learning session once for the lab.
- Matching happens locally; only verified events are sent to the KYP portal.

## Required vendor runtime

Keep these files together beside the Windows x86 connector executable:

- MIDIris_Auth.dll
- MIDIris_Auth_Core.dll
- iris_engine_v3.dll
- iris_image_record.dll
- MR014_MIS100V2_Windows_Auth_IPL.dll

Install the supplied MIS100V2 driver before starting the connector. The Mantra test application and KYP connector must not run at the same time.

The supplied authentication assembly is 32-bit. Build and run the connector as x86, including on 64-bit Windows.

## Portal configuration

Generate a long random secret and add it to production .env:

    KYP_IRIS_CONNECTOR_TOKEN=<random-secret>
    KYP_IRIS_MINIMUM_SESSION_MINUTES=110

Then run:

    php artisan migrate --force
    php artisan optimize:clear

Store the same secret in the Windows connector's protected machine-level configuration. Never put it in browser JavaScript, screenshots or source control.

## Connector API

Every request must use HTTPS and:

    Authorization: Bearer <KYP_IRIS_CONNECTOR_TOKEN>
    Accept: application/json

Endpoints:

- GET /api/iris/health - connectivity check
- GET /api/iris/candidates - active encrypted-at-rest student templates for the authenticated local matcher
- POST /api/iris/enroll - save or replace a student's iris enrollment
- POST /api/iris/attendance - idempotent check-in/check-out event sync

Each attendance event needs a connector-generated UUID. Retrying the same UUID does not create duplicate attendance.

## Privacy and operating rules

- The portal stores templates encrypted at rest; raw preview images are not stored.
- Candidate templates are available only to the token-authenticated connector over HTTPS.
- The connector should keep only an encrypted local cache and clear preview images immediately after matching.
- Check-out requires an earlier check-in for the same student/session/date.
- Session completion requires at least 110 verified minutes, capped at the learning session duration.
- Offline events may sync for up to seven days and cannot be future-dated by more than five minutes.
