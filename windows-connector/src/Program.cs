using System;
using System.Collections.Generic;
using System.Drawing;
using System.IO;
using System.Net;
using System.Security.Cryptography;
using System.Text;
using System.Web.Script.Serialization;
using System.Windows.Forms;
using MIDIris_Auth;

namespace KYPIrisConnector
{
    public class ApiList<T> { public List<T> data { get; set; } }
    public class Student
    {
        public int id { get; set; }
        public string student_id { get; set; }
        public string name { get; set; }
        public bool iris_enrolled { get; set; }
        public override string ToString() { return student_id + " - " + name + (iris_enrolled ? " [IRIS READY]" : ""); }
    }
    public class Candidate
    {
        public int user_id { get; set; }
        public string student_id { get; set; }
        public string student_name { get; set; }
        public string left_template { get; set; }
        public string right_template { get; set; }
    }
    public class SessionItem
    {
        public int id { get; set; }
        public int session_number { get; set; }
        public string title_hi { get; set; }
        public string title_en { get; set; }
        public int duration_minutes { get; set; }
        public override string ToString() { return "Session " + session_number + " - " + (String.IsNullOrWhiteSpace(title_en) ? title_hi : title_en); }
    }
    public class CourseItem
    {
        public int id { get; set; }
        public string code { get; set; }
        public string name { get; set; }
        public List<SessionItem> sessions { get; set; }
        public override string ToString() { return code + " - " + name; }
    }
    public class AttendanceResponse
    {
        public bool ok { get; set; }
        public bool duplicate { get; set; }
        public string event_type { get; set; }
        public string status { get; set; }
        public int? minutes_completed { get; set; }
        public string message { get; set; }
    }

    public class MainForm : Form
    {
        const string Server = "https://kyp.mciedu.com";
        const int MatchThreshold = 1000; // fail-closed pilot threshold; validate further before broad rollout
        const int CaptureTimeoutMs = 60000;

        MIDIrisAuth iris;
        MIDIris_DEVICE_INFO deviceInfo;
        JavaScriptSerializer json = new JavaScriptSerializer();

        TextBox txtToken;
        ComboBox cboStudent, cboEye, cboCourse, cboSession;
        Label lblServer, lblDevice, lblStatus, lblMatched;
        Button btnEnroll, btnScan, btnCheckIn, btnCheckOut;
        Student matchedStudent;
        int matchedScore;
        string matchedEye;

