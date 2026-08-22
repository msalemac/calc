<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة لإنشاء جدول الروتين اليومي.
     */
    public function up(): void
    {
        Schema::create('user_routines', function (Blueprint $table) {
            $table->id();
            // ربط الروتين بالمستخدم (يُحذف تلقائياً عند حذف حساب المستخدم)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade'); 
            
            $table->string('activity_name'); // اسم النشاط الثابت (مثال: نوم، غداء، عمل رسمي، دراسة ثابتة)
            $table->time('start_time'); // وقت بداية النشاط (مثال: 23:00:00)
            $table->time('end_time'); // وقت نهاية النشاط (مثال: 07:00:00)
            $table->timestamps();
        });
    }

    /**
     * إلغاء الجدول في حال التراجع.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_routines');
    }
};