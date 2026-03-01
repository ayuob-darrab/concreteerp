<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentReceipt extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'receipt_number',
        'company_code',
        'branch_id',
        'transaction_id',
        'payer_type',
        'payer_id',
        'payer_name',
        'payer_phone',
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
        'status',
        'cancelled_reason',
        'received_by',
        'received_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'amount_in_default' => 'decimal:2',
        'check_date' => 'date',
        'received_at' => 'datetime',
    ];

    // ===== Status Constants =====

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_BOUNCED = 'bounced';

    const STATUSES = [
        self::STATUS_PENDING => 'معلق',
        self::STATUS_CONFIRMED => 'مؤكد',
        self::STATUS_CANCELLED => 'ملغي',
        self::STATUS_BOUNCED => 'مرتجع',
    ];

    const STATUS_BADGES = [
        self::STATUS_PENDING => 'warning',
        self::STATUS_CONFIRMED => 'success',
        self::STATUS_CANCELLED => 'secondary',
        self::STATUS_BOUNCED => 'danger',
    ];

    const PAYER_TYPES = [
        'contractor' => 'مقاول',
        'customer' => 'عميل',
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

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function payer()
    {
        if ($this->payer_type === 'contractor') {
            return $this->belongsTo(Contractor::class, 'payer_id');
        }
        // Add other payer types as needed
        return null;
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

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('received_at', [$from, $to]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('received_at', today());
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

    public function getPayerTypeLabelAttribute(): string
    {
        return self::PAYER_TYPES[$this->payer_type] ?? $this->payer_type;
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

    // ===== Static Methods =====

    /**
     * Generate receipt number
     */
    public static function generateReceiptNumber(string $companyCode, int $branchId): string
    {
        $prefix = 'RCP';
        $branchCode = str_pad($branchId, 3, '0', STR_PAD_LEFT);
        $yearMonth = now()->format('Ym');

        $lastReceipt = static::where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastReceipt && preg_match('/-(\d+)$/', $lastReceipt->receipt_number, $matches)) {
            $sequence = (int)$matches[1] + 1;
        }

        return "{$prefix}-BR{$branchCode}-{$yearMonth}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Can be cancelled
     */
    public function canCancel(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }
}
