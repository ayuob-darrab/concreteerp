<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'company_code',
        'price',
        'reserved_quantity',
        'unit_cost',
    ];

    protected $casts = [
        'reserved_quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
    ];
}
