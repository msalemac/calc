<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة لإنشاء جدول إعدادات النظام الديناميكية.
     */
    public function up(): void
    {
        Schema::create('system_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // اسم الإعداد الفريد (مثال: openai_api_key)
            $table->text('value')->nullable(); // القيمة المخزنة (مثال: sk-proj-...)
            $table->string('group')->default('general'); // لتصنيف الإعدادات في لوحة التحكم (ai, security, general)
            $table->string('display_name'); // المسمى العربي الذي يظهر للأدمن في لوحة التحكم (مثال: مفتاح الـ API لـ OpenAI)
            $table->timestamps();
        });
    }

    /**
     * تراجع عن التعديل.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_configs');
    }
};