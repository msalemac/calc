<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة لإنشاء الجدول.
     */
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name')->unique(); // لتخزين مسمى الدور مثل (student, employee, manager, investor)
            $table->text('system_prompt')->nullable(); // لتخزين نبرة وتوجيه الذكاء الاصطناعي الخاص بهذا الدور
            $table->integer('daily_credits')->default(20); // نظام النقاط اليومية المتاحة (لحماية استهلاك الـ API)
            $table->json('custom_fields_schema')->nullable(); // ميزة الحقول المخصصة الإضافية التي تظهر لكل مستخدم
            $table->timestamps();
        });
    }

    /**
     * إلغاء الجدول في حال التراجع.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};