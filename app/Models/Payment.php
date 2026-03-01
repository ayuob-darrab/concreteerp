<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * الجدول المرتبط
     */
    protected $table = 'payments';

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'company_code',
        'account_id',
        'direction',
        'payment_type',
        'amount',
        'currency',
        'payment_method',
        'reference_number',
        'receipt_number',
        'check_number',
        'check_date',
        'bank_name',
        'description',
        'notes',
        'received_by',
        'received_at',
        'created_by',
    ];

    /**
     * تحويل الأنواع
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'check_date' => 'date',
        'received_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * القيم الافتراضية
     */
    protected $attributes = [
        'currency' => 'IQD',
        'payment_type' => 'full',
        'payment_method' => 'cash',
    ];

    /**
     * أنواع الدفعات
     */
    const PAYMENT_TYPES = [
        'full' => 'كاملة',
        'partial' => 'جزئية',
        'advance' => 'مقدمة',
        'final' => 'نهائية',
    ];

    /**
     * الاتجاهات
     */
    const DIRECTIONS = [
        'in' => 'وارد',
        'out' => 'صادر',
    ];

    /**
     * طرق الدفع
     */
    const PAYMENT_METHODS = [
        'cash' => 'نقداً',
        'bank_transfer' => 'تحويل بنكي',
        'check' => 'شيك',
        'card' => 'بطاقة',
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
     * من استلم
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * من أنشأ
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * المعاملة المالية المرتبطة
     */
    public function transaction()
    {
        return $this->morphOne(FinancialTransaction::class, 'reference', 'reference_type', 'reference_id');
    }

    // ==================== Scopes ====================

    /**
     * دفعات شركة
     */
    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    /**
     * دفعات حساب
     */
    public function scopeForAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    /**
     * الدفعات الواردة
     */
    public function scopeIncoming($query)
    {
        return $query->where('direction', 'in');
    }

    /**
     * الدفعات الصادرة
     */
    public function scopeOutgoing($query)
    {
        return $query->where('direction', 'out');
    }

    /**
     * بطريقة دفع معينة
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * الشيكات
     */
    public function scopeChecks($query)
    {
        return $query->where('payment_method', 'check');
    }

    /**
     * الشيكات المستحقة
     */
    public function scopeDueChecks($query)
    {
        return $query->where('payment_method', 'check')
            ->where('check_date', '<=', now());
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
            $q->where('receipt_number', 'like', "%{$term}%")
                ->orWhere('reference_number', 'like', "%{$term}%")
                ->orWhere('check_number', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    // ==================== Accessors ====================

    /**
     * نوع الدفعة بالعربي
     */
    public function getPaymentTypeNameAttribute()
    {
        return self::PAYMENT_TYPES[$this->payment_type] ?? $this->payment_type;
    }

    /**
     * الاتجاه بالعربي
     */
    public function getDirectionNameAttribute()
    {
        return self::DIRECTIONS[$this->direction] ?? $this->direction;
    }

    /**
     * طريقة الدفع بالعربي
     */
    public function getPaymentMethodNameAttribute()
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * المبلغ المنسق
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    /**
     * هل شيك
     */
    public function getIsCheckAttribute()
    {
        return $this->payment_method === 'check';
    }

    /**
     * هل الشيك مستحق
     */
    public function getIsCheckDueAttribute()
    {
        if (!$this->is_check || !$this->check_date) {
            return false;
        }
        return $this->check_date->lte(now());
    }

    // ==================== Methods ====================

    /**
     * توليد رقم إيصال
     */
    public static function generateReceiptNumber($companyCode, $direction = 'in')
    {
        $prefix = $direction === 'in' ? 'RV' : 'PV'; // Receipt Voucher / Payment Voucher
        $year = date('Y');
        $month = date('m');

        $lastReceipt = self::where('company_code', $companyCode)
            ->where('direction', $direction)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastReceipt ? (int)substr($lastReceipt->receipt_number, -4) + 1 : 1;

        return "{$prefix}-{$companyCode}-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * إنشاء دفعة مع المعاملة
     */
    public static function createWithTransaction(array $data)
    {
        $payment = self::create($data);

        // إنشاء معاملة مالية مرتبطة
        $transactionType = $data['direction'] === 'in' ? 'payment_received' : 'payment_made';

        $payment->account->addTransaction($payment->amount, $transactionType, [
            'reference_type' => Payment::class,
            'reference_id' => $payment->id,
            'payment_method' => $payment->payment_method,
            'description' => $payment->description,
            'created_by' => $data['created_by'] ?? null,
        ]);

        return $payment;
    }
}
