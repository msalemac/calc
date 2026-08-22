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