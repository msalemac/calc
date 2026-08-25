<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $apiKey;

    /**
     * تهيئة الخدمة وجلب مفتاح الـ API ديناميكياً من إعدادات النظام وقت التشغيل.
     */
    public function __construct()
    {
        $this->apiKey = Config::get('services.openai.key');
    }

    /**
     * معالج تعارض وجدولة المهام وحل الازدحام الذكي (Conflict Resolution Engine).
     */
    public function resolveConflict($userRole, $systemPrompt, $taskTitle, $tomorrowTasks, $routines)
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'لم يتم تكوين مفتاح الـ API الخاص بـ OpenAI في لوحة تحكم الأدمن بعد! يرجى إضافته لتفعيل الذكاء الاصطناعي.',
                'suggestions' => []
            ];
        }

        $tomorrowTasksList = collect($tomorrowTasks)->map(function ($task) {
            return "- " . $task['title'] . " (الوقت المتوقع: " . $task['estimated_duration'] . " دقيقة، الأولوية: " . $task['priority'] . ")";
        })->implode("\n");

        $routinesList = collect($routines)->map(function ($r) {
            return "- " . $r['activity_name'] . " (من الساعة: " . $r['start_time'] . " إلى: " . $r['end_time'] . ")";
        })->implode("\n");

        $userPrompt = "المستخدم الحالي دوره هو: {$userRole}.\n";
        $userPrompt .= "يريد المستخدم تأجيل هذه المهمة الهامة: '{$taskTitle}' إلى يوم الغد.\n";
        $userPrompt .= "ولكن جدول يوم الغد مزدحم جداً ويحتوي على المهام التالية:\n{$tomorrowTasksList}\n";
        $userPrompt .= "ولديه أوقات روتينية ثابتة مغلقة ومقدسة في يومه كالتالي:\n{$routinesList}\n\n";
        $userPrompt .= "حلل جدول الغد بدقة وقدم له تماماً 3 حلول بديلة وذكية وواقعية تناسب طبيعة دوره لتجنب التسويف والازدحام.\n";
        $userPrompt .= "يجب أن تكون الإجابة حصرياً بصيغة JSON مصفوفة تحتوي على 3 عناصر، كل عنصر يحتوي على الحقول التالية: 'title' (عنوان الحل البديل)، 'description' (شرح مبسط جداً للحل بأسلوب تشجيعي)، 'type' (نوع الحل: 'splitting' أو 'swapping' أو 'gap'). لا تكتب أي مقدمات أو نصوص خارج كود الـ JSON.";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt . " يجب أن تجيب دائماً بصيغة JSON صالحة ومطابقة للهيكل المطلوب تماماً وبطريقة احترافية باللغة العربية."],
                    ['role' => 'user', 'content' => $userPrompt]
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.7
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $content = $result['choices'][0]['message']['content'] ?? '{}';
                $decoded = json_decode($content, true);
                $suggestions = $decoded['suggestions'] ?? $decoded;

                return [
                    'success' => true,
                    'message' => 'تم تحليل الجدول بنجاح بواسطة الذكاء الاصطناعي المساعد.',
                    'suggestions' => $suggestions
                ];
            }

            Log::error('فشل اتصال OpenAI API: ' . $response->body());
            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء الاتصال بالذكاء الاصطناعي. يرجى التحقق من مفتاح الـ API.',
                'suggestions' => []
            ];

        } catch (\Exception $e) {
            Log::error('خطأ غير متوقع في خدمة AIService: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'تعذر الاتصال بالذكاء الاصطناعي بسبب مشكلة في الشبكة أو الخادم الرئيسي.',
                'suggestions' => []
            ];
        }
    }

    /**
     * ترجمة وتحويل الملف الصوتي المرفوع إلى نص عربي فصيح (OpenAI Whisper-1).
     * 
     * @param string $filePath المسار المؤقت للملف الصوتي المرفوع على الخادم
     * @return array يحتوي على حالة العملية والنص المترجم المستخلص
     */
    public function transcribeAudio($filePath)
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'text' => 'مفتاح الـ API الخاص بـ Whisper غير مهيأ بعد في لوحة التحكم!'
            ];
        }

        try {
            // إرسال الملف الصوتي كطلب متعدد الأجزاء (Multipart) لـ Whisper API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->attach(
                'file', file_get_contents($filePath), 'audio.webm' // إرفاق محتوى الصوت مع اسم ملف افتراضي
            )->post('https://api.openai.com/v1/audio/transcriptions', [
                'model' => 'whisper-1',
                'language' => 'ar' // فرض اللغة العربية لرفع دقة الترجمة الفورية للنصوص المحلية والعامية
            ]);

            if ($response->successful()) {
                $text = $response->json()['text'] ?? '';
                return [
                    'success' => true,
                    'text' => $text
                ];
            }

            Log::error('فشل اتصال Whisper API: ' . $response->body());
            return [
                'success' => false,
                'text' => 'فشلت معالجة الملف الصوتي. يرجى التأكد من جودة التسجيل وصلاحية مفتاح الـ API.'
            ];

        } catch (\Exception $e) {
            Log::error('خطأ في معالجة الصوت في AIService: ' . $e->getMessage());
            return [
                'success' => false,
                'text' => 'تعذر الاتصال بـ Whisper بسبب مشكلة في الخادم أو حجم الملف.'
            ];
        }
    }

    /**
     * استخلاص تفاصيل المهمة (العنوان، الملاحظات، الأولوية، والوقت) برمجياً من النص الصوتي المترجم.
     * 
     * @param string $text النص الصوتي المترجم المستلم من Whisper
     * @param string $userRole دور المستخدم الحالي لتخصيص محتوى المهمة
     * @return array يحتوي على مصفوفة الحقول لملء فورم الإضافة تلقائياً
     */
    public function extractTaskFromText($text, $userRole)
    {
        if (empty($this->apiKey)) {
            return ['success' => false];
        }

        $prompt = "أنت خبير في معالجة المدخلات وتحويلها لمهام منظمة بصيغة JSON.\n";
        $prompt .= "حلل النص الصوتي التالي المستخرج من مستخدم دوره هو [{$userRole}] واستخلص منه بيانات المهمة المطلوبة بدقة:\n";
        $prompt .= "النص الصوتي: '{$text}'\n\n";
        $prompt .= "قم بصياغة رد JSON صالحة يحتوي حصراً على مصفوفة بالحقول التالية لملء فورم إضافة المهمة تلقائياً أمام المستخدم:\n";
        $prompt .= "- 'title' (عنوان مناسب ومختصر جداً للمهمة باللغة العربية)\n";
        $prompt .= "- 'description' (تفاصيل وملاحظات إضافية مستخلصة من النص لمساعدته على التنفيذ)\n";
        $prompt .= "- 'priority' (مستوى الأهمية مستخلصاً من نبرة النص: 'low' أو 'medium' أو 'high'، والافتراضي 'medium')\n";
        $prompt .= "- 'estimated_duration' (الوقت المتوقع بالدقائق بناءً على حجم العمل المقدر بالنص، والافتراضي 30 دقيقة)\n";
        $prompt .= "لا تكتب أي مقدمات أو نصوص خارج كود الـ JSON.";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => "أنت خبير معالجة وتحليل مدخلات وبناء هيكليات JSON."],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.3 // لضمان رد منظم ودقيق وأقل تشتتاً للموديل
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $content = $result['choices'][0]['message']['content'] ?? '{}';
                $decoded = json_decode($content, true);
                
                return array_merge(['success' => true], $decoded);
            }

            return ['success' => false];

        } catch (\Exception $e) {
            Log::error('خطأ في استخلاص بيانات المهمة من النص: ' . $e->getMessage());
            return ['success' => false];
        }
    }
}