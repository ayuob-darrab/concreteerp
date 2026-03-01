<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentVoucher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'voucher_number',
        'company_code',
        'branch_id',
        'transaction_id',
        'payee_type',
        'payee_id',
        'payee_name',
        'amount',
        'currency_code',
        'exchange_rate',
        'amount_in_default',
        'amount_in_words',
        'payment_method',
        'reference_number',
        'bank_name',
        'check_number',
        'check_date',
        'description',
        'related_type',
        'related_id',
        'requires_approval',
        'approved_by',
        'approved_at',
        'status',
        'cancelled_reason',
        'paid_by',
        'paid_at',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'amount_in_default' => 'decimal:2',
        'check_date' => 'date',
        'requires_approval' => 'boolean',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // ===== Status Constants =====

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_DRAFT => 'مسودة',
        self::STATUS_PENDING_APPROVAL => 'بانتظار الموافقة',
        self::STATUS_APPROVED => 'موافق عليه',
        self::STATUS_PAID => 'مدفوع',
        self::STATUS_CANCELLED => 'ملغي',
    ];

    const STATUS_BADGES = [
        self::STATUS_DRAFT => 'secondary',
        self::STATUS_PENDING_APPROVAL => 'warning',
        self::STATUS_APPROVED => 'info',
        self::STATUS_PAID => 'success',
        self::STATUS_CANCELLED => 'danger',
    ];

    const PAYEE_TYPES = [
        'supplier' => 'مورد',
        'contractor' => 'مقاول',
        'employee' => 'موظف',
        'other' => 'أخرى',
    ];

    const PAYMENT_METHODS = [
        'cash' => 'نقداً',
        'bank_transfer' => 'حوالة بنكية',
        'check' => 'شيك',
        'card' => 'بطاقة',
        'other' => 'أخرى',
    ];

    // ===== Relationships =====

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function transaction()
    {
        return $this->belongsTo(FinancialTransaction::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function payee()
    {
        switch ($this->payee_type) {
            case 'supplier':
                return $this->belongsTo(Supplier::class, 'payee_id');
            case 'contractor':
                return $this->belongsTo(Contractor::class, 'payee_id');
            case 'employee':
                return $this->belongsTo(Employee::class, 'payee_id');
            default:
                return null;
        }
    }

    // ===== Scopes =====

    public function scopeOfCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    public function scopeOfBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', self::STATUS_PENDING_APPROVAL);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeNotPaid($query)
    {
        return $query->whereNotIn('status', [self::STATUS_PAID, self::STATUS_CANCELLED]);
    }

    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('paid_at', [$from, $to]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('paid_at', today());
    }

    // ===== Accessors =====

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute(): string
    {
        return self::STATUS_BADGES[$this->status] ?? 'secondary';
    }

    public function getPayeeTypeLabelAttribute(): string
    {
        return self::PAYEE_TYPES[$this->payee_type] ?? $this->payee_type;
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    public function getFormattedAmountAttribute(): string
    {
        $currency = $this->currency;
        if ($currency) {
            return $currency->format($this->amount);
        }
        return number_format($this->amount, 0) . ' د.ع';
    }

    // ===== State Methods =====

    public function canApprove(): bool
    {
        return $this->status === self::STATUS_PENDING_APPROVAL;
    }

    public function canPay(): bool
    {
        if (!$this->requires_approval) {
            return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_APPROVED]);
        }
        return $this->status === self::STATUS_APPROVED;
    }

    public function canCancel(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PENDING_APPROVAL, self::STATUS_APPROVED]);
    }

    // ===== Static Methods =====

    /**
     * Generate voucher number
     */
    public static function generateVoucherNumber(string $companyCode, int $branchId): string
    {
        $prefix = 'PV';
        $branchCode = str_pad($branchId, 3, '0', STR_PAD_LEFT);
        $yearMonth = now()->format('Ym');

        $lastVoucher = static::where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastVoucher && preg_match('/-(\d+)$/', $lastVoucher->voucher_number, $matches)) {
            $sequence = (int)$matches[1] + 1;
        }

        return "{$prefix}-BR{$branchCode}-{$yearMonth}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
