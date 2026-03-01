<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentCardTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_card_id',
        'transaction_number',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'company_code',
        'description',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    /**
     * أنواع المعاملات
     */
    public static $types = [
        'deposit' => 'إيداع',
        'withdrawal' => 'سحب',
    ];

    /**
     * أنواع المراجع
     */
    public static $referenceTypes = [
        'subscription' => 'اشتراك شركة',
        'manual' => 'يدوي',
        'adjustment' => 'تعديل',
    ];

    /**
     * علاقة مع البطاقة
     */
    public function paymentCard()
    {
        return $this->belongsTo(PaymentCard::class);
    }

    /**
     * علاقة مع المستخدم المنشئ
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * علاقة مع الشركة
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    /**
     * توليد رقم معاملة جديد
     */
    public static function generateTransactionNumber()
    {
        $year = date('Y');
        $lastTransaction = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTransaction && preg_match('/PCT-' . $year . '-(\d+)/', $lastTransaction->transaction_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return 'PCT-' . $year . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * الحصول على اسم نوع المعاملة
     */
    public function getTypeNameAttribute()
    {
        return self::$types[$this->type] ?? $this->type;
    }

    /**
     * الحصول على لون نوع المعاملة
     */
    public function getTypeColorAttribute()
    {
        return $this->type === 'deposit' ? 'success' : 'danger';
    }

    /**
     * الحصول على اسم نوع المرجع
     */
    public function getReferenceTypeNameAttribute()
    {
        return self::$referenceTypes[$this->reference_type] ?? $this->reference_type;
    }

    /**
     * Scope للإيداعات
     */
    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    /**
     * Scope للسحوبات
     */
    public function scopeWithdrawals($query)
    {
        return $query->where('type', 'withdrawal');
    }

    /**
     * Scope لمعاملات شركة معينة
     */
    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }
}
