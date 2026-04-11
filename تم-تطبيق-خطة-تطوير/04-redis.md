# 4️⃣ تفعيل Redis

## 📋 المعلومات الأساسية

| العنصر | القيمة |
|--------|--------|
| الأولوية | 🟠 متوسطة |
| الوقت المطلوب | 30 دقيقة |
| التحسين المتوقع | 30-50% |
| المخاطر | منخفضة |

---

## 🔍 الوضع الحالي

**ملف `.env` الحالي:**
```env
CACHE_DRIVER=file      ← بطيء (يقرأ من القرص)
SESSION_DRIVER=file    ← بطيء (يقرأ من القرص)
QUEUE_CONNECTION=sync  ← يحجب الطلبات
```

---

## 📖 شرح Redis

### ما هو Redis؟

```
Redis = قاعدة بيانات في الذاكرة (RAM)

┌─────────────────────────────────────────────────────────────┐
│                    مقارنة السرعة                            │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  💾 File Cache (القرص)                                      │
│     └─ سرعة القراءة: 50-100ms                               │
│     └─ يبقى بعد إعادة التشغيل ✅                            │
│                                                             │
│  🗄️ Database Cache                                          │
│     └─ سرعة القراءة: 30-60ms                                │
│     └─ يحتاج استعلام SQL                                    │
│                                                             │
│  🔴 Redis (الذاكرة)                                         │
│     └─ سرعة القراءة: 0.1-1ms ⚡⚡⚡                           │
│     └─ أسرع 100x من الملفات!                                │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### ماذا نخزن في Redis؟

| النوع | الفائدة |
|-------|---------|
| **Sessions** | تسجيل دخول أسرع، دعم عدة سيرفرات |
| **Cache** | تخزين البيانات المتكررة |
| **Queues** | تنفيذ المهام في الخلفية |
| **Rate Limiting** | حماية من الطلبات الكثيرة |

---

## 🛠️ خطوات التنفيذ

### الخطوة 1: تثبيت Redis في Laragon

```
1. افتح Laragon
2. اضغط بزر الماوس الأيمن → Menu
3. اختر: Tools → Quick add
4. اكتب: redis
5. انتظر التحميل والتثبيت
6. Menu → Redis → Start Redis
```

### الخطوة 2: التحقق من تشغيل Redis

```bash
# في Terminal
redis-cli ping
```

**النتيجة المتوقعة:**
```
PONG
```

### الخطوة 3: تثبيت مكتبة PHP

```bash
composer require predis/predis
```

### الخطوة 4: تعديل ملف .env

```env
# ═══════════════════════════════════════════════════════════
# إعدادات Redis
# ═══════════════════════════════════════════════════════════

# تغيير من file إلى redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# إعدادات الاتصال (الافتراضية تعمل مع Laragon)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# قاعدة بيانات Redis لكل خدمة (اختياري للفصل)
REDIS_CACHE_DB=1
REDIS_SESSION_DB=2
REDIS_QUEUE_DB=3
```

### الخطوة 5: تعديل config/database.php (اختياري)

```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'predis'),

    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', 'concreteerp_'),
    ],

    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],

    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
    ],

    'session' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_SESSION_DB', '2'),
    ],
],
```

### الخطوة 6: مسح الـ Cache القديم

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## 💡 استخدام Cache في الكود

### مثال 1: تخزين البيانات المتكررة

```php
// ❌ قبل - كل طلب يسأل قاعدة البيانات
public function index()
{
    $cities = City::all();  // 50ms كل مرة
    return view('form', compact('cities'));
}

// ✅ بعد - يسأل مرة واحدة ويخزن
public function index()
{
    $cities = Cache::remember('all_cities', 3600, function () {
        return City::all();
    });
    // المرة الأولى: 50ms
    // باقي الطلبات: 1ms ⚡
    
    return view('form', compact('cities'));
}
```

### مثال 2: تخزين إعدادات النظام

```php
// في AppServiceProvider أو Helper
public static function getSettings()
{
    return Cache::remember('system_settings', 86400, function () {
        return Setting::all()->pluck('value', 'key')->toArray();
    });
}

// مسح الـ Cache عند تحديث الإعدادات
public function updateSettings(Request $request)
{
    // ... حفظ الإعدادات ...
    
    Cache::forget('system_settings');  // مسح الـ Cache
}
```

### مثال 3: تخزين قوائم الـ Dropdown

```php
// Helper Class
class CacheHelper
{
    public static function getEmployeeTypes()
    {
        return Cache::remember('employee_types', 3600, function () {
            return EmployeeType::all();
        });
    }
    
    public static function getMeasurementUnits()
    {
        return Cache::remember('measurement_units', 3600, function () {
            return MeasurementUnit::all();
        });
    }
    
    public static function getCities()
    {
        return Cache::remember('cities', 86400, function () {
            return City::all();
        });
    }
    
    // مسح كل الـ Cache
    public static function clearAll()
    {
        Cache::forget('employee_types');
        Cache::forget('measurement_units');
        Cache::forget('cities');
        Cache::forget('system_settings');
    }
}
```

### مثال 4: تخزين مؤقت لكل شركة

```php
// تخزين بيانات خاصة بكل شركة
public function getBranches($companyCode)
{
    $cacheKey = "branches_{$companyCode}";
    
    return Cache::remember($cacheKey, 1800, function () use ($companyCode) {
        return Branch::where('company_code', $companyCode)
            ->where('isactive', true)
            ->get();
    });
}

