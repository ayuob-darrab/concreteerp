<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConcreteMix extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'classification',
        'company_code',
        'costPrice',
        'salePrice',
        'price',
        'branch_id',
        'cement',
        'sand',
        'gravel',
        'water',
        'notes',
        'cement_code',
        'sand_code',
        'gravel_code',
        'water_code',
    ];



    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'classification', 'id');
    }


    public function CompanyName()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    public function chemicals()
    {
        return $this->belongsToMany(Chemical::class, 'concrete_mix_chemicals')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    // تم إزالة ConcreteMixType واستبداله بنظام الفئات السعرية
    public function categoryPrices()
    {
        return $this->hasMany(ConcreteMixCategoryPrice::class, 'concrete_mix_id');
    }
    public function branchName()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }



    public function cementInventory()
    {
        return $this->belongsTo(Inventory::class, 'cement_code', 'code');
    }

    public function sandInventory()
    {
        return $this->belongsTo(Inventory::class, 'sand_code', 'code');
    }

    public function gravelInventory()
    {
        return $this->belongsTo(Inventory::class, 'gravel_code', 'code');
    }

    public function waterInventory()
    {
        return $this->belongsTo(Inventory::class, 'water_code', 'code');
    }
}
