<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractorAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'contractor_id',
        'company_code',
        'branch_id',
        'account_number',
        'opening_balance',
        'opening_balance_type',
        'current_balance',
        'currency',
        'total_invoiced',
        'total_paid',
        'total_discount',
        'last_invoice_date',
        'last_payment_date',
        'last_transaction_date',
        'is_frozen',
        'freeze_reason',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'total_invoiced' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'is_frozen' => 'boolean',
        'last_invoice_date' => 'datetime',
        'last_payment_date' => 'datetime',
        'last_transaction_date' => 'datetime',
    ];

    // ═══════════════════════════════════════════════════════════════
    // Boot
    // ═══════════════════════════════════════════════════════════════

    protected static function booted()
    {
        static::creating(function (ContractorAccount $account) {
            if (empty($account->account_number)) {
                $account->account_number = self::generateAccountNumber($account->company_code);
            }
        });
    }

    // ═══════════════════════════════════════════════════════════════
    // العلاقات
    // ═══════════════════════════════════════════════════════════════

    /**
     * المقاول
     */
    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

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
     * المعاملات المالية
     */
    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class, 'account_id');
    }

    /**
     * الفواتير
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'account_id');
    }

    // ═══════════════════════════════════════════════════════════════
    // Scopes
    // ═══════════════════════════════════════════════════════════════

    /**
     * حسب الشركة
     */
    public function scopeForCompany($query, string $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    /**
     * حسب الفرع
     */
    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * الحسابات التي لها رصيد
     */
    public function scopeWithBalance($query)
    {
        return $query->where('current_balance', '!=', 0);
    }

    /**
     * الحسابات المدينة
     */
    public function scopeDebit($query)
    {
        return $query->where('current_balance', '>', 0);
    }

    /**
     * الحسابات الدائنة
     */
    public function scopeCredit($query)
    {
        return $query->where('current_balance', '<', 0);
    }

    // ═══════════════════════════════════════════════════════════════
    // Accessors
    // ═══════════════════════════════════════════════════════════════

    /**
     * نوع الرصيد الحالي
     */
    public function getBalanceTypeAttribute(): string
    {
        if ($this->current_balance > 0) return 'debit';
        if ($this->current_balance < 0) return 'credit';
        return 'zero';
    }

    /**
     * نص الرصيد
     */
    public function getBalanceLabelAttribute(): string
    {
        return match ($this->balance_type) {
            'debit' => 'مدين',
            'credit' => 'دائن',
            'zero' => 'متوازن',
        };
    }

    /**
     * الرصيد المطلق
     */
    public function getAbsoluteBalanceAttribute(): float
    {
        return abs($this->current_balance);
    }

    // ═══════════════════════════════════════════════════════════════
    // Methods
    // ═══════════════════════════════════════════════════════════════

    /**
     * توليد رقم حساب
     */
    public static function generateAccountNumber(string $companyCode): string
    {
        $prefix = 'ACC';
        $year = date('Y');

        $lastAccount = self::where('company_code', $companyCode)
            ->where('account_number', 'like', "{$prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastAccount
            ? (int)substr($lastAccount->account_number, -5) + 1
            : 1;

        return sprintf('%s-%s-%05d', $prefix, $year, $nextNumber);
    }

    /**
     * إضافة للرصيد (مدين)
     */
    public function addDebit(float $amount): void
    {
        $this->increment('current_balance', $amount);
        $this->increment('total_invoiced', $amount);
        $this->update(['last_transaction_date' => now()]);
    }

    /**
     * خصم من الرصيد (دائن)
     */
    public function addCredit(float $amount): void
    {
        $this->decrement('current_balance', $amount);
        $this->increment('total_paid', $amount);
        $this->update([
            'last_payment_date' => now(),
            'last_transaction_date' => now(),
        ]);
    }

    /**
     * تجميد الحساب
     */
    public function freeze(string $reason): bool
    {
        return $this->update([
            'is_frozen' => true,
            'freeze_reason' => $reason,
        ]);
    }

    /**
     * إلغاء تجميد الحساب
     */
    public function unfreeze(): bool
    {
        return $this->update([
            'is_frozen' => false,
            'freeze_reason' => null,
        ]);
    }

    /**
     * إعادة حساب الرصيد من المعاملات
     */
    public function recalculateBalance(): float
    {
        $balance = $this->opening_balance;

        if ($this->opening_balance_type === 'credit') {
            $balance = -$balance;
        }

        $transactions = $this->transactions()->orderBy('transaction_date')->get();

        foreach ($transactions as $transaction) {
            if ($transaction->balance_effect === 'debit') {
                $balance += $transaction->amount;
            } else {
                $balance -= $transaction->amount;
            }
        }

        $this->update(['current_balance' => $balance]);

        return $balance;
    }
}
