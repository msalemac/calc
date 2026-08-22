<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * تشغيل تغذية قاعدة البيانات بالأدوار الافتراضية.
     */
    public function run(): void
    {
        DB::table('user_roles')->insert([
            [
                'role_name' => 'student',
                'system_prompt' => 'أنت مساعد إنتاجية ودراسة ذكي مخصص للطلاب. تحدث بأسلوب تشجيعي، دافئ، ومحفز. ركز على تقسيم المذاكرة، إدارة وقت التحضير للامتحانات، ومحاربة التسويف الأكاديمي والكسر المعنوي.',
                'daily_credits' => 15, // حد الاستهلاك اليومي للطلاب لحماية التكلفة المادية
                'custom_fields_schema' => json_encode([
                    ['name' => 'subject', 'label' => 'المادة الدراسية', 'type' => 'text', 'required' => true],
                    ['name' => 'exam_prep', 'label' => 'هل المهمة تحضير لامتحان؟', 'type' => 'boolean', 'required' => false]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'employee',
                'system_prompt' => 'أنت مدرب إنتاجية متخصص للموظفين والعمال. ركز في نصائحك على التوازن بين العمل والحياة الشخصية، تجنب الإرهاق المهني (Burnout)، وترتيب أولويات المهام المهنية اليومية لزيادة كفاءتك.',
                'daily_credits' => 30, // حد الاستهلاك اليومي للموظفين
                'custom_fields_schema' => json_encode([
                    ['name' => 'project_name', 'label' => 'اسم المشروع أو المبادرة التابعة لها', 'type' => 'text', 'required' => false],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'manager',
                'system_prompt' => 'أنت مستشار إداري وتنظيمي ذكي للمدراء والمشرفين. تحدث بأسلوب عملي، مختصر، ومباشر للغاية. ركز على إدارة الوقت، تفويض المهام للإدارات، وحل اختناقات العمل الإداري.',
                'daily_credits' => 50, // حد الاستهلاك اليومي للمدراء
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