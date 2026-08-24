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

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    /**
     * تحديث بيانات الملف الشخصي وكلمة المرور للمستخدم ديناميكياً بأمان.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // التحقق من صحة البيانات (البريد يجب أن يكون فريداً باستثناء إيميل المستخدم الحالي نفسه)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // تعديل كلمة المرور فقط في حال قام المستخدم بكتابة باسورداً جديداً
        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'تم تحديث بيانات ملفك الشخصي وكلمة المرور الخاصة بك بنجاح!');
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