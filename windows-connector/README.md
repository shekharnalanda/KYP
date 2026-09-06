# KYP Iris Connector

Windows biometric attendance connector for:

Kushal Youth Programme
Mantra MIS100V2

## Final workflow

1. Secure KYP server authentication
2. Detect Mantra MIS100V2
3. Load active students
4. Iris enrollment
5. Live iris capture
6. Identify enrolled student
7. Select course and published session
8. Check-In
9. Check-Out
10. Sync verified attendance to KYP

## Security

- Connector token is NOT stored in Git.
- Windows DPAPI CurrentUser protects the local connector token.
- Raw iris captures/templates must never be committed.
- Biometric material must not be written to application logs.
- Attendance is submitted only after successful biometric identification.
- Server provides duplicate-event protection and attendance validation.

## Branding

Official product name:

Kushal Youth Programme

Official logo:

assets/kyp-logo.webp
