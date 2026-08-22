<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة لإنشاء جدول المهام الرئيسي.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            
            // ربط المهمة بالمستخدم (تُحذف المهام تلقائياً عند حذف حساب المستخدم)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade'); 
            
            // ربط المهمة بالتصنيف (إذا حُذف التصنيف، تظل المهمة سليمة بدون تصنيف)
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('categories')
                  ->onDelete('set null'); 

            // ربط المهمة بالجهة المعنية/الإدارة (إذا حُذفت الجهة، تظل المهمة سليمة بدون جهة)
            $table->foreignId('entity_id')
                  ->nullable()
                  ->constrained('entities')
                  ->onDelete('set null'); 

            $table->string('title'); // عنوان المهمة الرئيسي
            $table->text('description')->nullable(); // تفاصيل المهمة الإضافية
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium'); // مستوى الأهمية
            $table->integer('estimated_duration')->default(30); // الوقت المتوقع بالدقائق لإنجاز المهمة (الافتراضي 30 دقيقة)
            $table->enum('status', ['pending', 'completed', 'postponed'])->default('pending'); // حالة المهمة الحالية
            $table->integer('postpone_count')->default(0); // عداد مرات التأجيل (هام جداً لتنبيه منقذ التسويف)
            $table->dateTime('due_date'); // الموعد النهائي المحدد للتسليم أو الإنجاز
            
            // نظام التذكيرات المخصص والمتكرر
            $table->enum('reminder_interval', ['once', 'hourly', 'daily', 'weekly'])->default('once');
            $table->timestamp('last_reminded_at')->nullable(); // لتسجيل آخر وقت تم إرسال تذكير فيه لعدم تكرار الإزعاج

            // حقل البيانات الديناميكية المخصصة حسب دور المستخدم بصيغة JSON
            $table->json('custom_fields')->nullable(); 

            // حقل المزامنة لمتابعة وضعية العمل بدون إنترنت (Offline Mode)
            $table->boolean('is_synced')->default(true); 

            $table->timestamps();
        });
    }

    /**
     * إلغاء الجدول في حال التراجع.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};