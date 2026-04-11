# 3️⃣ تحسين Eager Loading

## 📋 المعلومات الأساسية

| العنصر | القيمة |
|--------|--------|
| الأولوية | 🟠 متوسطة-عالية |
| الوقت المطلوب | 1-2 ساعة |
| التحسين المتوقع | 40-60% |
| المخاطر | منخفضة |

---

## 🔍 تحليل المشروع

### إحصائيات الاستعلامات

| النوع | العدد |
|-------|-------|
| استعلامات بدون `with()` | ~250 |
| استعلامات مع `with()` | ~550 |
| Controllers تحتاج تحسين | 38 |

### أكثر Controllers تحتاج تحسين

| Controller | استعلامات بدون with() |
|------------|----------------------|
| CompanyBranchController | 33 |
| WarehouseController | 30 |
| SubscriptionController | 17 |
| CarsController | 18 |
| EmployeeController | 14 |

---

## 📖 شرح مشكلة N+1

### ❌ الكود السيء (N+1 Problem)

```php
// في Controller
$employees = Employee::where('company_code', $code)->get();

// في Blade
@foreach($employees as $employee)
    {{ $employee->branch->branch_name }}     // استعلام!
    {{ $employee->employeeType->name }}      // استعلام آخر!
    {{ $employee->shift->name }}             // استعلام ثالث!
@endforeach
```

**النتيجة:**
```
إذا عندك 100 موظف:
1 + (100 × 3) = 301 استعلام! 😱

الوقت: 3-5 ثواني 🐌
```

### ✅ الكود الصحيح (Eager Loading)

```php
// في Controller
$employees = Employee::where('company_code', $code)
    ->with(['branch', 'employeeType', 'shift'])
    ->get();

// في Blade (نفس الكود!)
@foreach($employees as $employee)
    {{ $employee->branch->branch_name }}
    {{ $employee->employeeType->name }}
    {{ $employee->shift->name }}
@endforeach
```

**النتيجة:**
```
4 استعلامات فقط:
1. SELECT * FROM employees WHERE company_code = 'X'
2. SELECT * FROM branches WHERE id IN (1, 2, 3...)
3. SELECT * FROM employee_types WHERE id IN (1, 2...)
4. SELECT * FROM shift_times WHERE id IN (1, 2, 3...)

الوقت: 50-100ms ⚡
```

---

## 🛠️ خطوات التنفيذ

### المرحلة 1: تفعيل كشف N+1 (للتطوير)

**الملف: `app/Providers/AppServiceProvider.php`**

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // كشف N+1 في بيئة التطوير فقط
        if (config('app.debug')) {
            Model::preventLazyLoading();
        }
    }
}
```

**ملاحظة:** هذا سيُظهر خطأ عند حدوث N+1، مما يساعدك على اكتشافها.

---

### المرحلة 2: إصلاح Controllers الرئيسية

#### 2.1 EmployeeController

**الملف: `app/Http/Controllers/EmployeeController.php`**

```php
// ❌ قبل
public function show($id)
{
    if ($id == 'ListEmployees') {
        $employees = Employee::where('company_code', Auth::user()->company_code)->get();
        // ...
    }
}

// ✅ بعد
public function show($id)
{
    if ($id == 'ListEmployees') {
        $employees = Employee::where('company_code', Auth::user()->company_code)
            ->with([
                'Branchesname',           // الفرع
                'employeeType',           // نوع الموظف
                'shift',                  // الشفت الأساسي
                'activeShifts.shift',     // الشفتات النشطة
                'user'                    // المستخدم المرتبط
            ])
            ->get();
        // ...
    }
}
```

#### 2.2 WarehouseController

**الملف: `app/Http/Controllers/WarehouseController.php`**

```php
// ❌ قبل
$allmaterials = Inventory::where('company_code', auth()->user()->company_code)->get();

// ✅ بعد
$allmaterials = Inventory::where('company_code', auth()->user()->company_code)
    ->with(['companyName', 'branch', 'measurementUnit'])
    ->get();
```

#### 2.3 CompanyBranchController

**الملف: `app/Http/Controllers/CompanyBranchController.php`**

```php
// ❌ قبل
$orders = WorkOrder::where('branch_id', $branchId)->get();

// ✅ بعد
$orders = WorkOrder::where('branch_id', $branchId)
    ->with([
        'company',
        'branch',
        'concreteMix',
        'creator',
        'latestStage',
        'jobs.car',
        'jobs.driver'
    ])
    ->get();
```

#### 2.4 ContractorController

**الملف: `app/Http/Controllers/ContractorController.php`**

```php
// ❌ قبل
$contractors = Contractor::where('company_code', $code)->get();

// ✅ بعد
$contractors = Contractor::where('company_code', $code)
    ->with(['branch', 'invoices', 'checks', 'receipts'])
    ->get();
```

#### 2.5 CarsController

**الملف: `app/Http/Controllers/CarsController.php`**

```php
// ❌ قبل
$cars = Cars::where('company_code', $code)->get();

// ✅ بعد
$cars = Cars::where('company_code', $code)
    ->with([
        'branch',
        'carType',
        'currentDriver.employee',
        'maintenances' => fn($q) => $q->latest()->limit(5)
    ])
    ->get();
