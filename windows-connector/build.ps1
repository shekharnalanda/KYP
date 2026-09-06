$ErrorActionPreference="Stop"

$Root=Split-Path -Parent $MyInvocation.MyCommand.Path
$Src=Join-Path $Root "src\Program.cs"
$Runtime=Join-Path $Root "runtime"
$Build=Join-Path $Root "build"

$CSC="$env:WINDIR\Microsoft.NET\Framework64\v4.0.30319\csc.exe"

if(!(Test-Path $CSC)){
    throw ".NET Framework 4.x C# compiler not found."
}

if(!(Test-Path $Src)){
    throw "V3 Program.cs not found."
}

$Required=@(
 "MIDIris_Auth.dll",
 "MIDIris_Auth_Core.dll",
 "MR014_MIS100V2_Windows_Auth_IPL.dll",
 "iris_engine_v3.dll",
 "iris_image_record.dll"
)

foreach($Name in $Required){
    if(!(Test-Path (Join-Path $Runtime $Name))){
        throw "Missing Mantra runtime: $Name"
    }
}

New-Item -ItemType Directory -Force -Path $Build | Out-Null

& $CSC `
 /nologo `
 /target:winexe `
 /platform:x64 `
 /out:"$Build\KYP-Iris-Connector.exe" `
 /reference:System.dll `
 /reference:System.Drawing.dll `
 /reference:System.Windows.Forms.dll `
 /reference:System.Web.Extensions.dll `
 /reference:System.Security.dll `
 /reference:"$Runtime\MIDIris_Auth.dll" `
 "$Src"

if($LASTEXITCODE -ne 0){
    throw "Connector compilation failed."
}

Copy-Item "$Runtime\*.dll" $Build -Force
Copy-Item "$Root\assets\kyp-logo.webp" $Build -Force

Write-Host "KYP_IRIS_CONNECTOR_V3_BUILD=PASS"
Write-Host "EXE=$Build\KYP-Iris-Connector.exe"
