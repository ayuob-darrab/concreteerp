<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'stage',
        'user_id',
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
     * Scope: آخر مرحلة للطلب
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope: مراحل محددة
     */
    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }
}
