<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConcreteMixCategoryPrice extends Model
{
    use HasFactory;

    protected $table = 'concrete_mix_category_prices';

    protected $fillable = [
        'company_code',
        'concrete_mix_id',
        'pricing_category_id',
        'price_per_meter',
        'cost_per_meter',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'price_per_meter' => 'decimal:2',
        'cost_per_meter' => 'decimal:2',
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
     * العلاقة مع الخلطة
     */
    public function concreteMix()
    {
        return $this->belongsTo(ConcreteMix::class, 'concrete_mix_id');
    }

    /**
     * العلاقة مع الفئة السعرية
     */
    public function pricingCategory()
    {
        return $this->belongsTo(PricingCategory::class, 'pricing_category_id');
    }

    /**
     * الأسعار النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * أسعار شركة معينة
     */
    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    /**
     * أسعار خلطة معينة
     */
    public function scopeForMix($query, $mixId)
    {
        return $query->where('concrete_mix_id', $mixId);
    }

    /**
     * حساب هامش الربح
     */
    public function getProfitMarginAttribute()
    {
        if ($this->cost_per_meter && $this->cost_per_meter > 0) {
            return (($this->price_per_meter - $this->cost_per_meter) / $this->cost_per_meter) * 100;
        }
        return null;
    }
}
