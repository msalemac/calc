<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة لتعديل جدول المستخدمين.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // إضافة معرف الدور برابط مفتاح خارجي آمن ومحمي
            $table->foreignId('role_id')
                  ->after('id')
                  ->nullable() // نجعله Nullable مؤقتاً لسهولة تنزيل البيانات الافتراضية
                  ->constrained('user_roles')
                  ->onDelete('restrict'); // حظر حذف الدور نهائياً إذا كان مرتبطاً بمستخدم نشط لمنع الانهيارات

            // إضافة نقاط استهلاك الـ API اليومية المتبقية للمستخدم
            $table->integer('credits_left')->default(20)->after('password');

            // إضافة ميزة الحذف الناعم لحفظ السجل التاريخي وحسابات المستخدمين من الحذف النهائي الخاطئ
            $table->softDeletes();
        });
    }

    /**
     * تراجع عن التعديلات في حال الحاجة.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // حذف المفتاح الخارجي أولاً لتجنب أخطاء القيود في SQL
            $table->dropForeign(['role_id']);
            // ثم حذف الأعمدة بالكامل
            $table->dropColumn(['role_id', 'credits_left', 'deleted_at']);
        });
    }
};