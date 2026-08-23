<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $fillable = ['role_name', 'system_prompt', 'daily_credits', 'custom_fields_schema'];

    /**
     * علاقة الدور بالمستخدمين (الدور الواحد ينتمي إليه العديد من المستخدمين)
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}