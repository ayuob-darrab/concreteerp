<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'quantity',
        'car_id',
        'driver_id',
        'execution_date',
        'departure_time',
        'arrival_time',
        'pour_start_time',
        'pour_end_time',
        'return_time',
        'temperature',
        'slump',
        'quality_status',
        'unit_price',
        'total_price',
        'inventory_deducted',
        'inventory_deducted_at',
        'inventory_deducted_by',
        'location',
        'latitude',
        'longitude',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'temperature' => 'decimal:2',
        'slump' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'inventory_deducted' => 'boolean',
        'execution_date' => 'datetime',
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'pour_start_time' => 'datetime',
        'pour_end_time' => 'datetime',
        'return_time' => 'datetime',
        'inventory_deducted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقة مع الطلب
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * العلاقة مع السيارة
     */
    public function car()
    {
        return $this->belongsTo(Cars::class);
    }

    /**
     * العلاقة مع السائق
     */
    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    /**
     * من أنشأ التنفيذ
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: التنفيذات المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: التنفيذات الجارية
     */
    public function scopeInProgress($query)
    {
        return $query->whereIn('status', ['loading', 'in_transit', 'pouring']);
    }

    /**
     * Scope: التنفيذات التي تم خصم المخزن لها
     */
    public function scopeInventoryDeducted($query)
    {
        return $query->where('inventory_deducted', true);
    }

    /**
     * Scope: التنفيذات التي لم يتم خصم المخزن لها
     */
    public function scopeInventoryPending($query)
    {
        return $query->where('inventory_deducted', false);
    }

    /**
     * حساب إجمالي وقت الرحلة
     */
    public function getTripDurationAttribute()
    {
        if ($this->departure_time && $this->return_time) {
            return $this->departure_time->diffInMinutes($this->return_time);
        }
        return null;
    }

    /**
     * حساب وقت الصب
     */
    public function getPourDurationAttribute()
    {
        if ($this->pour_start_time && $this->pour_end_time) {
            return $this->pour_start_time->diffInMinutes($this->pour_end_time);
        }
        return null;
    }
}
