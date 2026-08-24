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
    
    <!-- تحميل مكتبة اختيار التاريخ السهلة Flatpickr وتنسيقها الأنيق -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>

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
        
        /* التنسيق الافتراضي للتقويم في الوضع العادي */
        .fc-theme-standard td, .fc-theme-standard th { border-color: #e2e8f0 !important; }
        .fc-event { border: none !important; padding: 2px 6px; border-radius: 6px; cursor: pointer; }
        .fc-daygrid-day-number, .fc-col-header-cell-cushion { text-decoration: none !important; color: #1e293b !important; }

        /* تخصيص مظهر Flatpickr ليتناسب تماماً مع ألواننا الداكنة والزاهية */
        .flatpickr-calendar { background: #0f172a !important; border: 1px solid #1e293b !important; border-radius: 16px !important; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.5) !important; }
        .flatpickr-day.selected { background: #4f46e5 !important; border-color: #4f46e5 !important; }

        /* ==================================================== */
        /* تخصيص مظهر التقويم الاحترافي بالكامل في الوضع الليلي (Dark Mode) */
        /* ==================================================== */
        .dark .fc {
            color: #f8fafc !important; /* لون النص الأساسي أبيض ثلجي */
        }
        .dark .fc-col-header-cell-cushion, 
        .dark .fc-daygrid-day-number,
        .dark .fc-daygrid-day-top {
            color: #cbd5e1 !important; /* أرقام الأيام وأسماء الأسبوع بلون فضي ناصع مريح للعين */
            text-decoration: none !important;
        }
        .dark .fc-button {
            background-color: #1e293b !important; /* أزرار التقويم بلون كحلي داكن متناسق مع بطاقاتنا */
            border-color: #334155 !important;
            color: #f8fafc !important;
            text-transform: capitalize;
        }
        .dark .fc-button-active {
            background-color: #4f46e5 !important; /* زر اليوم الحالي النشط باللون البنفسجي الزاهي */
            border-color: #4f46e5 !important;
        }
        .dark .fc-theme-standard td, 
        .dark .fc-theme-standard th {
            border-color: #1e293b !important; /* تفتيح وتبسيط حدود شبكة التقويم بلون هادئ */
        }
        .dark .fc-day-today {
            background-color: rgba(79, 70, 229, 0.15) !important; /* تظليل ناعم لليوم الحالي باللون البنفسجي */
        }
        .dark .fc-daygrid-day:hover {
            background-color: rgba(255, 255, 255, 0.02) !important; /* تأثير تمرير خفيف جداً فوق الأيام */
        }
    </style>
</head>
<!-- دمج متغير detailModal لفتح وإغلاق نافذة تفاصيل كروت التقويم -->
<body x-data="{ darkMode: true, taskModal: false, profileModal: false, detailModal: false, activeTab: 'tasks', selectedTask: {} }" :class="darkMode ? 'dark' : ''" class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen transition-colors duration-300">

    <div class="flex h-screen overflow-hidden">
        
        <!-- الشريط الجانبي (Sidebar) متجاوب تماماً -->
        <aside class="w-64 bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 flex flex-col justify-between p-6 hidden md:flex transition-colors duration-300">
            <div>
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white text-xl shadow-lg shadow-indigo-600/20">
                        <i class="ri-sparkling-fill"></i>
                    </div>
                    <span class="font-extrabold text-lg tracking-wide text-slate-900 dark:text-white">مساعدي الذكي</span>
                </div>

                <nav class="space-y-2">
                    <button @click="activeTab = 'tasks'" :class="activeTab === 'tasks' ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/40'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all text-right">
                        <i class="ri-list-check-3 text-lg"></i> قائمة مهامي اليومية
                    </button>
                    <button @click="activeTab = 'calendar'; setTimeout(() => { if(window.calendar) { window.calendar.updateSize(); } }, 150);" :class="activeTab === 'calendar' ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/40'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all text-right">
                        <i class="ri-calendar-todo-line text-lg"></i> تقويمي التفاعلي
                    </button>
                </nav>
            </div>

            <div class="border-t border-slate-200 dark:border-slate-800 pt-6">
                <div class="bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-4 relative group">
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

        <!-- منطقة العمل الرئيسية -->
        <main class="flex-1 flex flex-col overflow-hidden">
            
            <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 py-4 flex items-center justify-between transition-colors duration-300">
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-lg text-slate-900 dark:text-white">أهلاً بك، {{ $user->name }}</h2>
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 border border-indigo-500/20 capitalize">
                        @if($role->role_name === 'student') طالب علم @elseif($role->role_name === 'manager') مدير عام @else موظف محترف @endif
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <button @click="profileModal = true" class="w-10 h-10 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700/80 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-300 transition-all" title="إعدادات الحساب وكلمة المرور">
                        <i class="ri-user-settings-line text-lg"></i>
                    </button>
                    <button @click="darkMode = !darkMode" class="w-10 h-10 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700/80 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-300 transition-all" title="تغيير المظهر">
                        <i :class="darkMode ? 'ri-sun-line' : 'ri-moon-line'" class="text-lg"></i>
                    </button>
                    <button @click="taskModal = true" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/15 flex items-center gap-2 transition-all duration-300">
                        <i class="ri-add-line text-lg"></i> إضافة مهمة ذكية
                    </button>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                
                @if(session('success'))
                    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 text-xs rounded-xl p-4 flex items-center gap-2">
                        <i class="ri-checkbox-circle-line text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

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

                <!-- تبويب المهام اليومية -->
                <div x-show="activeTab === 'tasks'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-extrabold text-md text-slate-900 dark:text-white">قائمة المهام والجدول اليومي</h3>
                        <span class="text-xs text-slate-500">إجمالي المهام المتبقية: {{ $tasks->where('status', 'pending')->count() }} مهمة</span>
                    </div>

                    @if($tasks->isEmpty())
                        <div class="text-center py-16 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl">
                            <i class="ri-folder-open-line text-5xl text-slate-400 mb-3 block"></i>
                            <p class="text-slate-500 text-sm">لا توجد مهام مسجلة حالياً، ابدأ بإضافة مهمتك الأولى بذكاء!</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($tasks as $task)
                                <div id="task-card-{{ $task->id }}" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 relative group transition-all duration-300 hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-lg">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-3 h-3 rounded-full" style="background-color: {{ $task->category->color_code ?? '#3B82F6' }}"></span>
                                            <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $task->title }}</h4>
                                        </div>
                                        <span class="text-xs font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 uppercase">{{ $task->priority }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 leading-relaxed mb-4" style="white-space: pre-line;">{{ $task->description ?? 'لا توجد تفاصيل إضافية مكتوبة.' }}</p>
                                    
                                    <div class="flex items-center justify-between text-xs text-slate-400 border-t border-slate-100 dark:border-slate-800/80 pt-3">
                                        <div class="flex items-center gap-3">
                                            <span><i class="ri-time-line mr-1"></i> {{ $task->estimated_duration }} دقيقة</span>
                                            <span><i class="ri-calendar-line mr-1"></i> {{ $task->due_date->format('Y/m/d h:i A') }}</span>
                                        </div>
                                        
                                        <div class="flex items-center gap-1.5 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <!-- زر التأجيل الذكي (سهم أصفر ملتف للأمام) لليوم التالي -->
                                            <a href="{{ route('tasks.postpone', $task->id) }}" class="w-7 h-7 bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-white rounded-lg flex items-center justify-center transition-all" title="تأجيل المهمة للغد">
                                                <i class="ri-arrow-go-forward-line text-xs"></i>
                                            </a>
                                            <!-- زر التعديل (Edit) -->
                                            <button onclick="editTask({{ $task->id }})" class="w-7 h-7 bg-blue-500/10 hover:bg-blue-500 text-blue-500 hover:text-white rounded-lg flex items-center justify-center transition-all" title="تعديل المهمة">
                                                <i class="ri-edit-line text-xs"></i>
                                            </button>
                                            <!-- زر الطباعة / تصدير PDF للمهمة -->
                                            <button onclick="printTask('{{ $task->title }}', '{{ $task->description }}')" class="w-7 h-7 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-500 hover:text-white rounded-lg flex items-center justify-center transition-all" title="طباعة / تصدير PDF">
                                                <i class="ri-printer-line text-xs"></i>
                                            </button>
                                            <!-- زر مشاركة المهمة (Share API) لفتح الـ WhatsApp أو غيره -->
                                            <button onclick="shareTask('{{ $task->title }}', '{{ $task->description }}')" class="w-7 h-7 bg-purple-500/10 hover:bg-purple-500 text-purple-500 hover:text-white rounded-lg flex items-center justify-center transition-all" title="مشاركة المهمة">
                                                <i class="ri-share-line text-xs"></i>
                                            </button>
                                            <!-- زر الحذف السريع والآمن (Delete) -->
                                            <button onclick="deleteTask({{ $task->id }})" class="w-7 h-7 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white rounded-lg flex items-center justify-center transition-all" title="حذف المهمة">
                                                <i class="ri-delete-bin-line text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- تبويب التقويم مضاف إليه حدث الضغط التفاعلي لعرض التفاصيل -->
                <div x-show="activeTab === 'calendar'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 transition-colors duration-300" x-init="
                    setTimeout(() => {
                        var calendarEl = document.getElementById('calendar');
                        var calendar = new FullCalendar.Calendar(calendarEl, {
                            initialView: 'dayGridMonth',
                            locale: 'ar',
                            direction: 'rtl',
                            contentHeight: 'auto',
                            aspectRatio: 1.35,
                            events: [
                                @foreach($tasks as $task)
                                {
                                    id: '{{ $task->id }}',
                                    title: '{{ $task->title }}',
                                    start: '{{ $task->due_date->format('Y-m-d\TH:i:s') }}',
                                    backgroundColor: '{{ $task->category->color_code ?? '#3B82F6' }}',
                                    // تمرير الخصائص الممتدة للذكاء الاصطناعي والواجهة لقراءتها عند الضغط
                                    extendedProps: {
                                        description: '{{ addslashes(str_replace(["\r", "\n"], ' ', $task->description)) }}',
                                        priority: '{{ $task->priority }}',
                                        due_date: '{{ $task->due_date->format('Y/m/d h:i A') }}'
                                    }
                                },
                                @endforeach
                            ],
                            // برمجة تفاعلية الضغط على الحدث لفتح نافذة التفاصيل الفورية لتقويم الأدمن والطلاب
                            eventClick: function(info) {
                                selectedTask = {
                                    id: info.event.id,
                                    title: info.event.title,
                                    description: info.event.extendedProps.description,
                                    priority: info.event.extendedProps.priority,
                                    due_date: info.event.extendedProps.due_date
                                };
                                detailModal = true;
                            }
                        });
                        calendar.render();
                        window.calendar = calendar;
                    }, 100);
                ">
                    <div id="calendar" class="text-sm"></div>
                </div>

            </div>
        </main>
    </div>

    <!-- نافذة إضافة مهمة جديدة المنبثقة -->
    <div x-show="taskModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50" x-transition>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative text-right">
            
            <div class="flex items-center justify-between mb-6 border-b border-slate-100 dark:border-slate-800 pb-4">
                <h3 class="font-extrabold text-lg text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="ri-sparkling-line text-indigo-500"></i> إضافة مهمة ذكية جديدة
                </h3>
                <button @click="taskModal = false" class="text-slate-400 hover:text-slate-500"><i class="ri-close-line text-2xl"></i></button>
            </div>

            <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-slate-600 dark:text-slate-400 block mb-1">عنوان المهمة الأساسي</label>
                    <div class="relative">
                        <input type="text" name="title" required placeholder="مثال: مراجعة ميزانية الربع الأول" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-slate-900 dark:text-white">
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
                        <div class="relative">
                            <input type="text" id="due_date_input" name="due_date" required placeholder="اختر الموعد" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-slate-900 dark:text-white cursor-pointer">
                            <i class="ri-calendar-event-line absolute left-3 top-3 text-slate-400 pointer-events-none"></i>
                        </div>
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

    <!-- نافذة إعدادات الملف الشخصي وتغيير الباسورد المنبثقة التفاعلية الفخمة -->
    <div x-show="profileModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50" x-transition>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl relative text-right">
            
            <div class="flex items-center justify-between mb-6 border-b border-slate-100 dark:border-slate-800 pb-4">
                <h3 class="font-extrabold text-lg text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="ri-user-settings-line text-indigo-500"></i> إعدادات الحساب والأمان
                </h3>
                <button @click="profileModal = false" class="text-slate-400 hover:text-slate-500"><i class="ri-close-line text-2xl"></i></button>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-slate-600 dark:text-slate-400 block mb-1">الاسم الكامل</label>
                    <div class="relative">
                        <input type="text" name="name" value="{{ $user->name }}" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-slate-900 dark:text-white pr-10">
                        <i class="ri-user-line absolute right-3 top-3.5 text-slate-400 text-lg"></i>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-600 dark:text-slate-400 block mb-1">البريد الإلكتروني</label>
                    <div class="relative">
                        <input type="email" name="email" value="{{ $user->email }}" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-slate-900 dark:text-white pr-10">
                        <i class="ri-mail-line absolute right-3 top-3.5 text-slate-400 text-lg"></i>
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-800/80 pt-4">
                    <span class="text-xs text-indigo-500 font-bold block mb-3"><i class="ri-lock-password-line"></i> تغيير كلمة المرور (اكتبها فقط في حال الرغبة في التغيير):</span>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-slate-600 dark:text-slate-400 block mb-1">كلمة المرور الجديدة</label>
                            <div class="relative">
                                <input type="password" name="password" placeholder="••••••••" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-slate-900 dark:text-white pr-10">
                                <i class="ri-lock-line absolute right-3 top-3.5 text-slate-400 text-lg"></i>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-600 dark:text-slate-400 block mb-1">تأكيد كلمة المرور الجديدة</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" placeholder="••••••••" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-slate-900 dark:text-white pr-10">
                                <i class="ri-lock-check-line absolute right-3 top-3.5 text-slate-400 text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3 pt-4 border-t border-slate-100 dark:border-slate-800/80">
                    <button type="button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="px-4 py-2.5 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white font-bold rounded-xl text-xs flex items-center gap-1.5 transition-all">
                        <i class="ri-logout-box-line"></i> تسجيل الخروج
                    </button>
                    
                    <div class="flex items-center gap-2">
                        <button type="button" @click="profileModal = false" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-200 transition-all">إلغاء</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow-lg shadow-indigo-600/15 transition-all">حفظ التغييرات <i class="ri-save-line mr-1"></i></button>
                    </div>
                </div>
            </form>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>

        </div>
    </div>

    <!-- نافذة منبثقة تفاعلية جديدة لعرض تفاصيل المهمة عند الضغط عليها في التقويم -->
    <div x-show="detailModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50" x-transition>
        <div @click.away="detailModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl relative text-right">
            
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="font-extrabold text-md text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="ri-information-line text-indigo-500"></i> تفاصيل المهمة المجدولة
                </h3>
                <button @click="detailModal = false" class="text-slate-400 hover:text-slate-500"><i class="ri-close-line text-2xl"></i></button>
            </div>

            <!-- عرض تفاصيل المهمة ديناميكياً بناءً على ما نقر عليه المستخدم في التقويم -->
            <div class="space-y-4 mb-6">
                <div>
                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-500" x-text="selectedTask.priority"></span>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mt-2" x-text="selectedTask.title"></h2>
                </div>
                
                <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-100 dark:border-slate-800/80">
                    <p class="text-xs text-slate-500 leading-relaxed" style="white-space: pre-line;" x-text="selectedTask.description ? selectedTask.description : 'لا توجد تفاصيل إضافية مكتوبة لهذه المهمة.'"></p>
                </div>

                <div class="flex items-center gap-4 text-xs text-slate-400">
                    <span><i class="ri-calendar-line mr-1 text-indigo-500"></i> تاريخ الاستحقاق: <span class="text-slate-900 dark:text-white font-bold" x-text="selectedTask.due_date"></span></span>
                </div>
            </div>

            <!-- الإجراءات المتاحة للمهمة من داخل التقويم المطور -->
            <div class="flex items-center justify-between gap-3 pt-4 border-t border-slate-100 dark:border-slate-800/80">
                <!-- زر الحذف المباشر من التقويم -->
                <button @click="detailModal = false; deleteTask(selectedTask.id);" class="px-4 py-2 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white text-xs font-bold rounded-xl flex items-center gap-1.5 transition-all">
                    <i class="ri-delete-bin-line"></i> حذف المهمة
                </button>
                
                <div class="flex items-center gap-2">
                    <button @click="detailModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-200 transition-all">إغلاق</button>
                    <!-- زر التعديل المباشر من التقويم -->
                    <button @click="detailModal = false; editTask(selectedTask.id);" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow-lg shadow-indigo-600/15 transition-all">تعديل التفاصيل <i class="ri-edit-line mr-1"></i></button>
                </div>
            </div>

        </div>
    </div>

    <!-- كود الجافا سكريبت لتفعيل Flatpickr وأزرار المتابعة والطباعة والمشاركة الذكية -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#due_date_input", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                locale: "ar",
                theme: "dark",
                minDate: "today",
                time_24hr: false,
                static: true
            });
        });

        function printTask(title, description) {
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html lang="ar" dir="rtl"><head><title>طباعة مهمة</title>');
            printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700;400&display=swap" rel="stylesheet">');
            printWindow.document.write('<style>body{font-family:\'Cairo\',sans-serif;padding:40px;color:#333;line-height:1.6;}.card{border:2px solid #333;padding:24px;border-radius:12px;max-width:600px;margin:auto;}h1{margin-top:0;color:#1e3a8a;border-bottom:1px solid #ccc;padding-bottom:12px;}</style></head><body>');
            printWindow.document.write('<div class="card"><h1>' + title + '</h1><p>' + (description ? description : 'لا توجد تفاصيل إضافية مكتوبة لهذه المهمة.') + '</p></div>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            setTimeout(function() {
                printWindow.print();
            }, 500);
        }

        function shareTask(title, description) {
            var text = 'المهمة الذكية: ' + title + '\nالتفاصيل: ' + (description ? description : 'لا توجد تفاصيل إضافية.');
            
            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: text,
                    url: window.location.href
                }).catch(console.error);
            } else {
                navigator.clipboard.writeText(text).then(function() {
                    alert('تم نسخ تفاصيل المهمة إلى الحافظة تلقائياً! يمكنك لصقها الآن في الواتساب أو أي تطبيق آخر.');
                }, function(err) {
                    console.error('فشل النسخ تلقائياً: ', err);
                });
            }
        }

        function editTask(id) {
            alert('ميزة التعديل السريع قيد التطوير وستعمل بكفاءة في الخطوة التالية!');
        }

        function deleteTask(id) {
            if(confirm('هل أنت متأكد من رغبتك في حذف هذه المهمة من جدولك اليومي؟')) {
                alert('ميزة الحذف السريع قيد التطوير في الخطوة التالية!');
            }
        }
    </script>
</body>
</html>