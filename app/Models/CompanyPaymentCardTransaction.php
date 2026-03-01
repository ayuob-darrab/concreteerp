<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyPaymentCardTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_payment_card_id',
        'transaction_number',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'company_code',
        'branch_id',
        'description',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public static $types = [
        'deposit' => 'إيداع',
        'withdrawal' => 'سحب',
    ];

    public static $referenceTypes = [
        'order_payment' => 'دفعة طلب',
        'manual' => 'يدوي',
        'adjustment' => 'تعديل',
    ];

    // ==================== العلاقات ====================

    public function paymentCard()
    {
        return $this->belongsTo(CompanyPaymentCard::class, 'company_payment_card_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // ==================== المساعدات ====================

    public static function generateTransactionNumber()
    {
        $year = date('Y');
        $lastTransaction = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTransaction && preg_match('/CPCT-' . $year . '-(\d+)/', $lastTransaction->transaction_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return 'CPCT-' . $year . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function getTypeNameAttribute()
    {
        return self::$types[$this->type] ?? $this->type;
    }

    public function getTypeColorAttribute()
    {
        return $this->type === 'deposit' ? 'success' : 'danger';
    }

    public function getReferenceTypeNameAttribute()
    {
        return self::$referenceTypes[$this->reference_type] ?? $this->reference_type;
    }

    // ==================== Scopes ====================

    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    public function scopeWithdrawals($query)
    {
        return $query->where('type', 'withdrawal');
    }

    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
