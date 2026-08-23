<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRoutine extends Model
{
    protected $fillable = ['user_id', 'activity_name', 'start_time', 'end_time'];

    /**
     * علاقة الروتين بالمستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}