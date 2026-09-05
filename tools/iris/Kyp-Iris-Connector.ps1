param(
    [ValidateSet("menu", "health", "enroll", "attendance")]
    [string]$Mode = "menu",
    [ValidateSet("check_in", "check_out")]
    [string]$EventType = "check_in",
    [string]$ApiBase = "https://kyp.mciedu.com",
    [int]$MatchThreshold = 60
)

$ErrorActionPreference = "Stop"

if ([Environment]::Is64BitProcess) {
    $x86PowerShell = "$env:WINDIR\SysWOW64\WindowsPowerShell\v1.0\powershell.exe"
    if (-not (Test-Path $x86PowerShell)) {
        throw "32-bit Windows PowerShell is required for the MIS100V2 SDK."
    }

    $arguments = @(
        "-NoProfile", "-ExecutionPolicy", "Bypass", "-File", $PSCommandPath,
        "-Mode", $Mode, "-EventType", $EventType, "-ApiBase", $ApiBase,
        "-MatchThreshold", $MatchThreshold
    )
    & $x86PowerShell @arguments
    exit $LASTEXITCODE
}

function Find-Mis100Sdk {
    $roots = @(
        $PSScriptRoot,
        (Join-Path $env:USERPROFILE "Downloads"),
        (Join-Path $env:USERPROFILE "Desktop"),
        (Join-Path $env:ProgramFiles "Mantra")
    ) | Where-Object { $_ -and (Test-Path $_) } | Select-Object -Unique

    foreach ($root in $roots) {
        $found = Get-ChildItem -Path $root -Filter "MIDIris_Auth.dll" -File -Recurse -ErrorAction SilentlyContinue |
            Select-Object -First 1
        if ($found) { return $found }
    }

    $zip = @(
        (Join-Path $env:USERPROFILE "Downloads"),
        (Join-Path $env:USERPROFILE "Desktop")
    ) | Where-Object { Test-Path $_ } | ForEach-Object {
        Get-ChildItem -Path $_ -Filter "MIS100V*.zip" -File -ErrorAction SilentlyContinue
    } | Sort-Object LastWriteTime -Descending | Select-Object -First 1

    if ($zip) {
        $destination = Join-Path $env:LOCALAPPDATA "KYP\MIS100V2"
        New-Item -ItemType Directory -Path $destination -Force | Out-Null
        Expand-Archive -LiteralPath $zip.FullName -DestinationPath $destination -Force
        return Get-ChildItem -Path $destination -Filter "MIDIris_Auth.dll" -File -Recurse |
            Select-Object -First 1
    }

    throw "MIS100V2 SDK was not found in Downloads, Desktop, or the connector folder."
}

function Get-ConnectorToken {
    if ($env:KYP_IRIS_CONNECTOR_TOKEN) {
        return $env:KYP_IRIS_CONNECTOR_TOKEN
    }

    $tokenFile = Join-Path $env:LOCALAPPDATA "KYP\iris-token.dat"
    if (Test-Path $tokenFile) {
        $encrypted = Get-Content -Path $tokenFile -Raw
        return [Runtime.InteropServices.Marshal]::PtrToStringAuto(
            [Runtime.InteropServices.Marshal]::SecureStringToBSTR(
                ($encrypted | ConvertTo-SecureString)
            )
        )
    }

    throw "The protected KYP connector token is not installed on this computer."
}

function Invoke-KypApi {
    param(
        [string]$Path,
        [ValidateSet("GET", "POST")]
        [string]$Method = "GET",
        [object]$Body = $null
    )

    $headers = @{
        Authorization = "Bearer $script:ConnectorToken"
        Accept = "application/json"
    }
    $parameters = @{
        Uri = "$ApiBase$Path"
        Method = $Method
        Headers = $headers
        UseBasicParsing = $true
    }
    if ($null -ne $Body) {
        $parameters.ContentType = "application/json"
        $parameters.Body = $Body | ConvertTo-Json -Depth 8 -Compress
    }

    return Invoke-RestMethod @parameters
}

