# 🔄 المرحلة 2: الترقية إلى Laravel 9

## 🎯 الهدف
ترقية المشروع من Laravel 8.x إلى Laravel 9.x

---

## 📋 التغييرات الرئيسية في Laravel 9

| التغيير | التأثير على المشروع |
|---------|---------------------|
| PHP 8.0+ مطلوب | ✅ لدينا PHP 8.2 |
| Symfony 6 components | تلقائي |
| Anonymous stub migrations | اختياري |
| New query builder interface | تلقائي |
| Improved route:list | تلقائي |

---

## 1️⃣ تحديث composer.json

### أ) تحديث إصدار Laravel
```json
{
    "require": {
        "php": "^8.0",
        "laravel/framework": "^9.0",
        "laravel/sanctum": "^3.0",
        "laravel/tinker": "^2.7",
        "laravel/ui": "^4.0",
        "guzzlehttp/guzzle": "^7.2"
    },
    "require-dev": {
        "fakerphp/faker": "^1.9.1",
        "laravel/sail": "^1.0.1",
        "mockery/mockery": "^1.4.4",
        "nunomaduro/collision": "^6.1",
        "phpunit/phpunit": "^9.5.10",
        "spatie/laravel-ignition": "^1.0"
    }
}
```

### ب) الحزم التي يجب إزالتها
```json
// إزالة هذه من require:
"fruitcake/laravel-cors": "^2.0"  // مدمج في Laravel 9

// إزالة هذه من require-dev:
"facade/ignition": "^2.5"  // استبدال بـ spatie/laravel-ignition
```

---

## 2️⃣ تنفيذ التحديث

```powershell
# الخطوة 1: تحديث composer.json يدوياً (كما في الأعلى)

# الخطوة 2: حذف vendor و composer.lock
Remove-Item -Recurse -Force vendor -ErrorAction SilentlyContinue
Remove-Item composer.lock -ErrorAction SilentlyContinue

# الخطوة 3: تثبيت الحزم الجديدة
composer install

# إذا حدثت أخطاء، جرب:
composer update --with-all-dependencies
```

---

## 3️⃣ تحديث ملفات التكوين

### أ) تحديث `config/app.php`
لا تغييرات مطلوبة عادةً.

### ب) إضافة `config/cors.php` (إذا لم يكن موجوداً)
```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

### ج) تحديث `app/Http/Kernel.php`
إضافة CORS middleware إذا لم يكن موجوداً:
```php
protected $middleware = [
    // ...
    \Illuminate\Http\Middleware\HandleCors::class,  // إضافة هذا
];
```

---

## 4️⃣ تحديث الـ Middleware

### إزالة CORS middleware القديم
إذا كان لديك `\Fruitcake\Cors\HandleCors::class` في Kernel.php، استبدله بـ:
```php
\Illuminate\Http\Middleware\HandleCors::class
```

---

## 5️⃣ تحديث الـ Routes

### التغيير في Route Groups
```php
// Laravel 8 (القديم)
Route::group(['middleware' => 'auth'], function () {
    // ...
});

// Laravel 9 (يعمل أيضاً - لا تغيير مطلوب)
Route::middleware('auth')->group(function () {
    // ...
});
```

> ✅ **لا تغيير مطلوب** - الصيغة القديمة تعمل في Laravel 9

---

## 6️⃣ تحديث الـ Models

### Accessors و Mutators الجديدة (اختياري)
```php
// Laravel 8 (القديم - يعمل)
public function getNameAttribute($value)
{
    return ucfirst($value);
}

// Laravel 9 (الجديد - اختياري)
protected function name(): Attribute
{
    return Attribute::make(
        get: fn ($value) => ucfirst($value),
    );
}
```

> ✅ **لا تغيير مطلوب** - الصيغة القديمة تعمل في Laravel 9

---

## 7️⃣ اختبار بعد الترقية

```powershell
# مسح الـ cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# التحقق من الإصدار
php artisan --version
# المتوقع: Laravel Framework 9.x.x

# تشغيل الخادم
php artisan serve

# اختبار الصفحة الرئيسية
# افتح: http://localhost:8000
```

---

## 8️⃣ قائمة الاختبار السريع

| الوظيفة | الحالة |
|---------|--------|
| تسجيل الدخول | [ ] |
| لوحة التحكم | [ ] |
| إضافة شركة | [ ] |
| إضافة فرع | [ ] |
| إضافة موظف | [ ] |
| إضافة طلب | [ ] |
| التقارير | [ ] |

---

## ⚠️ المشاكل الشائعة وحلولها

### المشكلة 1: خطأ في CORS
```
Access to XMLHttpRequest has been blocked by CORS policy
```
**الحل:**
```php
// في config/cors.php
'paths' => ['api/*', 'sanctum/csrf-cookie', '*'],
```

### المشكلة 2: خطأ في Ignition
```
Class 'Facade\Ignition\...' not found
```
**الحل:**
```powershell
composer remove facade/ignition
composer require spatie/laravel-ignition --dev
```

### المشكلة 3: خطأ في الـ Cache
```
Failed to clear cache
```
**الحل:**
```powershell
Remove-Item -Recurse -Force bootstrap\cache\*
php artisan cache:clear
```

---

## ✅ تأكيد نجاح الترقية

قبل الانتقال للمرحلة التالية:

- [ ] ✅ `php artisan --version` يُظهر Laravel 9.x
- [ ] ✅ الصفحة الرئيسية تعمل
- [ ] ✅ تسجيل الدخول يعمل
- [ ] ✅ لا أخطاء في الـ console

---

## 💾 حفظ التقدم (محلياً فقط)

> ⚠️ **تنبيه:** الحفظ يكون محلياً فقط - لا نرفع على GitHub!

```powershell
git add .
git commit -m "Upgraded to Laravel 9"
 
---

**المرحلة التالية:** `03-الترقية-الى-Laravel-10.md`
