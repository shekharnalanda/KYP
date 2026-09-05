$ErrorActionPreference='Stop'
$Root=Split-Path -Parent $MyInvocation.MyCommand.Path
$Src=Join-Path $Root 'src\Program.cs'
$Runtime=Join-Path $Root 'runtime'
$Build=Join-Path $Root 'build'
$Logo=Join-Path $Build 'kyp-logo.png'
$CSC="$env:WINDIR\Microsoft.NET\Framework64\v4.0.30319\csc.exe"

New-Item -ItemType Directory -Force -Path $Build | Out-Null

# Official KYP logo only.
Invoke-WebRequest 'https://kyp.mciedu.com/downloads/kyp-iris/kyp-logo.png' -OutFile $Logo
if(!(Test-Path $Logo)){ throw 'Official KYP logo download failed.' }

$Text=Get-Content $Src -Raw

# MIDIris IrisData is a value type in the installed MIS100V2 SDK.
$Text=$Text.Replace('IrisData d=null;','IrisData d=new IrisData();')
$Text=$Text.Replace('if(d!=null&&d.BitmapImage!=null)','if(d.BitmapImage!=null)')
$Text=$Text.Replace('IrisData probe=null;','IrisData probe=new IrisData();')
$Text=$Text.Replace('if(probe!=null&&probe.BitmapImage!=null)','if(probe.BitmapImage!=null)')

# Avoid hiding Control.Capture.
$Text=$Text.Replace('IrisData Capture(out int quality)','IrisData CaptureIris(out int quality)')
$Text=$Text.Replace('d=Capture(out q);','d=CaptureIris(out q);')
$Text=$Text.Replace('probe=Capture(out q);','probe=CaptureIris(out q);')

# Premium V3.1 branding hooks.
$Text=$Text.Replace('UI();LoadToken();','UI();Premium();LoadOfficialLogo();LoadToken();')
$marker=' protected override void OnFormClosed(FormClosedEventArgs e)'
$premium=@'
 void Premium(){
  Text="KYP Iris Connector V3.1 - Kushal Youth Programme";
  BackColor=Color.FromArgb(241,247,255);
  foreach(Control c in Controls)StyleTree(c);
 }
 void StyleTree(Control c){
  Button b=c as Button;if(b!=null){b.FlatStyle=FlatStyle.Flat;b.FlatAppearance.BorderSize=1;b.FlatAppearance.BorderColor=Color.FromArgb(166,190,220);b.Cursor=Cursors.Hand;b.Font=new Font("Segoe UI",10,FontStyle.Bold);}
  Panel p=c as Panel;if(p!=null&&p.Top>100){p.BackColor=Color.White;}
  foreach(Control child in c.Controls)StyleTree(child);
 }
 void LoadOfficialLogo(){
  try{
   string path=Path.Combine(AppDomain.CurrentDomain.BaseDirectory,"kyp-logo.png");
   if(!File.Exists(path))return;
   PictureBox pic=new PictureBox();pic.Image=Image.FromFile(path);pic.SizeMode=PictureBoxSizeMode.Zoom;pic.BackColor=Color.White;pic.SetBounds(925,8,72,72);Controls.Add(pic);pic.BringToFront();
   Label v=new Label();v.Text="V3.1  •  SECURE";v.TextAlign=ContentAlignment.MiddleCenter;v.Font=new Font("Segoe UI",9,FontStyle.Bold);v.ForeColor=Color.White;v.BackColor=Color.FromArgb(12,62,122);v.SetBounds(825,28,95,28);Controls.Add(v);v.BringToFront();
  }catch{}
 }
'@
if($Text -notmatch 'void Premium\('){$Text=$Text.Replace($marker,"`r`n"+$premium+$marker)}

$Patched=Join-Path $Build 'Program.cs'
Set-Content $Patched $Text -Encoding UTF8

$Required=@('MIDIris_Auth.dll','MIDIris_Auth_Core.dll','MR014_MIS100V2_Windows_Auth_IPL.dll','iris_engine_v3.dll','iris_image_record.dll')
foreach($Name in $Required){if(!(Test-Path (Join-Path $Runtime $Name))){throw "Missing Mantra runtime: $Name"}}

$Exe=Join-Path $Build 'KYP-Iris-Connector-V3.1.exe'
& $CSC /nologo /target:winexe /platform:x64 /out:$Exe /reference:System.dll /reference:System.Drawing.dll /reference:System.Windows.Forms.dll /reference:System.Web.Extensions.dll /reference:System.Security.dll /reference:"$Runtime\MIDIris_Auth.dll" $Patched
if($LASTEXITCODE -ne 0){throw 'V3.1 compilation failed.'}
Copy-Item "$Runtime\*.dll" $Build -Force
Write-Host 'KYP_IRIS_CONNECTOR_V31_BUILD=PASS'
Write-Host "EXE=$Exe"
Start-Process -FilePath $Exe -WorkingDirectory $Build
