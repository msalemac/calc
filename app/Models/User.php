<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * الحقول القابلة للتعبئة بأمان.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'credits_left',
    ];

    /**
     * الحقول المخفية للتشفير.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * تحويل الحقول تلقائياً للإصدارات البرمجية المناسبة.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * علاقة المستخدم بالدور الخاص به (طالب، موظف، مدير...)
     */
    public function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    /**
     * علاقة المستخدم بمهامه اليومية
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * علاقة المستخدم بالتصنيفات الملونة التي ينشئها
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * علاقة المستخدم بالجهات أو الإدارات التابعة له
     */
    public function entities()
    {
        return $this->hasMany(Entity::class);
    }

    /**
     * علاقة المستخدم بجدول الروتين الثابت (النوم، العمل...)
     */
    public function routines()
    {
        return $this->hasMany(UserRoutine::class);
    }
}