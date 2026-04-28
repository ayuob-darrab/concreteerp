<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{ 
    use HasFactory;


    protected $fillable = [
        'id','material_code','company_code','supplier_id',
        'unit_capacity','unit_code',
        'countUnit','total_cost','shipment_date','user_id','note',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'material_code', 'code');
    } 
    public function Chemical()
    {
        return $this->belongsTo(Chemical::class, 'material_code', 'id');
    } 

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
