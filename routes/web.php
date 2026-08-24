<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| مسارات خيوط الويب للمشروع (Web Routes)
|--------------------------------------------------------------------------
|
| هنا نقوم بتعريف وتحديد كافة الروابط الفعالة والآمنة للمنصة، وتوجيهها
| للمتحكمات المخصصة للقيام بالعمليات البرمجية وقراءة البيانات من MySQL.
|
*/

// توجيه رابط الموقع الرئيسي تلقائياً إلى لوحة التحكم
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// 1. مسارات الحسابات المتاحة للزوار فقط (Guest Middleware - يمنع دخول المسجلين إليها)
Route::middleware(['guest'])->group(function () {
    // صفحة ومعالجة تسجيل الدخول الآمن
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // صفحة ومعالجة تسجيل حساب مستخدم جديد مجاني
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// 2. مسارات لوحة التحكم والعمليات المحمية (Auth Middleware - لا تفتح إلا بعد تسجيل الدخول)
Route::middleware(['auth'])->group(function () {
    
    // تسجيل الخروج الآمن وتدمير الجلسة في المتصفح والسيرفر
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // لوحة التحكم الرئيسية المخصصة حسب دور المستخدم الحقيقي
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // واجهة اختيار وتحديد الدور الترحيبية للمستخدم الجديد (Onboarding)
    Route::get('/dashboard/select-role', [DashboardController::class, 'showSelectRole'])->name('dashboard.select-role');
    
    // استقبال واعتماد الدور المختار وزرع التصنيفات الملونة الافتراضية المناسبة للمستخدم
    Route::post('/dashboard/select-role', [DashboardController::class, 'storeRole'])->name('dashboard.store-role');
    
    // استقبال وحفظ المهمة اليومية الجديدة الموجهة لـ TaskController
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

    // استقبال وتحديث بيانات الملف الشخصي للمستخدم وتغيير كلمة المرور بأمان عالي
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
    
});