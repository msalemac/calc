<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * استقبال وحفظ المهمة الجديدة للمستخدم في قاعدة البيانات MySQL.
     */
    public function store(Request $request)
    {
        // 1. التحقق من صحة المدخلات الأساسية للمهمة
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'estimated_duration' => 'required|integer|min:5',
            'due_date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'entity_id' => 'nullable|exists:entities,id',
            'custom_fields' => 'nullable|array', // استقبال الحقول الديناميكية المخصصة للدور كمصفوفة
        ]);

        // 2. إنشاء وحفظ المهمة في قاعدة البيانات
        Task::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'entity_id' => $request->entity_id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'estimated_duration' => $request->estimated_duration,
            'due_date' => $request->due_date,
            'custom_fields' => $request->custom_fields, // سيقوم لارافيل بتحويلها لـ JSON وحفظها تلقائياً
            'is_synced' => true, // الحفظ تم أونلاين ومباشر
        ]);

        // 3. التوجيه مجدداً للوحة التحكم مع إشعار نجاح أخضر وأنيق
        return redirect()->route('dashboard')->with('success', 'تم حفظ مهمتك الذكية الجديدة بنجاح في جدولك اليومي!');
    }
}