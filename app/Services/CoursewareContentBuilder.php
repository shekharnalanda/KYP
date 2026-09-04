<?php

namespace App\Services;

use App\Models\LearningSession;

class CoursewareContentBuilder
{
    public function build(LearningSession $session): array
    {
        $code = $session->course->code;
        $number = (int) $session->session_number;
        $topic = trim($session->title_hi);
        $profile = $this->profile($code, $number, $topic);
        $stage = ['देखें और पहचानें', 'समझाकर बताएँ', 'निर्देश के साथ करें', 'स्वयं करके जाँचें', 'त्रुटि खोजकर सुधारें'][$number % 5];

        $processQuestion = $this->question("{$code}-{$number}-process", "{$topic} सीखते समय सबसे प्रभावी कार्य-पद्धति कौन-सी है?", $profile['correct_process'], [
            'बिना उद्देश्य समझे जल्दी-जल्दी सभी विकल्प दबाना',
            'केवल उत्तर याद करना और practical छोड़ देना',
            'दूसरे विद्यार्थी का output अपने नाम से जमा करना',
        ], $number, 0);
        $evidenceQuestion = $this->question("{$code}-{$number}-evidence", "{$topic} की practical completion का सबसे विश्वसनीय evidence क्या है?", $profile['evidence'], [
            'केवल यह कहना कि काम पूरा हो गया',
            'खाली file या अधूरा response',
            'विषय से अलग screenshot या उत्तर',
        ], $number, 1);
        $safetyQuestion = $this->question("{$code}-{$number}-safety", "{$topic} को वास्तविक परिस्थिति में लागू करते समय सबसे जरूरी बात क्या है?", $profile['safety'], [
            'गति के लिए जाँच और अनुमति को छोड़ देना',
            'निजी जानकारी सार्वजनिक रूप से साझा करना',
            'गलती छिपाकर बिना review के submit करना',
        ], $number, 2);

        return [
            'version' => 2,
            'language' => 'hi',
            'topic' => $topic,
            'outcomes' => [
                "{$topic} की जरूरी अवधारणाओं को अपने शब्दों में समझाना",
                "{$topic} से जुड़ी सही प्रक्रिया को क्रम से पूरा करना",
                "तैयार कार्य को checklist से जाँचकर evidence प्रस्तुत करना",
            ],
            'steps' => [
                [
                    'id' => 'orientation',
                    'minutes' => 8,
                    'type' => 'orientation',
                    'title' => 'आज का लक्ष्य और वास्तविक उपयोग',
                    'content' => "आज का विषय: {$topic}\n\nयह कौशल {$profile['domain']} में सीधे उपयोग होता है। पहले अपने अनुभव से सोचें कि आपने इसे कहाँ देखा या उपयोग किया है। इस session के अंत तक आपको केवल परिभाषा याद नहीं करनी है; आपको सही प्रक्रिया करके परिणाम दिखाना है।\n\nLearning target:\n1. {$profile['foundation']}\n2. {$profile['method']}\n3. {$profile['evidence']}\n\nआज की कार्य-पद्धति: {$stage}। शुरू करने से पहले आवश्यक device/application खोलें, work folder बनाएँ और अपने काम को नियमित रूप से save करें।",
                ],
                [
                    'id' => 'foundation',
                    'minutes' => 16,
                    'type' => 'concept',
                    'title' => 'मूल अवधारणा और जरूरी शब्द',
                    'content' => "{$topic} को समझने का आधार\n\n{$profile['foundation']} इसका उद्देश्य काम को अधिक सही, सुरक्षित और उपयोगी बनाना है। केवल button या sentence याद करने के बजाय यह समझें कि input क्या है, प्रक्रिया में क्या बदलता है और अपेक्षित output क्या होना चाहिए।\n\nजरूरी शब्दावली: {$profile['terms']}। हर शब्द को screen, संवाद या परिस्थिति में पहचानें और उसका एक उदाहरण बोलकर/लिखकर बताएँ।\n\nकाम करते समय तीन प्रश्न हमेशा पूछें: मैं क्या बनाना चाहता हूँ? सही तरीका कौन-सा है? तैयार परिणाम को कैसे जाँचूँगा? इन्हीं प्रश्नों से अनुमान लगाने की जगह समझ के साथ काम करने की आदत बनती है।",
                ],
                [
                    'id' => 'worked-example',
                    'minutes' => 16,
                    'type' => 'concept',
                    'title' => 'उदाहरण से गहराई से समझें',
                    'content' => "Worked example — {$topic}\n\nपरिस्थिति: {$profile['scenario']}\n\nसही सोच: पहले आवश्यकता स्पष्ट करें, फिर उपलब्ध जानकारी/साधन पहचानें, छोटे चरणों में कार्य करें और हर महत्वपूर्ण चरण के बाद परिणाम जाँचें। {$profile['method']}\n\nअच्छे परिणाम की पहचान: {$profile['evidence']} यदि परिणाम अपेक्षा के अनुसार नहीं है तो तुरंत पूरा काम दोबारा न करें। अंतिम सही चरण तक लौटें, एक परिवर्तन करें, जाँचें और कारण लिखें। इससे troubleshooting तथा आत्मनिर्भरता विकसित होती है।\n\nअब अपने शब्दों में बताएँ कि इस उदाहरण में input, action और output क्या थे।",
                ],
                [
                    'id' => 'guided-demo',
                    'minutes' => 14,
                    'type' => 'demo',
                    'title' => 'Guided Demonstration',
                    'content' => "शिक्षक/स्क्रीन के साथ चरणबद्ध प्रदर्शन\n\n1. उद्देश्य और expected output स्पष्ट करें।\n2. आवश्यक साधन, document, शब्द या परिस्थिति तैयार करें।\n3. {$profile['method']}\n4. प्रत्येक मुख्य action के बाद visible result देखें।\n5. {$profile['safety']}\n6. अंतिम output को नाम देकर save/record करें।\n7. {$profile['evidence']}\n\nविद्यार्थी हर चरण के बाद रुककर बताए कि उसने क्या देखा और अगला चरण क्यों जरूरी है। केवल demonstration देखने से learning पूरी नहीं होगी; कम-से-कम एक चरण विद्यार्थी स्वयं करके दिखाए।",
                ],
                [
                    'id' => 'guided-practice',
                    'minutes' => 14,
                    'type' => 'practice',
                    'title' => 'Guided Practice — अब साथ-साथ करें',
                    'content' => "अभ्यास कार्य: {$profile['practice']}\n\nपहला प्रयास निर्देश देखकर करें। दूसरे प्रयास में निर्देश छिपाकर वही प्रक्रिया दोहराएँ। जहाँ कठिनाई हो वहाँ exact step note करें—'समझ नहीं आया' लिखना पर्याप्त नहीं है।\n\nSelf-check checklist:\n• कार्य topic से संबंधित है।\n• प्रक्रिया सही क्रम में हुई है।\n• output पढ़ने/देखने/उपयोग करने योग्य है।\n• file, response या evidence सुरक्षित किया गया है।\n• किसी personal या unsafe data का उपयोग नहीं हुआ।\n\nपूरा होने पर अपने साथी/शिक्षक को परिणाम दिखाएँ और एक सुधार लागू करें।",
                ],
                [
                    'id' => 'check-1',
                    'minutes' => 8,
                    'type' => 'quiz',
                    'title' => 'Checkpoint 1 — सही प्रक्रिया',
                    'content' => "नीचे दिए विकल्पों में {$topic} के लिए सबसे सही प्रक्रिया चुनें। उत्तर देने से पहले guided demonstration के क्रम को याद करें।",
                    'interaction' => $processQuestion,
                ],
                [
                    'id' => 'lab-project',
                    'minutes' => 18,
                    'type' => 'practical',
                    'title' => 'Independent Lab Challenge',
                    'content' => "मुख्य practical: {$profile['practice']}\n\nअब यह कार्य बिना लगातार सहायता के पूरा करें। काम को तीन हिस्सों में बाँटें—तैयारी, निर्माण/प्रदर्शन और जाँच। कम-से-कम एक गलती या संभावित समस्या पहचानकर उसका समाधान भी लिखें।\n\nSubmission में शामिल करें:\n1. अपनाए गए मुख्य steps।\n2. तैयार output या उसका स्पष्ट वर्णन।\n3. self-check का परिणाम।\n4. आई कठिनाई और किया गया सुधार।\n5. यह skill वास्तविक जीवन/कार्यस्थल में कहाँ उपयोग होगी।\n\nExpected evidence: {$profile['evidence']}",
                ],
                [
                    'id' => 'check-2',
                    'minutes' => 8,
                    'type' => 'quiz',
                    'title' => 'Checkpoint 2 — परिणाम की जाँच',
                    'content' => "सिर्फ task कर देना पर्याप्त नहीं है। सही evidence से सिद्ध होना चाहिए कि skill समझी और लागू की गई है।",
                    'interaction' => $evidenceQuestion,
                ],
                [
                    'id' => 'check-3',
                    'minutes' => 10,
                    'type' => 'quiz',
                    'title' => 'Checkpoint 3 — सुरक्षित उपयोग',
                    'content' => "कौशल तभी उपयोगी है जब उसका उपयोग जिम्मेदारी, शुद्धता और सुरक्षा के साथ किया जाए। परिस्थिति पढ़कर सही प्राथमिकता चुनें।",
                    'interaction' => $safetyQuestion,
                ],
                [
                    'id' => 'reflection-final',
                    'minutes' => 8,
                    'type' => 'reflection',
                    'title' => 'Revision, Reflection और Final Check',
                    'content' => "Session recap — {$topic}\n\nआज आपने अवधारणा समझी, worked example देखा, guided practice किया, स्वतंत्र practical पूरा किया और तीन checkpoints दिए। अब बिना ऊपर देखे प्रक्रिया के पाँच मुख्य steps बोलें या लिखें।\n\nअपने practical response में यह सुनिश्चित करें कि output, कठिनाई, सुधार और वास्तविक उपयोग दर्ज हैं। सभी दस चरण पूरे करें; active learning time तथा quiz score system द्वारा जाँचा जाएगा।\n\nअगले session से पहले छोटा अभ्यास: इसी कौशल का एक नया उदाहरण खोजें और बताएँ कि उसमें आज की प्रक्रिया कैसे लागू होगी।",
                ],
            ],
        ];
    }

