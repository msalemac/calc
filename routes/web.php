<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

// تحويل الصفحة الرئيسية تلقائياً إلى لوحة التحكم
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// 1. مسارات الحسابات المتاحة للزوار فقط (غير المسجلين)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// 2. مسارات لوحة التحكم والعمليات المحمية (للمسجلين فقط)
Route::middleware(['auth'])->group(function () {
    
    // تسجيل الخروج الآمن
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // لوحة التحكم وتفضيلات الدور الترحيبية
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/select-role', [DashboardController::class, 'showSelectRole'])->name('dashboard.select-role');
    Route::post('/dashboard/select-role', [DashboardController::class, 'storeRole'])->name('dashboard.store-role');
    
    // إدارة وحفظ المهمة الجديدة الموجه لـ TaskController
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

    // تفعيل وفحص التأجيل الذكي وعلاج الازدحام عبر الذكاء الاصطناعي
    Route::get('/tasks/{id}/postpone', [TaskController::class, 'postpone'])->name('tasks.postpone');

    // تطبيق واعتماد الحل البديل المختار من الذكاء الاصطناعي على المهمة
    Route::post('/tasks/{id}/accept-suggestion', [TaskController::class, 'acceptSuggestion'])->name('tasks.accept-suggestion');

    // رابط تحديث بيانات الملف الشخصي للمستخدم وتغيير كلمة المرور بأمان عالي
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
    
});