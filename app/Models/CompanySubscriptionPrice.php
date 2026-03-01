<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySubscriptionPrice extends Model
{
    protected $table = 'company_subscription_prices';

    protected $fillable = [
        'company_code',
        'price_per_user_monthly',
        'price_per_user_yearly',
        'custom_percentage_rate',
        'custom_fixed_order_fee',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'price_per_user_monthly' => 'decimal:2',
        'price_per_user_yearly' => 'decimal:2',
        'custom_percentage_rate' => 'decimal:2',
        'custom_fixed_order_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع الشركة
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    /**
     * الحصول على سعر المستخدم الشهري (الخاص أو العام)
     */
    public static function getMonthlyPrice($companyCode)
    {
        $customPrice = self::where('company_code', $companyCode)
            ->where('is_active', true)
            ->first();
        
        if ($customPrice && $customPrice->price_per_user_monthly) {
            return $customPrice->price_per_user_monthly;
        }

        return SubscriptionPricing::getSettings()->standard_price_monthly;
    }

    /**
     * الحصول على سعر المستخدم السنوي (الخاص أو العام)
     */
    public static function getYearlyPrice($companyCode)
    {
        $customPrice = self::where('company_code', $companyCode)
            ->where('is_active', true)
            ->first();
        
        if ($customPrice && $customPrice->price_per_user_yearly) {
            return $customPrice->price_per_user_yearly;
        }

        return SubscriptionPricing::getSettings()->standard_price_yearly;
    }

    /**
     * الحصول على نسبة الطلبات (الخاصة أو العامة)
     */
    public static function getPercentageRate($companyCode)
    {
        $customPrice = self::where('company_code', $companyCode)
            ->where('is_active', true)
            ->first();
        
        if ($customPrice && $customPrice->custom_percentage_rate) {
            return $customPrice->custom_percentage_rate;
        }

        return SubscriptionPricing::getSettings()->default_percentage_rate;
    }

    /**
     * الحصول على المبلغ الثابت لكل طلب (الخاص أو العام)
     */
    public static function getFixedOrderFee($companyCode)
    {
        $customPrice = self::where('company_code', $companyCode)
            ->where('is_active', true)
            ->first();
        
        if ($customPrice && $customPrice->custom_fixed_order_fee) {
            return $customPrice->custom_fixed_order_fee;
        }

        return SubscriptionPricing::getSettings()->default_fixed_order_fee;
    }

    /**
     * الحصول على السعر الخاص بالشركة (إن وجد)
     */
    public static function getCompanyPricing($companyCode)
    {
        return self::where('company_code', $companyCode)
            ->where('is_active', true)
            ->first();
    }
}
