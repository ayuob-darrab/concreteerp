<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLoss extends Model
{
    protected $fillable = [
        'company_code',
        'branch_id',
        'material_type',
        'material_code',
        'material_id',
        'material_name',
        'unit',
        'quantity_lost',
        'quantity_base',
        'unit_cost',
        'unit_price_display',
        'total_cost',
        'note',
        'created_by',
        'reported_at',
    ];

    protected $casts = [
        'quantity_lost' => 'decimal:4',
        'quantity_base' => 'decimal:4',
        'unit_cost' => 'decimal:6',
        'unit_price_display' => 'decimal:6',
        'total_cost' => 'decimal:2',
        'reported_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

