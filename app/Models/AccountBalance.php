<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountBalance extends Model
{
    protected $fillable = [
        'company_code',
        'branch_id',
        'account_type',
        'account_id',
        'currency_code',
        'opening_balance',
        'total_debits',
        'total_credits',
        'current_balance',
        'balance_type',
        'last_transaction_id',
        'last_transaction_at',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'total_debits' => 'decimal:2',
        'total_credits' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'last_transaction_at' => 'datetime',
    ];

    // ===== Account Type Constants =====

    const TYPE_CONTRACTOR = 'contractor';
    const TYPE_SUPPLIER = 'supplier';
    const TYPE_EMPLOYEE = 'employee';
    const TYPE_CUSTOMER = 'customer';
    const TYPE_CASH_REGISTER = 'cash_register';
    const TYPE_BANK = 'bank';

    const ACCOUNT_TYPES = [
        self::TYPE_CONTRACTOR => 'مقاول',
        self::TYPE_SUPPLIER => 'مورد',
        self::TYPE_EMPLOYEE => 'موظف',
        self::TYPE_CUSTOMER => 'عميل',
        self::TYPE_CASH_REGISTER => 'صندوق',
        self::TYPE_BANK => 'بنك',
    ];

    const BALANCE_TYPES = [
        'debit' => 'مدين (له علينا)',
        'credit' => 'دائن (لنا عليه)',
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

    public function account()
    {
        switch ($this->account_type) {
            case self::TYPE_CONTRACTOR:
                return $this->belongsTo(Contractor::class, 'account_id');
            case self::TYPE_SUPPLIER:
                return $this->belongsTo(Supplier::class, 'account_id');
            case self::TYPE_EMPLOYEE:
                return $this->belongsTo(Employee::class, 'account_id');
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

    public function scopeOfType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeWithBalance($query)
    {
        return $query->where('current_balance', '!=', 0);
    }

    public function scopeDebit($query)
    {
        return $query->where('balance_type', 'debit');
    }

    public function scopeCredit($query)
    {
        return $query->where('balance_type', 'credit');
    }

    // ===== Accessors =====

    public function getAccountTypeLabelAttribute(): string
    {
        return self::ACCOUNT_TYPES[$this->account_type] ?? $this->account_type;
    }

    public function getBalanceTypeLabelAttribute(): string
    {
        return self::BALANCE_TYPES[$this->balance_type] ?? '-';
    }

    public function getFormattedBalanceAttribute(): string
    {
        $currency = $this->currency;
        if ($currency) {
            return $currency->format($this->current_balance);
        }
        return number_format($this->current_balance, 0) . ' د.ع';
    }

    public function getAccountNameAttribute(): string
    {
        $account = $this->account;
        return $account ? $account->name : "حساب #{$this->account_id}";
    }

    // ===== Static Methods =====

    /**
     * Get or create balance record
     */
    public static function getOrCreate(
        string $companyCode,
        ?int $branchId,
        string $accountType,
        int $accountId,
        string $currencyCode = 'IQD'
    ): AccountBalance {
        return static::firstOrCreate(
            [
                'company_code' => $companyCode,
                'branch_id' => $branchId,
                'account_type' => $accountType,
                'account_id' => $accountId,
                'currency_code' => $currencyCode,
            ],
            [
                'opening_balance' => 0,
                'total_debits' => 0,
                'total_credits' => 0,
                'current_balance' => 0,
            ]
        );
    }

    /**
     * Add debit (increase what they owe us)
     */
    public function addDebit(float $amount, ?int $transactionId = null): void
    {
        $this->total_debits += $amount;
        $this->current_balance = $this->opening_balance + $this->total_debits - $this->total_credits;
        $this->balance_type = $this->current_balance >= 0 ? 'debit' : 'credit';
        $this->last_transaction_id = $transactionId;
        $this->last_transaction_at = now();
        $this->save();
    }

    /**
     * Add credit (increase what we owe them)
     */
    public function addCredit(float $amount, ?int $transactionId = null): void
    {
        $this->total_credits += $amount;
        $this->current_balance = $this->opening_balance + $this->total_debits - $this->total_credits;
        $this->balance_type = $this->current_balance >= 0 ? 'debit' : 'credit';
        $this->last_transaction_id = $transactionId;
        $this->last_transaction_at = now();
        $this->save();
    }

    /**
     * Recalculate balance from transactions
     */
    public function recalculate(): void
    {
        // This would need to sum all related transactions
        // Implementation depends on how transactions are structured
    }
}
