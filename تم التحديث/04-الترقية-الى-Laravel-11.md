# 🔄 المرحلة 4: الترقية إلى Laravel 11

## 🎯 الهدف
ترقية المشروع من Laravel 10.x إلى Laravel 11.x

---

## 📋 التغييرات الرئيسية في Laravel 11

| التغيير | التأثير على المشروع |
|---------|---------------------|
| PHP 8.2+ مطلوب | ✅ لدينا PHP 8.2 |
| بنية مشروع مبسطة | ⚠️ يحتاج تعديل |
| إزالة Kernel files | ⚠️ يحتاج تعديل |
| bootstrap/app.php جديد | ⚠️ يحتاج إنشاء |
| config مبسط | اختياري |

---

## ⚠️ تحذير مهم

Laravel 11 يستخدم **بنية مشروع جديدة**. لكن يمكنك الاحتفاظ بالبنية القديمة مع بعض التعديلات.

### خياران للترقية:

| الخيار | الوصف | التوصية |
|--------|-------|---------|
| **الخيار A** | الاحتفاظ بالبنية القديمة | ✅ **موصى به** |
| **الخيار B** | التحويل للبنية الجديدة | ⚠️ أكثر تعقيداً |

---

## 🅰️ الخيار A: الاحتفاظ بالبنية القديمة (موصى به)

### 1️⃣ تحديث composer.json

```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^11.0",
        "laravel/sanctum": "^4.0",
        "laravel/tinker": "^2.9",
        "laravel/ui": "^4.4",
        "guzzlehttp/guzzle": "^7.2"
    },
    "require-dev": {
        "fakerphp/faker": "^1.23",
        "laravel/sail": "^1.26",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.0",
        "phpunit/phpunit": "^10.5",
        "spatie/laravel-ignition": "^2.4"
    }
}
```

### 2️⃣ تنفيذ التحديث

```powershell
# حذف vendor و composer.lock
Remove-Item -Recurse -Force vendor -ErrorAction SilentlyContinue
Remove-Item composer.lock -ErrorAction SilentlyContinue

# تثبيت الحزم
composer install

# إذا حدثت أخطاء:
composer update --with-all-dependencies
```

### 3️⃣ تحديث `bootstrap/app.php`

**استبدل المحتوى الحالي بـ:**

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // إضافة middleware aliases
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            // أضف middleware المخصصة هنا
            'super.admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            // أضف أي middleware أخرى من Kernel.php
        ]);

        // middleware groups
        $middleware->web(append: [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->api(append: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

### 4️⃣ الاحتفاظ بـ Kernel.php (اختياري)

يمكنك الاحتفاظ بملفات Kernel.php القديمة، لكن Laravel 11 لن يستخدمها. الأفضل نقل المحتوى إلى `bootstrap/app.php`.

### 5️⃣ تحديث `routes/console.php`

```php
<?php

use Illuminate\Support\Facades\Schedule;

// إضافة scheduled tasks هنا إذا كان لديك
// Schedule::command('inspire')->hourly();
```

---

## 4️⃣ تحديث الـ Service Providers

### تحديث `app/Providers/AppServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // منع N+1 في التطوير
        Model::preventLazyLoading(! app()->isProduction());

        // مشاركة الإعدادات مع جميع الـ views
        View::composer('*', function ($view) {
            // أضف الكود الموجود حالياً في AppServiceProvider
        });
    }
}
```

---

## 5️⃣ تحديث ملفات التكوين

### إنشاء `config/app.php` المبسط (اختياري)

Laravel 11 يستخدم ملفات config أقل. لكن يمكنك الاحتفاظ بالملفات القديمة.

---

## 6️⃣ اختبار بعد الترقية

```powershell
# مسح جميع الـ cache
php artisan optimize:clear

# التحقق من الإصدار
php artisan --version
# المتوقع: Laravel Framework 11.x.x

# تشغيل الخادم
php artisan serve

# اختبار الـ routes
php artisan route:list
```

---

## 7️⃣ قائمة الاختبار الشامل

### الوظائف الأساسية
| الوظيفة | الحالة |
|---------|--------|
| تسجيل الدخول | [ ] |
| تسجيل الخروج | [ ] |
| لوحة التحكم | [ ] |
| تغيير كلمة المرور | [ ] |

### إدارة الشركات
| الوظيفة | الحالة |
|---------|--------|
| عرض الشركات | [ ] |
| إضافة شركة | [ ] |
| تعديل شركة | [ ] |
| حذف شركة | [ ] |

### إدارة الفروع
| الوظيفة | الحالة |
|---------|--------|
| عرض الفروع | [ ] |
| إضافة فرع | [ ] |
| تعديل فرع | [ ] |

### إدارة الموظفين
| الوظيفة | الحالة |
|---------|--------|
| عرض الموظفين | [ ] |
| إضافة موظف | [ ] |
| تعديل موظف | [ ] |

### إدارة الطلبات
| الوظيفة | الحالة |
|---------|--------|
| إنشاء طلب | [ ] |
| الموافقة على طلب | [ ] |
| رفض طلب | [ ] |

### التقارير والتصدير
| الوظيفة | الحالة |
|---------|--------|
| عرض التقارير | [ ] |
| تصدير Excel | [ ] |
| طباعة | [ ] |

---

## ⚠️ المشاكل الشائعة وحلولها

### المشكلة 1: Middleware not found
```
Target class [middleware] does not exist
```
**الحل:**
تأكد من إضافة جميع middleware aliases في `bootstrap/app.php`

### المشكلة 2: Route not defined
```
Route [name] not defined
```
**الحل:**
تأكد من أن `routes/web.php` محمل في `bootstrap/app.php`

### المشكلة 3: Session issues
```
Session store not set on request
```
**الحل:**
تأكد من أن session middleware مضاف في web middleware group

### المشكلة 4: CSRF token mismatch
```
419 | Page Expired
```
**الحل:**
تأكد من أن VerifyCsrfToken middleware مضاف

---

## ✅ تأكيد نجاح الترقية

- [ ] ✅ `php artisan --version` يُظهر Laravel 11.x
- [ ] ✅ جميع الصفحات تعمل
- [ ] ✅ تسجيل الدخول يعمل
- [ ] ✅ إضافة/تعديل/حذف البيانات يعمل
- [ ] ✅ التقارير تعمل
- [ ] ✅ التصدير إلى Excel يعمل
- [ ] ✅ لا أخطاء في الـ console

---

## 💾 حفظ التقدم النهائي (محلياً فقط)

> ⚠️ **تنبيه مهم:** جميع الحفظ يكون **محلياً فقط** - لا نرفع أي شيء على GitHub!

```powershell
git add .
git commit -m "Upgraded to Laravel 11 - Complete"
git tag -a "v2.0-laravel11" -m "First stable version on Laravel 11"

 

---

## 🎉 تهانينا!

تم ترقية المشروع بنجاح إلى Laravel 11!

---

**الملف التالي:** `05-تحديث-الحزم.md`
