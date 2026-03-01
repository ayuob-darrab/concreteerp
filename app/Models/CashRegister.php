<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashRegister extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * الجدول المرتبط
     */
    protected $table = 'cash_registers';

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'company_code',
        'branch_id',
        'transaction_type',
        'amount',
        'currency',
        'payment_id',
        'financial_transaction_id',
        'opening_balance',
        'closing_balance',
        'description',
        'notes',
        'handled_by',
        'handled_at',
    ];

    /**
     * تحويل الأنواع
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'handled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * القيم الافتراضية
     */
    protected $attributes = [
        'currency' => 'IQD',
        'opening_balance' => 0,
        'closing_balance' => 0,
    ];

    /**
     * أنواع الحركات
     */
    const TRANSACTION_TYPES = [
        'cash_in' => 'إيداع',
        'cash_out' => 'سحب',
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
     * الفرع
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * الدفعة المرتبطة
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * المعاملة المالية المرتبطة
     */
    public function financialTransaction()
    {
        return $this->belongsTo(FinancialTransaction::class, 'financial_transaction_id');
    }

    /**
     * من تعامل
     */
    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    // ==================== Scopes ====================

    /**
     * حركات شركة
     */
    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    /**
     * حركات فرع
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * الإيداعات
     */
    public function scopeCashIn($query)
    {
        return $query->where('transaction_type', 'cash_in');
    }

    /**
     * السحوبات
     */
    public function scopeCashOut($query)
    {
        return $query->where('transaction_type', 'cash_out');
    }

    /**
     * في فترة
     */
    public function scopeBetween($query, $from, $to)
    {
        return $query->whereBetween('handled_at', [$from, $to]);
    }

    /**
     * اليوم
     */
    public function scopeToday($query)
    {
        return $query->whereDate('handled_at', today());
    }

    // ==================== Accessors ====================

    /**
     * نوع الحركة بالعربي
     */
    public function getTransactionTypeNameAttribute()
    {
        return self::TRANSACTION_TYPES[$this->transaction_type] ?? $this->transaction_type;
    }

    /**
     * المبلغ المنسق
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    /**
     * الرصيد الافتتاحي المنسق
     */
    public function getFormattedOpeningBalanceAttribute()
    {
        return number_format($this->opening_balance, 2) . ' ' . $this->currency;
    }

    /**
     * الرصيد الختامي المنسق
     */
    public function getFormattedClosingBalanceAttribute()
    {
        return number_format($this->closing_balance, 2) . ' ' . $this->currency;
    }

    // ==================== Methods ====================

    /**
     * الحصول على الرصيد الحالي لفرع
     */
    public static function getCurrentBalance($branchId)
    {
        $lastEntry = self::where('branch_id', $branchId)
            ->orderBy('handled_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $lastEntry ? $lastEntry->closing_balance : 0;
    }

    /**
     * إضافة حركة جديدة
     */
    public static function addEntry($branchId, $type, $amount, $data = [])
    {
        $currentBalance = self::getCurrentBalance($branchId);

        $newBalance = $type === 'cash_in'
            ? $currentBalance + $amount
            : $currentBalance - $amount;

        // التحقق من كفاية الرصيد للسحب
        if ($type === 'cash_out' && $newBalance < 0) {
            throw new \Exception('رصيد الصندوق غير كافٍ');
        }

        return self::create(array_merge([
            'branch_id' => $branchId,
            'transaction_type' => $type,
            'amount' => $amount,
            'opening_balance' => $currentBalance,
            'closing_balance' => $newBalance,
            'handled_at' => now(),
        ], $data));
    }

    /**
     * ملخص الصندوق اليومي
     */
    public static function getDailySummary($branchId, $date = null)
    {
        $date = $date ?? today();

        $entries = self::where('branch_id', $branchId)
            ->whereDate('handled_at', $date)
            ->get();

        return [
            'date' => $date->format('Y-m-d'),
            'opening_balance' => $entries->first()?->opening_balance ?? 0,
            'closing_balance' => $entries->last()?->closing_balance ?? self::getCurrentBalance($branchId),
            'total_in' => $entries->where('transaction_type', 'cash_in')->sum('amount'),
            'total_out' => $entries->where('transaction_type', 'cash_out')->sum('amount'),
            'transactions_count' => $entries->count(),
        ];
    }

    /**
     * تقرير فترة
     */
    public static function getPeriodReport($branchId, $from, $to)
    {
        $entries = self::where('branch_id', $branchId)
            ->whereBetween('handled_at', [$from, $to])
            ->orderBy('handled_at')
            ->get();

        return [
            'from' => $from,
            'to' => $to,
            'opening_balance' => $entries->first()?->opening_balance ?? 0,
            'closing_balance' => $entries->last()?->closing_balance ?? 0,
            'total_in' => $entries->where('transaction_type', 'cash_in')->sum('amount'),
            'total_out' => $entries->where('transaction_type', 'cash_out')->sum('amount'),
            'net_change' => $entries->where('transaction_type', 'cash_in')->sum('amount')
                - $entries->where('transaction_type', 'cash_out')->sum('amount'),
            'entries' => $entries,
        ];
    }
}
