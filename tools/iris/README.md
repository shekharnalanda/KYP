# Mantra MIS100V2 Windows Connector

KYP attendance uses **iris only**. Fingerprint/MFS100 support must not be added.

## SDK signature inspection

1. Close `MIDIrisTest.exe`.
2. Extract the supplied MIS100V2 ZIP under Downloads.
3. Open Windows PowerShell.
4. Run:

```powershell
powershell -ExecutionPolicy Bypass -File .\Inspect-MantraSdk.ps1
```

The utility searches the script directory, Downloads, and Desktop for
`MIDIris_Auth.dll`, loads the x86 .NET SDK, and creates
`KYP-MIS100V2-SDK-SIGNATURES.txt` on the Desktop.

The report contains method/delegate signatures only. It does not export iris
images, iris templates, student data, passwords, or the production connector
token.

The final connector must be compiled for **x86**, even on 64-bit Windows.
