<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * تشغيل التغذية الشاملة لقاعدة بيانات المشروع.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SystemConfigSeeder::class, // استدعاء مغذي إعدادات النظام ومفاتيح الـ APIs
        ]);
    }
}