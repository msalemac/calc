<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // السماح للكود بكتابة هذه الحقول في قاعدة البياناتMySQL
    protected $fillable = ['user_id', 'title', 'color_code'];

    /**
     * علاقة التصنيف بالمستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة التصنيف بالمهام المرتبطة به
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}