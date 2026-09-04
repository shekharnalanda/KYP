<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\LearningSession;
use Illuminate\Database\Seeder;

class CourseStructureSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['code' => 'CIT', 'name' => 'Certificate in Information Technology', 'description' => 'Computer fundamentals, productivity, internet safety and practical digital skills.', 'total_sessions' => 60, 'total_hours' => 120, 'minimum_exam_sessions' => 48, 'position' => 1],
            ['code' => 'CLS', 'name' => 'Certificate in Language Skills', 'description' => 'Hindi-English workplace communication, listening, speaking and confidence.', 'total_sessions' => 40, 'total_hours' => 80, 'minimum_exam_sessions' => 32, 'position' => 2],
            ['code' => 'CSS', 'name' => 'Certificate in Soft Skills', 'description' => 'Professional behaviour, teamwork, interview readiness and personal effectiveness.', 'total_sessions' => 20, 'total_hours' => 40, 'minimum_exam_sessions' => 16, 'position' => 3],
            ['code' => 'AI-DM', 'name' => 'AI Technology & Digital Marketing', 'description' => 'Responsible AI tools, digital presence and modern marketing essentials.', 'total_sessions' => 15, 'total_hours' => 30, 'minimum_exam_sessions' => 12, 'position' => 4],
        ];

        $phases = [
            'CIT' => ['डिजिटल परिचय एवं कंप्यूटर मूल बातें', 'ऑपरेटिंग सिस्टम और फाइल प्रबंधन', 'टाइपिंग एवं डॉक्यूमेंट निर्माण', 'स्प्रेडशीट और डेटा कौशल', 'प्रेजेंटेशन एवं डिजिटल संचार', 'इंटरनेट, साइबर सुरक्षा और नागरिक सेवाएँ'],
            'CLS' => ['भाषा आत्मविश्वास और सुनने का अभ्यास', 'दैनिक एवं कार्यस्थल संवाद', 'पठन, शब्दावली और उच्चारण', 'लेखन, ईमेल और प्रस्तुति कौशल'],
            'CSS' => ['स्व-जागरूकता और सकारात्मक दृष्टिकोण', 'टीमवर्क, समय प्रबंधन और समस्या समाधान', 'कार्यस्थल व्यवहार और इंटरव्यू तैयारी', 'उद्यमिता एवं करियर तैयारी'],
            'AI-DM' => ['AI की मूल अवधारणाएँ और जिम्मेदार उपयोग', 'Productivity AI Tools और Prompt Skills', 'Digital Presence एवं Content Creation', 'Social Media और Digital Marketing'],
        ];

        foreach ($courses as $courseData) {
            $course = Course::updateOrCreate(['code' => $courseData['code']], $courseData + ['is_active' => true]);
            $phaseList = $phases[$course->code];

            for ($number = 1; $number <= $course->total_sessions; $number++) {
                $phaseIndex = min(count($phaseList) - 1, (int) floor(($number - 1) * count($phaseList) / $course->total_sessions));
                LearningSession::updateOrCreate(
                    ['course_id' => $course->id, 'session_number' => $number],
                    [
                        'title_hi' => $phaseList[$phaseIndex].' — सत्र '.$number,
                        'title_en' => $course->code.' Learning Session '.$number,
                        'delivery_mode' => 'blended',
                        'duration_minutes' => 120,
                        'content_status' => 'outline',
                    ]
                );
            }
        }
    }
}
