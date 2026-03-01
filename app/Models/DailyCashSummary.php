<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyCashSummary extends Model
{
    protected $fillable = [
        'company_code',
        'branch_id',
        'summary_date',
        'currency_code',
        'opening_balance',
        'total_receipts',
        'total_payments',
        'closing_balance',
        'receipts_count',
        'payments_count',
        'status',
        'opened_by',
        'opened_at',
        'closed_by',
        'closed_at',
        'notes',
    ];

    protected $casts = [
        'summary_date' => 'date',
        'opening_balance' => 'decimal:2',
        'total_receipts' => 'decimal:2',
        'total_payments' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // ===== Status Constants =====

    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';

    const STATUSES = [
        self::STATUS_OPEN => 'مفتوح',
        self::STATUS_CLOSED => 'مغلق',
    ];

    // ===== Relationships =====

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function openedByUser()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedByUser()
    {
        return $this->belongsTo(User::class, 'closed_by');
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

    public function scopeOfDate($query, $date)
    {
        return $query->whereDate('summary_date', $date);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    // ===== Accessors =====

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getIsOpenAttribute(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function getNetChangeAttribute(): float
    {
        return $this->total_receipts - $this->total_payments;
    }

    public function getFormattedClosingBalanceAttribute(): string
    {
        $currency = $this->currency;
        if ($currency) {
            return $currency->format($this->closing_balance);
        }
        return number_format($this->closing_balance, 0) . ' د.ع';
    }

    // ===== Instance Methods =====

    /**
     * Add receipt to summary
     */
    public function addReceipt(float $amount): void
    {
        $this->total_receipts += $amount;
        $this->receipts_count++;
        $this->closing_balance = $this->opening_balance + $this->total_receipts - $this->total_payments;
        $this->save();
    }

    /**
     * Add payment to summary
     */
    public function addPayment(float $amount): void
    {
        $this->total_payments += $amount;
        $this->payments_count++;
        $this->closing_balance = $this->opening_balance + $this->total_receipts - $this->total_payments;
        $this->save();
    }

    /**
     * Close the day
     */
    public function close(int $userId, ?string $notes = null): void
    {
        $this->status = self::STATUS_CLOSED;
        $this->closed_by = $userId;
        $this->closed_at = now();
        $this->notes = $notes;
        $this->save();
    }

    // ===== Static Methods =====

    /**
     * Get or create today's summary
     */
    public static function getOrCreateToday(
        string $companyCode,
        int $branchId,
        string $currencyCode = 'IQD'
    ): DailyCashSummary {
        $today = today();

        $summary = static::where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->where('currency_code', $currencyCode)
            ->whereDate('summary_date', $today)
            ->first();

        if (!$summary) {
            // Get yesterday's closing balance as opening balance
            $yesterday = static::where('company_code', $companyCode)
                ->where('branch_id', $branchId)
                ->where('currency_code', $currencyCode)
                ->whereDate('summary_date', $today->copy()->subDay())
                ->first();

            $openingBalance = $yesterday ? $yesterday->closing_balance : 0;

            $summary = static::create([
                'company_code' => $companyCode,
                'branch_id' => $branchId,
                'summary_date' => $today,
                'currency_code' => $currencyCode,
                'opening_balance' => $openingBalance,
                'total_receipts' => 0,
                'total_payments' => 0,
                'closing_balance' => $openingBalance,
                'receipts_count' => 0,
                'payments_count' => 0,
                'status' => self::STATUS_OPEN,
                'opened_by' => auth()->id(),
                'opened_at' => now(),
            ]);
        }

        return $summary;
    }

    /**
     * Recalculate summary from transactions
     */
    public function recalculate(): void
    {
        $receipts = PaymentReceipt::where('company_code', $this->company_code)
            ->where('branch_id', $this->branch_id)
            ->where('currency_code', $this->currency_code)
            ->whereDate('received_at', $this->summary_date)
            ->where('status', PaymentReceipt::STATUS_CONFIRMED)
            ->get();

        $vouchers = PaymentVoucher::where('company_code', $this->company_code)
            ->where('branch_id', $this->branch_id)
            ->where('currency_code', $this->currency_code)
            ->whereDate('paid_at', $this->summary_date)
            ->where('status', PaymentVoucher::STATUS_PAID)
            ->get();

        $this->total_receipts = $receipts->sum('amount');
        $this->receipts_count = $receipts->count();
        $this->total_payments = $vouchers->sum('amount');
        $this->payments_count = $vouchers->count();
        $this->closing_balance = $this->opening_balance + $this->total_receipts - $this->total_payments;
        $this->save();
    }
}
