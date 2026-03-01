<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyPaymentCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_code',
        'branch_id',
        'card_type',
        'card_name',
        'holder_name',
        'card_number',
        'card_number_masked',
        'opening_balance',
        'current_balance',
        'expiry_date',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * أنواع البطاقات المتاحة
     */
    public static $cardTypes = [
        'mastercard' => 'ماستر كارد',
        'visa' => 'فيزا',
        'zaincash' => 'زين كاش',
        'asiacell' => 'آسيا هوك',
        'fastpay' => 'فاست باي',
        'qi_card' => 'كي كارد',
        'other' => 'أخرى',
    ];

    // ==================== العلاقات ====================

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactions()
    {
        return $this->hasMany(CompanyPaymentCardTransaction::class);
    }

    // ==================== المعالجات ====================

    public static function maskCardNumber($number)
    {
        $length = strlen($number);
        if ($length <= 4) {
            return $number;
        }
        return str_repeat('*', $length - 4) . substr($number, -4);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->card_number_masked = self::maskCardNumber($model->card_number);
        });
    }

    // ==================== الخصائص المحسوبة ====================

    public function getCardTypeNameAttribute()
    {
        return self::$cardTypes[$this->card_type] ?? $this->card_type;
    }

    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'نشطة' : 'معطلة';
    }

    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'success' : 'danger';
    }

    public function getTotalDepositsAttribute()
    {
        return $this->transactions()->where('type', 'deposit')->sum('amount');
    }

    public function getTotalWithdrawalsAttribute()
    {
        return $this->transactions()->where('type', 'withdrawal')->sum('amount');
    }

    // ==================== العمليات المالية ====================

    public function deposit($amount, $description = null, $referenceType = null, $referenceId = null, $branchId = null)
    {
        $balanceBefore = $this->current_balance;
        $balanceAfter = $balanceBefore + $amount;

        $transaction = $this->transactions()->create([
            'transaction_number' => CompanyPaymentCardTransaction::generateTransactionNumber(),
            'type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'company_code' => $this->company_code,
            'branch_id' => $branchId ?? $this->branch_id,
            'description' => $description,
            'created_by' => auth()->id(),
        ]);

        $this->current_balance = $balanceAfter;
        $this->save();

        return $transaction;
    }

    public function withdraw($amount, $description = null, $referenceType = null, $referenceId = null, $branchId = null)
    {
        if ($amount > $this->current_balance) {
            throw new \Exception('الرصيد غير كافي');
        }

        $balanceBefore = $this->current_balance;
        $balanceAfter = $balanceBefore - $amount;

        $transaction = $this->transactions()->create([
            'transaction_number' => CompanyPaymentCardTransaction::generateTransactionNumber(),
            'type' => 'withdrawal',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'company_code' => $this->company_code,
            'branch_id' => $branchId ?? $this->branch_id,
            'description' => $description,
            'created_by' => auth()->id(),
        ]);

        $this->current_balance = $balanceAfter;
        $this->save();

        return $transaction;
    }

    // ==================== Scopes ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
