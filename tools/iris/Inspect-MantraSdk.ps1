param(
    [string]$SdkRoot = ""
)

$ErrorActionPreference = "Stop"

function Get-FriendlyTypeName {
    param([Type]$Type)

    if ($null -eq $Type) { return "void" }

    if ($Type.IsByRef) {
        return (Get-FriendlyTypeName $Type.GetElementType()) + "&"
    }

    if ($Type.IsArray) {
        return (Get-FriendlyTypeName $Type.GetElementType()) + "[]"
    }

    if ($Type.IsGenericType) {
        $name = $Type.Name.Split([char]96)[0]
        $arguments = ($Type.GetGenericArguments() | ForEach-Object { Get-FriendlyTypeName $_ }) -join ", "
        return "$name<$arguments>"
    }

    return $Type.FullName
}

function Format-Method {
    param([Reflection.MethodInfo]$Method)

    $parameters = $Method.GetParameters() | ForEach-Object {
        $direction = if ($_.IsOut) { "out " } elseif ($_.ParameterType.IsByRef) { "ref " } else { "" }
        $optional = if ($_.IsOptional) { " = optional" } else { "" }
        "$direction$(Get-FriendlyTypeName $_.ParameterType) $($_.Name)$optional"
    }

    $static = if ($Method.IsStatic) { "static " } else { "" }
    return "$static$(Get-FriendlyTypeName $Method.ReturnType) $($Method.Name)($($parameters -join ', '))"
}

$searchRoots = @()
if ($SdkRoot) {
    $searchRoots += $SdkRoot
}

$searchRoots += @(
    $PSScriptRoot,
    (Join-Path $env:USERPROFILE "Downloads"),
    (Join-Path $env:USERPROFILE "Desktop")
) | Where-Object { $_ -and (Test-Path $_) } | Select-Object -Unique

$dll = $null
foreach ($root in $searchRoots) {
    $dll = Get-ChildItem -Path $root -Filter "MIDIris_Auth.dll" -File -Recurse -ErrorAction SilentlyContinue |
        Select-Object -First 1
    if ($dll) { break }
}

if (-not $dll) {
    $zipRoots = @(
        (Join-Path $env:USERPROFILE "Downloads"),
        (Join-Path $env:USERPROFILE "Desktop")
    ) | Where-Object { Test-Path $_ }

    $sdkZip = $zipRoots | ForEach-Object {
        Get-ChildItem -Path $_ -Filter "MIS100V*.zip" -File -ErrorAction SilentlyContinue
    } | Sort-Object LastWriteTime -Descending | Select-Object -First 1

    if ($sdkZip) {
        $extractRoot = Join-Path $env:TEMP "KYP-MIS100V2-SDK"
        if (Test-Path $extractRoot) {
            Remove-Item -Path $extractRoot -Recurse -Force
        }

        Expand-Archive -LiteralPath $sdkZip.FullName -DestinationPath $extractRoot -Force
        $dll = Get-ChildItem -Path $extractRoot -Filter "MIDIris_Auth.dll" -File -Recurse -ErrorAction SilentlyContinue |
            Select-Object -First 1
    }
}

if (-not $dll) {
    throw "MIDIris_Auth.dll or MIS100V2 ZIP was not found in Downloads or Desktop."
}

$assemblyDirectory = $dll.Directory.FullName
$env:PATH = "$assemblyDirectory;$env:PATH"

$assembly = [Reflection.Assembly]::LoadFrom($dll.FullName)

try {
    $types = $assembly.GetTypes()
}
catch [Reflection.ReflectionTypeLoadException] {
    $types = $_.Exception.Types | Where-Object { $null -ne $_ }
    $loaderErrors = $_.Exception.LoaderExceptions | ForEach-Object { $_.Message }
}

$targetNames = @(
    "InitDevice",
    "IsConnected",
    "StartCapture",
    "StopCapture",
    "AutoCapture",
    "GetImage",
    "MatchIrisData"
)

$lines = [System.Collections.Generic.List[string]]::new()
$lines.Add("KYP MIS100V2 SDK Signature Report")
$lines.Add("Generated: $(Get-Date -Format o)")
$lines.Add("Assembly: $($dll.FullName)")
$lines.Add("Architecture requirement: x86")
$lines.Add("")

foreach ($type in ($types | Sort-Object FullName)) {
    $methods = $type.GetMethods(
        [Reflection.BindingFlags]"Public,NonPublic,Instance,Static,DeclaredOnly"
    ) | Where-Object {
        $targetNames -contains $_.Name -or
        $_.Name -match "Capture|Iris|Image|Match|Device|Connect"
    }

    if ($methods.Count -eq 0 -and -not $type.IsSubclassOf([MulticastDelegate])) {
        continue
    }

    $lines.Add("TYPE: $($type.FullName)")

    if ($type.IsSubclassOf([MulticastDelegate])) {
        $invoke = $type.GetMethod("Invoke")
        if ($invoke) {
            $lines.Add("  DELEGATE: $(Format-Method $invoke)")
        }
    }

    foreach ($method in ($methods | Sort-Object Name)) {
        $lines.Add("  METHOD: $(Format-Method $method)")
    }

    $lines.Add("")
}

if ($loaderErrors) {
    $lines.Add("LOADER WARNINGS:")
    foreach ($loaderError in $loaderErrors) {
        $lines.Add("  $loaderError")
    }
}

$outputPath = Join-Path ([Environment]::GetFolderPath("Desktop")) "KYP-MIS100V2-SDK-SIGNATURES.txt"
$lines | Set-Content -Path $outputPath -Encoding UTF8

Write-Host ""
Write-Host "MIS100V2 SDK inspection complete." -ForegroundColor Green
Write-Host "Report: $outputPath" -ForegroundColor Cyan
Write-Host "This report contains no biometric data or connector token." -ForegroundColor Yellow
