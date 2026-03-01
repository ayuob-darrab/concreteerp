<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPaymentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_number',
        'customer_payment_id',
        'company_code',
        'branch_id',
        'payment_method',
        'amount',
        'balance_before',
        'balance_after',
        'company_payment_card_id',
        'reference_number',
        'receipt_number',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public static $paymentMethods = [
        'cash' => 'نقدي',
        'bank_transfer' => 'تحويل بنكي',
        'check' => 'شيك',
        'online' => 'دفع إلكتروني',
    ];

    // ==================== العلاقات ====================

    public function customerPayment()
    {
        return $this->belongsTo(CustomerPayment::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function paymentCard()
    {
        return $this->belongsTo(CompanyPaymentCard::class, 'company_payment_card_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==================== المساعدات ====================

    public static function generateRecordNumber($companyCode)
    {
        $year = date('Y');
        $month = date('m');
        $prefix = 'CPR-' . $companyCode . '-' . $year . $month;

        $lastRecord = self::where('record_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRecord && preg_match('/-(\d+)$/', $lastRecord->record_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getPaymentMethodTextAttribute()
    {
        return self::$paymentMethods[$this->payment_method] ?? $this->payment_method;
    }
}
