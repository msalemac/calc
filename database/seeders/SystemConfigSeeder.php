<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemConfigSeeder extends Seeder
{
    /**
     * تشغيل تغذية قاعدة البيانات بالإعدادات الافتراضية للـ APIs والأمان.
     */
    public function run(): void
    {
        DB::table('system_configs')->insert([
            [
                'key' => 'openai_api_key',
                'value' => null, // يُترك فارغاً ليقوم الأدمن بإدخاله وحفظه بأمان من لوحة التحكم لاحقاً
                'group' => 'ai',
                'display_name' => 'مفتاح الـ API الخاص بـ OpenAI (GPT-4)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'whisper_api_key',
                'value' => null, // يُترك فارغاً ليقوم الأدمن بإدخاله من لوحة التحكم
                'group' => 'ai',
                'display_name' => 'مفتاح الـ API الخاص بـ Whisper (تحويل الصوت لمهام)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'two_factor_status',
                'value' => 'disabled', // القيمة الافتراضية معطلة لحين تفعيلها من الأدمن ('enabled' أو 'disabled')
                'group' => 'security',
                'display_name' => 'حالة المصادقة الثنائية (2FA)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'two_factor_api_key',
                'value' => null,
                'group' => 'security',
                'display_name' => 'مفتاح الـ API الخاص ببوابة رسائل المصادقة الثنائية (2FA)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}