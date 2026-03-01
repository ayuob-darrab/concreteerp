<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeave extends Model
{
    protected $table = 'employee_leaves';

    // أنواع الإجازات
    const TYPE_ANNUAL = 'annual';
    const TYPE_SICK = 'sick';
    const TYPE_EMERGENCY = 'emergency';
    const TYPE_UNPAID = 'unpaid';
    const TYPE_MATERNITY = 'maternity';
    const TYPE_PATERNITY = 'paternity';
    const TYPE_STUDY = 'study';
    const TYPE_OTHER = 'other';

    // تسميات الإجازات
    const TYPES = [
        self::TYPE_ANNUAL => 'إجازة سنوية',
        self::TYPE_SICK => 'إجازة مرضية',
        self::TYPE_EMERGENCY => 'إجازة طارئة',
        self::TYPE_UNPAID => 'إجازة بدون راتب',
        self::TYPE_MATERNITY => 'إجازة أمومة',
        self::TYPE_PATERNITY => 'إجازة أبوة',
        self::TYPE_STUDY => 'إجازة دراسية',
        self::TYPE_OTHER => 'إجازة أخرى',
    ];

    // الحالات
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING => 'معلقة',
        self::STATUS_APPROVED => 'موافق عليها',
        self::STATUS_REJECTED => 'مرفوضة',
        self::STATUS_CANCELLED => 'ملغاة',
    ];

    protected $fillable = [
        'company_code',
        'branch_id',
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'days_count',
        'reason',
        'attachment',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'requested_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // ============ العلاقات ============

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // ============ Scopes ============

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($q2) use ($startDate, $endDate) {
                    $q2->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        });
    }

    // ============ Accessors ============

    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->leave_type] ?? $this->leave_type;
    }

    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    // ============ Methods ============

    /**
     * الموافقة على الإجازة
     */
    public function approve($userId, $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        // خصم من رصيد الإجازات
        $this->deductFromBalance();
    }

    /**
     * رفض الإجازة
     */
    public function reject($userId, $reason): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_by' => $userId,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * إلغاء الإجازة
     */
    public function cancel(): void
    {
        // إعادة الرصيد إذا كانت موافق عليها
        if ($this->status === self::STATUS_APPROVED) {
            $this->restoreBalance();
        }

        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    /**
     * خصم من رصيد الإجازات
     */
    protected function deductFromBalance(): void
    {
        if ($this->leave_type === self::TYPE_ANNUAL) {
            $this->employee->decrement('annual_leave_balance', $this->days_count);
        } elseif ($this->leave_type === self::TYPE_SICK) {
            $this->employee->decrement('sick_leave_balance', $this->days_count);
        }
    }

    /**
     * إعادة الرصيد
     */
    protected function restoreBalance(): void
    {
        if ($this->leave_type === self::TYPE_ANNUAL) {
            $this->employee->increment('annual_leave_balance', $this->days_count);
        } elseif ($this->leave_type === self::TYPE_SICK) {
            $this->employee->increment('sick_leave_balance', $this->days_count);
        }
    }

    /**
     * حساب عدد الأيام
     */
    public static function calculateDays($startDate, $endDate): int
    {
        return \Carbon\Carbon::parse($startDate)->diffInDays($endDate) + 1;
    }
}
