<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialAccount extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * الجدول المرتبط
     */
    protected $table = 'financial_accounts';

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'company_code',
        'branch_id',
        'account_type',
        'account_holder_type',
        'account_holder_id',
        'account_name',
        'account_number',
        'opening_balance',
        'current_balance',
        'credit_limit',
        'currency',
        'is_active',
        'notes',
        'created_by',
    ];

    /**
     * تحويل الأنواع
     */
    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * القيم الافتراضية
     */
    protected $attributes = [
        'opening_balance' => 0,
        'current_balance' => 0,
        'credit_limit' => 0,
        'currency' => 'IQD',
        'is_active' => true,
    ];

    /**
     * أنواع الحسابات المتاحة
     */
    const ACCOUNT_TYPES = [
        'contractor' => 'مقاول',
        'supplier' => 'مورد',
        'direct_client' => 'عميل مباشر',
        'employee' => 'موظف',
        'delegate' => 'مندوب',
        'expense' => 'مصروفات',
        'revenue' => 'إيرادات',
        'bank' => 'بنك',
        'cash' => 'صندوق',
    ];

    // ==================== العلاقات ====================

    /**
     * الشركة التابع لها
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'company_code');
    }

    /**
     * الفرع التابع له
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * المعاملات المالية
     */
    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class, 'account_id');
    }

    /**
     * المدفوعات
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'account_id');
    }

    /**
     * من أنشأ الحساب
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * صاحب الحساب (polymorphic)
     */
    public function accountHolder()
    {
        return $this->morphTo('account_holder', 'account_holder_type', 'account_holder_id');
    }

    // ==================== Scopes ====================

    /**
     * الحسابات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * حسابات شركة معينة
     */
    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    /**
     * حسابات فرع معين
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * حسابات نوع معين
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    /**
     * الحسابات ذات رصيد مدين
     */
    public function scopeWithDebitBalance($query)
    {
        return $query->where('current_balance', '>', 0);
    }

    /**
     * الحسابات ذات رصيد دائن
     */
    public function scopeWithCreditBalance($query)
    {
        return $query->where('current_balance', '<', 0);
    }

    /**
     * البحث
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('account_name', 'like', "%{$term}%")
                ->orWhere('account_number', 'like', "%{$term}%");
        });
    }

    // ==================== Accessors ====================

    /**
     * نوع الحساب بالعربي
     */
    public function getAccountTypeNameAttribute()
    {
        return self::ACCOUNT_TYPES[$this->account_type] ?? $this->account_type;
    }

    /**
     * الرصيد المنسق
     */
    public function getFormattedBalanceAttribute()
    {
        return number_format($this->current_balance, 2) . ' ' . $this->currency;
    }

    /**
     * هل الرصيد مدين
     */
    public function getIsDebitAttribute()
    {
        return $this->current_balance > 0;
    }

    /**
     * هل الرصيد دائن
     */
    public function getIsCreditAttribute()
    {
        return $this->current_balance < 0;
    }

    /**
     * مجموع المدفوعات الواردة
     */
    public function getTotalPaymentsInAttribute()
    {
        return $this->payments()->where('direction', 'in')->sum('amount');
    }

    /**
     * مجموع المدفوعات الصادرة
     */
    public function getTotalPaymentsOutAttribute()
    {
        return $this->payments()->where('direction', 'out')->sum('amount');
    }

    // ==================== Methods ====================

    /**
     * إضافة معاملة (دين/دائن)
     */
    public function addTransaction(float $amount, string $type, array $data = [])
    {
        // تحديد إذا كان دين أو دائن
        $isDebit = in_array($type, ['sale_invoice', 'payment_made', 'salary', 'expense', 'loss']);

        // تحديث الرصيد
        if ($isDebit) {
            $this->current_balance += $amount;
        } else {
            $this->current_balance -= $amount;
        }
        $this->save();

        // إنشاء سجل المعاملة
        return $this->transactions()->create(array_merge([
            'company_code' => $this->company_code,
            'transaction_type' => $type,
            'amount' => $amount,
            'balance_before' => $this->current_balance - ($isDebit ? $amount : -$amount),
            'balance_after' => $this->current_balance,
        ], $data));
    }

    /**
     * إضافة دفعة
     */
    public function addPayment(float $amount, string $direction, array $data = [])
    {
        // تحديث الرصيد
        if ($direction === 'in') {
            $this->current_balance -= $amount; // استلام دفعة يقلل المستحق
        } else {
            $this->current_balance += $amount; // دفع يزيد المستحق
        }
        $this->save();

        // إنشاء سجل الدفعة
        return $this->payments()->create(array_merge([
            'company_code' => $this->company_code,
            'direction' => $direction,
            'amount' => $amount,
        ], $data));
    }

    /**
     * الحصول على كشف حساب
     */
    public function getStatement($from = null, $to = null)
    {
        $query = $this->transactions();

        if ($from) {
            $query->where('created_at', '>=', $from);
        }
        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    /**
     * إعادة حساب الرصيد
     */
    public function recalculateBalance()
    {
        $totalIn = $this->payments()->where('direction', 'in')->sum('amount');
        $totalOut = $this->payments()->where('direction', 'out')->sum('amount');

        $this->current_balance = $this->opening_balance + $totalOut - $totalIn;
        $this->save();

        return $this->current_balance;
    }
}
