<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $fillable = ['user_id', 'title', 'type'];

    /**
     * علاقة الجهة بالمستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة الجهة بالمهام المرتبطة بها
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}