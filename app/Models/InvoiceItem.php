<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'item_type',
        'description',
        'work_order_item_id',
        'quantity',
        'unit',
        'unit_price',
        'discount_percentage',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'total_amount',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // ═══════════════════════════════════════════════════════════════
    // Boot
    // ═══════════════════════════════════════════════════════════════

    protected static function booted()
    {
        static::saving(function (InvoiceItem $item) {
            $item->calculateTotals();
        });

        static::saved(function (InvoiceItem $item) {
            $item->invoice?->updateTotals();
        });

        static::deleted(function (InvoiceItem $item) {
            $item->invoice?->updateTotals();
        });
    }

    // ═══════════════════════════════════════════════════════════════
    // العلاقات
    // ═══════════════════════════════════════════════════════════════

    /**
     * الفاتورة
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * بند طلب العمل
     */
    public function workOrderItem()
    {
        return $this->belongsTo(WorkOrderItem::class);
    }

    // ═══════════════════════════════════════════════════════════════
    // Methods
    // ═══════════════════════════════════════════════════════════════

    /**
     * حساب الإجماليات
     */
    public function calculateTotals(): void
    {
        $subtotal = $this->quantity * $this->unit_price;

        // حساب الخصم
        if ($this->discount_percentage > 0) {
            $this->discount_amount = $subtotal * ($this->discount_percentage / 100);
        }
        $subtotalAfterDiscount = $subtotal - ($this->discount_amount ?? 0);

        // حساب الضريبة
        if ($this->tax_percentage > 0) {
            $this->tax_amount = $subtotalAfterDiscount * ($this->tax_percentage / 100);
        }

        $this->total_amount = $subtotalAfterDiscount + ($this->tax_amount ?? 0);
    }

    // ═══════════════════════════════════════════════════════════════
    // Accessors
    // ═══════════════════════════════════════════════════════════════

    /**
     * المجموع الفرعي
     */
    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }
}
