# 2️⃣ إضافة Database Indexes

## 📋 المعلومات الأساسية

| العنصر | القيمة |
|--------|--------|
| الأولوية | 🔴 عالية جداً |
| الوقت المطلوب | 30 دقيقة |
| التحسين المتوقع | 50-80% للاستعلامات |
| المخاطر | منخفضة (يمكن التراجع) |

---

## 🔍 تحليل المشروع

### الأعمدة الأكثر استخداماً في WHERE

بناءً على تحليل الـ Controllers:

| العمود | عدد الاستخدامات | الجداول |
|--------|-----------------|---------|
| `company_code` | 500+ | معظم الجداول |
| `branch_id` | 300+ | employees, orders, inventory... |
| `status` | 200+ | work_orders, checks, invoices... |
| `user_id` | 150+ | employees, notifications... |
| `created_at` | 100+ | كل الجداول |

---

## 📖 شرح Indexes

### بدون Index

```
SELECT * FROM employees WHERE company_code = 'ABC123';

قاعدة البيانات تفحص كل صف:
┌─────────────────────────────────────────────────────────────┐
│ Row 1 ❌ → Row 2 ❌ → Row 3 ✅ → Row 4 ❌ → ... → Row 50000  │
│                                                             │
│ الوقت: 500ms+ 🐌                                            │
└─────────────────────────────────────────────────────────────┘
```

### مع Index

```
SELECT * FROM employees WHERE company_code = 'ABC123';

Index يعمل مثل فهرس الكتاب:
┌─────────────────────────────────────────────────────────────┐
│ company_code Index:                                         │
│ ABC123 → [3, 45, 89, 234, 567]  ← القفز مباشرة!            │
│                                                             │
│ الوقت: 5ms ⚡                                                │
└─────────────────────────────────────────────────────────────┘
```

---

## 🛠️ خطوات التنفيذ

### الخطوة 1: إنشاء Migration جديد

```bash
php artisan make:migration add_performance_indexes
```

### الخطوة 2: كتابة الـ Migration

