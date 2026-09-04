<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            'CIT' => [
                ['कंप्यूटर का मुख्य प्रोसेसिंग भाग कौन-सा है?', 'Which is the main processing part of a computer?', ['A' => ['CPU', 'CPU'], 'B' => ['Monitor', 'Monitor'], 'C' => ['Keyboard', 'Keyboard'], 'D' => ['Printer', 'Printer']], 'A'],
                ['किस shortcut से selected text copy होता है?', 'Which shortcut copies selected text?', ['A' => ['Ctrl+X', 'Ctrl+X'], 'B' => ['Ctrl+C', 'Ctrl+C'], 'C' => ['Ctrl+V', 'Ctrl+V'], 'D' => ['Ctrl+Z', 'Ctrl+Z']], 'B'],
                ['मजबूत password की सबसे अच्छी विशेषता क्या है?', 'What best describes a strong password?', ['A' => ['केवल नाम', 'Only a name'], 'B' => ['केवल अंक', 'Only numbers'], 'C' => ['लंबा और मिश्रित', 'Long and mixed'], 'D' => ['जन्मतिथि', 'Date of birth']], 'C'],
                ['Spreadsheet में formula किस चिह्न से शुरू होता है?', 'Which symbol starts a spreadsheet formula?', ['A' => ['=', '='], 'B' => ['#', '#'], 'C' => ['@', '@'], 'D' => ['&', '&']], 'A'],
                ['Phishing से बचने का सुरक्षित तरीका क्या है?', 'What is a safe way to avoid phishing?', ['A' => ['हर link खोलना', 'Open every link'], 'B' => ['Sender और URL जाँचना', 'Verify sender and URL'], 'C' => ['OTP साझा करना', 'Share OTP'], 'D' => ['Password भेजना', 'Send password']], 'B'],
            ],
            'CLS' => [
                ['“Please” का प्रयोग किसलिए होता है?', 'What is “Please” used for?', ['A' => ['विनम्र अनुरोध', 'A polite request'], 'B' => ['क्रोध', 'Anger'], 'C' => ['इंकार', 'Refusal'], 'D' => ['मौन', 'Silence']], 'A'],
                ['सही वाक्य चुनें।', 'Choose the correct sentence.', ['A' => ['He go to work.', 'He go to work.'], 'B' => ['He goes to work.', 'He goes to work.'], 'C' => ['He going work.', 'He going work.'], 'D' => ['He gone work.', 'He gone work.']], 'B'],
                ['ईमेल का स्पष्ट विषय क्या बताता है?', 'What does a clear email subject show?', ['A' => ['संदेश का उद्देश्य', 'Purpose of the message'], 'B' => ['केवल नाम', 'Only the name'], 'C' => ['Password', 'Password'], 'D' => ['Attachment size', 'Attachment size']], 'A'],
                ['Active listening में क्या शामिल है?', 'What is part of active listening?', ['A' => ['बीच में रोकना', 'Interrupting'], 'B' => ['ध्यान और प्रतिक्रिया', 'Attention and response'], 'C' => ['फोन देखना', 'Checking phone'], 'D' => ['विषय बदलना', 'Changing topic']], 'B'],
                ['“Thank you” का उचित उत्तर क्या है?', 'What is an appropriate reply to “Thank you”?', ['A' => ['You are welcome.', 'You are welcome.'], 'B' => ['Never.', 'Never.'], 'C' => ['Stop.', 'Stop.'], 'D' => ['Why?', 'Why?']], 'A'],
            ],
            'CSS' => [
                ['समय प्रबंधन का पहला उपयोगी कदम क्या है?', 'What is a useful first step in time management?', ['A' => ['कार्य सूची बनाना', 'Create a task list'], 'B' => ['सब टालना', 'Postpone everything'], 'C' => ['लक्ष्य हटाना', 'Remove goals'], 'D' => ['समय न देखना', 'Ignore time']], 'A'],
                ['Teamwork में सबसे जरूरी क्या है?', 'What is essential in teamwork?', ['A' => ['सहयोग', 'Cooperation'], 'B' => ['दोषारोपण', 'Blaming'], 'C' => ['गोपनीय प्रतिस्पर्धा', 'Hidden competition'], 'D' => ['मौन', 'Silence']], 'A'],
                ['Interview में उचित व्यवहार क्या है?', 'What is appropriate interview behaviour?', ['A' => ['समय पर पहुँचना', 'Arrive on time'], 'B' => ['बिना तैयारी जाना', 'Go unprepared'], 'C' => ['फोन चलाना', 'Use the phone'], 'D' => ['असभ्य होना', 'Be rude']], 'A'],
                ['समस्या समाधान में पहले क्या करना चाहिए?', 'What should be done first in problem solving?', ['A' => ['समस्या पहचानना', 'Identify the problem'], 'B' => ['दोष देना', 'Assign blame'], 'C' => ['भाग जाना', 'Walk away'], 'D' => ['अनुमान लगाना', 'Guess']], 'A'],
                ['Professional communication कैसी होनी चाहिए?', 'How should professional communication be?', ['A' => ['स्पष्ट और सम्मानपूर्ण', 'Clear and respectful'], 'B' => ['अस्पष्ट', 'Unclear'], 'C' => ['अपमानजनक', 'Insulting'], 'D' => ['अधूरी', 'Incomplete']], 'A'],
            ],
            'AI-DM' => [
                ['AI output उपयोग करने से पहले क्या करना चाहिए?', 'What should be done before using AI output?', ['A' => ['तथ्य जाँच', 'Fact-check it'], 'B' => ['बिना पढ़े publish', 'Publish without reading'], 'C' => ['Password साझा', 'Share password'], 'D' => ['Source हटाना', 'Remove sources']], 'A'],
                ['Prompt में context देने से क्या लाभ होता है?', 'What is the benefit of adding context to a prompt?', ['A' => ['बेहतर relevance', 'Better relevance'], 'B' => ['Internet बंद', 'Internet stops'], 'C' => ['File delete', 'Files are deleted'], 'D' => ['कोई लाभ नहीं', 'No benefit']], 'A'],
                ['Digital marketing में target audience क्या है?', 'What is a target audience in digital marketing?', ['A' => ['इच्छित ग्राहक समूह', 'Intended customer group'], 'B' => ['सभी devices', 'All devices'], 'C' => ['Password list', 'Password list'], 'D' => ['केवल staff', 'Only staff']], 'A'],
                ['Responsible AI usage में क्या जरूरी है?', 'What is essential for responsible AI use?', ['A' => ['Privacy और verification', 'Privacy and verification'], 'B' => ['Personal data खुला रखना', 'Expose personal data'], 'C' => ['Fake content फैलाना', 'Spread fake content'], 'D' => ['Copyright ignore करना', 'Ignore copyright']], 'A'],
                ['CTA का अर्थ क्या है?', 'What does CTA mean?', ['A' => ['Call to Action', 'Call to Action'], 'B' => ['Computer Text Area', 'Computer Text Area'], 'C' => ['Content Transfer App', 'Content Transfer App'], 'D' => ['Customer Time Audit', 'Customer Time Audit']], 'A'],
            ],
        ];

        foreach ($banks as $code => $items) {
            $course = Course::where('code', $code)->firstOrFail();
            $exam = Exam::updateOrCreate(
                ['course_id' => $course->id, 'title_en' => $code.' Foundation Assessment'],
                ['title_hi' => $code.' आधार मूल्यांकन', 'duration_minutes' => 30, 'total_questions' => count($items), 'max_marks' => 200, 'status' => 'published', 'published_at' => now()]
            );

            foreach ($items as $position => [$hi, $en, $options, $correct]) {
                $question = Question::updateOrCreate(
                    ['course_id' => $course->id, 'text_en' => $en],
                    ['learning_session_id' => $course->sessions()->orderBy('session_number')->value('id'), 'text_hi' => $hi, 'type' => 'single_choice', 'marks' => 40, 'negative_marks' => 0, 'difficulty' => 'foundation', 'status' => 'published']
                );

                foreach ($options as $key => [$optionHi, $optionEn]) {
                    $question->options()->updateOrCreate(
                        ['option_key' => $key],
                        ['text_hi' => $optionHi, 'text_en' => $optionEn, 'is_correct' => $key === $correct]
                    );
                }

                $exam->questions()->syncWithoutDetaching([$question->id => ['position' => $position + 1]]);
            }
        }
    }
}
