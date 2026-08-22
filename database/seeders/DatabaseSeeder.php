<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * تشغيل تغذية قاعدة البيانات الرئيسية للمشروع.
     */
    public function run(): void
    {
        // استدعاء مغذي الأدوار لملء الجدول تلقائياً بالأدوار والـ Prompts والنقاط
        $this->call([
            RoleSeeder::class,
        ]);
    }
}