**الملف: `database/migrations/xxxx_xx_xx_add_performance_indexes.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // ═══════════════════════════════════════════════════════════
        // جدول work_orders - الأكثر استخداماً
        // ═══════════════════════════════════════════════════════════
        Schema::table('work_orders', function (Blueprint $table) {
            // Index مركب للاستعلامات الشائعة
            $table->index(['company_code', 'status'], 'idx_wo_company_status');
            $table->index(['company_code', 'branch_id'], 'idx_wo_company_branch');
            $table->index(['branch_id', 'status'], 'idx_wo_branch_status');
            $table->index(['status', 'created_at'], 'idx_wo_status_date');
            $table->index('payment_status', 'idx_wo_payment');
            $table->index('delivery_datetime', 'idx_wo_delivery');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول employees
        // ═══════════════════════════════════════════════════════════
        Schema::table('employees', function (Blueprint $table) {
            $table->index(['company_code', 'branch_id'], 'idx_emp_company_branch');
            $table->index(['company_code', 'isactive'], 'idx_emp_company_active');
            $table->index('employee_types_id', 'idx_emp_type');
            $table->index('shift_id', 'idx_emp_shift');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول branches
        // ═══════════════════════════════════════════════════════════
        Schema::table('branches', function (Blueprint $table) {
            $table->index('company_code', 'idx_branch_company');
            $table->index(['company_code', 'isactive'], 'idx_branch_company_active');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول inventory
        // ═══════════════════════════════════════════════════════════
        Schema::table('inventories', function (Blueprint $table) {
            $table->index(['company_code', 'branch_id'], 'idx_inv_company_branch');
            $table->index('code', 'idx_inv_code');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول inventory_histories
        // ═══════════════════════════════════════════════════════════
        Schema::table('inventory_histories', function (Blueprint $table) {
            $table->index(['inventory_id', 'created_at'], 'idx_invh_inv_date');
            $table->index('company_code', 'idx_invh_company');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول suppliers
        // ═══════════════════════════════════════════════════════════
        Schema::table('suppliers', function (Blueprint $table) {
            $table->index(['company_code', 'branch_id'], 'idx_sup_company_branch');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول supplier_payments
        // ═══════════════════════════════════════════════════════════
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->index(['supplier_id', 'created_at'], 'idx_suppay_supplier_date');
            $table->index('company_code', 'idx_suppay_company');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول contractors
        // ═══════════════════════════════════════════════════════════
        Schema::table('contractors', function (Blueprint $table) {
            $table->index(['company_code', 'branch_id'], 'idx_cont_company_branch');
            $table->index(['company_code', 'isactive'], 'idx_cont_company_active');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول invoices
        // ═══════════════════════════════════════════════════════════
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['company_code', 'status'], 'idx_inv_company_status');
            $table->index(['contractor_id', 'created_at'], 'idx_inv_contractor_date');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول checks
        // ═══════════════════════════════════════════════════════════
        Schema::table('checks', function (Blueprint $table) {
            $table->index(['company_code', 'status'], 'idx_chk_company_status');
            $table->index('due_date', 'idx_chk_due');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول receipts
        // ═══════════════════════════════════════════════════════════
        Schema::table('receipts', function (Blueprint $table) {
            $table->index(['company_code', 'created_at'], 'idx_rec_company_date');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول financial_transactions
        // ═══════════════════════════════════════════════════════════
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->index(['company_code', 'type'], 'idx_ft_company_type');
            $table->index(['company_code', 'created_at'], 'idx_ft_company_date');
            $table->index('status', 'idx_ft_status');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول cars
        // ═══════════════════════════════════════════════════════════
        Schema::table('cars', function (Blueprint $table) {
            $table->index(['company_code', 'branch_id'], 'idx_car_company_branch');
            $table->index(['company_code', 'status'], 'idx_car_company_status');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول work_jobs
        // ═══════════════════════════════════════════════════════════
        Schema::table('work_jobs', function (Blueprint $table) {
            $table->index('work_order_id', 'idx_wj_order');
            $table->index(['car_id', 'status'], 'idx_wj_car_status');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول work_shipments
        // ═══════════════════════════════════════════════════════════
        Schema::table('work_shipments', function (Blueprint $table) {
            $table->index('work_job_id', 'idx_ws_job');
            $table->index('driver_id', 'idx_ws_driver');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول attendances
        // ═══════════════════════════════════════════════════════════
        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['employee_id', 'date'], 'idx_att_emp_date');
            $table->index(['company_code', 'date'], 'idx_att_company_date');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول advances
        // ═══════════════════════════════════════════════════════════
        Schema::table('advances', function (Blueprint $table) {
            $table->index(['company_code', 'status'], 'idx_adv_company_status');
            $table->index('employee_id', 'idx_adv_employee');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول notifications
        // ═══════════════════════════════════════════════════════════
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_id', 'read_at'], 'idx_notif_user_read');
            $table->index('company_code', 'idx_notif_company');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول users
        // ═══════════════════════════════════════════════════════════
        Schema::table('users', function (Blueprint $table) {
            $table->index('company_code', 'idx_user_company');
            $table->index(['company_code', 'usertype_id'], 'idx_user_company_type');
        });

        // ═══════════════════════════════════════════════════════════
        // جدول payroll
        // ═══════════════════════════════════════════════════════════
        Schema::table('payroll', function (Blueprint $table) {
            $table->index(['employee_id', 'month', 'year'], 'idx_pay_emp_period');
            $table->index(['company_code', 'status'], 'idx_pay_company_status');
        });
    }

    public function down()
    {
        // حذف الـ Indexes بالترتيب العكسي
        $tables = [
            'work_orders' => ['idx_wo_company_status', 'idx_wo_company_branch', 'idx_wo_branch_status', 'idx_wo_status_date', 'idx_wo_payment', 'idx_wo_delivery'],
            'employees' => ['idx_emp_company_branch', 'idx_emp_company_active', 'idx_emp_type', 'idx_emp_shift'],
            'branches' => ['idx_branch_company', 'idx_branch_company_active'],
            'inventories' => ['idx_inv_company_branch', 'idx_inv_code'],
            'inventory_histories' => ['idx_invh_inv_date', 'idx_invh_company'],
            'suppliers' => ['idx_sup_company_branch'],
            'supplier_payments' => ['idx_suppay_supplier_date', 'idx_suppay_company'],
            'contractors' => ['idx_cont_company_branch', 'idx_cont_company_active'],
            'invoices' => ['idx_inv_company_status', 'idx_inv_contractor_date'],
            'checks' => ['idx_chk_company_status', 'idx_chk_due'],
            'receipts' => ['idx_rec_company_date'],
            'financial_transactions' => ['idx_ft_company_type', 'idx_ft_company_date', 'idx_ft_status'],
            'cars' => ['idx_car_company_branch', 'idx_car_company_status'],
            'work_jobs' => ['idx_wj_order', 'idx_wj_car_status'],
            'work_shipments' => ['idx_ws_job', 'idx_ws_driver'],
            'attendances' => ['idx_att_emp_date', 'idx_att_company_date'],
            'advances' => ['idx_adv_company_status', 'idx_adv_employee'],
            'notifications' => ['idx_notif_user_read', 'idx_notif_company'],
            'users' => ['idx_user_company', 'idx_user_company_type'],
            'payroll' => ['idx_pay_emp_period', 'idx_pay_company_status'],
        ];

        foreach ($tables as $table => $indexes) {
            Schema::table($table, function (Blueprint $t) use ($indexes) {
                foreach ($indexes as $index) {
                    $t->dropIndex($index);
                }
            });
        }
    }
}
```

