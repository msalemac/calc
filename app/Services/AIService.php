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
     * 
     * @param string $userRole نوع دور المستخدم (طالب، مدير...)
     * @param string $systemPrompt التوجيه والنبرة المخصصة للدور من قاعدة البيانات
     * @param string $taskTitle عنوان المهمة المراد تأجيلها
     * @param array $tomorrowTasks قائمة مهام الغد المزدحمة
     * @param array $routines قائمة الروتين اليومي الثابت للمستخدم
     * @return array يحتوي على 3 حلول بديلة ومصاغة بصيغة هيكلية
     */
    public function resolveConflict($userRole, $systemPrompt, $taskTitle, $tomorrowTasks, $routines)
    {
        // التحقق الاحترازي: إذا لم يقم الأدمن بإدخال مفتاح الـ API بعد في لوحة التحكم
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'لم يتم تكوين مفتاح الـ API الخاص بـ OpenAI في لوحة تحكم الأدمن بعد! يرجى إضافته لتفعيل الذكاء الاصطناعي.',
                'suggestions' => []
            ];
        }

        // تجهيز بيانات المهام المزدحمة للغد بصيغة نصية ليفهمها الذكاء الاصطناعي
        $tomorrowTasksList = collect($tomorrowTasks)->map(function ($task) {
            return "- " . $task['title'] . " (الوقت المتوقع: " . $task['estimated_duration'] . " دقيقة، الأولوية: " . $task['priority'] . ")";
        })->implode("\n");

        // تجهيز بيانات الروتين الثابت لليوم بصيغة نصية
        $routinesList = collect($routines)->map(function ($r) {
            return "- " . $r['activity_name'] . " (من الساعة: " . $r['start_time'] . " إلى: " . $r['end_time'] . ")";
        })->implode("\n");

        // صياغة الـ Prompt الهندسي المحكم لإجبار الذكاء الاصطناعي على إعادة رد مهيكل JSON حصراً
        $userPrompt = "المستخدم الحالي دوره هو: {$userRole}.\n";
        $userPrompt .= "يريد المستخدم تأجيل هذه المهمة الهامة: '{$taskTitle}' إلى يوم الغد.\n";
        $userPrompt .= "ولكن جدول يوم الغد مزدحم جداً ويحتوي على المهام التالية:\n{$tomorrowTasksList}\n";
        $userPrompt .= "ولديه أوقات روتينية ثابتة مغلقة ومقدسة في يومه كالتالي:\n{$routinesList}\n\n";
        $userPrompt .= "حلل جدول الغد بدقة وقدم له تماماً 3 حلول بديلة وذكية وواقعية تناسب طبيعة دوره لتجنب التسويف والازدحام.\n";
        $userPrompt .= "يجب أن تكون الإجابة حصرياً بصيغة JSON مصفوفة تحتوي على 3 عناصر، كل عنصر يحتوي على الحقول التالية: 'title' (عنوان الحل البديل)، 'description' (شرح مبسط جداً للحل بأسلوب تشجيعي)، 'type' (نوع الحل: 'splitting' أو 'swapping' أو 'gap'). لا تكتب أي مقدمات أو نصوص خارج كود الـ JSON.";

        try {
            // إرسال طلب HTTP آمن وسريع لسيرفرات OpenAI باستخدام نموذج gpt-4o-mini فائق السرعة والتوفير المادي
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt . " يجب أن تجيب دائماً بصيغة JSON صالحة ومطابقة للهيكل المطلوب تماماً وبطريقة احترافية باللغة العربية."],
                    ['role' => 'user', 'content' => $userPrompt]
                ],
                'response_format' => ['type' => 'json_object'], // إجبار الموديل على الرد بصيغة JSON
                'temperature' => 0.7
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $content = $result['choices'][0]['message']['content'] ?? '{}';
                $decoded = json_decode($content, true);

                // استخراج الاقتراحات الثلاثة المنسقة
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
                'message' => 'حدث خطأ أثناء الاتصال بالذكاء الاصطناعي. يرجى التحقق من صلاحية مفتاح الـ API.',
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
}