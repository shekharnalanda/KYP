# KYP Iris Connector V4

## Preserved from V3.2
- Kushal Youth Programme premium Windows UI
- DPAPI CurrentUser connector-token protection
- Student loading and course/session catalog
- Left/right iris enrollment
- Candidate identification
- Check-In / Check-Out workflow
- Laravel KYP Iris API integration
- Duplicate-event protection on server
- Minimum-session attendance validation

## V4 hardware corrections
- Mantra MIS100V2 runtime is built as x86.
- InitDevice uses runtime reflection compatible with the tested SDK.
- MIS100V2 hardware detection, live AutoCapture and MatchIrisData have been validated on the target Windows machine.
- Raw transient biometric byte arrays are cleared after use.

## Threshold policy
V3.2 used a hard-coded threshold of 1000.

V4 does not silently replace it with an arbitrary lower value.
`matchThreshold` is configuration-controlled and must be validated before production attendance is enabled.

Observed development tests are evidence for integration testing only and are not vendor certification of a production biometric threshold.
