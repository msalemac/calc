<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة لإنشاء جدول التصنيفات الملونة.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            // ربط التصنيف بالمستخدم (تُحذف التصنيفات تلقائياً في حال حذف حساب المستخدم)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade'); 
            
            $table->string('title'); // اسم التصنيف (مثال: مهام منزلية، مراجعة هامة)
            $table->string('color_code')->default('#3B82F6'); // كود اللون السداسي (Hex Code) لتلوين الواجهات، افتراضياً هو اللون الأزرق الهادئ
            $table->timestamps();
        });
    }

    /**
     * إلغاء الجدول في حال التراجع.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};