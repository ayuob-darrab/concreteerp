<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialComponent extends Model
{
    use HasFactory;
    protected $table = 'material_components';

    protected $fillable = ['id','company_code','material_name','material_type','unit_price','notes'   ];


    public function CompanyName()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    
}
