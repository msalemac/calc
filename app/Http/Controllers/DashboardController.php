<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserRole;
use App\Models\Category;

class DashboardController extends Controller
{
    /**
     * عرض لوحة التحكم الرئيسية المخصصة بناءً على دور المستخدم وبياناته الحقيقية.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. التحقق الأمني: إذا لم يقم المستخدم باختيار دوره بعد، يتم توجيهه لواجهة الترحيب والتهيئة
        if (!$user->role_id) {
            return redirect()->route('dashboard.select-role');
        }

        // 2. جلب كافة البيانات المرتبطة بالمستخدم بشكل مجمع فائق السرعة لتقليل الضغط على السيرفر (Eager Loading)
        $role = $user->role;
        $tasks = $user->tasks()->with(['category', 'entity'])->orderBy('due_date', 'asc')->get();
        $categories = $user->categories;
        $entities = $user->entities;
        $routines = $user->routines;

        // 3. توجيه المستخدم لصفحة لوحة العمل الرئيسية وتمرير الحقول المخصصة له
        return view('dashboard.main', compact('user', 'role', 'tasks', 'categories', 'entities', 'routines'));
    }

    /**
     * عرض واجهة اختيار الدور للمدراء والطلاب والموظفين الجدد (Onboarding Screen).
     */
    public function showSelectRole()
    {
        $roles = UserRole::all();
        return view('dashboard.select-role', compact('roles'));
    }

    /**
     * معالجة وحفظ الدور المختار للمستخدم والتحقق من الرمز السري للأدوار الحساسة (مثل المدير).
     */
    public function storeRole(Request $request)
    {
        // التحقق من صحة المدخلات الأساسية
        $request->validate([
            'role_id' => 'required|exists:user_roles,id'
        ]);

        $user = Auth::user();
        $role = UserRole::findOrFail($request->role_id);

        // 1. التحقق الأمني من الرمز السري للأدوار الخاصة والمحمية
        if ($role->activation_pin) {
            $request->validate([
                'activation_pin' => 'required|string'
            ]);

            // مقارنة الرمز المدخل بالرمز السري المخزن في قاعدة البيانات لتأكيد الصلاحية
            if ($request->activation_pin !== $role->activation_pin) {
                return back()->withErrors([
                    'activation_pin' => 'رمز تفعيل الصلاحية المدخل غير صحيح! يرجى مراجعة الإدارة للحصول على الرمز.'
                ])->withInput();
            }
        }

        // 2. اعتماد وحفظ الدور وتخصيص رصيد استهلاك الـ API اليومي للمستخدم بناءً على دوره
        $user->role_id = $request->role_id;
        $user->credits_left = $role->daily_credits;
        $user->save();

        // 3. زرع وتهيئة تصنيفات ملونة تلقائية وذكية تناسب طبيعة الدور لتسهيل البدء الفوري
        $this->seedDefaultCategories($user, $role->role_name);

        // التوجيه النهائي للوحة التحكم مع رسالة ترحيبية
        return redirect()->route('dashboard')->with('success', 'تم تهيئة حسابك بنجاح! مرحباً بك في لوحة تحكمك الذكية.');
    }

    /**
     * زرع تصنيفات افتراضية ملونة ومخصصة حسب فئة المستخدم (طالب / أعمال) لتوفير وقت الإعداد.
     */
    private function seedDefaultCategories($user, $roleName)
    {
        $defaults = [];

        // تخصيص الألوان والتصنيفات للطلاب
        if ($roleName === 'student') {
            $defaults = [
                ['title' => 'واجبات دراسية ومشاريع', 'color_code' => '#3B82F6'], // لون أزرق زاهٍ
                ['title' => 'تحضير ومراجعة اختبارات', 'color_code' => '#EF4444'], // لون أحمر منبه
                ['title' => 'مهام روتينية وتطوير شخصي', 'color_code' => '#10B981'], // لون أخضر مريح
            ];
        } 
        // تخصيص الألوان والتصنيفات للمدراء والموظفين والمستثمرين
        elseif ($roleName === 'manager' || $roleName === 'employee' || $roleName === 'investor') {
            $defaults = [
                ['title' => 'اجتماعات ولقاءات هامة', 'color_code' => '#F59E0B'], // لون برتقالي دافئ
                ['title' => 'مهام عمل ومتابعة يومية', 'color_code' => '#3B82F6'], // لون أزرق زاهٍ
                ['title' => 'مراجعة وتدقيق تقارير مالية', 'color_code' => '#8B5CF6'], // لون بنفسجي فخم
            ];
        }

        // إدخال التصنيفات الافتراضية في قاعدة البيانات MySQL
        foreach ($defaults as $cat) {
            Category::create([
                'user_id' => $user->id,
                'title' => $cat['title'],
                'color_code' => $cat['color_code']
            ]);
        }
    }
}