    private function profile(string $code, int $number, string $topic): array
    {
        return match ($code) {
            'CIT' => $this->cit($number, $topic),
            'CLS' => $this->cls($number, $topic),
            'CSS' => $this->css($number, $topic),
            'AI-DM' => $this->aiDm($number, $topic),
            default => $this->generic($topic),
        };
    }

    private function cit(int $number, string $topic): array
    {
        if ($number <= 10) {
            return $this->make('डिजिटल उपकरणों और computer environment', "{$topic} में hardware, software, input, process और output के संबंध को पहचानना आवश्यक है।", 'device/interface को पहचानें, सही control चुनें, सुरक्षित रूप से action करें और screen पर आए परिणाम को verify करें।', "{$topic} से संबंधित components/controls पहचानकर उनका कार्य समझाएँ और supervised task पूरा करें।", 'सही component या setting की पहचान, visible result और learner explanation', 'device को सावधानी से चलाएँ, accessibility और data-safety settings का सम्मान करें।', 'एक नए learner को computer/device पर सुरक्षित रूप से पहला task पूरा कराना है।', 'input, output, processing, memory, interface, accessibility', 'उद्देश्य समझकर सही component/control चुनना, action करना और परिणाम verify करना');
        }
        if ($number <= 13) {
            return $this->make('file तथा information management', "{$topic} में file, folder, path, name और backup की स्पष्ट समझ data को व्यवस्थित रखती है।", 'पहले सही location खोलें; meaningful नाम दें; create/copy/move/rename/delete action करें; फिर destination तथा Recycle Bin/backup जाँचें।', "{$topic} का उपयोग करके निर्धारित folder structure और files बनाएँ, व्यवस्थित करें तथा recovery/backup evidence दें।", 'सही नाम/location वाला folder tree और action के बाद verified files', 'delete या overwrite से पहले target जाँचें; महत्वपूर्ण data का backup रखें।', 'कार्यालय की बिखरी files को विषय और तारीख के अनुसार व्यवस्थित करना है।', 'file, folder, path, extension, recycle bin, backup', 'सही location में चरणबद्ध file operation करना और अंतिम structure verify करना');
        }
        if ($number <= 16) {
            return $this->make('Hindi-English digital typing', "{$topic} में posture, keyboard familiarity, accuracy और rhythm speed से अधिक महत्वपूर्ण शुरुआती आधार हैं।", 'सही posture रखें, home position पहचानें, छोटे समूह में type करें, screen देखकर त्रुटि सुधारें और accuracy record करें।', "{$topic} पर निर्धारित passage type करें; गलतियाँ सुधारें और समय तथा accuracy दर्ज करें।", 'save किया हुआ typed passage, word count, time और corrected error list', 'अनावश्यक personal text type न करें; आरामदायक posture और नियमित micro-break रखें।', 'एक साफ Hindi-English notice निर्धारित समय में तैयार करना है।', 'keyboard, Unicode, posture, accuracy, words per minute', 'पहले accuracy, फिर controlled speed और अंत में proofread/save करना');
        }
        if ($number <= 26) {
            return $this->make('professional document creation', "{$topic} document की readability, structure और professional presentation को बेहतर बनाता है।", 'content plan करें, उपयुक्त tool/command चुनें, formatting लागू करें, preview से जाँचें और सही format में save/export करें।', "{$topic} का उपयोग करते हुए एक professional notice/letter/report तैयार करें और PDF/print preview जाँचें।", 'सुसंगत formatting वाला editable document और verified PDF/print preview', 'अनधिकृत image/text न लें; spelling, page layout और recipient data जाँचें।', 'संस्थान के लिए पढ़ने योग्य official document तैयार करना है।', 'document, formatting, alignment, layout, review, export', 'content तैयार करके उद्देश्यपूर्ण formatting, review और verified export करना');
        }
        if ($number <= 37) {
            return $this->make('spreadsheet तथा data analysis', "{$topic} data को सही structure में रखकर calculation, checking और decision में मदद करता है।", 'headings बनाएँ, clean data भरें, सही cells/range चुनें, formula/tool लागू करें, sample calculation से verify करें और result format करें।', "{$topic} से attendance/budget/data sheet बनाएँ; formula, validation या chart लगाकर result verify करें।", 'labels सहित spreadsheet, correct formulas/results और visible validation/chart evidence', 'formula में cell references जाँचें; निजी data सीमित रखें और original data का backup रखें।', 'संस्थान के monthly records से उपयोगी summary बनानी है।', 'cell, row, column, range, formula, function, validation', 'structured data पर सही formula/tool लगाना और independent check से result verify करना');
        }
        if ($number <= 44) {
            return $this->make('presentation और visual communication', "{$topic} audience को कम शब्दों में स्पष्ट, क्रमबद्ध और दृश्य रूप से जानकारी समझाने में सहायक है।", 'audience तय करें, slide outline बनाएँ, एक slide पर एक मुख्य विचार रखें, readable visuals जोड़ें और slideshow rehearsal करें।', "{$topic} का उपयोग करके 5-slide presentation तैयार करें और presenter mode में समझाएँ।", 'consistent 5-slide deck, readable visuals और completed slideshow rehearsal', 'copyright-safe visuals लें; बहुत छोटा text, अनावश्यक animation और sensitive data से बचें।', 'कक्षा/कार्यस्थल में किसी योजना को पाँच मिनट में प्रस्तुत करना है।', 'audience, slide, layout, visual hierarchy, transition, slideshow', 'audience-केंद्रित outline बनाकर readable slides और rehearsed presentation तैयार करना');
        }
        if ($number <= 49) {
            return $this->make('internet, email और online collaboration', "{$topic} में सही search/communication technique समय बचाती है और विश्वसनीय information तक पहुँच देती है।", 'स्पष्ट query/message बनाएँ, trusted result/recipient चुनें, details verify करें, आवश्यक action करें और sent/shared result जाँचें।', "{$topic} पर trusted information खोजें या professional communication/collaboration task पूरा करें।", 'verified source/recipient, स्पष्ट message/work product और successful send/share evidence', 'URL, sender, recipient, attachment permission और privacy settings जाँचें।', 'दूर बैठे सहकर्मी के साथ सुरक्षित रूप से information और file साझा करनी है।', 'browser, query, source, email etiquette, attachment, collaboration', 'trusted source/recipient verify करके स्पष्ट और सुरक्षित digital communication करना');
        }
        if ($number <= 57) {
            return $this->make('cyber safety और digital public services', "{$topic} में पहचान, सहमति, privacy और transaction verification व्यक्ति तथा data की सुरक्षा के लिए अनिवार्य हैं।", 'official source खोलें, URL/identity verify करें, minimum data दें, MFA/secure method अपनाएँ और receipt/logout जाँचें।', "{$topic} पर safe/unsafe scenario पहचानें और official demo environment में सही प्रक्रिया दिखाएँ।", 'risk identification, सही protective action और बिना sensitive data के process evidence', 'OTP, password, PIN या personal document साझा न करें; केवल official verified service उपयोग करें।', 'एक suspicious message/service request मिलने पर सुरक्षित निर्णय लेना है।', 'privacy, consent, phishing, MFA, encryption, official portal', 'पहचान और URL verify करके minimum-data, secure authentication और documented completion अपनाना');
        }

        return $this->make('समेकित IT problem solving', "{$topic} पहले सीखे hardware, files, documents, data, presentation और internet skills को एक परिणाम में जोड़ता है।", 'requirement पढ़ें, task plan बनाएँ, सही tools चुनें, चरणबद्ध output बनाएँ, troubleshoot करें और final checklist से submit करें।', "{$topic} के लिए integrated digital product तैयार करें जिसमें कम-से-कम तीन CIT skills का प्रमाण हो।", 'working final product, source files, checklist और learner explanation', 'original work करें, backups रखें, private data हटाएँ और हर output खोलकर जाँचें।', 'एक वास्तविक संस्थागत आवश्यकता को end-to-end digital solution में बदलना है।', 'planning, integration, troubleshooting, quality check, submission', 'plan-build-check-improve-submit cycle से independently working solution बनाना');
    }

