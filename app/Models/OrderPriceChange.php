<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPriceChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'old_price',
        'new_price',
        'change_amount',
        'change_percentage',
        'change_type',
        'reason',
        'notes',
        'changed_by',
        'changed_by_role',
        'requires_approval',
        'is_approved',
        'approved_by',
        'approved_at',
        'approval_notes',
        'accounting_impact',
        'accounting_processed',
        'accounting_processed_at',
    ];

    protected $casts = [
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'change_percentage' => 'decimal:2',
        'requires_approval' => 'boolean',
        'is_approved' => 'boolean',
        'accounting_impact' => 'boolean',
        'accounting_processed' => 'boolean',
        'approved_at' => 'datetime',
        'accounting_processed_at' => 'datetime',
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
     * من قام بالتغيير
     */
    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * من وافق على التغيير
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: التغييرات المعتمدة
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope: التغييرات المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('requires_approval', true)
            ->where('is_approved', false);
    }

    /**
     * Scope: التغييرات ذات التأثير المحاسبي
     */
    public function scopeWithAccountingImpact($query)
    {
        return $query->where('accounting_impact', true);
    }

    /**
     * Scope: التغييرات التي تم معالجتها محاسبياً
     */
    public function scopeAccountingProcessed($query)
    {
        return $query->where('accounting_processed', true);
    }

    /**
     * Scope: التغييرات التي لم تتم معالجتها محاسبياً
     */
    public function scopeAccountingPending($query)
    {
        return $query->where('accounting_impact', true)
            ->where('accounting_processed', false);
    }

    /**
     * Helper: حساب التغيير تلقائياً
     */
    public static function calculateChange($oldPrice, $newPrice)
    {
        $changeAmount = $newPrice - ($oldPrice ?? 0);
        $changePercentage = $oldPrice > 0 ? (($changeAmount / $oldPrice) * 100) : 0;

        return [
            'change_amount' => $changeAmount,
            'change_percentage' => round($changePercentage, 2)
        ];
    }

    /**
     * Helper: إنشاء سجل تغيير سعر
     */
    public static function logPriceChange($workOrderId, $oldPrice, $newPrice, $changeType, $reason = null, $requiresApproval = false)
    {
        $change = self::calculateChange($oldPrice, $newPrice);

        return self::create([
            'work_order_id' => $workOrderId,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'change_amount' => $change['change_amount'],
            'change_percentage' => $change['change_percentage'],
            'change_type' => $changeType,
            'reason' => $reason,
            'changed_by' => auth()->id(),
            'requires_approval' => $requiresApproval,
            'accounting_impact' => $changeType === 'final_approval',
        ]);
    }
}
