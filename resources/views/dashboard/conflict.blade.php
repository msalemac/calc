<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مساعد الجدولة الذكي - حل التعارض</title>
    <!-- خط Cairo المريح للعين من Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { cairo: ['Cairo', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-950 to-indigo-950 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-4xl w-full bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-2xl text-right">
        
        <!-- أيقونة التنبيه بالازدحام الدافئة والجميلة -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-amber-500/10 border border-amber-500/30 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-amber-500/10">
                <i class="ri-alert-line text-3xl text-amber-400"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-white mb-2">جدول يوم الغد مزدحم ومكتظ بالمهام!</h1>
            <p class="text-slate-400 text-sm max-w-lg mx-auto leading-relaxed">
                تأجيل مهمة <span class="text-indigo-400 font-bold">"{{ $task->title }}"</span> بالكامل للغد سيتسبب في ضغط عمل كبير قد يعوق إنتاجيتك. إليك 3 حلول بديلة ومخصصة صاغها مستشارك الذكي الآن لتنظيم وقتك بذكاء:
            </p>
        </div>

        <!-- شبكة الخيارات الذكية الثلاثة المولدة من الـ AI في نفس الثانية -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @foreach($suggestions as $suggestion)
                @php
                    // تعيين أيقونات وألوان مشبعة عصرية لكل نوع من أنواع الحلول الثلاثة المقترحة من الـ AI
                    $icon = 'ri-time-line';
                    $color = 'from-blue-500 to-cyan-500';
                    if ($suggestion['type'] === 'splitting') {
                        $icon = 'ri-scissors-cut-line';
                        $color = 'from-emerald-500 to-teal-500';
                    } elseif ($suggestion['type'] === 'swapping') {
                        $icon = 'ri-arrow-left-right-line';
                        $color = 'from-amber-500 to-orange-500';
                    }
                @endphp

                <!-- كرت الخيار البديل -->
                <div class="bg-slate-800/40 border border-slate-700/50 rounded-2xl p-6 flex flex-col justify-between transition-all duration-300 hover:scale-[1.03] hover:border-indigo-500/30 hover:bg-slate-800/60 shadow-lg">
                    <div>
                        <!-- الأيقونة البصرية للحل البديل -->
                        <div class="w-12 h-12 bg-gradient-to-br {{ $color }} rounded-xl flex items-center justify-center mb-4 text-white text-xl">
                            <i class="{{ $icon }}"></i>
                        </div>

                        <!-- عنوان الحل والشرح التوجيهي اللطيف -->
                        <h3 class="text-md font-bold text-white mb-2">{{ $suggestion['title'] }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed mb-6">{{ $suggestion['description'] }}</p>
                    </div>

                    <!-- نموذج قبول الحل الذكي واعتماده لتعديل المهمة تلقائياً في قاعدة البيانات -->
                    <form action="{{ route('tasks.accept-suggestion', $task->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="suggestion_title" value="{{ $suggestion['title'] }}">
                        <input type="hidden" name="suggestion_desc" value="{{ $suggestion['description'] }}">
                        <input type="hidden" name="suggestion_type" value="{{ $suggestion['type'] }}">
                        <input type="hidden" name="tomorrow_date" value="{{ $tomorrow }}">
                        
                        <button type="submit" class="w-full py-2.5 bg-indigo-600/10 hover:bg-indigo-600 text-indigo-400 hover:text-white text-xs font-bold rounded-xl transition-all duration-300">
                            اعتماد هذا الحل الذكي <i class="ri-check-line mr-1"></i>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <!-- خيار التأجيل على أي حال التقليدي في الأسفل -->
        <div class="text-center border-t border-slate-800 pt-6 flex items-center justify-between">
            <span class="text-xs text-slate-400">إذا كنت تفضل تجاهل نصائح المساعد الذكي وجدولة العمل بنفسك:</span>
            
            <form action="{{ route('tasks.accept-suggestion', $task->id) }}" method="POST">
                @csrf
                <input type="hidden" name="suggestion_title" value="تأجيل اعتيادي">
                <input type="hidden" name="suggestion_desc" value="تأجيل يدوي بدون تعديلات ذكية.">
                <input type="hidden" name="suggestion_type" value="manual">
                <input type="hidden" name="tomorrow_date" value="{{ $tomorrow }}">
                
                <button type="submit" class="text-xs text-slate-400 hover:text-red-400 font-bold transition-colors">
                    التأجيل على أي حال (تجاوز النصيحة) <i class="ri-arrow-left-line"></i>
                </button>
            </form>
        </div>
    </div>

</body>
</html>