    private function cls(int $number, string $topic): array
    {
        if ($number <= 10) {
            return $this->make('भाषा आत्मविश्वास, listening और दैनिक संवाद', "{$topic} में अर्थ समझना, स्पष्ट ध्वनि और छोटे सही वाक्य confidence का आधार बनाते हैं।", 'पहले सुनें/पढ़ें, key words पहचानें, model दोहराएँ, अपने बारे में नया वाक्य बनाएँ और partner response सुनें।', "{$topic} पर 6–8 वाक्यों का बोलने-सुनने का अभ्यास करें और मुख्य शब्द लिखें।", 'स्पष्ट recorded/spoken response, key-word notes और partner feedback', 'सम्मानजनक भाषा रखें; accent का मजाक न बनाएँ और personal information सीमित रखें।', 'दैनिक जीवन में नए व्यक्ति से स्पष्ट और विनम्र बातचीत करनी है।', 'meaning, pronunciation, stress, sentence, listening cue, response', 'सुनना, अर्थ पहचानना, model दोहराना और अपने संदर्भ में सही वाक्य बोलना');
        }
        if ($number <= 20) {
            return $this->make('दैनिक तथा workplace conversation', "{$topic} में सही tense, question pattern और polite expression संवाद को स्पष्ट बनाते हैं।", 'situation पहचानें, suitable opening चुनें, छोटा प्रश्न/request बनाएँ, उत्तर ध्यान से सुनें और polite closing दें।', "{$topic} पर partner role-play करें; role बदलकर वही संवाद दोबारा करें।", 'पूर्ण role-play, सही functional phrases और self-corrected sentence notes', 'सम्मानजनक tone, consent और cultural sensitivity बनाए रखें।', 'कार्यस्थल/सेवा केंद्र में जानकारी माँगनी या समस्या समझानी है।', 'opening, question, request, response, tense, polite closing', 'situation के अनुसार स्पष्ट question/request, attentive response और polite closing करना');
        }
        if ($number <= 25) {
            return $this->make('reading, pronunciation और listening comprehension', "{$topic} में heading, key words, context और tone से message का अर्थ निकाला जाता है।", 'पहले preview करें, फिर उद्देश्य के साथ पढ़ें/सुनें, key details mark करें, कठिन शब्द context से समझें और summary बनाएँ।', "{$topic} पर passage/audio का main idea, तीन details और पाँच नए शब्द दर्ज करें।", 'main idea, accurate details, vocabulary notes और oral/written summary', 'source तथा speaker का सम्मान करें; सुनी हुई निजी जानकारी साझा न करें।', 'छोटे workplace message से सही action तय करना है।', 'main idea, detail, context, pronunciation, note-taking, summary', 'purposeful reading/listening से main idea और evidence निकालकर concise summary बनाना');
        }
        if ($number <= 31) {
            return $this->make('professional writing और written communication', "{$topic} में उद्देश्य, reader, structure और proofreading अच्छे लेखन की पहचान हैं।", 'purpose/reader तय करें, points outline करें, clear sentences लिखें, suitable format लगाएँ और grammar/spelling/details proofread करें।', "{$topic} के format में वास्तविक उपयोग का draft लिखें, review लें और corrected final version बनाएँ।", 'उचित format, complete details, professional tone और corrected final draft', 'recipient, date, attachment और personal details submit करने से पहले verify करें।', 'संस्थान/कार्यस्थल के लिए स्पष्ट written message तैयार करना है।', 'purpose, audience, format, paragraph, tone, proofreading', 'reader-केंद्रित outline से clear draft लिखना, revise करना और final details verify करना');
        }

        return $this->make('performance communication, interview और public speaking', "{$topic} में content organization, confident delivery, listening और relevant response एक साथ आवश्यक हैं।", 'opening तैयार करें, 3-point structure रखें, examples दें, eye contact/voice नियंत्रित करें, questions सुनकर relevant answer और closing दें।', "{$topic} का timed role-play/performance करें, record/review करें और दूसरे attempt में सुधार दिखाएँ।", 'structured performance, relevant responses, timing और before-after feedback record', 'ईमानदार जानकारी दें; discriminatory/personal questions में professional boundaries समझें।', 'interview, group discussion या customer situation में प्रभावी प्रदर्शन करना है।', 'opening, structure, evidence, delivery, active listening, closing', 'prepare-practise-perform-review cycle से relevant और confident communication करना');
    }

