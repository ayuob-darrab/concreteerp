<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'invoice_type',
        'account_id',
        'work_order_id',
        'company_code',
        'branch_id',
        'party_name',
        'party_phone',
        'party_address',
        'party_tax_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'discount_percentage',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'currency',
        'exchange_rate',
        'status',
        'notes',
        'terms',
        'created_by',
        'approved_by',
        'approved_at',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // الحالات
    const STATUS_DRAFT = 'draft';
    const STATUS_ISSUED = 'issued';
    const STATUS_PARTIALLY_PAID = 'partially_paid';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_CANCELLED = 'cancelled';

    // ═══════════════════════════════════════════════════════════════
    // Boot
    // ═══════════════════════════════════════════════════════════════

    protected static function booted()
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateNumber($invoice->company_code);
            }
            $invoice->remaining_amount = $invoice->total_amount - ($invoice->paid_amount ?? 0);
        });
    }

    // ═══════════════════════════════════════════════════════════════
    // العلاقات
    // ═══════════════════════════════════════════════════════════════

    /**
     * الحساب
     */
    public function account()
    {
        return $this->belongsTo(ContractorAccount::class, 'account_id');
    }

    /**
     * طلب العمل
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * بنود الفاتورة
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * المعاملات المالية
     */
    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    /**
     * من أنشأ الفاتورة
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * من وافق على الفاتورة
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ═══════════════════════════════════════════════════════════════
    // Scopes
    // ═══════════════════════════════════════════════════════════════

    /**
     * حسب الشركة
     */
    public function scopeForCompany($query, string $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    /**
     * حسب الفرع
     */
    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * حسب الحالة
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * الفواتير المستحقة
     */
    public function scopeUnpaid($query)
    {
        return $query->where('remaining_amount', '>', 0)
            ->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_PAID]);
    }

    /**
     * الفواتير المتأخرة
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', today())
            ->where('remaining_amount', '>', 0)
            ->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_PAID]);
    }

    /**
     * حسب الفترة
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('invoice_date', [$startDate, $endDate]);
    }

    // ═══════════════════════════════════════════════════════════════
    // Accessors
    // ═══════════════════════════════════════════════════════════════

    /**
     * نص الحالة
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_ISSUED => 'صادرة',
            self::STATUS_PARTIALLY_PAID => 'مدفوعة جزئياً',
            self::STATUS_PAID => 'مدفوعة',
            self::STATUS_OVERDUE => 'متأخرة',
            self::STATUS_CANCELLED => 'ملغاة',
            default => 'غير معروف',
        };
    }

    /**
     * لون الحالة
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_ISSUED => 'blue',
            self::STATUS_PARTIALLY_PAID => 'yellow',
            self::STATUS_PAID => 'green',
            self::STATUS_OVERDUE => 'red',
            self::STATUS_CANCELLED => 'gray',
            default => 'gray',
        };
    }

    /**
     * هل متأخرة
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->isPast() && $this->remaining_amount > 0;
    }

    /**
     * أيام التأخير
     */
    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) return 0;
        return today()->diffInDays($this->due_date);
    }

    /**
     * نسبة السداد
     */
    public function getPaymentPercentageAttribute(): float
    {
        if ($this->total_amount <= 0) return 0;
        return round(($this->paid_amount / $this->total_amount) * 100, 2);
    }

    // ═══════════════════════════════════════════════════════════════
    // Methods
    // ═══════════════════════════════════════════════════════════════

    /**
     * توليد رقم فاتورة
     */
    public static function generateNumber(string $companyCode): string
    {
        $prefix = 'INV';
        $year = date('Y');

        $lastInvoice = self::where('company_code', $companyCode)
            ->where('invoice_number', 'like', "{$prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastInvoice
            ? (int)substr($lastInvoice->invoice_number, -6) + 1
            : 1;

        return sprintf('%s-%s-%06d', $prefix, $year, $nextNumber);
    }

    /**
     * إصدار الفاتورة
     */
    public function issue(): bool
    {
        return $this->update([
            'status' => self::STATUS_ISSUED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }

    /**
     * إلغاء الفاتورة
     */
    public function cancel(string $reason): bool
    {
        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * تسجيل دفعة
     */
    public function recordPayment(float $amount): void
    {
        $this->paid_amount += $amount;
        $this->remaining_amount = max(0, $this->total_amount - $this->paid_amount);

        if ($this->remaining_amount <= 0) {
            $this->status = self::STATUS_PAID;
        } else {
            $this->status = self::STATUS_PARTIALLY_PAID;
        }

        $this->save();
    }

    /**
     * تحديث حالة التأخير
     */
    public function checkOverdue(): void
    {
        if ($this->is_overdue && $this->status !== self::STATUS_OVERDUE) {
            $this->update(['status' => self::STATUS_OVERDUE]);
        }
    }
}
