# 🔄 المرحلة 3: الترقية إلى Laravel 10

## 🎯 الهدف
ترقية المشروع من Laravel 9.x إلى Laravel 10.x

---

## 📋 التغييرات الرئيسية في Laravel 10

| التغيير | التأثير على المشروع |
|---------|---------------------|
| PHP 8.1+ مطلوب | ✅ لدينا PHP 8.2 |
| Native type declarations | تلقائي |
| Laravel Pennant | اختياري |
| Process facade | جديد |
| Invokable validation rules | اختياري |

---

## 1️⃣ تحديث composer.json

### أ) تحديث الإصدارات
```json
{
    "require": {
        "php": "^8.1",
        "laravel/framework": "^10.0",
        "laravel/sanctum": "^3.2",
        "laravel/tinker": "^2.8",
        "laravel/ui": "^4.2",
        "guzzlehttp/guzzle": "^7.2"
    },
    "require-dev": {
        "fakerphp/faker": "^1.9.1",
        "laravel/sail": "^1.18",
        "mockery/mockery": "^1.4.4",
        "nunomaduro/collision": "^7.0",
        "phpunit/phpunit": "^10.0",
        "spatie/laravel-ignition": "^2.0"
    }
}
```

---

## 2️⃣ تنفيذ التحديث

```powershell
# الخطوة 1: تحديث composer.json يدوياً

# الخطوة 2: حذف vendor و composer.lock
Remove-Item -Recurse -Force vendor -ErrorAction SilentlyContinue
Remove-Item composer.lock -ErrorAction SilentlyContinue

# الخطوة 3: تثبيت الحزم
composer install

# إذا حدثت أخطاء:
composer update --with-all-dependencies
```

---

## 3️⃣ تحديث ملف `app/Http/Kernel.php`

### إضافة TrustHosts middleware (اختياري)
```php
protected $middleware = [
    // \App\Http\Middleware\TrustHosts::class,  // إلغاء التعليق إذا أردت
    \App\Http\Middleware\TrustProxies::class,
    \Illuminate\Http\Middleware\HandleCors::class,
    // ...
];
```

---

## 4️⃣ تحديث ملف `app/Providers/RouteServiceProvider.php`

### Laravel 10 يستخدم طريقة جديدة للـ Rate Limiting
```php
<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
```

---

## 5️⃣ تحديث الـ Exception Handler

### تحديث `app/Exceptions/Handler.php`
```php
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
```

---

## 6️⃣ تحديث الـ Validation Rules (اختياري)

### الصيغة القديمة (تعمل)
```php
// في Controller
$request->validate([
    'email' => 'required|email|unique:users',
]);
```

### الصيغة الجديدة (اختياري)
```php
// في Controller
$request->validate([
    'email' => ['required', 'email', 'unique:users'],
]);
```

> ✅ **لا تغيير مطلوب** - الصيغة القديمة تعمل

---

## 7️⃣ تحديث الـ Casts في Models (اختياري)

### الصيغة القديمة (تعمل)
```php
protected $casts = [
    'email_verified_at' => 'datetime',
    'is_active' => 'boolean',
];
```

### الصيغة الجديدة (اختياري)
```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}
```

> ✅ **لا تغيير مطلوب** - الصيغة القديمة تعمل

---

## 8️⃣ اختبار بعد الترقية

```powershell
# مسح الـ cache
php artisan optimize:clear

# التحقق من الإصدار
php artisan --version
# المتوقع: Laravel Framework 10.x.x

# تشغيل الخادم
php artisan serve
```

---

## 9️⃣ قائمة الاختبار

| الوظيفة | الحالة |
|---------|--------|
| تسجيل الدخول | [ ] |
| لوحة التحكم | [ ] |
| إدارة الشركات | [ ] |
| إدارة الفروع | [ ] |
| إدارة الموظفين | [ ] |
| إدارة الطلبات | [ ] |
| التقارير | [ ] |
| التصدير إلى Excel | [ ] |

---

## ⚠️ المشاكل الشائعة وحلولها

### المشكلة 1: خطأ في PHPUnit
```
PHPUnit 10 requires PHP 8.1
```
**الحل:**
```powershell
composer require phpunit/phpunit:^10.0 --dev --with-all-dependencies
```

### المشكلة 2: خطأ في Collision
```
Class 'NunoMaduro\Collision\...' not found
```
**الحل:**
```powershell
composer require nunomaduro/collision:^7.0 --dev
```

### المشكلة 3: خطأ في Type Declarations
```
Return type must be ... or ...
```
**الحل:**
أضف return type للدالة المذكورة في الخطأ.

---

## ✅ تأكيد نجاح الترقية

قبل الانتقال للمرحلة التالية:

- [ ] ✅ `php artisan --version` يُظهر Laravel 10.x
- [ ] ✅ الصفحة الرئيسية تعمل
- [ ] ✅ تسجيل الدخول يعمل
- [ ] ✅ إضافة بيانات جديدة يعمل
- [ ] ✅ لا أخطاء في الـ console

---

## 💾 حفظ التقدم (محلياً فقط)

> ⚠️ **تنبيه:** الحفظ يكون محلياً فقط - لا نرفع على GitHub!

```powershell
git add .
git commit -m "Upgraded to Laravel 10"
 

---

**المرحلة التالية:** `04-الترقية-الى-Laravel-11.md`
