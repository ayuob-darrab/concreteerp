<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractorReceipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_code',
        'branch_id',
        'contractor_id',
        'invoice_id',
        'type',
        'receipt_number',
        'receipt_date',
        'amount',
        'payment_method',
        'bank_name',
        'transfer_reference',
        'check_number',
        'check_bank',
        'check_date',
        'description',
        'status',
        'approved_at',
        'approved_by',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'receipt_date' => 'date',
        'check_date' => 'date',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // =====================
    // الثوابت
    // =====================

    // أنواع السندات
    const TYPE_RECEIPT = 'receipt';
    const TYPE_PAYMENT = 'payment';

    const TYPES = [
        self::TYPE_RECEIPT => 'سند قبض',
        self::TYPE_PAYMENT => 'سند صرف',
    ];

    // طرق الدفع
    const PAYMENT_CASH = 'cash';
    const PAYMENT_BANK_TRANSFER = 'bank_transfer';
    const PAYMENT_CHECK = 'check';

    const PAYMENT_METHODS = [
        self::PAYMENT_CASH => 'نقداً',
        self::PAYMENT_BANK_TRANSFER => 'تحويل بنكي',
        self::PAYMENT_CHECK => 'شيك',
    ];

    // حالات السند
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING => 'قيد الاعتماد',
        self::STATUS_APPROVED => 'معتمد',
        self::STATUS_CANCELLED => 'ملغي',
    ];

    const STATUS_BADGES = [
        self::STATUS_PENDING => 'warning',
        self::STATUS_APPROVED => 'success',
        self::STATUS_CANCELLED => 'danger',
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

    public function invoice()
    {
        return $this->belongsTo(ContractorInvoice::class, 'invoice_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
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

    public function scopeReceipts($query)
    {
        return $query->where('type', self::TYPE_RECEIPT);
    }

    public function scopePayments($query)
    {
        return $query->where('type', self::TYPE_PAYMENT);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeDateBetween($query, $from, $to)
    {
        return $query->whereBetween('receipt_date', [$from, $to]);
    }

    // =====================
    // Accessors
    // =====================

    public function getTypeTextAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getPaymentMethodTextAttribute()
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    public function getStatusTextAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        return self::STATUS_BADGES[$this->status] ?? 'secondary';
    }

    public function getTypeBadgeAttribute()
    {
        return $this->type === self::TYPE_RECEIPT ? 'success' : 'danger';
    }
}
