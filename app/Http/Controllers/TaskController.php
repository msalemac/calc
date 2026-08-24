<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Services\AIService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * استقبال وحفظ المهمة الجديدة للمستخدم في قاعدة البيانات MySQL.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'estimated_duration' => 'required|integer|min:5',
            'due_date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'entity_id' => 'nullable|exists:entities,id',
            'custom_fields' => 'nullable|array',
        ]);

        Task::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'entity_id' => $request->entity_id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'estimated_duration' => $request->estimated_duration,
            'due_date' => $request->due_date,
            'custom_fields' => $request->custom_fields,
            'is_synced' => true,
        ]);

        return redirect()->route('dashboard')->with('success', 'تم حفظ مهمتك الذكية الجديدة بنجاح في جدولك اليومي!');
    }

    /**
     * معالجة وتأجيل المهمة لليوم التالي وفحص الازدحام بالذكاء الاصطناعي.
     */
    public function postpone($id, AIService $aiService)
    {
        $user = Auth::user();
        $task = Task::where('user_id', $user->id)->findOrFail($id);

        // حساب تاريخ الغد (اليوم المستهدف للتأجيل)
        $tomorrow = Carbon::parse($task->due_date)->addDay();

        // 1. جلب مهام الغد المفتوحة للتحقق من ضغط العمل
        $tomorrowTasks = Task::where('user_id', $user->id)
                             ->whereDate('due_date', $tomorrow)
                             ->where('status', 'pending')
                             ->get();

        // حساب مجموع الدقائق لمهام الغد
        $totalDuration = $tomorrowTasks->sum('estimated_duration');

        // 2. التحقق: إذا كان يوم الغد فارغاً (مجموع المهام أقل من 4 ساعات / 240 دقيقة)
        if ($totalDuration <= 240) {
            
            // زيادة عداد التأجيل بمقدار 1
            $task->postpone_count += 1;
            $task->due_date = $tomorrow;
            
            // تفعيل منقذ التسويف تلقائياً إذا وصل التأجيل لـ 3 مرات متتالية
            if ($task->postpone_count >= 3) {
                $task->description = "🚨 [منبه منقذ التسويف]: تم تأجيل هذه المهمة 3 مرات متتالية. نقترح العمل عليها فوراً وتفكيكها لخطوات بسيطة جداً لا تتجاوز 10 دقائق لتجاوز جمود البدايات!\n\n" . $task->description;
            }

            $task->status = 'postponed';
            $task->save();

            $msg = $task->postpone_count >= 3 
                ? 'تم تأجيل المهمة للغد، وتم تفعيل منقذ التسويف تلقائياً لتأجيلها المتكرر!' 
                : 'تم تأجيل المهمة تلقائياً للغد لعدم وجود ضغط عمل في جدول الغد!';

            return redirect()->route('dashboard')->with('success', $msg);
        }

        // 3. في حال وجود ضغط عمل (ازدحام) ◄ استدعاء عقل الذكاء الاصطناعي لحل التعارض
        $systemPrompt = $user->role->system_prompt ?? 'أنت مساعد إنتاجية شخصي ذكي.';
        $routines = $user->routines; // جلب الروتين الثابت كحظر وقت

        $aiResult = $aiService->resolveConflict(
            $user->role->role_name,
            $systemPrompt,
            $task->title,
            $tomorrowTasks,
            $routines
        );

        if ($aiResult['success']) {
            $suggestions = $aiResult['suggestions'];
            return view('dashboard.conflict', compact('task', 'suggestions', 'tomorrow'));
        }

        // في حال فشل الـ API لأي سبب، نقوم بالتأجيل التقليدي الاحتياطي لتجنب توقف التطبيق
        $task->due_date = $tomorrow;
        $task->save();

        return redirect()->route('dashboard')->with('success', 'تم تأجيل المهمة للغد (تأجيل تلقائي احتياطي).');
    }

    /**
     * تطبيق واعتماد الحل البديل المختار من الذكاء الاصطناعي على المهمة في قاعدة البيانات MySQL.
     */
    public function acceptSuggestion(Request $request, $id)
    {
        $user = Auth::user();
        $task = Task::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'suggestion_title' => 'required|string',
            'suggestion_desc' => 'required|string',
            'suggestion_type' => 'required|string',
            'tomorrow_date' => 'required|date',
        ]);

        // زيادة عداد التأجيل بمقدار 1 وحفظ التاريخ الجديد
        $task->postpone_count += 1;
        $task->due_date = $request->tomorrow_date;
        $task->status = 'postponed';

        // دمج وحفظ اقتراح الذكاء الاصطناعي المختار داخل وصف المهمة ليرى المستخدم خطته على الكارت فوراً!
        if ($request->suggestion_type !== 'manual') {
            $task->description = "✨ [تم تطبيق الحل الذكي: " . $request->suggestion_title . "]\n" . 
                                 "الخطة: " . $request->suggestion_desc . "\n\n" . 
                                 $task->description;
        }

        $task->save();

        $msg = $request->suggestion_type === 'manual' 
            ? 'تم تأجيل المهمة بنجاح وتجاوز نصيحة المساعد الذكي.' 
            : 'تم بنجاح تطبيق واعتماد خيار الجدولة البديلة الذكي المختار لجدول الغد!';

        return redirect()->route('dashboard')->with('success', $msg);
    }
}