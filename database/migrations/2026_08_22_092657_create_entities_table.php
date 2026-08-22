<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة لإنشاء جدول الجهات المعنية.
     */
    public function up(): void
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            // ربط الجهة بالمستخدم من جدول المستخدمين (تُحذف الجهات تلقائياً إذا حُذف حساب المستخدم)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade'); 
            
            $table->string('title'); // اسم الجهة أو الإدارة أو المدرس (مثل: إدارة التسويق، أستاذ الكيمياء)
            $table->string('type'); // نوع الجهة (مثل: department, teacher, client, academy) لسهولة الفلترة
            $table->timestamps();
        });
    }

    /**
     * إلغاء الجدول في حال التراجع.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};