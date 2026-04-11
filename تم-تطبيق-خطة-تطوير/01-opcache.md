# 1️⃣ تفعيل OPcache

## 📋 المعلومات الأساسية

| العنصر | القيمة |
|--------|--------|
| الأولوية | 🔴 عالية جداً |
| الوقت المطلوب | 5 دقائق |
| التحسين المتوقع | 60-70% |
| المخاطر | منخفضة جداً |

---

## 🔍 الوضع الحالي

```
OPcache: غير مفعل ❌
```

**المشكلة**: كل طلب HTTP يُترجم كود PHP من جديد!

---

## 📖 شرح OPcache

### ماذا يحدث بدون OPcache؟

```
كل طلب (Request):
┌──────────────────────────────────────────────────────────────┐
│  📄 PHP File  →  📖 Parse  →  🔄 Compile  →  ⚙️ Execute      │
│                                                              │
│  هذا يحدث 1000 مرة إذا كان عندك 1000 زائر!                  │
└──────────────────────────────────────────────────────────────┘
```

### ماذا يحدث مع OPcache؟

```
المرة الأولى فقط:
┌──────────────────────────────────────────────────────────────┐
│  📄 PHP File  →  📖 Parse  →  🔄 Compile  →  💾 Cache        │
└──────────────────────────────────────────────────────────────┘

باقي الطلبات:
┌──────────────────────────────────────────────────────────────┐
│  💾 Cached Bytecode  →  ⚙️ Execute  (فوري!)                  │
└──────────────────────────────────────────────────────────────┘
```

---

## 🛠️ خطوات التفعيل

### الخطوة 1: فتح php.ini

```
1. افتح Laragon
2. اضغط بزر الماوس الأيمن على أيقونة Laragon
3. اختر: Menu → PHP → php.ini
```

### الخطوة 2: البحث عن OPcache

ابحث عن:
```ini
;zend_extension=opcache
```

أو:
```ini
[opcache]
```

### الخطوة 3: إضافة/تعديل الإعدادات

**انسخ هذا الكود وضعه في نهاية الملف:**

```ini
; ============================================
; OPcache Settings for ConcreteERP
; ============================================

zend_extension=opcache

[opcache]
; تفعيل OPcache
opcache.enable=1
opcache.enable_cli=1

; الذاكرة المخصصة (256 ميجا كافية للمشروع)
opcache.memory_consumption=256

; ذاكرة النصوص المتكررة
opcache.interned_strings_buffer=16

; عدد الملفات المسموح بتخزينها (المشروع فيه ~500 ملف PHP)
opcache.max_accelerated_files=20000

; ============================================
; إعدادات بيئة التطوير (Development)
; ============================================
; تحقق من تغييرات الملفات كل 2 ثانية
opcache.revalidate_freq=2
opcache.validate_timestamps=1

; ============================================
; إعدادات بيئة الإنتاج (Production)
; عند الرفع للسيرفر، غيّر هذه القيم:
; ============================================
; opcache.revalidate_freq=0
; opcache.validate_timestamps=0

; تحسينات إضافية
opcache.fast_shutdown=1
opcache.save_comments=1
```

### الخطوة 4: إعادة تشغيل Laragon

```
1. في Laragon، اضغط: Stop All
2. انتظر 3 ثواني
3. اضغط: Start All
```

---

## ✅ التحقق من التفعيل

### الطريقة 1: عبر الطرفية

```bash
php -m | findstr opcache
```

**النتيجة المتوقعة:**
```
Zend OPcache
```

### الطريقة 2: إنشاء ملف اختبار

أنشئ ملف `public/opcache-test.php`:

```php
<?php
if (function_exists('opcache_get_status')) {
    $status = opcache_get_status();
    echo "<h1>OPcache Status</h1>";
    echo "<pre>";
    echo "Enabled: " . ($status['opcache_enabled'] ? 'Yes ✅' : 'No ❌') . "\n";
    echo "Memory Used: " . round($status['memory_usage']['used_memory'] / 1024 / 1024, 2) . " MB\n";
    echo "Memory Free: " . round($status['memory_usage']['free_memory'] / 1024 / 1024, 2) . " MB\n";
    echo "Cached Scripts: " . $status['opcache_statistics']['num_cached_scripts'] . "\n";
    echo "Hit Rate: " . round($status['opcache_statistics']['opcache_hit_rate'], 2) . "%\n";
    echo "</pre>";
} else {
    echo "<h1>OPcache is NOT installed ❌</h1>";
}
```

افتح: `http://localhost/ConcreteERP/opcache-test.php`

### الطريقة 3: عبر phpinfo

```php
<?php phpinfo();
```

ابحث عن قسم "Zend OPcache"

---

## 📊 قياس الأداء

### قبل التفعيل

```php
// أضف هذا في بداية public/index.php
define('LARAVEL_START', microtime(true));

// وهذا في نهاية الصفحة (في blade)
// {{ round((microtime(true) - LARAVEL_START) * 1000) }}ms
```

### مقارنة النتائج

| الصفحة | بدون OPcache | مع OPcache |
|--------|--------------|------------|
| Dashboard | ~300ms | ~100ms |
| قائمة الموظفين | ~400ms | ~150ms |
| قائمة الطلبات | ~500ms | ~180ms |

---

## ⚠️ ملاحظات مهمة

### للتطوير (Development)
```ini
opcache.validate_timestamps=1
opcache.revalidate_freq=2
```
هذا يسمح برؤية التغييرات فوراً عند تعديل الكود.

### للإنتاج (Production)
```ini
opcache.validate_timestamps=0
opcache.revalidate_freq=0
```
هذا يعطي أفضل أداء، لكن يجب مسح الـ cache بعد كل تحديث:
```bash
php artisan opcache:clear
# أو
sudo service php-fpm restart
```

---

## 🔧 أوامر مفيدة

### مسح OPcache
```php
opcache_reset();
```

### عرض الحالة
```php
opcache_get_status();
```

### عرض الإعدادات
```php
opcache_get_configuration();
```

---

## ✅ قائمة التحقق

- [ ] فتح php.ini
- [ ] إضافة إعدادات OPcache
- [ ] حفظ الملف
- [ ] إعادة تشغيل Laragon
- [ ] التحقق بـ `php -m`
- [ ] اختبار الموقع
- [ ] قياس الأداء

---

## ➡️ الخطوة التالية

بعد تفعيل OPcache، انتقل إلى:
**`02-database-indexes.md`** - إضافة Indexes لقاعدة البيانات

---

*تحسين متوقع: 60-70% ⚡⚡⚡*
