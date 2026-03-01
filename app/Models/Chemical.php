<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chemical extends Model
{
    use HasFactory;



    protected $fillable = [
        'id',
        'company_code',
        'branch_id',
        'name',
        'unit',
        'quantity_total',
        'unit_cost',
        'description',
    ];

    public function concreteMixes()
    {
        return $this->belongsToMany(ConcreteMix::class, 'concrete_mix_chemicals')
            ->withPivot('quantity')
            ->withTimestamps();
    }


    public function branchName()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function MeasurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class, 'unit', 'code');
    }
}
