<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    use HasFactory;

    protected $table = 'subscription_history';

    protected $fillable = [
        'company_code',
        'subscription_id',
        'plan_type',
        'base_fee',
        'percentage_rate',
        'order_fee_type',
        'fixed_order_fee',
        'orders_limit',
        'orders_used',
        'start_date',
        'end_date',
        'actual_start_date',
        'actual_end_date',
        'auto_renew',
        'status',
        'notes',
        'action_type',
        'created_by',
        // حقول الدفع
        'payment_status',
        'paid_amount',
        'paid_at',
        'payment_method',
        'payment_reference',
        // حقول التمديد
        'extension_days',
        'extension_deducted',
        // حقول مدة الاشتراك
        'duration_quantity',
        'total_days',
    ];

    protected $casts = [
        'base_fee' => 'decimal:2',
        'percentage_rate' => 'decimal:2',
        'fixed_order_fee' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'paid_at' => 'datetime',
        'auto_renew' => 'boolean',
        'extension_deducted' => 'boolean',
    ];

    /**
     * العلاقة مع جدول الشركات
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    /**
     * العلاقة مع الاشتراك الأصلي
     */
    public function subscription()
    {
        return $this->belongsTo(CompanySubscription::class, 'subscription_id');
    }

    /**
     * العلاقة مع المستخدم الذي أنشأ السجل
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
