<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مساعد الإنتاجية الذكي - اختيار الدور</title>
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
<body class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-4xl w-full bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-2xl text-center">
        
        <div class="mb-8">
            <div class="w-16 h-16 bg-indigo-500/10 border border-indigo-500/30 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-indigo-500/10">
                <i class="ri-sparkling-fill text-3xl text-indigo-400"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-white mb-2">مرحباً بك في عالم الإنتاجية الذكي!</h1>
            <p class="text-slate-400 text-sm max-w-md mx-auto leading-relaxed">
                يرجى اختيار دورك الأساسي اليوم لتخصيص لوحة تحكمك ونبرة مستشار الذكاء الاصطناعي:
            </p>
        </div>

        <!-- عرض أخطاء التحقق من الرمز السري إن وجدت -->
        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-xs rounded-xl p-3 mb-6 max-w-md mx-auto leading-relaxed text-right">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('dashboard.store-role') }}" method="POST" id="roleForm">
            @csrf
            <input type="hidden" name="role_id" id="selected_role_id">

            <!-- شبكة الكروت -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                @foreach($roles as $role)
                    @php
                        $icon = 'ri-briefcase-line';
                        $color = 'from-blue-500 to-indigo-500';
                        $desc = 'لتنظيم مهام العمل والمتابعة اليومية للإدارات.';
                        if ($role->role_name === 'student') {
                            $icon = 'ri-book-open-line';
                            $color = 'from-emerald-500 to-teal-500';
                            $desc = 'لإدارة المذاكرة، المدرسين، والتحضير للامتحانات.';
                        } elseif ($role->role_name === 'manager') {
                            $icon = 'ri-shield-user-line';
                            $color = 'from-amber-500 to-orange-500';
                            $desc = 'لتفويض المهام للأقسام وحل اختناقات العمل.';
                        }
                    @endphp

                    <!-- كرت الدور التفاعلي، يمرر معرف الـ ID وهل يتطلب رمزاً سرياً أم لا -->
                    <div onclick="selectRole({{ $role->id }}, {{ $role->activation_pin ? 'true' : 'false' }}, this)" 
                         class="group relative bg-slate-800/30 border border-slate-700/50 rounded-2xl p-6 text-right cursor-pointer transition-all duration-300 hover:scale-[1.03] hover:border-indigo-500/50 hover:bg-slate-800/70 select-none">
                        
                        <div class="absolute top-4 left-4 group/tooltip">
                            <i class="ri-question-line text-slate-500 hover:text-indigo-400 transition-colors text-lg"></i>
                            <div class="absolute bottom-full left-0 mb-2 w-52 bg-slate-950 border border-slate-800 text-slate-300 text-xs rounded-xl p-3 shadow-2xl opacity-0 pointer-events-none group-hover/tooltip:opacity-100 transition-opacity z-50">
                                <p class="font-bold text-white mb-1">تفاصيل الدعم الذكي:</p>
                                <p class="mb-1 text-slate-400">النقاط اليومية: <span class="text-indigo-400 font-bold">{{ $role->daily_credits }} نقطة</span></p>
                                <p class="text-slate-400 leading-relaxed">يتطلب رمز تفعيل: <span class="{{ $role->activation_pin ? 'text-amber-400 font-bold' : 'text-emerald-400' }}">{{ $role->activation_pin ? 'نعم' : 'لا (عام)' }}</span></p>
                            </div>
                        </div>

                        <div class="w-12 h-12 bg-gradient-to-br {{ $color }} rounded-xl flex items-center justify-center mb-4 text-white text-xl shadow-lg shadow-indigo-500/5">
                            <i class="{{ $icon }}"></i>
                        </div>

                        <h3 class="text-lg font-bold text-white mb-1">
                            @if($role->role_name === 'student') طالب @elseif($role->role_name === 'manager') مدير @else موظف / عام @endif
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed">{{ $desc }}</p>

                        <div class="absolute bottom-4 left-4 w-5 h-5 rounded-full border-2 border-slate-700 flex items-center justify-center transition-colors group-hover:border-indigo-500 checked-circle">
                            <i class="ri-check-line text-xs text-white opacity-0 transition-opacity"></i>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- حقل إدخال الرمز السري المنزلق تفاعلياً للأدوار الخاصة فقط -->
            <div id="pinSection" class="max-w-xs mx-auto mb-8 hidden transition-all duration-300">
                <label class="text-xs font-bold text-slate-400 block mb-2 text-right">رمز تفعيل صلاحية المدير الحصرية</label>
                <div class="relative">
                    <input type="password" name="activation_pin" id="activation_pin_input" placeholder="أدخل الرمز المكون من 6 أرقام" 
                           class="w-full text-center bg-slate-800/40 border border-slate-700/50 rounded-xl px-4 py-3.5 text-sm focus:border-indigo-500 focus:outline-none text-white font-extrabold tracking-[0.3em] placeholder:tracking-normal placeholder:font-normal">
                    <i class="ri-key-2-line absolute right-3 top-3.5 text-slate-500 text-lg"></i>
                </div>
            </div>

            <button type="submit" id="submitBtn" disabled
                    class="px-8 py-3.5 bg-indigo-600 hover:bg-indigo-500 disabled:bg-slate-800 disabled:text-slate-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/15 transition-all duration-300 translate-y-1 hover:translate-y-0 disabled:translate-y-0">
                تهيئة لوحة التحكم والدخول <i class="ri-arrow-left-line mr-2"></i>
            </button>
        </form>
    </div>

    <script>
        function selectRole(roleId, hasPin, cardElement) {
            document.getElementById('selected_role_id').value = roleId;

            // تصفير الألوان للكروت الأخرى
            const cards = document.querySelectorAll('.group');
            cards.forEach(card => {
                card.classList.remove('border-indigo-500', 'bg-slate-800/70');
                card.classList.add('border-slate-700/50', 'bg-slate-800/30');
                const circle = card.querySelector('.checked-circle');
                circle.classList.remove('bg-indigo-600', 'border-indigo-600');
                circle.querySelector('i').classList.add('opacity-0');
            });

            // تفعيل وتلوين الكارت الحالي
            cardElement.classList.remove('border-slate-700/50', 'bg-slate-800/30');
            cardElement.classList.add('border-indigo-500', 'bg-slate-800/70');
            const activeCircle = cardElement.querySelector('.checked-circle');
            activeCircle.classList.add('bg-indigo-600', 'border-indigo-600');
            activeCircle.querySelector('i').classList.remove('opacity-0');

            // إظهار أو إخفاء حقل الرمز السري بطريقة انسيابية وتفاعلية
            const pinSection = document.getElementById('pinSection');
            const pinInput = document.getElementById('activation_pin_input');
            if (hasPin) {
                pinSection.classList.remove('hidden');
                pinInput.required = true;
                pinInput.focus();
            } else {
                pinSection.classList.add('hidden');
                pinInput.required = false;
                pinInput.value = ''; // تصفير الحقل عند اختيار دور عام
            }

            document.getElementById('submitBtn').disabled = false;
        }
    </script>
</body>
</html>