```

---

### المرحلة 3: تحسين Models بـ $with الافتراضي

**للعلاقات التي تُستخدم دائماً:**

```php
// app/Models/Employee.php
class Employee extends Model
{
    // تحميل تلقائي لهذه العلاقات
    protected $with = ['employeeType', 'Branchesname'];
    
    // العلاقات
    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'employee_types_id');
    }
    
    public function Branchesname()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
```

**⚠️ تحذير:** استخدم `$with` بحذر! فقط للعلاقات المستخدمة في 90%+ من الحالات.

---

### المرحلة 4: تحسين الاستعلامات المتكررة

#### استخدام withCount بدلاً من count()

```php
// ❌ قبل (N+1)
@foreach($branches as $branch)
    {{ $branch->employees->count() }}  // استعلام لكل فرع!
@endforeach

// ✅ بعد
$branches = Branch::withCount('employees')->get();

@foreach($branches as $branch)
    {{ $branch->employees_count }}  // لا استعلام إضافي
@endforeach
```

#### استخدام withSum للمجاميع

```php
// ❌ قبل
@foreach($employees as $employee)
    {{ $employee->advances->sum('amount') }}  // استعلام!
@endforeach

// ✅ بعد
$employees = Employee::withSum('advances', 'amount')->get();

@foreach($employees as $employee)
    {{ $employee->advances_sum_amount }}
@endforeach
```

---

## 📋 قائمة الملفات للتحسين

### الأولوية العالية (الأكثر استخداماً)

| الملف | التحسينات المطلوبة |
|-------|-------------------|
| `EmployeeController.php` | إضافة with() في 5 methods |
| `WarehouseController.php` | إضافة with() في 8 methods |
| `CompanyBranchController.php` | إضافة with() في 10 methods |
| `WorkOrderController.php` | تحسين with() الموجود |
| `ContractorController.php` | إضافة with() في 4 methods |

### الأولوية المتوسطة

| الملف | التحسينات المطلوبة |
|-------|-------------------|
| `CarsController.php` | إضافة with() في 6 methods |
| `SubscriptionController.php` | إضافة with() في 5 methods |
| `AdvanceController.php` | إضافة with() في 4 methods |
| `AttendanceController.php` | إضافة with() في 3 methods |

### الأولوية المنخفضة

| الملف | التحسينات المطلوبة |
|-------|-------------------|
| `PayrollController.php` | إضافة with() في 2 methods |
| `ReportController.php` | إضافة with() في 2 methods |
| `NotificationController.php` | إضافة with() في 1 method |

---

## 🔍 أداة كشف N+1

### تثبيت Laravel Debugbar

```bash
composer require barryvdh/laravel-debugbar --dev
```

### استخدامه

1. افتح أي صفحة في المتصفح
2. ستظهر شريط في الأسفل
3. اضغط على "Queries"
4. شوف عدد الاستعلامات المكررة

### مثال على النتيجة

```
❌ سيء:
Queries: 156 (duplicates: 150)
Time: 2.5s

✅ جيد:
Queries: 6 (duplicates: 0)
Time: 0.1s
```

---

## 📊 قياس التحسين

### قبل التحسين

```php
// أضف في بداية Controller method
DB::enableQueryLog();

// ... الكود ...

// في النهاية
$queries = DB::getQueryLog();
Log::info('Query Count: ' . count($queries));
Log::info('Queries: ', $queries);
```

### مقارنة النتائج

| الصفحة | قبل | بعد |
|--------|-----|-----|
| قائمة الموظفين | 150 query / 2s | 5 query / 0.1s |
| قائمة الطلبات | 200 query / 3s | 8 query / 0.15s |
| تقرير المالية | 300 query / 4s | 12 query / 0.2s |

---

## ⚠️ ملاحظات مهمة

### 1. لا تحمّل كل شيء!

```php
// ❌ سيء - تحميل كل العلاقات
$employee = Employee::with([
    'branch', 'company', 'shifts', 'advances', 
    'payrolls', 'attendances', 'leaves', 'bonuses'
])->find($id);

// ✅ جيد - فقط ما تحتاجه
$employee = Employee::with(['branch', 'employeeType'])->find($id);
```

### 2. استخدم select() لتقليل البيانات

```php
// ✅ تحميل أعمدة محددة فقط
$employees = Employee::select('id', 'fullname', 'branch_id', 'employee_types_id')
    ->with([
        'branch:id,branch_name',
        'employeeType:id,name'
    ])
    ->get();
```

### 3. استخدم Lazy Loading للعلاقات الكبيرة

```php
// للتقارير الكبيرة
Employee::where('company_code', $code)
    ->with(['branch'])
    ->chunk(100, function ($employees) {
        foreach ($employees as $employee) {
            // معالجة
        }
    });
```

---

## ✅ قائمة التحقق

- [ ] تفعيل `preventLazyLoading()` في التطوير
- [ ] تثبيت Laravel Debugbar
- [ ] مراجعة EmployeeController
- [ ] مراجعة WarehouseController
- [ ] مراجعة CompanyBranchController
- [ ] مراجعة ContractorController
- [ ] مراجعة CarsController
- [ ] اختبار كل صفحة
- [ ] قياس عدد الاستعلامات

---

## ➡️ الخطوة التالية

بعد تحسين Eager Loading، انتقل إلى:
**`04-redis.md`** - تفعيل Redis للـ Cache والـ Sessions

---

*تحسين متوقع: 40-60% ⚡⚡*
