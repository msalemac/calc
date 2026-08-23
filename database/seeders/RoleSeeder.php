<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_roles')->insert([
            [
                'role_name' => 'student',
                'system_prompt' => 'أنت مساعد إنتاجية ودراسة ذكي مخصص للطلاب. تحدث بأسلوب تشجيعي، دافئ، ومحفز. ركز على تقسيم المذاكرة ومحاربة التسويف الأكاديمي.',
                'daily_credits' => 15,
                'activation_pin' => null, // متاح للجميع مجاناً
                'custom_fields_schema' => json_encode([
                    ['name' => 'subject', 'label' => 'المادة الدراسية', 'type' => 'text', 'required' => true],
                    ['name' => 'exam_prep', 'label' => 'هل المهمة تحضير لامتحان؟', 'type' => 'boolean', 'required' => false]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'employee',
                'system_prompt' => 'أنت مدرب إنتاجية متخصص للموظفين. ركز في نصائحك على التوازن بين العمل والحياة الشخصية، وتجنب الإرهاق المهني.',
                'daily_credits' => 30,
                'activation_pin' => null, // متاح للجميع مجاناً
                'custom_fields_schema' => json_encode([
                    ['name' => 'project_name', 'label' => 'اسم المشروع أو المبادرة التابعة لها', 'type' => 'text', 'required' => false],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'manager',
                'system_prompt' => 'أنت مستشار إداري وتنظيمي ذكي للمدراء والمشرفين. تحدث بأسلوب عملي، مختصر، ومباشر للغاية. ركز على تفويض المهام وحل اختناقات العمل.',
                'daily_credits' => 50,
                'activation_pin' => '110099', // الرمز السري الحصري لحسابات المدراء!
                'custom_fields_schema' => json_encode([
                    ['name' => 'delegated_to', 'label' => 'الموظف المسؤول عن التنفيذ', 'type' => 'text', 'required' => false],
                    ['name' => 'budget', 'label' => 'الميزانية التقريبية المخصصة للمهمة ($)', 'type' => 'number', 'required' => false]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}