// مسح عند إضافة/تعديل فرع
public function storeBranch(Request $request)
{
    // ... حفظ الفرع ...
    
    Cache::forget("branches_{$request->company_code}");
}
```

---

## 📋 البيانات المقترح تخزينها

### بيانات ثابتة (Cache طويل - 24 ساعة)

| البيانات | مفتاح الـ Cache | المدة |
|----------|----------------|-------|
| المدن | `cities` | 86400 |
| أنواع الموظفين | `employee_types` | 86400 |
| وحدات القياس | `measurement_units` | 86400 |
| أنواع المستخدمين | `user_types` | 86400 |
| إعدادات النظام | `system_settings` | 86400 |

### بيانات متغيرة (Cache قصير - 30 دقيقة)

| البيانات | مفتاح الـ Cache | المدة |
|----------|----------------|-------|
| فروع الشركة | `branches_{company_code}` | 1800 |
| موظفي الشركة | `employees_{company_code}` | 1800 |
| خلطات الخرسانة | `concrete_mixes_{company_code}` | 1800 |

### بيانات لا تُخزن

- الطلبات (work_orders) - تتغير باستمرار
- المعاملات المالية - حساسة
- الإشعارات - تحتاج تحديث فوري

---

## 🔧 أوامر Redis المفيدة

### في Terminal

```bash
# الاتصال بـ Redis
redis-cli

# عرض كل المفاتيح
KEYS *

# عرض مفاتيح معينة
KEYS concreteerp_cache:*

# حذف مفتاح
DEL concreteerp_cache:cities

# عرض قيمة
GET concreteerp_cache:cities

# مسح كل شيء (حذر!)
FLUSHALL

# معلومات الذاكرة
INFO memory
```

### في Laravel

```php
// مسح كل الـ Cache
Cache::flush();

// مسح مفتاح معين
Cache::forget('cities');

// التحقق من وجود مفتاح
if (Cache::has('cities')) {
    // ...
}

// الحصول على قيمة أو default
$value = Cache::get('key', 'default');

// تخزين للأبد
Cache::forever('key', $value);

// تخزين مع Tags (للمسح الجماعي)
Cache::tags(['company', 'ABC123'])->put('branches', $branches, 1800);
Cache::tags(['company', 'ABC123'])->flush();  // مسح كل cache الشركة
```

---

## 📊 قياس التحسين

### قبل Redis

```php
// قياس وقت جلب الإعدادات
$start = microtime(true);
$settings = Setting::all()->pluck('value', 'key');
$time = (microtime(true) - $start) * 1000;
// النتيجة: 50-100ms
```

### بعد Redis

```php
$start = microtime(true);
$settings = Cache::remember('settings', 3600, fn() => 
    Setting::all()->pluck('value', 'key')
);
$time = (microtime(true) - $start) * 1000;
// المرة الأولى: 50ms
// باقي الطلبات: 1-2ms ⚡
```

---

## ⚠️ ملاحظات مهمة

### 1. Redis يحتاج ذاكرة

```
تأكد من وجود ذاكرة كافية:
- الحد الأدنى: 512MB RAM لـ Redis
- المقترح: 1-2GB للمشاريع المتوسطة
```

### 2. مسح الـ Cache عند التحديث

```php
// ❌ خطأ شائع - نسيان مسح الـ Cache
public function update(Request $request, City $city)
{
    $city->update($request->all());
    return back()->with('success', 'تم التحديث');
    // المشكلة: الـ Cache القديم لا يزال موجود!
}

// ✅ صحيح
public function update(Request $request, City $city)
{
    $city->update($request->all());
    Cache::forget('cities');  // مسح الـ Cache
    return back()->with('success', 'تم التحديث');
}
```

### 3. استخدم Cache Tags للتنظيم

```php
// تخزين مع Tags
Cache::tags(['company', $companyCode])->put('branches', $branches, 1800);
Cache::tags(['company', $companyCode])->put('employees', $employees, 1800);

// مسح كل cache الشركة دفعة واحدة
Cache::tags(['company', $companyCode])->flush();
```

### 4. Fallback إذا Redis توقف

```php
// في config/cache.php
'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
    ],
    
    // Fallback
    'file' => [
        'driver' => 'file',
        'path' => storage_path('framework/cache/data'),
    ],
],
```

---

## 🚀 Queue للمهام الخلفية

### إعداد Queue Worker

```bash
# تشغيل Worker
php artisan queue:work redis --queue=default

# للإنتاج (مع Supervisor)
php artisan queue:work redis --queue=high,default,low --sleep=3 --tries=3
```

### مثال: إرسال إشعارات في الخلفية

```php
// ❌ قبل - يحجب الطلب
public function createOrder(Request $request)
{
    $order = WorkOrder::create($request->all());
    
    // إرسال إشعار (يأخذ 2-3 ثواني!)
    Notification::send($users, new OrderCreated($order));
    
    return redirect()->route('orders.show', $order);
}

// ✅ بعد - في الخلفية
public function createOrder(Request $request)
{
    $order = WorkOrder::create($request->all());
    
    // إرسال في الخلفية (فوري!)
    dispatch(new SendOrderNotification($order));
    
    return redirect()->route('orders.show', $order);
}
```

---

## ✅ قائمة التحقق

- [ ] تثبيت Redis في Laragon
- [ ] التحقق بـ `redis-cli ping`
- [ ] تثبيت `predis/predis`
- [ ] تعديل `.env`
- [ ] مسح الـ Cache القديم
- [ ] اختبار تسجيل الدخول
- [ ] إضافة Cache للبيانات الثابتة
- [ ] إضافة Cache للبيانات المتكررة
- [ ] اختبار الأداء

---

## ➡️ الخطوة التالية

بعد تفعيل Redis، انتقل إلى:
**`05-cdn.md`** - استخدام CDN للملفات الثابتة

---

*تحسين متوقع: 30-50% ⚡*
