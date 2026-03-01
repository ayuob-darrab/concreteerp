<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Contractor extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    // ═══════════════════════════════════════════════════════════════
    // الخصائص
    // ═══════════════════════════════════════════════════════════════

    protected $fillable = [
        // معلومات أساسية
        'code',
        'contract_name',
        'contract_name_en',
        'contract_adminstarter',
        'license_number',
        'tax_number',

        // معلومات الاتصال
        'phone1',
        'phone2',
        'whatsapp',
        'email',
        'website',

        // العنوان والموقع
        'address',
        'city_id',
        'latitude',
        'longitude',

        // الربط بالنظام
        'company_code',
        'branch_id',
        'user_id',

        // المعلومات المالية
        'opening_balance',
        'opening_balance_type',
        'credit_limit',
        'payment_terms',
        'discount_percentage',
        'price_category_id',
        'currency',

        // التصنيف
        'contractor_type',
        'classification',
        'rating',

        // الحالة
        'isactive',
        'is_blocked',
        'block_reason',
        'blocked_at',
        'blocked_by',

        // الملفات
        'logo',
        'contract_file',
        'documents',

        // ملاحظات
        'internal_notes',
        'note',

        // التتبع
        'createdate',
        'last_order_date',
        'total_orders',
        'total_quantity',
        'total_amount',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'rating' => 'decimal:1',
        'total_quantity' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'isactive' => 'boolean',
        'is_blocked' => 'boolean',
        'documents' => 'array',
        'createdate' => 'date',
        'last_order_date' => 'date',
        'blocked_at' => 'datetime',
    ];

    protected $dates = [
        'createdate',
        'last_order_date',
        'blocked_at',
        'deleted_at',
    ];

    // ═══════════════════════════════════════════════════════════════
    // Boot
    // ═══════════════════════════════════════════════════════════════

    protected static function booted()
    {
        // توليد كود تلقائي عند الإنشاء
        static::creating(function (Contractor $contractor) {
            if (empty($contractor->code)) {
                $contractor->code = self::generateCode($contractor->company_code);
            }
            $contractor->createdate = $contractor->createdate ?? today();
        });

        // إنشاء حساب مالي تلقائياً
        static::created(function (Contractor $contractor) {
            $contractor->account()->create([
                'company_code' => $contractor->company_code,
                'branch_id' => $contractor->branch_id,
                'opening_balance' => $contractor->opening_balance ?? 0,
                'opening_balance_type' => $contractor->opening_balance_type ?? 'debit',
                'current_balance' => $contractor->opening_balance ?? 0,
                'currency' => $contractor->currency ?? 'IQD',
            ]);
        });
    }

    // ═══════════════════════════════════════════════════════════════
    // العلاقات الأساسية
    // ═══════════════════════════════════════════════════════════════

    /**
     * الشركة
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    /**
     * الفرع
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * حساب المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * الحساب المالي
     */
    public function account()
    {
        return $this->hasOne(ContractorAccount::class);
    }

    /**
     * طلبات العمل
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    /**
     * الفواتير
     */
    public function invoices()
    {
        return $this->hasManyThrough(
            Invoice::class,
            ContractorAccount::class,
            'contractor_id',
            'account_id'
        );
    }

    /**
     * الشيكات
     */
    public function checks()
    {
        return $this->hasMany(Check::class, 'drawer_id')
            ->where('drawer_type', 'contractor');
    }

    /**
     * من قام بإنشائه
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * من قام بحظره
     */
    public function blocker()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    // ═══════════════════════════════════════════════════════════════
    // Scopes
    // ═══════════════════════════════════════════════════════════════

    /**
     * فلتر حسب الشركة
     */
    public function scopeForCompany($query, string $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    /**
     * فلتر حسب الفرع
     */
    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * المقاولين النشطين
     */
    public function scopeActive($query)
    {
        return $query->where('isactive', true)->where('is_blocked', false);
    }

    /**
     * المقاولين غير النشطين
     */
    public function scopeInactive($query)
    {
        return $query->where('isactive', false);
    }

    /**
     * المقاولين المحظورين
     */
    public function scopeBlocked($query)
    {
        return $query->where('is_blocked', true);
    }

    /**
     * فلتر حسب التصنيف
     */
    public function scopeClassification($query, string $classification)
    {
        return $query->where('classification', $classification);
    }

    /**
     * مع حسابهم
     */
    public function scopeWithUserAccount($query)
    {
        return $query->whereHas('user');
    }

    /**
     * بدون حساب مستخدم
     */
    public function scopeWithoutUserAccount($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * البحث
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('contract_name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('phone1', 'like', "%{$search}%")
                ->orWhere('phone2', 'like', "%{$search}%")
                ->orWhere('contract_adminstarter', 'like', "%{$search}%");
        });
    }

    /**
     * مع الرصيد المستحق
     */
    public function scopeWithBalance($query)
    {
        return $query->addSelect([
            'current_balance' => ContractorAccount::select('current_balance')
                ->whereColumn('contractor_id', 'contractors.id')
                ->limit(1)
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // Accessors
    // ═══════════════════════════════════════════════════════════════

    /**
     * الرصيد الحالي
     */
    public function getCurrentBalanceAttribute()
    {
        return $this->account?->current_balance ?? 0;
    }

    /**
     * الائتمان المتاح
     */
    public function getAvailableCreditAttribute(): float
    {
        $used = $this->current_balance;
        $limit = $this->credit_limit ?? PHP_INT_MAX;
        return max(0, $limit - $used);
    }

    /**
     * هل تجاوز حد الائتمان
     */
    public function getIsOverCreditLimitAttribute(): bool
    {
        if (!$this->credit_limit) return false;
        return $this->current_balance > $this->credit_limit;
    }

    /**
     * الاسم الكامل
     */
    public function getFullNameAttribute(): string
    {
        return $this->contract_name . ($this->code ? " ({$this->code})" : '');
    }

    /**
     * حالة النشاط
     */
    public function getStatusAttribute(): string
    {
        if ($this->is_blocked) return 'blocked';
        if (!$this->isactive) return 'inactive';
        return 'active';
    }

    /**
     * نص الحالة
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'blocked' => 'محظور',
            'inactive' => 'غير نشط',
            'active' => 'نشط',
            default => 'غير معروف',
        };
    }

    /**
     * لون الحالة
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'blocked' => 'red',
            'inactive' => 'gray',
            'active' => 'green',
            default => 'gray',
        };
    }

    // ═══════════════════════════════════════════════════════════════
    // Methods
    // ═══════════════════════════════════════════════════════════════

    /**
     * توليد كود فريد
     */
    public static function generateCode(string $companyCode): string
    {
        $prefix = 'CON';
        $lastContractor = self::where('company_code', $companyCode)
            ->where('code', 'like', "{$prefix}-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastContractor
            ? (int)substr($lastContractor->code, -5) + 1
            : 1;

        return sprintf('%s-%05d', $prefix, $nextNumber);
    }

    /**
     * حظر المقاول
     */
    public function block(string $reason): bool
    {
        return $this->update([
            'is_blocked' => true,
            'block_reason' => $reason,
            'blocked_at' => now(),
            'blocked_by' => auth()->id(),
        ]);
    }

    /**
     * إلغاء حظر المقاول
     */
    public function unblock(): bool
    {
        return $this->update([
            'is_blocked' => false,
            'block_reason' => null,
            'blocked_at' => null,
            'blocked_by' => null,
        ]);
    }

    /**
     * تفعيل
     */
    public function activate(): bool
    {
        return $this->update(['isactive' => true]);
    }

    /**
     * إلغاء تفعيل
     */
    public function deactivate(): bool
    {
        return $this->update(['isactive' => false]);
    }

    /**
     * تحديث إحصائيات الطلبات
     */
    public function updateOrderStatistics(): void
    {
        $stats = $this->workOrders()
            ->selectRaw('
                COUNT(*) as total_orders,
                COALESCE(SUM(quantity), 0) as total_quantity,
                COALESCE(SUM(final_price), 0) as total_amount,
                MAX(request_date) as last_order_date
            ')
            ->first();

        $this->update([
            'total_orders' => $stats->total_orders,
            'total_quantity' => $stats->total_quantity,
            'total_amount' => $stats->total_amount,
            'last_order_date' => $stats->last_order_date,
        ]);
    }

    /**
     * التحقق من إمكانية الطلب
     */
    public function canPlaceOrder(): bool
    {
        if ($this->is_blocked) return false;
        if (!$this->isactive) return false;
        if ($this->is_over_credit_limit) return false;

        return true;
    }

    /**
     * الحصول على رقم الهاتف للإشعارات
     */
    public function routeNotificationForWhatsApp()
    {
        return $this->whatsapp ?? $this->phone1;
    }

    /**
     * البريد الإلكتروني للإشعارات
     */
    public function routeNotificationForMail()
    {
        return $this->email;
    }
}
