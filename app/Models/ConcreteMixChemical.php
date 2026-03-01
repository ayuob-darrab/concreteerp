<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConcreteMixChemical extends Model
{
    use HasFactory;

    protected $fillable = [
        'id','concrete_mix_id','chemical_id','quantity',
    ];

    public function concreteMix()
    {
        return $this->belongsTo(ConcreteMix::class , 'concrete_mix_id');
    }
    public function ChemicalQuantity()
    {
        return $this->belongsTo(Chemical::class , 'chemical_id');
    }

}
 