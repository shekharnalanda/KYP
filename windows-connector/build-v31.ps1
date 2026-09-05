$ErrorActionPreference='Stop'
$Root=Split-Path -Parent $MyInvocation.MyCommand.Path
$Src=Join-Path $Root 'src\Program.cs'
$Runtime=Join-Path $Root 'runtime'
$Build=Join-Path $Root 'build'
$Logo=Join-Path $Build 'kyp-logo.png'
$CSC="$env:WINDIR\Microsoft.NET\Framework64\v4.0.30319\csc.exe"
New-Item -ItemType Directory -Force -Path $Build | Out-Null
Invoke-WebRequest 'https://kyp.mciedu.com/downloads/kyp-iris/kyp-logo.png' -OutFile $Logo
if(!(Test-Path $Logo)){throw 'Official KYP logo download failed.'}
$Text=Get-Content $Src -Raw
$Text=$Text.Replace('IrisData d=null;','IrisData d=new IrisData();').Replace('if(d!=null&&d.BitmapImage!=null)','if(d.BitmapImage!=null)').Replace('IrisData probe=null;','IrisData probe=new IrisData();').Replace('if(probe!=null&&probe.BitmapImage!=null)','if(probe.BitmapImage!=null)')
$Text=$Text.Replace('IrisData Capture(out int quality)','IrisData CaptureIris(out int quality)').Replace('d=Capture(out q);','d=CaptureIris(out q);').Replace('probe=Capture(out q);','probe=CaptureIris(out q);')
$Text=$Text.Replace('UI();LoadToken();','UI();Premium();LoadToken();')
$marker=' protected override void OnFormClosed(FormClosedEventArgs e)'
$premium=@'
 Control FindText(Control root,string text){foreach(Control c in root.Controls){if(c.Text==text)return c;Control z=FindText(c,text);if(z!=null)return z;}return null;}
 void Premium(){
  Text="KYP Iris Connector V3.1 - Kushal Youth Programme";Width=1260;Height=820;MinimumSize=new Size(1180,760);BackColor=Color.FromArgb(244,249,255);
  Panel header=null;foreach(Control c in Controls){Panel p=c as Panel;if(p!=null&&p.Top==0){header=p;break;}}
  if(header!=null){header.SetBounds(0,0,1260,145);header.BackColor=Color.FromArgb(5,55,118);foreach(Control c in header.Controls)c.Visible=false;
   string lp=Path.Combine(AppDomain.CurrentDomain.BaseDirectory,"kyp-logo.png");if(File.Exists(lp)){PictureBox pic=new PictureBox();pic.Image=Image.FromFile(lp);pic.SizeMode=PictureBoxSizeMode.Zoom;pic.BackColor=Color.White;pic.SetBounds(28,12,116,116);header.Controls.Add(pic);}
   Label title=new Label();title.Text="Kushal Youth Programme";title.Font=new Font("Segoe UI",27,FontStyle.Bold);title.ForeColor=Color.White;title.SetBounds(170,22,650,46);header.Controls.Add(title);
   Label tag=new Label();tag.Text="Skills for the Digital Future";tag.Font=new Font("Segoe UI",13);tag.ForeColor=Color.FromArgb(220,238,255);tag.SetBounds(172,70,400,28);header.Controls.Add(tag);
   Label sub=new Label();sub.Text="Secure Biometric Attendance Connector  |  Mantra MIS100V2";sub.Font=new Font("Segoe UI",11);sub.ForeColor=Color.White;sub.SetBounds(172,103,610,25);header.Controls.Add(sub);
   Label ver=new Label();ver.Text="V3.1\nSECURE EDITION";ver.TextAlign=ContentAlignment.MiddleCenter;ver.Font=new Font("Segoe UI",11,FontStyle.Bold);ver.ForeColor=Color.White;ver.BorderStyle=BorderStyle.FixedSingle;ver.SetBounds(1040,30,165,70);header.Controls.Add(ver);
  }
  Control sec=FindText(this,"Secure Connector");if(sec!=null)sec.Visible=false;
  token.SetBounds(930,164,275,30);token.Visible=false;
  Control save=FindText(this,"Save Securely");if(save!=null){save.SetBounds(1060,160,145,40);StyleButton(save,Color.FromArgb(5,80,165),Color.White);}
  Control test=FindText(this,"Test Server");if(test!=null){test.SetBounds(900,160,145,40);StyleButton(test,Color.White,Color.FromArgb(5,65,135));}
  server.SetBounds(35,168,250,55);device.SetBounds(315,168,270,55);server.Font=new Font("Segoe UI",11,FontStyle.Bold);device.Font=new Font("Segoe UI",11,FontStyle.Bold);
  Control detect=FindText(this,"Detect MIS100V2");if(detect!=null){detect.SetBounds(315,215,250,38);StyleButton(detect,Color.White,Color.FromArgb(5,65,135));}
  Control ls=FindText(this,"Load Students");if(ls!=null){ls.SetBounds(600,215,190,38);StyleButton(ls,Color.White,Color.FromArgb(5,65,135));}
  Control lc=FindText(this,"Load Courses / Sessions");if(lc!=null){lc.SetBounds(805,215,235,38);StyleButton(lc,Color.White,Color.FromArgb(5,65,135));}
  Panel enroll=null,att=null;foreach(Control c in Controls){Panel p=c as Panel;if(p!=null&&p.Top>200){if(enroll==null)enroll=p;else if(att==null)att=p;}}
  if(enroll!=null){enroll.SetBounds(30,280,570,390);enroll.BackColor=Color.White;enroll.Padding=new Padding(10);StylePanel(enroll);foreach(Control c in enroll.Controls){if(c is Button)StyleButton(c,Color.FromArgb(0,151,92),Color.White);}}
  if(att!=null){att.SetBounds(625,280,585,390);att.BackColor=Color.White;att.Padding=new Padding(10);StylePanel(att);foreach(Control c in att.Controls){Button b=c as Button;if(b!=null){if(b.Text=="CHECK-OUT")StyleButton(b,Color.FromArgb(215,42,46),Color.White);else if(b.Text=="CHECK-IN")StyleButton(b,Color.FromArgb(0,151,92),Color.White);else StyleButton(b,Color.White,Color.FromArgb(5,65,135));}}}
  if(enroll!=null){students.SetBounds(25,70,515,34);eye.SetBounds(25,125,220,34);Control eb=FindText(enroll,"Capture & Enroll Iris");if(eb!=null)eb.SetBounds(25,190,515,54);}
  if(att!=null){Control sb=FindText(att,"Scan & Identify Student");if(sb!=null)sb.SetBounds(25,60,535,50);matched.SetBounds(25,120,535,45);courses.SetBounds(25,175,255,34);sessions.SetBounds(295,175,265,34);Control ci=FindText(att,"CHECK-IN");if(ci!=null)ci.SetBounds(25,230,255,55);Control co=FindText(att,"CHECK-OUT");if(co!=null)co.SetBounds(295,230,265,55);}
  status.SetBounds(30,690,1180,48);status.Font=new Font("Segoe UI",10);status.ForeColor=Color.FromArgb(45,70,100);
  Label footer=new Label();footer.Text="Downloads & Resources     |     KYP Portal     |     User Manual     |     Support                         Kushal Youth Programme";footer.TextAlign=ContentAlignment.MiddleCenter;footer.Font=new Font("Segoe UI",10,FontStyle.Bold);footer.ForeColor=Color.FromArgb(5,65,135);footer.BackColor=Color.White;footer.SetBounds(0,742,1260,38);Controls.Add(footer);footer.BringToFront();
 }
 void StylePanel(Panel p){p.BorderStyle=BorderStyle.FixedSingle;}
 void StyleButton(Control c,Color bg,Color fg){Button b=c as Button;if(b==null)return;b.FlatStyle=FlatStyle.Flat;b.FlatAppearance.BorderColor=Color.FromArgb(170,195,225);b.FlatAppearance.BorderSize=1;b.BackColor=bg;b.ForeColor=fg;b.Font=new Font("Segoe UI",10,FontStyle.Bold);b.Cursor=Cursors.Hand;}
