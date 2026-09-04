<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\LearningSession;
use Illuminate\Database\Seeder;

class SessionContentSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            'CIT' => [
                'डिजिटल दुनिया और कंप्यूटर परिचय','कंप्यूटर हार्डवेयर की पहचान','इनपुट डिवाइस का उपयोग','आउटपुट डिवाइस का उपयोग','CPU, Memory और Processing','Storage Devices और क्षमता','System एवं Application Software','Operating System की मूल बातें','Desktop, Taskbar और Windows','Settings एवं Accessibility','Files और Folders बनाना','Copy, Move, Rename और Delete','Search, Recycle Bin और Backup','सही Typing Posture','Hindi Unicode Typing','English Typing Speed','Word Processor Interface','Text और Font Formatting','Paragraph Formatting','Page Layout और Margins','Tables बनाना','Images, Shapes और Icons','Header, Footer और Page Number','Mail Merge परिचय','Document Review और Spell Check','PDF Export और Printing','Spreadsheet Interface','Data Entry और Cell Reference','Cell Formatting','Basic Formulas','SUM, AVERAGE, MIN और MAX','IF Function की मूल बातें','Sort और Filter','Charts एवं Visualization','Data Validation','Monthly Budget Sheet','Attendance Sheet Project','Presentation Interface','Effective Slide Design','Text, Image और Media','Transitions और Animations','Charts एवं SmartArt','Slide Show और Presenter View','Professional Presentation Project','Internet और Network परिचय','Browser एवं Smart Search','Email Account और Etiquette','Attachments और Cloud Storage','Video Meeting एवं Collaboration','Cyber Safety की मूल बातें','Strong Password और MFA','Phishing एवं Online Fraud','Privacy और Digital Footprint','UPI एवं Digital Banking Safety','Online Government Services','DigiLocker और Digital Documents','Basic Troubleshooting','Integrated Practical Project','Course Revision एवं Practice','CIT Capstone Assessment'
            ],
            'CLS' => [
                'भाषा आत्मविश्वास की शुरुआत','Listening Skills का परिचय','Greetings और Introduction','Daily-use English Sentences','Hindi से English विचार निर्माण','सही उच्चारण की मूल बातें','Alphabet Sounds और Phonics','Common Vocabulary Building','Numbers, Date और Time','Family और Community Conversation','Workplace Greetings','Request और Permission','Telephone Conversation','Directions पूछना और बताना','Shopping और Service Dialogue','Present Tense Practice','Past Tense Practice','Future Expression','Question Formation','Polite और Positive Language','Reading छोटे अनुच्छेद','Main Idea पहचानना','Context से शब्दार्थ','Pronunciation और Reading Aloud','Listening Note-taking','Formal और Informal Writing','Sentence और Paragraph Writing','Application एवं Leave Letter','Professional Email Writing','Notice और Message Writing','Resume Language Basics','Group Discussion','Public Speaking Confidence','Presentation Opening और Closing','Customer Service Communication','Workplace Problem Conversation','Interview Questions Practice','Mock Interview','Communication Revision','CLS Final Performance'
            ],
            'CSS' => [
                'Self-awareness और Strengths','Positive Attitude','Goal Setting','Time Management','Personal Discipline','Effective Communication','Teamwork और Cooperation','Leadership Basics','Problem Solving','Decision Making','Stress Management','Conflict Resolution','Workplace Etiquette','Professional Appearance','Customer Orientation','Interview Readiness','Resume और Career Planning','Financial Awareness','Entrepreneurship Basics','CSS Capstone Activity'
            ],
            'AI-DM' => [
                'AI का परिचय','Generative AI कैसे काम करता है','Responsible AI और Privacy','Prompt Writing Basics','Context और Better Prompts','AI से Document Productivity','AI से Spreadsheet सहायता','AI Image एवं Content Ethics','Digital Presence Basics','Target Audience पहचान','Content Planning','Social Media Marketing','SEO की मूल बातें','Campaign Measurement','AI-Digital Marketing Project'
            ],
        ];

        foreach ($topics as $code => $courseTopics) {
            $course = Course::where('code', $code)->firstOrFail();

            foreach ($courseTopics as $index => $topic) {
                $number = $index + 1;
                LearningSession::where('course_id', $course->id)->where('session_number', $number)->update([
                    'title_hi' => $topic,
                    'title_en' => $code.' Session '.$number,
                    'objectives_hi' => "इस सत्र के बाद विद्यार्थी {$topic} की मूल अवधारणा समझेगा, सही प्रक्रिया पहचान सकेगा और इसे व्यावहारिक परिस्थिति में लागू करेगा।",
                    'lesson_content_hi' => "परिचय: {$topic} को सरल उदाहरणों से समझें।\n\nमुख्य सीख: आवश्यक शब्दावली, चरणबद्ध प्रक्रिया, सुरक्षित एवं जिम्मेदार उपयोग और कार्यस्थल से जुड़े उदाहरण।\n\nअभ्यास: शिक्षक के प्रदर्शन के बाद विद्यार्थी स्वयं निर्देशित गतिविधि पूरी करेगा और परिणाम जाँचेगा।",
                    'classroom_notes_hi' => "शिक्षक 20 मिनट परिचय, 25 मिनट demonstration, 20 मिनट guided practice, 15 मिनट discussion और 10 मिनट recap कराएँ। स्थानीय एवं रोजगार-संबंधी उदाहरणों का उपयोग करें।",
                    'lab_activity_hi' => "{$topic} पर निर्धारित practical कार्य पूरा करें। कार्य को save/record करें, स्वयं जाँचें और session completion से पहले शिक्षक को दिखाएँ।",
                    'assessment_prompt_hi' => "{$topic} से संबंधित मुख्य प्रक्रिया अपने शब्दों में लिखें और एक practical उदाहरण प्रस्तुत करें।",
                    'content_status' => 'published',
                    'published_at' => now(),
                ]);
            }
        }
    }
}
