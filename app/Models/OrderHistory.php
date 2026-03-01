<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'action_type',
        'field_name',
        'old_value',
        'new_value',
        'user_id',
        'user_type',
        'description',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public $timestamps = false; // نستخدم created_at فقط

    /**
     * العلاقة مع الطلب
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: حسب نوع العملية
     */
    public function scopeByActionType($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope: آخر التغييرات
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope: تغييرات الأسعار فقط
     */
    public function scopePriceChanges($query)
    {
        return $query->where('action_type', 'price_changed');
    }

    /**
     * Scope: تغييرات الحالة فقط
     */
    public function scopeStatusChanges($query)
    {
        return $query->where('action_type', 'status_changed');
    }

    /**
     * Helper: إنشاء سجل تاريخي
     */
    public static function logChange($workOrderId, $actionType, $data = [])
    {
        return self::create([
            'work_order_id' => $workOrderId,
            'action_type' => $actionType,
            'field_name' => $data['field_name'] ?? null,
            'old_value' => $data['old_value'] ?? null,
            'new_value' => $data['new_value'] ?? null,
            'user_id' => auth()->id(),
            'user_type' => $data['user_type'] ?? 'employee',
            'description' => $data['description'] ?? null,
            'notes' => $data['notes'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);
    }
}