    private function css(int $number, string $topic): array
    {
        if ($number <= 5) {
            return $this->make('व्यक्तिगत प्रभावशीलता और self-management', "{$topic} में स्वयं का ईमानदार आकलन, व्यवहार योग्य लक्ष्य और नियमित review आवश्यक हैं।", 'वर्तमान स्थिति लिखें, priority चुनें, छोटा measurable action तय करें, समय सीमा रखें और progress evidence review करें।', "{$topic} पर व्यक्तिगत worksheet/action plan बनाएँ और एक सप्ताह की tracking विधि तय करें।", 'specific action plan, realistic timeline और measurable progress indicator', 'निजी reflection साझा करना वैकल्पिक रखें; लक्ष्य सुरक्षित, नैतिक और यथार्थवादी हो।', 'सीमित समय में पढ़ाई और जिम्मेदारियों को संतुलित करना है।', 'strength, priority, SMART goal, habit, discipline, review', 'ईमानदार self-assessment से measurable action plan बनाकर नियमित review करना');
        }
        if ($number <= 12) {
            return $this->make('teamwork, leadership और problem solving', "{$topic} में facts सुनना, अलग दृष्टिकोण समझना और साझा solution पर जिम्मेदारी लेना जरूरी है।", 'समस्या स्पष्ट करें, facts और assumptions अलग करें, सभी की बात सुनें, विकल्प बनाएँ, criteria से चुनें और जिम्मेदारी/time तय करें।', "{$topic} का group scenario हल करें; roles, decision और follow-up लिखकर प्रस्तुत करें।", 'साझा problem statement, reasoned decision, assigned roles और review plan', 'व्यक्ति पर नहीं समस्या पर ध्यान दें; सम्मान, fairness और consent बनाए रखें।', 'team में मतभेद या बाधा के बावजूद समय पर परिणाम देना है।', 'role, cooperation, evidence, option, decision, accountability', 'facts और perspectives से विकल्प बनाकर fair decision तथा clear responsibility तय करना');
        }
        if ($number <= 17) {
            return $this->make('workplace professionalism और career readiness', "{$topic} employability में behaviour, preparation, communication और evidence of skill को जोड़ता है।", 'स्थिति/role समझें, professional standard पहचानें, relevant evidence तैयार करें, practice करें और feedback के आधार पर सुधारें।', "{$topic} पर workplace simulation/interview task करें और professional checklist से self-review करें।", 'professional behaviour/output, relevant examples और feedback के बाद improved attempt', 'ईमानदार qualification दें, confidentiality रखें और सभी व्यक्तियों के प्रति सम्मान दिखाएँ।', 'नौकरी/ग्राहक/कार्यस्थल की वास्तविक परिस्थिति में professional response देना है।', 'professionalism, etiquette, evidence, feedback, employability, improvement', 'स्थिति समझकर prepared, respectful और evidence-based professional response देना');
        }

        return $this->make('financial awareness, entrepreneurship और capstone planning', "{$topic} में आवश्यकता पहचानना, value बनाना, लागत/जोखिम समझना और जिम्मेदार action plan आवश्यक है।", 'समस्या/ग्राहक पहचानें, solution और value लिखें, resources/cost अनुमानित करें, risk जाँचें, छोटे pilot तथा measurement plan बनाएँ।', "{$topic} पर सरल business/career project canvas और 2-minute pitch तैयार करें।", 'problem-solution fit, basic numbers, ethical risk check और actionable next step', 'झूठे दावे, unsafe loan, personal-data misuse और बिना जाँच investment से बचें।', 'स्थानीय आवश्यकता को कम संसाधन वाले उपयोगी समाधान में बदलना है।', 'need, value, customer, cost, revenue, risk, pilot', 'need-value-cost-risk-pilot framework से ethical और measurable plan बनाना');
    }

