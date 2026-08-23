<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * عرض صفحة تسجيل الدخول المخصصة.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * معالجة عملية التحقق وتسجيل الدخول للمستخدم.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // محاولة تسجيل الدخول وحفظ خيار "تذكرني"
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            // توجيه المستخدم لصفحة لوحة التحكم مباشرة
            return redirect()->intended('dashboard');
        }

        // في حال فشل الدخول، العودة مع رسالة تنبيه ودية
        return back()->withErrors([
            'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
        ])->onlyInput('email');
    }

    /**
     * عرض صفحة إنشاء حساب جديد.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * معالجة وإنشاء حساب المستخدم الجديد وتسجيل دخوله تلقائياً.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // إنشاء المستخدم وتشفير كلمة المرور بأمان عالي
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // تسجيل الدخول تلقائياً للمستخدم الجديد
        Auth::login($user);

        // توجيهه فوراً للوحة التحكم لتبدأ مرحلة اختيار دوره الترحيبية
        return redirect()->route('dashboard');
    }

    /**
     * تسجيل الخروج بأمان وتدمير الجلسة الحالية.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}