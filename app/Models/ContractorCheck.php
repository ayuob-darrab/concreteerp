<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractorCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_code',
        'branch_id',
        'contractor_id',
        'type',
        'check_number',
        'bank_name',
        'bank_account',
        'amount',
        'issue_date',
        'due_date',
        'drawer_name',
        'payee_name',
        'status',
        'notes',
        // إيداع
        'deposited_at',
        'deposited_by',
        // تحصيل
        'collected_at',
        'collected_amount',
        'collected_by',
        // رفض
        'rejection_reason',
        'rejected_at',
        // إرجاع
        'return_reason',
        'returned_at',
        'returned_by',
        // تظهير
        'endorsed_to',
        'endorsed_at',
        'endorsement_notes',
        'endorsed_by',
        // إلغاء
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'collected_amount' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
        'deposited_at' => 'datetime',
        'collected_at' => 'datetime',
        'rejected_at' => 'datetime',
        'returned_at' => 'datetime',
        'endorsed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // =====================
    // الثوابت
    // =====================

    // أنواع الشيكات
    const TYPE_RECEIVED = 'received';
    const TYPE_ISSUED = 'issued';

    const TYPES = [
        self::TYPE_RECEIVED => 'شيك مستلم',
        self::TYPE_ISSUED => 'شيك صادر',
    ];

    // حالات الشيك
    const STATUS_PENDING = 'pending';
    const STATUS_DEPOSITED = 'deposited';
    const STATUS_COLLECTED = 'collected';
    const STATUS_REJECTED = 'rejected';
    const STATUS_RETURNED = 'returned';
    const STATUS_ENDORSED = 'endorsed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING => 'قيد الانتظار',
        self::STATUS_DEPOSITED => 'مودع',
        self::STATUS_COLLECTED => 'محصل',
        self::STATUS_REJECTED => 'مرفوض',
        self::STATUS_RETURNED => 'مرتجع',
        self::STATUS_ENDORSED => 'مظهر',
        self::STATUS_CANCELLED => 'ملغي',
    ];

    const STATUS_BADGES = [
        self::STATUS_PENDING => 'warning',
        self::STATUS_DEPOSITED => 'info',
        self::STATUS_COLLECTED => 'success',
        self::STATUS_REJECTED => 'danger',
        self::STATUS_RETURNED => 'secondary',
        self::STATUS_ENDORSED => 'primary',
        self::STATUS_CANCELLED => 'dark',
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function depositedBy()
    {
        return $this->belongsTo(User::class, 'deposited_by');
    }

    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function endorsedBy()
    {
        return $this->belongsTo(User::class, 'endorsed_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function statusHistory()
    {
        return $this->hasMany(ContractorCheckStatusHistory::class, 'check_id');
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

    public function scopeReceived($query)
    {
        return $query->where('type', self::TYPE_RECEIVED);
    }

    public function scopeIssued($query)
    {
        return $query->where('type', self::TYPE_ISSUED);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDueBetween($query, $from, $to)
    {
        return $query->whereBetween('due_date', [$from, $to]);
    }

    public function scopeOverdue($query)
    {
        return $query->whereIn('status', ['pending', 'deposited'])
            ->where('due_date', '<', now());
    }

    // =====================
    // Accessors
    // =====================

    public function getTypeTextAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatusTextAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        return self::STATUS_BADGES[$this->status] ?? 'secondary';
    }

    public function getIsOverdueAttribute()
    {
        return in_array($this->status, ['pending', 'deposited'])
            && $this->due_date < now();
    }

    public function getDaysToMaturityAttribute()
    {
        if ($this->due_date < now()) {
            return -now()->diffInDays($this->due_date);
        }
        return now()->diffInDays($this->due_date);
    }
}