    private function aiDm(int $number, string $topic): array
    {
        if ($number <= 3) {
            return $this->make('AI literacy, responsible use और privacy', "{$topic} में AI की क्षमता के साथ उसकी सीमाएँ, bias, hallucination और data privacy समझना जरूरी है।", 'task स्पष्ट करें, non-sensitive input दें, output generate करें, facts/source verify करें, bias/privacy review करें और human judgement से सुधारें।', "{$topic} पर safe example जाँचें; AI output में एक strength, limitation और verification step दर्ज करें।", 'prompt, generated output, fact/privacy review और human-corrected final version', 'password, पहचान-पत्र, निजी विद्यार्थी/ग्राहक data या confidential file AI tool में न डालें।', 'AI output को बिना अंधविश्वास के जिम्मेदारी से उपयोग करना है।', 'model, prompt, output, hallucination, bias, privacy, verification', 'minimum safe data के साथ AI output लेना, independently verify करना और human review से सुधारना');
        }
        if ($number <= 8) {
            return $this->make('prompting और AI-assisted productivity', "{$topic} में अच्छा परिणाम पाने के लिए role, context, task, constraints और output format स्पष्ट होना चाहिए।", 'objective लिखें, पर्याप्त non-sensitive context दें, format/quality criteria बताएँ, output जाँचें और focused follow-up prompt से सुधारें।', "{$topic} का first prompt और improved prompt चलाएँ; दोनों outputs compare करके सुधार का कारण लिखें।", 'दो prompt versions, output comparison, verification और edited final work', 'AI output को अपना original fact न मानें; confidential data, plagiarism और misleading media से बचें।', 'AI की सहायता से उपयोगी document/data/content बनाना है, लेकिन final जिम्मेदारी learner की होगी।', 'role, context, instruction, constraint, format, iteration', 'clear structured prompt, verification और human editing से fit-for-purpose output बनाना');
        }
        if ($number <= 11) {
            return $this->make('digital presence, audience और content planning', "{$topic} में audience need, clear value, consistent identity और ethical content planning मुख्य आधार हैं।", 'audience persona बनाएँ, objective चुनें, key message लिखें, suitable format/channel तय करें, calendar बनाएँ और quality/accessibility review करें।', "{$topic} के लिए audience-specific content brief/post/calendar तैयार करें और CTA सहित review करें।", 'defined audience, clear message, original content asset, CTA और review checklist', 'copyright, consent, accessibility और truthful representation का पालन करें।', 'स्थानीय संस्था/सेवा के लिए भरोसेमंद digital communication तैयार करना है।', 'audience, objective, value, content pillar, format, CTA', 'audience need से objective और message जोड़कर original, accessible और measurable content बनाना');
        }

        return $this->make('social media, SEO और campaign measurement', "{$topic} में objective, discoverability, ethical publishing और data-based improvement एक साथ काम करते हैं।", 'SMART objective/KPI तय करें, audience/keyword चुनें, content/CTA प्रकाशित योजना बनाएँ, metrics record करें और evidence के आधार पर अगला सुधार तय करें।', "{$topic} पर mini campaign/SEO plan बनाएँ; sample content, KPI table और improvement decision प्रस्तुत करें।", 'objective-linked content, relevant keywords/CTA, metric calculation और justified improvement', 'fake engagement, spam, misleading claims, privacy violation और unauthorized content से बचें।', 'कम budget में स्थानीय service के लिए measurable digital campaign बनाना है।', 'keyword, reach, engagement, conversion, KPI, insight, optimization', 'objective-content-metric-insight cycle से ethical campaign चलाना और data से सुधार करना');
    }