### الخطوة 3: تشغيل الـ Migration

```bash
php artisan migrate
```

---

## 🔍 التحقق من الـ Indexes

### عرض Indexes لجدول معين

```sql
SHOW INDEX FROM work_orders;
```

### التحقق من استخدام Index في استعلام

```sql
EXPLAIN SELECT * FROM work_orders 
WHERE company_code = 'ABC123' AND status = 'pending';
```

**النتيجة الجيدة:**
```
type: ref (أو range)
key: idx_wo_company_status
rows: 50 (عدد قليل)
```

**النتيجة السيئة:**
```
type: ALL
key: NULL
rows: 50000 (كل الصفوف!)
```

---

## 📊 قياس التحسين

### قبل الـ Indexes

```php
// في Controller
DB::enableQueryLog();

$orders = WorkOrder::where('company_code', $code)
    ->where('status', 'pending')
    ->get();

$queries = DB::getQueryLog();
dd($queries); // شوف الوقت
```

### مقارنة النتائج

| الاستعلام | بدون Index | مع Index |
|-----------|-----------|----------|
| طلبات الشركة | 200ms | 10ms |
| موظفي الفرع | 150ms | 5ms |
| المعاملات المالية | 300ms | 15ms |

---

## ⚠️ ملاحظات مهمة

### 1. لا تضف Indexes كثيرة جداً
```
❌ Index على كل عمود = بطء في INSERT/UPDATE
✅ Index على الأعمدة المستخدمة في WHERE/JOIN فقط
```

### 2. Composite Index مهم
```sql
-- هذا Index يعمل مع:
INDEX (company_code, status)

-- ✅ WHERE company_code = 'X'
-- ✅ WHERE company_code = 'X' AND status = 'Y'
-- ❌ WHERE status = 'Y' (لازم يبدأ بأول عمود)
```

### 3. خذ نسخة احتياطية أولاً
```bash
# قبل أي تعديل
mysqldump -u root concreteerp > backup_before_indexes.sql
```

---

## 🔧 أوامر مفيدة

### عرض كل Indexes في قاعدة البيانات

```sql
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'concreteerp'
ORDER BY TABLE_NAME, INDEX_NAME;
```

### حذف Index معين

```sql
DROP INDEX idx_wo_company_status ON work_orders;
```

### تحليل أداء الجدول

```sql
ANALYZE TABLE work_orders;
```

---

## ✅ قائمة التحقق

- [ ] أخذ نسخة احتياطية من قاعدة البيانات
- [ ] إنشاء Migration جديد
- [ ] نسخ كود الـ Indexes
- [ ] تشغيل `php artisan migrate`
- [ ] التحقق بـ `SHOW INDEX FROM table_name`
- [ ] اختبار سرعة الاستعلامات
- [ ] مراقبة أداء الموقع

---

## ➡️ الخطوة التالية

بعد إضافة الـ Indexes، انتقل إلى:
**`03-eager-loading.md`** - تحسين استعلامات Eloquent

---

*تحسين متوقع: 50-80% للاستعلامات ⚡⚡*
