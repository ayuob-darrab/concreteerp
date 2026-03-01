<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'advance_id',
        'payment_number',
        'payment_type',
        'amount',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'payment_method',
        'notes',
        'paid_at',
        'paid_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // ==================== الثوابت ====================

    const TYPE_MANUAL = 'manual';
    const TYPE_SALARY_DEDUCTION = 'salary_deduction';
    const TYPE_INVOICE_DEDUCTION = 'invoice_deduction';
    const TYPE_COMMISSION_DEDUCTION = 'commission_deduction';

    const PAYMENT_TYPES = [
        self::TYPE_MANUAL => 'دفع يدوي',
        self::TYPE_SALARY_DEDUCTION => 'استقطاع من الراتب',
        self::TYPE_INVOICE_DEDUCTION => 'استقطاع من فاتورة',
        self::TYPE_COMMISSION_DEDUCTION => 'استقطاع من عمولة',
    ];

    const PAYMENT_METHODS = [
        'cash' => 'نقداً',
        'bank_transfer' => 'تحويل بنكي',
        'check' => 'شيك',
    ];

    // ==================== العلاقات ====================

    /**
     * السلفة
     */
    public function advance()
    {
        return $this->belongsTo(Advance::class);
    }

    /**
     * من دفع
     */
    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * المرجع (polymorphic)
     */
    public function reference()
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }

    // ==================== Accessors ====================

    /**
     * نص نوع الدفع
     */
    public function getPaymentTypeTextAttribute()
    {
        return self::PAYMENT_TYPES[$this->payment_type] ?? $this->payment_type;
    }

    /**
     * نص طريقة الدفع
     */
    public function getPaymentMethodTextAttribute()
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    // ==================== Methods ====================

    /**
     * توليد رقم الدفعة
     */
    public static function generatePaymentNumber($advanceId)
    {
        $prefix = 'PAY-' . $advanceId . '-';
        $lastPayment = self::where('payment_number', 'like', $prefix . '%')
            ->orderBy('payment_number', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment->payment_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
