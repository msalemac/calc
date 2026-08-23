<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use App\Models\SystemConfig;

class AppServiceProvider extends ServiceProvider
{
    /**
     * تسجيل أي خدمات تابعة للمشروع.
     */
    public function register(): void
    {
        //
    }

    /**
     * تشغيل وتحميل الخدمات تلقائياً عند بداية كل طلب للموقع.
     */
    public function boot(): void
    {
        // تحقق احترازي هام جداً: للتأكد من أن جدول الإعدادات موجود فعلياً في قاعدة البيانات MySQL 
        // لتفادي أي انهيارات برمجية أثناء عمليات التثبيت الأولية أو الـ migrations
        if (Schema::hasTable('system_configs')) {
            
            // جلب كافة الإعدادات ومفاتيح الـ APIs من قاعدة البيانات على شكل (مفتاح وقيمة)
            $configs = SystemConfig::pluck('value', 'key')->all();

            // 1. تعيين مفتاح OpenAI ديناميكياً في إعدادات النظام في وقت التشغيل
            if (!empty($configs['openai_api_key'])) {
                Config::set('services.openai.key', $configs['openai_api_key']);
            }

            // 2. تعيين مفتاح Whisper الصوتي ديناميكياً في وقت التشغيل
            if (!empty($configs['whisper_api_key'])) {
                Config::set('services.whisper.key', $configs['whisper_api_key']);
            }

            // 3. تعيين إعدادات المصادقة الثنائية (2FA) والأمان ديناميكياً في وقت التشغيل
            if (!empty($configs['two_factor_status'])) {
                Config::set('services.two_factor.status', $configs['two_factor_status']);
            }
            if (!empty($configs['two_factor_api_key'])) {
                Config::set('services.two_factor.key', $configs['two_factor_api_key']);
            }
        }
    }
}