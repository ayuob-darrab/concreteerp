<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'supplier_name',
        'company_code',
        'branch_id',
        'company_name',
        'opening_balance',
        'phone',
        'address',
        'note',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
    ];

    public function Supplier_InventoryHistory()
    {
        return $this->hasMany(InventoryHistory::class, 'supplier_id', 'id');
    }

    public function branchName()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * علاقة مع الدفعات
     */
    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    /**
     * حساب إجمالي المدفوعات
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    /**
     * حساب الرصيد المتبقي (المستحق)
     */
    public function getRemainingBalanceAttribute()
    {
        return $this->opening_balance - $this->total_paid;
    }

    /**
     * التحقق من إمكانية التسديد
     */
    public function canMakePayment()
    {
        return $this->remaining_balance > 0;
    }
}
