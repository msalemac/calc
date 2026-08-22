[واجهة المستخدم - HTML5/CSS3/Tailwind/AlpineJS]
                                    │             ▲
              Offline Cache         │             │ Online Synchronization
     (Service Worker / IndexedDB)   ▼             │
                        [بوابة الطلبات البرمجية - Laravel API Gateway]
                                    │
                  ┌─────────────────┴─────────────────┐
                  ▼                                   ▼
        [قاعدة البيانات - MySQL]             [الخدمات والـ APIs الخارجية]
    (تخزين المستخدمين، المهام، الروتين،        (OpenAI, Whisper API, Firebase)
     النسخ الاحتياطي، الحقول المخصصة)