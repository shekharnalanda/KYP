$ErrorActionPreference="Stop"

$Root=Split-Path -Parent $MyInvocation.MyCommand.Path
$Src=Join-Path $Root "src\Program.cs"
$Runtime=Join-Path $Root "runtime"
$Build=Join-Path $Root "build"

# MIS100V2 MIDIris_Auth runtime validated as x86.
$CSC="$env:WINDIR\Microsoft.NET\Framework\v4.0.30319\csc.exe"

if(!(Test-Path $CSC)){
    throw "32-bit .NET Framework C# compiler not found."
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

Get-Process "KYP-Iris-Connector-V4" -ErrorAction SilentlyContinue |
    Stop-Process -Force -ErrorAction SilentlyContinue

$Exe=Join-Path $Build "KYP-Iris-Connector-V4.exe"

& $CSC `
 /nologo `
 /target:winexe `
 /platform:x86 `
 /optimize+ `
 /out:$Exe `
 /reference:System.dll `
 /reference:System.Core.dll `
 /reference:System.Drawing.dll `
 /reference:System.Windows.Forms.dll `
 /reference:System.Web.Extensions.dll `
 /reference:System.Security.dll `
 /reference:"$Runtime\MIDIris_Auth.dll" `
 $Src

if($LASTEXITCODE -ne 0){
    throw "KYP Iris Connector V4 compilation failed."
}

Copy-Item "$Runtime\*.dll" $Build -Force

if(Test-Path "$Root\assets\kyp-logo.png"){
    Copy-Item "$Root\assets\kyp-logo.png" $Build -Force
}

if(Test-Path "$Root\appsettings.example.json"){
    Copy-Item "$Root\appsettings.example.json" $Build -Force
}

Write-Host ""
Write-Host "KYP_IRIS_CONNECTOR_V4_BUILD=PASS" -ForegroundColor Green
Write-Host "ARCHITECTURE=x86"
Write-Host "EXE=$Exe"
Write-Host ""
Write-Host "IMPORTANT: Configure a validated matchThreshold before production attendance."
