<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractorInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_code',
        'branch_id',
        'contractor_id',
        'work_order_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'description',
        'items',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount',
        'total',
        'paid_amount',
        'status',
        'notes',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'issued_at',
        'paid_at',
        'created_by',
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'issued_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // =====================
    // الثوابت
    // =====================

    const STATUS_DRAFT = 'draft';
    const STATUS_ISSUED = 'issued';
    const STATUS_PARTIAL = 'partial';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_OVERDUE = 'overdue';

    const STATUSES = [
        self::STATUS_DRAFT => 'مسودة',
        self::STATUS_ISSUED => 'صادرة',
        self::STATUS_PARTIAL => 'مسددة جزئياً',
        self::STATUS_PAID => 'مسددة',
        self::STATUS_CANCELLED => 'ملغاة',
        self::STATUS_OVERDUE => 'متأخرة',
    ];

    const STATUS_BADGES = [
        self::STATUS_DRAFT => 'secondary',
        self::STATUS_ISSUED => 'primary',
        self::STATUS_PARTIAL => 'warning',
        self::STATUS_PAID => 'success',
        self::STATUS_CANCELLED => 'danger',
        self::STATUS_OVERDUE => 'dark',
    ];

    // =====================
    // العلاقات
    // =====================

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function payments()
    {
        return $this->hasMany(ContractorReceipt::class, 'invoice_id');
    }

    // =====================
    // Scopes
    // =====================

    public function scopeCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    public function scopeBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'issued')
            ->where('due_date', '<', now())
            ->whereColumn('paid_amount', '<', 'total');
    }

    // =====================
    // Accessors
    // =====================

    public function getStatusTextAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        return self::STATUS_BADGES[$this->status] ?? 'secondary';
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total - $this->paid_amount;
    }

    public function getIsOverdueAttribute()
    {
        return $this->status === 'issued'
            && $this->due_date < now()
            && $this->paid_amount < $this->total;
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->is_overdue) {
            return 0;
        }
        return now()->diffInDays($this->due_date);
    }
}
