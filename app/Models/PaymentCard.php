<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
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

    /**
     * علاقة مع المستخدم المنشئ
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * علاقة مع معاملات البطاقة
     */
    public function transactions()
    {
        return $this->hasMany(PaymentCardTransaction::class);
    }

    /**
     * إخفاء رقم البطاقة
     */
    public static function maskCardNumber($number)
    {
        $length = strlen($number);
        if ($length <= 4) {
            return $number;
        }
        return str_repeat('*', $length - 4) . substr($number, -4);
    }

    /**
     * تحديث الرقم المخفي عند الحفظ
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->card_number_masked = self::maskCardNumber($model->card_number);
        });
    }

    /**
     * الحصول على اسم نوع البطاقة
     */
    public function getCardTypeNameAttribute()
    {
        return self::$cardTypes[$this->card_type] ?? $this->card_type;
    }

    /**
     * الحصول على حالة البطاقة بالعربية
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'نشطة' : 'معطلة';
    }

    /**
     * الحصول على لون حالة البطاقة
     */
    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'success' : 'danger';
    }

    /**
     * إضافة رصيد (إيداع)
     */
    public function deposit($amount, $description = null, $referenceType = null, $referenceId = null, $companyCode = null)
    {
        $balanceBefore = $this->current_balance;
        $balanceAfter = $balanceBefore + $amount;

        // إنشاء معاملة الإيداع
        $transaction = $this->transactions()->create([
            'transaction_number' => PaymentCardTransaction::generateTransactionNumber(),
            'type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'company_code' => $companyCode,
            'description' => $description,
            'created_by' => auth()->id(),
        ]);

        // تحديث الرصيد الحالي
        $this->current_balance = $balanceAfter;
        $this->save();

        return $transaction;
    }

    /**
     * سحب رصيد
     */
    public function withdraw($amount, $description = null, $referenceType = null, $referenceId = null, $companyCode = null)
    {
        if ($amount > $this->current_balance) {
            throw new \Exception('الرصيد غير كافي');
        }

        $balanceBefore = $this->current_balance;
        $balanceAfter = $balanceBefore - $amount;

        // إنشاء معاملة السحب
        $transaction = $this->transactions()->create([
            'transaction_number' => PaymentCardTransaction::generateTransactionNumber(),
            'type' => 'withdrawal',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'company_code' => $companyCode,
            'description' => $description,
            'created_by' => auth()->id(),
        ]);

        // تحديث الرصيد الحالي
        $this->current_balance = $balanceAfter;
        $this->save();

        return $transaction;
    }

    /**
     * Scope للبطاقات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * الحصول على إجمالي الإيداعات
     */
    public function getTotalDepositsAttribute()
    {
        return $this->transactions()->where('type', 'deposit')->sum('amount');
    }

    /**
     * الحصول على إجمالي السحوبات
     */
    public function getTotalWithdrawalsAttribute()
    {
        return $this->transactions()->where('type', 'withdrawal')->sum('amount');
    }
}
