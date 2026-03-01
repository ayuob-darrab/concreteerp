<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Inventory extends Model
{
    use HasFactory; 

    protected $table = 'inventories';

    protected $fillable = [
        'id','code','company_code','branch_id','name','unit','quantity_total','unit_cost','note',
    ];

    
  


    public function branchName()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function companyName()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }
    public function MeasurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class, 'unit', 'code');
    }
}