function Initialize-IrisDevice {
    $script:SdkDll = Find-Mis100Sdk
    $sdkDirectory = $script:SdkDll.Directory.FullName
    $env:PATH = "$sdkDirectory;$env:PATH"
    $script:SdkAssembly = [Reflection.Assembly]::LoadFrom($script:SdkDll.FullName)
    $authType = $script:SdkAssembly.GetType("MIDIris_Auth.MIDIrisAuth", $true)
    $script:IrisAuth = [Activator]::CreateInstance($authType)

    $deviceNames = New-Object "System.Collections.Generic.List[string]"
    $deviceResult = $script:IrisAuth.GetConnectedDevices($deviceNames)
    if ($deviceResult -ne 0 -or $deviceNames.Count -eq 0) {
        throw "MIS100V2 is not connected. Close MIDIrisTest.exe and reconnect the device."
    }

    $script:DeviceName = $deviceNames[0]
    $deviceInfoType = $script:SdkAssembly.GetType("MIDIris_Auth.MIDIris_DEVICE_INFO", $true)
    $deviceInfo = [Activator]::CreateInstance($deviceInfoType)
    $initResult = $script:IrisAuth.InitDevice($script:DeviceName, [ref]$deviceInfo)
    if ($initResult -ne 0) {
        throw "MIS100V2 initialization failed with SDK code $initResult."
    }
}

function Capture-IrisImage {
    $irisDataType = $script:SdkAssembly.GetType("MIDIris_Auth.IrisData", $true)
    $irisData = [Activator]::CreateInstance($irisDataType)
    [int]$quality = 0
    $captureResult = $script:IrisAuth.AutoCapture([ref]$quality, [ref]$irisData, 20000)
    if ($captureResult -ne 0) {
        throw "Iris capture failed with SDK code $captureResult."
    }

    $formatType = $script:SdkAssembly.GetType("MIDIris_Auth.IMAGE_FORMAT", $true)
    $format = [Enum]::GetValues($formatType).GetValue(0)
    [byte[]]$image = $null
    $imageResult = $script:IrisAuth.GetImage([ref]$image, $format, 100)
    if ($imageResult -ne 0 -or -not $image -or $image.Length -eq 0) {
        throw "The SDK captured the iris but did not return matching data (code $imageResult)."
    }

    return @{ Image = $image; Quality = $quality }
}

function Select-NumberedItem {
    param([object[]]$Items, [string]$LabelScript)

    for ($index = 0; $index -lt $Items.Count; $index++) {
        $item = $Items[$index]
        $label = & ([ScriptBlock]::Create($LabelScript)) $item
        Write-Host "[$($index + 1)] $label"
    }
    do {
        $choice = Read-Host "Select number"
    } until (($choice -as [int]) -and [int]$choice -ge 1 -and [int]$choice -le $Items.Count)

    return $Items[[int]$choice - 1]
}

function Start-Enrollment {
    $students = @((Invoke-KypApi -Path "/api/iris/students").data)
    if ($students.Count -eq 0) { throw "No active enrolled students were found." }

    $student = Select-NumberedItem -Items $students -LabelScript {
        param($s) "$($s.student_id) - $($s.name) (iris enrolled: $($s.iris_enrolled))"
    }
    Write-Host "Present the student's iris to MIS100V2..." -ForegroundColor Cyan
    $capture = Capture-IrisImage
    $payload = @{
        user_id = $student.id
        right_template = [Convert]::ToBase64String($capture.Image)
        quality_score = [Math]::Max(0, [Math]::Min(100, $capture.Quality))
        device_reference = "$env:COMPUTERNAME-$($script:DeviceName)"
    }
    $result = Invoke-KypApi -Path "/api/iris/enroll" -Method POST -Body $payload
    Write-Host "Enrollment saved for $($student.name)." -ForegroundColor Green
    return $result
}

