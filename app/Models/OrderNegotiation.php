<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderNegotiation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_negotiations';

    // مراحل التفاوض
    const STAGE_INITIAL = 'initial_request';
    const STAGE_BRANCH_OFFER = 'branch_offer';
    const STAGE_REQUESTER_ACCEPT = 'requester_accept';
    const STAGE_REQUESTER_REJECT = 'requester_reject';
    const STAGE_REQUESTER_COUNTER = 'requester_counter';
    const STAGE_BRANCH_COUNTER = 'branch_counter';
    const STAGE_FINAL = 'final_agreement';
    const STAGE_CANCELLED = 'cancelled';

    const STAGES = [
        self::STAGE_INITIAL => 'طلب مبدئي',
        self::STAGE_BRANCH_OFFER => 'عرض الفرع',
        self::STAGE_REQUESTER_ACCEPT => 'قبول الطالب',
        self::STAGE_REQUESTER_REJECT => 'رفض الطالب',
        self::STAGE_REQUESTER_COUNTER => 'عرض مضاد من الطالب',
        self::STAGE_BRANCH_COUNTER => 'عرض مضاد من الفرع',
        self::STAGE_FINAL => 'اتفاق نهائي',
        self::STAGE_CANCELLED => 'ملغي',
    ];

    protected $fillable = [
        'order_id',
        'stage',
        'offered_price',
        'offered_quantity',
        'offered_concrete_type',
        'offered_pump_type',
        'offered_delivery_date',
        'offered_delivery_time',
        'notes',
        'rejection_reason',
        'created_by',
        'created_by_type',
    ];

    protected $casts = [
        'offered_price' => 'decimal:2',
        'offered_quantity' => 'decimal:2',
        'offered_delivery_date' => 'date',
    ];

    // العلاقات
    public function order()
    {
        return $this->belongsTo(WorkOrder::class, 'order_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeForOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function scopeStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // الدوال المساعدة
    public function getStageLabelAttribute()
    {
        return self::STAGES[$this->stage] ?? $this->stage;
    }

    public function isAccepted()
    {
        return $this->stage === self::STAGE_REQUESTER_ACCEPT || $this->stage === self::STAGE_FINAL;
    }

    public function isRejected()
    {
        return $this->stage === self::STAGE_REQUESTER_REJECT || $this->stage === self::STAGE_CANCELLED;
    }

    public function isPending()
    {
        return in_array($this->stage, [
            self::STAGE_INITIAL,
            self::STAGE_BRANCH_OFFER,
            self::STAGE_REQUESTER_COUNTER,
            self::STAGE_BRANCH_COUNTER
        ]);
    }

    public function isFromBranch()
    {
        return in_array($this->stage, [
            self::STAGE_BRANCH_OFFER,
            self::STAGE_BRANCH_COUNTER,
        ]);
    }

    public function isFromRequester()
    {
        return in_array($this->stage, [
            self::STAGE_INITIAL,
            self::STAGE_REQUESTER_ACCEPT,
            self::STAGE_REQUESTER_REJECT,
            self::STAGE_REQUESTER_COUNTER,
        ]);
    }

    // الحصول على آخر عرض سعر
    public static function getLatestOffer($orderId)
    {
        return self::forOrder($orderId)
            ->whereNotNull('offered_price')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    // الحصول على سجل التفاوض الكامل
    public static function getNegotiationHistory($orderId)
    {
        return self::forOrder($orderId)
            ->with('creator')
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