    private function generic(string $topic): array
    {
        return $this->make('व्यावहारिक कौशल विकास', "{$topic} को concept, process और verified output के रूप में सीखना चाहिए।", 'उद्देश्य समझें, चरणबद्ध task करें, result जाँचें और evidence प्रस्तुत करें।', "{$topic} पर guided तथा independent task पूरा करें।", 'पूर्ण output, checklist और learner explanation', 'सुरक्षा, privacy, honesty और permission का पालन करें।', 'वास्तविक जीवन में कौशल लागू करना है।', 'concept, process, practice, evidence, review', 'समझकर चरणबद्ध अभ्यास और verification करना');
    }

    private function question(string $id, string $prompt, string $correctAnswer, array $distractors, int $sessionNumber, int $offset): array
    {
        $options = [$correctAnswer, ...$distractors];
        $rotation = ($sessionNumber + $offset) % count($options);
        $options = array_merge(array_slice($options, $rotation), array_slice($options, 0, $rotation));
        $correct = array_search($correctAnswer, $options, true);

        return [
            'id' => $id,
            'prompt' => $prompt,
            'options' => $options,
            'correct' => (string) $correct,
        ];
    }

    private function make(string $domain, string $foundation, string $method, string $practice, string $evidence, string $safety, string $scenario, string $terms, string $correctProcess): array
    {
        return compact('domain', 'foundation', 'method', 'practice', 'evidence', 'safety', 'scenario', 'terms') + ['correct_process' => $correctProcess];
    }
}