        string TokenFile
        {
            get
            {
                string dir = Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData), "KYP-Iris-Connector");
                Directory.CreateDirectory(dir);
                return Path.Combine(dir, "connector-token.dat");
            }
        }

        public MainForm()
        {
            Text = "KYP Iris Connector V3 - Kushal Youth Programme";
            Width = 1100; Height = 760; StartPosition = FormStartPosition.CenterScreen;
            Font = new Font("Segoe UI", 10); BackColor = Color.FromArgb(245, 249, 255);
            BuildUI(); LoadSavedToken();
            try { iris = new MIDIrisAuth(); DeviceStatus("SDK Loaded", Color.DarkGreen); }
            catch (Exception ex) { DeviceStatus("SDK Load Failed: " + ex.Message, Color.DarkRed); }
        }

        Label MakeLabel(string text, int x, int y, int w, int h, float size, FontStyle style, Color color)
        {
            Label l = new Label(); l.Text = text; l.SetBounds(x,y,w,h); l.Font = new Font("Segoe UI",size,style); l.ForeColor=color; Controls.Add(l); return l;
        }
        Button MakeButton(string text, int x, int y, int w, int h)
        {
            Button b=new Button(); b.Text=text; b.SetBounds(x,y,w,h); b.FlatStyle=FlatStyle.Flat; b.BackColor=Color.White; b.FlatAppearance.BorderColor=Color.FromArgb(170,195,225); Controls.Add(b); return b;
        }
        void BuildUI()
        {
            Panel top=new Panel(); top.SetBounds(0,0,1100,92); top.BackColor=Color.FromArgb(12,62,122); Controls.Add(top);
            Label brand=new Label(); brand.Text="Kushal Youth Programme"; brand.SetBounds(28,17,600,38); brand.Font=new Font("Segoe UI",23,FontStyle.Bold); brand.ForeColor=Color.White; top.Controls.Add(brand);
            Label sub=new Label(); sub.Text="Secure Biometric Attendance Connector - Mantra MIS100V2"; sub.SetBounds(31,57,650,22); sub.ForeColor=Color.FromArgb(215,232,252); top.Controls.Add(sub);
            Label badge=new Label(); badge.Text="KYP  |  V3"; badge.TextAlign=ContentAlignment.MiddleCenter; badge.SetBounds(920,22,135,45); badge.BackColor=Color.White; badge.ForeColor=Color.FromArgb(12,62,122); badge.Font=new Font("Segoe UI",12,FontStyle.Bold); top.Controls.Add(badge);

            MakeLabel("Secure Connector",30,112,250,28,15,FontStyle.Bold,Color.FromArgb(18,55,95));
            txtToken=new TextBox(); txtToken.PasswordChar='*'; txtToken.SetBounds(30,147,680,31); Controls.Add(txtToken);
            Button save=MakeButton("Save Securely",725,144,150,36); save.Click+=delegate{SaveTokenSecurely();};
            Button test=MakeButton("Test Server",890,144,150,36); test.Click+=delegate{TestServer();};
            lblServer=MakeLabel("KYP Server: Not tested",30,190,490,27,11,FontStyle.Bold,Color.DimGray);
            lblDevice=MakeLabel("MIS100V2: Not tested",535,190,505,27,11,FontStyle.Bold,Color.DimGray);
            Button detect=MakeButton("Detect MIS100V2",30,225,220,40); detect.Click+=delegate{DetectDevice();};
            Button students=MakeButton("Load Students",265,225,180,40); students.Click+=delegate{LoadStudents();};
            Button catalog=MakeButton("Load Courses / Sessions",460,225,230,40); catalog.Click+=delegate{LoadCatalog();};

            Panel left=new Panel(); left.SetBounds(30,285,500,350); left.BackColor=Color.White; left.BorderStyle=BorderStyle.FixedSingle; Controls.Add(left);
            Label eh=new Label(); eh.Text="IRIS ENROLLMENT"; eh.SetBounds(22,18,300,28); eh.Font=new Font("Segoe UI",15,FontStyle.Bold); eh.ForeColor=Color.FromArgb(12,62,122); left.Controls.Add(eh);
            Label sl=new Label(); sl.Text="Student"; sl.SetBounds(22,62,100,22); left.Controls.Add(sl);
            cboStudent=new ComboBox(); cboStudent.DropDownStyle=ComboBoxStyle.DropDownList; cboStudent.SetBounds(22,88,445,31); left.Controls.Add(cboStudent);
            Label el=new Label(); el.Text="Eye"; el.SetBounds(22,135,100,22); left.Controls.Add(el);
            cboEye=new ComboBox(); cboEye.DropDownStyle=ComboBoxStyle.DropDownList; cboEye.Items.Add("Left"); cboEye.Items.Add("Right"); cboEye.SelectedIndex=0; cboEye.SetBounds(22,161,180,31); left.Controls.Add(cboEye);
            btnEnroll=new Button(); btnEnroll.Text="Capture & Enroll Iris"; btnEnroll.SetBounds(22,215,445,48); btnEnroll.BackColor=Color.FromArgb(12,62,122); btnEnroll.ForeColor=Color.White; btnEnroll.FlatStyle=FlatStyle.Flat; btnEnroll.Click+=delegate{EnrollIris();}; left.Controls.Add(btnEnroll);
            Label enote=new Label(); enote.Text="Enrollment stores biometric reference only on the authorized KYP server."; enote.SetBounds(22,282,445,45); enote.ForeColor=Color.DimGray; left.Controls.Add(enote);

            Panel right=new Panel(); right.SetBounds(550,285,510,350); right.BackColor=Color.White; right.BorderStyle=BorderStyle.FixedSingle; Controls.Add(right);
            Label ah=new Label(); ah.Text="BIOMETRIC ATTENDANCE"; ah.SetBounds(22,18,350,28); ah.Font=new Font("Segoe UI",15,FontStyle.Bold); ah.ForeColor=Color.FromArgb(12,62,122); right.Controls.Add(ah);
            btnScan=new Button(); btnScan.Text="Scan & Identify Student"; btnScan.SetBounds(22,58,465,42); btnScan.Click+=delegate{ScanIdentify();}; right.Controls.Add(btnScan);
            lblMatched=new Label(); lblMatched.Text="Matched Student: Not scanned"; lblMatched.SetBounds(22,108,465,43); lblMatched.Font=new Font("Segoe UI",10,FontStyle.Bold); right.Controls.Add(lblMatched);
            cboCourse=new ComboBox(); cboCourse.DropDownStyle=ComboBoxStyle.DropDownList; cboCourse.SetBounds(22,160,220,31); cboCourse.SelectedIndexChanged+=delegate{PopulateSessions();}; right.Controls.Add(cboCourse);
            cboSession=new ComboBox(); cboSession.DropDownStyle=ComboBoxStyle.DropDownList; cboSession.SetBounds(255,160,232,31); right.Controls.Add(cboSession);
            btnCheckIn=new Button(); btnCheckIn.Text="CHECK-IN"; btnCheckIn.SetBounds(22,215,220,48); btnCheckIn.BackColor=Color.FromArgb(15,137,96); btnCheckIn.ForeColor=Color.White; btnCheckIn.FlatStyle=FlatStyle.Flat; btnCheckIn.Click+=delegate{SubmitAttendance("check_in");}; right.Controls.Add(btnCheckIn);
            btnCheckOut=new Button(); btnCheckOut.Text="CHECK-OUT"; btnCheckOut.SetBounds(267,215,220,48); btnCheckOut.BackColor=Color.FromArgb(12,62,122); btnCheckOut.ForeColor=Color.White; btnCheckOut.FlatStyle=FlatStyle.Flat; btnCheckOut.Click+=delegate{SubmitAttendance("check_out");}; right.Controls.Add(btnCheckOut);
            Label rule=new Label(); rule.Text="Attendance is sent only after a fresh successful iris match."; rule.SetBounds(22,282,465,42); rule.ForeColor=Color.DimGray; right.Controls.Add(rule);

            lblStatus=MakeLabel("Ready. No biometric image/template is written to application logs.",30,655,1030,48,10,FontStyle.Regular,Color.DimGray);
        }

        string Token(){return txtToken.Text.Trim();}
        string Request(string path,string method,object body)
        {
            if(Token().Length<20) throw new Exception("Connector token required.");
            ServicePointManager.SecurityProtocol=SecurityProtocolType.Tls12;
            HttpWebRequest req=(HttpWebRequest)WebRequest.Create(Server+path); req.Method=method; req.Accept="application/json"; req.ContentType="application/json"; req.Headers["Authorization"]="Bearer "+Token(); req.Timeout=65000;
            if(body!=null){byte[] bytes=Encoding.UTF8.GetBytes(json.Serialize(body)); req.ContentLength=bytes.Length; using(Stream s=req.GetRequestStream())s.Write(bytes,0,bytes.Length); Array.Clear(bytes,0,bytes.Length);}
            try{using(HttpWebResponse res=(HttpWebResponse)req.GetResponse())using(StreamReader sr=new StreamReader(res.GetResponseStream()))return sr.ReadToEnd();}
            catch(WebException ex){string msg=ex.Message;if(ex.Response!=null)using(StreamReader sr=new StreamReader(ex.Response.GetResponseStream()))msg=sr.ReadToEnd();throw new Exception(msg);}
        }
        void SaveTokenSecurely()
        {
            string token=Token(); if(token.Length<20){MessageBox.Show("Valid connector token required.");return;}
            byte[] plain=Encoding.UTF8.GetBytes(token); byte[] enc=ProtectedData.Protect(plain,null,DataProtectionScope.CurrentUser); File.WriteAllBytes(TokenFile,enc); Array.Clear(plain,0,plain.Length); Array.Clear(enc,0,enc.Length); lblStatus.Text="Connector token securely saved for this Windows user."; lblStatus.ForeColor=Color.DarkGreen;
        }
        void LoadSavedToken(){try{if(!File.Exists(TokenFile))return;byte[] enc=File.ReadAllBytes(TokenFile);byte[] plain=ProtectedData.Unprotect(enc,null,DataProtectionScope.CurrentUser);txtToken.Text=Encoding.UTF8.GetString(plain);Array.Clear(plain,0,plain.Length);Array.Clear(enc,0,enc.Length);}catch{}}
        void ServerStatus(string s,Color c){lblServer.Text="KYP Server: "+s;lblServer.ForeColor=c;}
        void DeviceStatus(string s,Color c){lblDevice.Text="MIS100V2: "+s;lblDevice.ForeColor=c;}
        void TestServer(){try{Request("/api/iris/health","GET",null);ServerStatus("Connected / Authorized",Color.DarkGreen);}catch(Exception ex){ServerStatus(ex.Message,Color.DarkRed);}}

        void DetectDevice()
        {
            try{List<string> devices=new List<string>();int rc=iris.GetConnectedDevices(devices);if(rc!=0||devices.Count==0)throw new Exception("Device not detected. SDK="+rc);string model=devices[0];deviceInfo=new MIDIris_DEVICE_INFO();var m=iris.GetType().GetMethod("InitDevice",System.Reflection.BindingFlags.Public|System.Reflection.BindingFlags.NonPublic|System.Reflection.BindingFlags.Instance);if(m==null)throw new Exception("InitDevice unavailable.");object[] args=new object[]{model,deviceInfo};int init=Convert.ToInt32(m.Invoke(iris,args));deviceInfo=(MIDIris_DEVICE_INFO)args[1];if(init!=0)throw new Exception("InitDevice="+init);DeviceStatus("Connected - "+deviceInfo.Make+" "+deviceInfo.Model+" | S/N "+deviceInfo.SerialNo,Color.DarkGreen);}
            catch(Exception ex){DeviceStatus(ex.Message,Color.DarkRed);}
        }
        void EnsureDevice(){if(iris==null)throw new Exception("MIS100V2 SDK unavailable.");if(String.IsNullOrWhiteSpace(deviceInfo.Model))throw new Exception("Detect MIS100V2 first.");}
        IrisData Capture(out int quality)
        {
            EnsureDevice(); IrisData data=new IrisData(); int rc=iris.AutoCapture(out quality,ref data,CaptureTimeout