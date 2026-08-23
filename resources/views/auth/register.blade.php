<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مساعد الإنتاجية الذكي - إنشاء حساب</title>
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
<body class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-2xl text-right">
        
        <!-- الترحيب والشعار -->
        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-indigo-500/10 border border-indigo-500/30 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <i class="ri-user-add-line text-2xl text-indigo-400"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-white">ابدأ رحلتك اليوم!</h2>
            <p class="text-slate-400 text-xs mt-1">أنشئ حسابك المجاني في دقيقة وابدأ في تنظيم مهامك بذكاء</p>
        </div>

        <!-- عرض التنبيهات والأخطاء إن وجدت -->
        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-xs rounded-xl p-3 mb-4 leading-relaxed">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- نموذج إنشاء الحساب -->
        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold text-slate-400 block mb-1">الاسم الكامل</label>
                <div class="relative">
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: محمد سالم" 
                           class="w-full bg-slate-800/40 border border-slate-700/50 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-white pr-10">
                    <i class="ri-user-line absolute right-3 top-3.5 text-slate-500 text-lg"></i>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 block mb-1">البريد الإلكتروني</label>
                <div class="relative">
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="yourname@example.com" 
                           class="w-full bg-slate-800/40 border border-slate-700/50 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-white pr-10">
                    <i class="ri-mail-line absolute right-3 top-3.5 text-slate-500 text-lg"></i>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 block mb-1">كلمة المرور (8 رموز على الأقل)</label>
                <div class="relative">
                    <input type="password" name="password" required placeholder="••••••••" 
                           class="w-full bg-slate-800/40 border border-slate-700/50 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-white pr-10">
                    <i class="ri-lock-line absolute right-3 top-3.5 text-slate-500 text-lg"></i>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 block mb-1">تأكيد كلمة المرور</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" required placeholder="••••••••" 
                           class="w-full bg-slate-800/40 border border-slate-700/50 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none text-white pr-10">
                    <i class="ri-lock-check-line absolute right-3 top-3.5 text-slate-500 text-lg"></i>
                </div>
            </div>

            <button type="submit" 
                    class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/15 transition-all duration-300">
                تسجيل حساب جديد <i class="ri-sparkling-fill mr-1"></i>
            </button>
        </form>

        <div class="text-center mt-6 border-t border-slate-800 pt-4">
            <span class="text-xs text-slate-400">لديك حساب بالفعل؟</span>
            <a href="{{ route('login') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-bold mr-1">سجل دخولك الآن <i class="ri-arrow-left-line"></i></a>
        </div>
    </div>

</body>
</html>