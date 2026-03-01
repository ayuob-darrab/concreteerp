<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialEquipment extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'code',
        'material_type',
        'capacity',
        'company_code',
        'note',
    ];

    public function CompanyName()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }
    public function UnitName()
    {
        return $this->belongsTo(MeasurementUnit::class, 'code', 'code');
    }

    // علاقة مع المادة
    public function material()
    {
        return $this->belongsTo(Inventory::class, 'material_type', 'name');
    }
}
