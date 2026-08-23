<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة لإضافة حقل رمز التفعيل.
     */
    public function up(): void
    {
        Schema::table('user_roles', function (Blueprint $table) {
            // إضافة حقل الرمز السري بعد حقل النقاط اليومية ويكون قابلاً لأن يكون فارغاً للأدوار العامة
            $table->string('activation_pin')->nullable()->after('daily_credits');
        });
    }

    /**
     * تراجع عن التعديل.
     */
    public function down(): void
    {
        Schema::table('user_roles', function (Blueprint $table) {
            $table->dropColumn('activation_pin');
        });
    }
};