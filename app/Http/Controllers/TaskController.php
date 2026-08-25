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
     * حفظ المهمة الجديدة في قاعدة البيانات MySQL.
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

        $tomorrow = Carbon::parse($task->due_date)->addDay();

        $tomorrowTasks = Task::where('user_id', $user->id)
                             ->whereDate('due_date', $tomorrow)
                             ->where('status', 'pending')
                             ->get();

        $totalDuration = $tomorrowTasks->sum('estimated_duration');

        if ($totalDuration <= 240) {
            $task->postpone_count += 1;
            $task->due_date = $tomorrow;
            
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

        $systemPrompt = $user->role->system_prompt ?? 'أنت مساعد إنتاجية شخصي ذكي.';
        $routines = $user->routines; 

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

        $task->postpone_count += 1;
        $task->due_date = $request->tomorrow_date;
        $task->status = 'postponed';

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

    /**
     * استقبال الملف الصوتي المرفوع، تحويله لنص، واستخلاص بيانات المهمة ديناميكياً (AI Voice-to-Task).
     */
    public function transcribeVoice(Request $request, AIService $aiService)
    {
        // التحقق من أن الملف الصوتي مرفوع وبحجم لا يتجاوز 10 ميجابايت لسرعة المعالجة
        $request->validate([
            'audio' => 'required|file|max:10240', 
        ]);

        $user = Auth::user();
        $audioFile = $request->file('audio');

        // 1. جلب المسار المؤقت للملف الصوتي على السيرفر لإرساله لـ Whisper
        $tempPath = $audioFile->getPathname();

        // 2. معالجة وترجمة الملف الصوتي عبر Whisper API
        $transcription = $aiService->transcribeAudio($tempPath);

        if (!$transcription['success']) {
            return response()->json([
                'success' => false,
                'message' => $transcription['text']
            ], 400);
        }

        $text = $transcription['text'];

        // 3. استخلاص بيانات المهمة المنظمة (JSON) من النص المترجم عبر نموذج GPT-4o-mini المخصص للدور
        $extractedTask = $aiService->extractTaskFromText($text, $user->role->role_name);

        if (!$extractedTask['success']) {
            return response()->json([
                'success' => false,
                'message' => 'فشل الذكاء الاصطناعي في هيكلة واستخلاص بيانات المهمة من النص المترجم.'
            ], 422);
        }

        // إرجاع البيانات المهيكلة بالكامل للواجهة الأمامية لملء الحقول تلقائياً أمام المستخدم!
        return response()->json($extractedTask);
    }
}