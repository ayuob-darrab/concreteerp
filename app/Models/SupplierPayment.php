<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'supplier_id',
        'company_code',
        'branch_id',
        'amount',
        'balance_before',
        'balance_after',
        'payment_method',
        'reference_number',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    /**
     * طرق الدفع المتاحة
     */
    public static $paymentMethods = [
        'cash' => 'نقدي',
        'bank_transfer' => 'تحويل بنكي',
        'check' => 'شيك',
        'online' => 'دفع إلكتروني',
    ];

    public function paymentCard()
    {
        return $this->belongsTo(CompanyPaymentCard::class, 'company_payment_card_id');
    }

    /**
     * علاقة مع المورد
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * علاقة مع الفرع
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * علاقة مع المستخدم الذي أنشأ الدفعة
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * الحصول على اسم طريقة الدفع
     */
    public function getPaymentMethodNameAttribute()
    {
        return self::$paymentMethods[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * توليد رقم إيصال جديد
     */
    public static function generatePaymentNumber($companyCode)
    {
        $year = date('Y');
        $lastPayment = self::where('company_code', $companyCode)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastPayment ? intval(substr($lastPayment->payment_number, -4)) + 1 : 1;

        return 'PAY-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
