<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPricing extends Model
{
    protected $table = 'subscription_pricing';

    protected $fillable = [
        'standard_price_monthly',
        'standard_price_yearly',
        'default_percentage_rate',
        'default_fixed_order_fee',
        'grace_period_days',
        'warning_days',
        'payment_due_days',
        'trial_days',
        'notes',
    ];

    protected $casts = [
        'standard_price_monthly' => 'decimal:2',
        'standard_price_yearly' => 'decimal:2',
        'default_percentage_rate' => 'decimal:2',
        'default_fixed_order_fee' => 'decimal:2',
        'grace_period_days' => 'integer',
        'warning_days' => 'integer',
        'payment_due_days' => 'integer',
        'trial_days' => 'integer',
    ];

    /**
     * الحصول على الإعدادات الحالية (سطر واحد فقط)
     */
    public static function getSettings()
    {
        return self::first() ?? self::create([
            'standard_price_monthly' => 10000,
            'standard_price_yearly' => 10000, // نفس الشهري (السنوي = 12 شهر)
            'default_percentage_rate' => 5,
            'default_fixed_order_fee' => 1000,
            'grace_period_days' => 7,
            'warning_days' => 4,
            'payment_due_days' => 7,
            'trial_days' => 7,
        ]);
    }
}
