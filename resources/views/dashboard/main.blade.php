<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مساعد الإنتاجية الذكي - لوحة التحكم</title>
    <!-- خط Cairo المريح للعين من Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <!-- تحميل الملحقات الأساسية للسرعة والجمال البصري والتفاعل السريع -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- مكتبة التقويم التفاعلي FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { cairo: ['Cairo', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: #334155 !important; }
        .fc-event { border: none !important; padding: 2px 6px; border-radius: 6px; }
    </style>
</head>
<body x-data="{ darkMode: true, taskModal: false, activeTab: 'tasks' }" :class="darkMode ? 'dark' : ''" class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen transition-colors duration-300">

    <!-- شريط الواجهة الجانبية والمحتوى -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- الشريط الجانبي (Sidebar) متجاوب تماماً -->
        <aside class="w-64 bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 flex flex-col justify-between p-6 hidden md:flex transition-colors duration-300">
            <div>
                <!-- الشعار المميز للمنصة -->
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white text-xl shadow-lg shadow-indigo-600/20">
                        <i class="ri-sparkling-fill"></i>
                    </div>
                    <span class="font-extrabold text-lg tracking-wide text-slate-900 dark:text-white">مساعدي الذكي</span>
                </div>

                <!-- قائمة الروابط والتنقل -->
                <nav class="space-y-2">
                    <button @click="activeTab = 'tasks'" :class="activeTab === 'tasks' ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/40'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all text-right">
                        <i class="ri-list-check-3 text-lg"></i> قائمة مهامي اليومية
                    </button>
                    <button @click="activeTab = 'calendar'" :class="activeTab === 'calendar' ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/40'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all text-right">
                        <i class="ri-calendar-todo-line text-lg"></i> تقويمي التفاعلي
                    </button>
                </nav>
            </div>

            <!-- معلومات حساب المستخدم ونقاط الاستهلاك في ذيل الشريط الجانبي -->
            <div class="border-t border-slate-200 dark:border-slate-800 pt-6">
                <div class="bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-4 relative group">
                    <!-- نظام التلميحات الفورية لنقاط الاستهلاك للـ AI -->
                    <div class="absolute top-2 left-2 group/tooltip">
                        <i class="ri-question-line text-slate-400 hover:text-indigo-500 cursor-pointer text-sm"></i>
                        <div class="absolute bottom-full left-0 mb-2 w-48 bg-slate-950 border border-slate-800 text-slate-300 text-xs rounded-xl p-3 shadow-xl opacity-0 pointer-events-none group-hover/tooltip:opacity-100 transition-opacity z-50">
                            رصيد استشاراتك المتبقي اليوم لاستدعاء الذكاء الاصطناعي لحل الازدحام أو تفكيك المهام. يتجدد تلقائياً كل 24 ساعة.
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mb-1">نقاط استهلاك الـ AI المتبقية:</p>
                    <div class="flex items-center gap-2">
                        <i class="ri-copper-coin-line text-lg text-amber-500"></i>
                        <span class="font-extrabold text-sm text-slate-900 dark:text-white">{{ $user->credits_left }} / {{ $role->daily_credits }} نقطة</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- منطقة العمل الرئيسية للمحتوى -->
        <main class="flex-1 flex flex-col overflow-hidden">
            
            <!-- شريط الرأس العلوي (Header) -->
            <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 py-4 flex items-center justify-between transition-colors duration-300">
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-lg text-slate-900 dark:text-white">أهلاً بك، {{ $user->name }}</h2>
                    <!-- شارة دور المستخدم الملونة -->
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 border border-indigo-500/20 capitalize">
                        @if($role->role_name === 'student') طالب علم @elseif($role->role_name === 'manager') مدير عام @else موظف محترف @endif
                    </span>
                </div>

                <div class="flex items-center gap-4">
                    <!-- زر تغيير الوضع الليلي والنهاري بسلاسة -->
                    <button @click="darkMode = !darkMode" class="w-10 h-10 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700/80 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-300 transition-all">
                        <i ::class="darkMode ? 'ri-sun-line' : 'ri-moon-line'" class="text-lg"></i>
                    </button>
                    <!-- زر إضافة مهمة جديدة المتوهج والزاهي بالألوان -->
                    <button @click="taskModal = true" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/15 flex items-center gap-2 transition-all duration-300">
                        <i class="ri-add-line text-lg"></i> إضافة مهمة ذكية
                    </button>
                </div>
            </header>

            <!-- محتوى لوحة التحكم القابل للتمرير -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                
                <!-- شريط النصائح اليومية التفاعلية -->
                <div class="bg-gradient-to-r from-indigo-500/10 via-purple-500/5 to-transparent border border-indigo-500/15 rounded-2xl p-4 flex items-center gap-3 relative group">
                    <div class="w-10 h-10 bg-indigo-500/10 rounded-xl flex items-center justify-center text-indigo-500 shrink-0">
                        <i class="ri-lightbulb-line text-xl"></i>
                    </div>
                    <div>
                        <span class="text-xs text-indigo-500 font-bold block mb-0.5">نصيحة اليوم للإنتاجية والنجاح:</span>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            تشير الإحصاءات إلى أن إنجاز أصعب مهمة تطلب تركيزاً ذهنياً في أول ساعتين من يومك يرفع كفاءة يومك الإجمالية بنسبة 40%!
                        </p>
                    </div>
                </div>

                <!-- تبويب قائمة المهام اليومية -->
                <div x-show="activeTab === 'tasks'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-extrabold text-md text-slate-900 dark:text-white">قائمة المهام والجدول اليومي</h3>
                        <span class="text-xs text-slate-500">إجمالي المهام المتبقية: {{ $tasks->where('status', 'pending')->count() }} مهمة</span>
                    </div>

                    <!-- عرض كروت المهام الفعلية -->
                    @if($tasks->isEmpty())
                        <div class="text-center py-16 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl">
                            <i class="ri-folder-open-line text-5xl text-slate-400 mb-3 block"></i>
                            <p class="text-slate-500 text-sm">لا توجد مهام مسجلة حالياً، ابدأ بإضافة مهمتك الأولى بذكاء!</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($tasks as $task)
                                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 relative group transition-all duration-300 hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-lg">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <!-- لون التصنيف المخصص إن وجد -->
                                            <span class="w-3 h-3 rounded-full" style="background-color: {{ $task->category->color_code ?? '#3B82F6' }}"></span>
                                            <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $task->title }}</h4>
                                        </div>
                                        <span class="text-xs font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 uppercase">{{ $task->priority }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 leading-relaxed mb-4">{{ $task->description ?? 'لا توجد تفاصيل إضافية مكتوبة.' }}</p>
                                    
                                    <div class="flex items-center justify-between text-xs text-slate-400 border-t border-slate-100 dark:border-slate-800/80 pt-3">
                                        <div class="flex items-center gap-3">
                                            <span><i class="ri-time-line mr-1"></i> {{ $task->estimated_duration }} دقيقة</span>
                                            <span><i class="ri-calendar-line mr-1"></i> {{ $task->due_date->format('Y/m/d h:i A') }}</span>
                                        </div>
                                        @if($task->entity)
                                            <span class="px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-500 text-[10px] font-bold">{{ $task->entity->title }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- تبويب التقويم التفاعلي الكامل -->
                <div x-show="activeTab === 'calendar'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 transition-colors duration-300" x-init="
                    setTimeout(() => {
                        var calendarEl = document.getElementById('calendar');
                        var calendar = new FullCalendar.Calendar(calendarEl, {
                            initialView: 'dayGridMonth',
                            locale: 'ar',
                            direction: 'rtl',
                            events: [
                                @foreach($tasks as $task)
                                {
                                    title: '{{ $task->title }}',
                                    start: '{{ $task->due_date->format('Y-m-d\TH:i:s') }}',
                                    backgroundColor: '{{ $task->category->color_code ?? '#3B82F6' }}'
                                },
                                @endforeach
                            ]
                        });
                        calendar.render();
                    }, 100);
                ">
                    <div id="calendar" class="text-sm"></div>
                </div>

            </div>
        </main>
    </div>

    <!-- نافذة إضافة مهمة جديدة المنبثقة (Task Modal) -->
    <div x-show="taskModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50" x-transition>
        <div @click.away="taskModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative text-right">
            
            <div class="flex items-center justify-between mb-6 border-b border-slate-100 dark:border-slate-800 pb-4">
                <h3 class="font-extrabold text-lg text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="ri-sparkling-line text-indigo-500"></i> إضافة مهمة ذكية جديدة
                </h3>
                <button @click="taskModal = false" class="text-slate-400 hover:text-slate-500"><i class="ri-close-line text-2xl"></i></button>
            </div>

            <!-- نموذج إضافة المهمة الفعلي -->
            <form action="#" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-slate-600 dark:text-slate-400 block mb-1">عنوان المهمة الأساسي</label>
                    <div class="relative">
                        <input type="text" name="title" required placeholder="مثال: مراجعة ميزانية الربع الأول" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-slate-900 dark:text-white">
                        <!-- أيقونة الإدخال الصوتي المتوهجة والزاهية للمهمة -->
                        <button type="button" class="absolute left-3 top-3 text-slate-400 hover:text-indigo-500 transition-colors">
                            <i class="ri-mic-line text-lg"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-600 dark:text-slate-400 block mb-1">تفاصيل ومذكرات إضافية (اختياري)</label>
                    <textarea name="description" rows="2" placeholder="اكتب مذكرات إضافية تساعدك على التذكر والإنجاز..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-slate-900 dark:text-white"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-600 dark:text-slate-400 block mb-1">الأهمية والأولوية</label>
                        <select name="priority" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-slate-900 dark:text-white">
                            <option value="low">منخفضة</option>
                            <option value="medium" selected>متوسطة</option>
                            <option value="high">عالية جداً</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-600 dark:text-slate-400 block mb-1">الوقت المتوقع (بالدقائق)</label>
                        <input type="number" name="estimated_duration" value="30" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-slate-900 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-600 dark:text-slate-400 block mb-1">تاريخ ووقت التسليم</label>
                        <input type="datetime-local" name="due_date" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-600 dark:text-slate-400 block mb-1">التصنيف الملون للمهمة</label>
                        <select name="category_id" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-slate-900 dark:text-white">
                            <option value="">بدون تصنيف</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- تفريغ الحقول المخصصة ديناميكياً بناءً على دور المستخدم الحالي من جدول الأدوار -->
                @if($role->custom_fields_schema)
                    @php
                        $customFields = json_decode($role->custom_fields_schema, true);
                    @endphp
                    @if(is_array($customFields))
                        <div class="border-t border-slate-100 dark:border-slate-800/80 pt-4 space-y-3">
                            <span class="text-xs text-indigo-500 font-bold block"><i class="ri-focus-3-line"></i> تفاصيل تهمك كـ @if($role->role_name === 'student') طالب علم @else مهني محترف @endif:</span>
                            
                            <div class="grid grid-cols-2 gap-4">
                                @foreach($customFields as $field)
                                    <div>
                                        <label class="text-xs font-bold text-slate-600 dark:text-slate-400 block mb-1">{{ $field['label'] }}</label>
                                        @if($field['type'] === 'text')
                                            <input type="text" name="custom_fields[{{ $field['name'] }}]" {{ $field['required'] ? 'required' : '' }} class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-slate-900 dark:text-white">
                                        @elseif($field['type'] === 'number')
                                            <input type="number" name="custom_fields[{{ $field['name'] }}]" {{ $field['required'] ? 'required' : '' }} class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-slate-900 dark:text-white">
                                        @elseif($field['type'] === 'boolean')
                                            <select name="custom_fields[{{ $field['name'] }}]" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-slate-900 dark:text-white">
                                                <option value="1">نعم</option>
                                                <option value="0" selected>لا</option>
                                            </select>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800/80">
                    <button type="button" @click="taskModal = false" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-sm hover:bg-slate-200 transition-all">إلغاء</button>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-indigo-600/15 transition-all">إضافة الآن <i class="ri-sparkling-fill mr-1"></i></button>
                </div>
            </form>

        </div>
    </div>

</body>
</html>