'@
if($Text -notmatch 'void Premium\('){$Text=$Text.Replace($marker,"`r`n"+$premium+$marker)}
$Patched=Join-Path $Build 'Program.cs';Set-Content $Patched $Text -Encoding UTF8
$Required=@('MIDIris_Auth.dll','MIDIris_Auth_Core.dll','MR014_MIS100V2_Windows_Auth_IPL.dll','iris_engine_v3.dll','iris_image_record.dll')
foreach($Name in $Required){if(!(Test-Path (Join-Path $Runtime $Name))){throw "Missing Mantra runtime: $Name"}}
$Exe=Join-Path $Build 'KYP-Iris-Connector-V3.1.exe'
& $CSC /nologo /target:winexe /platform:x64 /out:$Exe /reference:System.dll /reference:System.Drawing.dll /reference:System.Windows.Forms.dll /reference:System.Web.Extensions.dll /reference:System.Security.dll /reference:"$Runtime\MIDIris_Auth.dll" $Patched
if($LASTEXITCODE -ne 0){throw 'V3.1 compilation failed.'}
Copy-Item "$Runtime\*.dll" $Build -Force
Write-Host 'KYP_IRIS_CONNECTOR_V31_PREMIUM_BUILD=PASS';Write-Host "EXE=$Exe";Start-Process -FilePath $Exe -WorkingDirectory $Build
