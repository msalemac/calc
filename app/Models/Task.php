<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'entity_id', 'title', 'description', 
        'priority', 'estimated_duration', 'status', 'postpone_count', 
        'due_date', 'reminder_interval', 'last_reminded_at', 'custom_fields', 'is_synced'
    ];

    // لكي يتعرف Laravel على حقل الـ JSON تلقائياً كمصفوفة عند القراءة والكتابة
    protected $casts = [
        'custom_fields' => 'array',
        'due_date' => 'datetime',
        'is_synced' => 'boolean'
    ];

    /**
     * علاقة المهمة بالمستخدم (المهمة تنتمي لمستخدم واحد)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة المهمة بالتصنيف الملون (المهمة تنتمي لتصنيف واحد - اختياري)
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * علاقة المهمة بالجهة المعنية أو الإدارة (المهمة تنتمي لجهة واحدة - اختياري)
     */
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}