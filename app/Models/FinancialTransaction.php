<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialTransaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * الجدول المرتبط
     */
    protected $table = 'financial_transactions';

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'company_code',
        'account_id',
        'transaction_type',
        'reference_type',
        'reference_id',
        'amount',
        'balance_before',
        'balance_after',
        'payment_method',
        'description',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    /**
     * تحويل الأنواع
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * أنواع المعاملات
     */
    const TRANSACTION_TYPES = [
        'sale_invoice' => 'فاتورة مبيعات',
        'purchase_invoice' => 'فاتورة مشتريات',
        'payment_received' => 'دفعة مستلمة',
        'payment_made' => 'دفعة مدفوعة',
        'salary' => 'راتب',
        'commission' => 'عمولة',
        'expense' => 'مصروف',
        'loss' => 'خسارة',
        'refund' => 'استرداد',
        'adjustment' => 'تسوية',
        'transfer' => 'تحويل',
        'opening_balance' => 'رصيد افتتاحي',
    ];

    /**
     * طرق الدفع
     */
    const PAYMENT_METHODS = [
        'cash' => 'نقداً',
        'bank_transfer' => 'تحويل بنكي',
        'check' => 'شيك',
        'credit' => 'آجل',
        'card' => 'بطاقة',
    ];

    /**
     * حالات المعاملة
     */
    const STATUSES = [
        'pending' => 'معلقة',
        'approved' => 'معتمدة',
        'rejected' => 'مرفوضة',
        'cancelled' => 'ملغية',
    ];

    // ==================== العلاقات ====================

    /**
     * الشركة
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'company_code');
    }

    /**
     * الحساب
     */
    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'account_id');
    }

    /**
     * المرجع (polymorphic)
     */
    public function reference()
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }

    /**
     * من أنشأ
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * من اعتمد
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ==================== Scopes ====================

    /**
     * معاملات شركة
     */
    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    /**
     * معاملات حساب
     */
    public function scopeForAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    /**
     * معاملات نوع معين
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * المعتمدة
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * في فترة
     */
    public function scopeBetween($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * البحث
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('description', 'like', "%{$term}%")
                ->orWhere('notes', 'like', "%{$term}%");
        });
    }

    // ==================== Accessors ====================

    /**
     * نوع المعاملة بالعربي
     */
    public function getTransactionTypeNameAttribute()
    {
        return self::TRANSACTION_TYPES[$this->transaction_type] ?? $this->transaction_type;
    }

    /**
     * طريقة الدفع بالعربي
     */
    public function getPaymentMethodNameAttribute()
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * الحالة بالعربي
     */
    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * المبلغ المنسق
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    /**
     * هل مدين
     */
    public function getIsDebitAttribute()
    {
        return in_array($this->transaction_type, [
            'sale_invoice',
            'payment_made',
            'salary',
            'expense',
            'loss'
        ]);
    }

    /**
     * هل دائن
     */
    public function getIsCreditAttribute()
    {
        return in_array($this->transaction_type, [
            'purchase_invoice',
            'payment_received',
            'commission',
            'refund'
        ]);
    }

    // ==================== Methods ====================

    /**
     * اعتماد المعاملة
     */
    public function approve($userId)
    {
        $this->status = 'approved';
        $this->approved_by = $userId;
        $this->approved_at = now();
        $this->save();

        return $this;
    }

    /**
     * رفض المعاملة
     */
    public function reject($userId, $reason = null)
    {
        $this->status = 'rejected';
        $this->approved_by = $userId;
        $this->approved_at = now();
        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . "سبب الرفض: {$reason}";
        }
        $this->save();

        return $this;
    }

    /**
     * إلغاء المعاملة
     */
    public function cancel($reason = null)
    {
        $this->status = 'cancelled';
        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . "سبب الإلغاء: {$reason}";
        }
        $this->save();

        // عكس التأثير على الحساب
        if ($this->account) {
            if ($this->is_debit) {
                $this->account->current_balance -= $this->amount;
            } else {
                $this->account->current_balance += $this->amount;
            }
            $this->account->save();
        }

        return $this;
    }
}
