<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingCategory extends Model
{
    use HasFactory;

    protected $table = 'pricing_categories';

    protected $fillable = [
        'name',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * العلاقة مع أسعار الخلطات
     */
    public function mixPrices()
    {
        return $this->hasMany(ConcreteMixCategoryPrice::class, 'pricing_category_id');
    }

    /**
     * الفئات النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * ترتيب حسب sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }
}