function Find-IrisMatch {
    param([byte[]]$Probe, [object[]]$Candidates)

    $best = $null
    foreach ($candidate in $Candidates) {
        foreach ($eye in @("left", "right")) {
            $encoded = if ($eye -eq "left") { $candidate.left_template } else { $candidate.right_template }
            if (-not $encoded) { continue }

            try { $gallery = [Convert]::FromBase64String($encoded) } catch { continue }
            [int]$score = 0
            $matchResult = $script:IrisAuth.MatchIrisData($gallery, $Probe, [ref]$score)
            if ($matchResult -eq 0 -and ($null -eq $best -or $score -gt $best.Score)) {
                $best = @{ Candidate = $candidate; Eye = $eye; Score = $score }
            }
        }
    }

    if ($best -and $best.Score -ge $MatchThreshold) { return $best }
    return $null
}

function Start-AttendanceStation {
    $catalog = @((Invoke-KypApi -Path "/api/iris/catalog").data)
    if ($catalog.Count -eq 0) { throw "No active courses were found." }
    $course = Select-NumberedItem -Items $catalog -LabelScript { param($c) "$($c.code) - $($c.name)" }
    $sessions = @($course.sessions)
    if ($sessions.Count -eq 0) { throw "No published sessions exist for this course." }
    $session = Select-NumberedItem -Items $sessions -LabelScript {
        param($s) "Session $($s.session_number) - $($s.title_hi) / $($s.title_en)"
    }
    $candidates = @((Invoke-KypApi -Path "/api/iris/candidates").data)
    if ($candidates.Count -eq 0) { throw "No iris-enrolled students were found." }

    Write-Host ""
    Write-Host "Attendance station ready: $($course.code), Session $($session.session_number), $EventType" -ForegroundColor Green
    Write-Host "Students only need to present their iris. Press Q before a capture to stop." -ForegroundColor Yellow

    while ($true) {
        $continue = Read-Host "Press ENTER for next student, or Q to stop"
        if ($continue -match "^[Qq]$") { break }

        try {
            $capture = Capture-IrisImage
            $match = Find-IrisMatch -Probe $capture.Image -Candidates $candidates
            if (-not $match) {
                Write-Host "Iris not recognized. Attendance was not submitted." -ForegroundColor Red
                continue
            }

            $payload = @{
                event_uuid = [Guid]::NewGuid().ToString()
                user_id = $match.Candidate.user_id
                course_id = $course.id
                learning_session_id = $session.id
                event_type = $EventType
                device_reference = "$env:COMPUTERNAME-$($script:DeviceName)"
                matched_eye = $match.Eye
                match_score = $match.Score
                captured_at = (Get-Date).ToUniversalTime().ToString("o")
                metadata = @{ connector = "KYP-MIS100V2-PS1"; threshold = $MatchThreshold }
            }
            $result = Invoke-KypApi -Path "/api/iris/attendance" -Method POST -Body $payload
            Write-Host "$($result.student.student_id) - $($result.student.name): $($result.event_type) saved (score $($match.Score))." -ForegroundColor Green
        }
        catch {
            Write-Host $_.Exception.Message -ForegroundColor Red
        }
    }
}

$script:ConnectorToken = Get-ConnectorToken

try {
    $health = Invoke-KypApi -Path "/api/iris/health"
    Write-Host "$($health.service) connected." -ForegroundColor Green
    if ($Mode -eq "health") { exit 0 }

    Initialize-IrisDevice
    if ($Mode -eq "menu") {
        Write-Host "[1] Enroll student iris"
        Write-Host "[2] Start check-in station"
        Write-Host "[3] Start check-out station"
        $selection = Read-Host "Select number"
        switch ($selection) {
            "1" { $Mode = "enroll" }
            "2" { $Mode = "attendance"; $EventType = "check_in" }
            "3" { $Mode = "attendance"; $EventType = "check_out" }
            default { throw "Invalid selection." }
        }
    }

    if ($Mode -eq "enroll") { Start-Enrollment | Out-Null }
    if ($Mode -eq "attendance") { Start-AttendanceStation }
}
finally {
    if ($script:IrisAuth) {
        try { $script:IrisAuth.StopCapture() | Out-Null } catch {}
    }
}
