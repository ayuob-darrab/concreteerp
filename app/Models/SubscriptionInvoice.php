<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionInvoice extends Model
{
    protected $table = 'subscription_invoices';

    protected $fillable = [
        'invoice_number',
        'company_code',
        'subscription_id',
        'invoice_type',
        'period_start',
        'period_end',
        'users_count',
        'price_per_user',
        'subtotal',
        'discount',
        'total_amount',
        'orders_count',
        'orders_total_value',
        'percentage_rate',
        'payment_status',
        'paid_amount',
        'due_date',
        'paid_at',
        'payment_method',
        'payment_reference',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'price_per_user' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'orders_total_value' => 'decimal:2',
        'percentage_rate' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'due_date' => 'date',
        'paid_at' => 'date',
        'users_count' => 'integer',
        'orders_count' => 'integer',
    ];

    /**
     * العلاقة مع الشركة
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    /**
     * العلاقة مع الاشتراك
     */
    public function subscription()
    {
        return $this->belongsTo(CompanySubscription::class, 'subscription_id');
    }

    /**
     * توليد رقم فاتورة جديد
     */
    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastInvoice = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastInvoice ? intval(substr($lastInvoice->invoice_number, -4)) + 1 : 1;
        
        return "INV-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * إنشاء فاتورة اشتراك
     */
    public static function createSubscriptionInvoice($subscription, $createdBy = null)
    {
        $settings = SubscriptionPricing::getSettings();
        
        return self::create([
            'invoice_number' => self::generateInvoiceNumber(),
            'company_code' => $subscription->company_code,
            'subscription_id' => $subscription->id,
            'invoice_type' => 'subscription',
            'period_start' => $subscription->start_date,
            'period_end' => $subscription->end_date,
            'users_count' => $subscription->users_count,
            'price_per_user' => $subscription->price_per_user,
            'subtotal' => $subscription->total_amount,
            'discount' => 0,
            'total_amount' => $subscription->total_amount,
            'payment_status' => $subscription->payment_status ?? 'pending',
            'paid_amount' => $subscription->paid_amount ?? 0,
            'due_date' => $subscription->start_date->addDays($settings->payment_due_days),
            'created_by' => $createdBy,
        ]);
    }

    /**
     * إنشاء فاتورة شهرية للاشتراك بالنسبة
     */
    public static function createOrdersInvoice($companyCode, $ordersCount, $ordersTotalValue, $percentageRate, $createdBy = null)
    {
        $settings = SubscriptionPricing::getSettings();
        $totalAmount = $ordersTotalValue * ($percentageRate / 100);
        
        return self::create([
            'invoice_number' => self::generateInvoiceNumber(),
            'company_code' => $companyCode,
            'invoice_type' => 'orders_percentage',
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'orders_count' => $ordersCount,
            'orders_total_value' => $ordersTotalValue,
            'percentage_rate' => $percentageRate,
            'subtotal' => $totalAmount,
            'total_amount' => $totalAmount,
            'payment_status' => 'pending',
            'due_date' => now()->addDays($settings->payment_due_days),
            'created_by' => $createdBy,
        ]);
    }

    /**
     * الحصول على نوع الفاتورة بالعربي
     */
    public function getTypeNameAttribute()
    {
        $types = [
            'subscription' => 'اشتراك',
            'orders_percentage' => 'نسبة طلبات',
            'renewal' => 'تجديد',
            'additional_user' => 'مستخدم إضافي',
        ];

        return $types[$this->invoice_type] ?? $this->invoice_type;
    }

    /**
     * الحصول على حالة الدفع بالعربي
     */
    public function getStatusNameAttribute()
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'paid' => 'مدفوع',
            'partial' => 'مدفوع جزئياً',
            'overdue' => 'متأخر',
        ];

        return $statuses[$this->payment_status] ?? $this->payment_status;
    }
}
