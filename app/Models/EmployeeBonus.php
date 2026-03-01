<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeBonus extends Model
{
    protected $table = 'employee_bonuses';

    // أنواع المكافآت
    const TYPE_PERFORMANCE = 'performance';
    const TYPE_ATTENDANCE = 'attendance';
    const TYPE_OVERTIME = 'overtime';
    const TYPE_EID = 'eid';
    const TYPE_ANNUAL = 'annual';
    const TYPE_PROJECT = 'project';
    const TYPE_OTHER = 'other';

    // تسميات المكافآت
    const TYPES = [
        self::TYPE_PERFORMANCE => 'مكافأة أداء',
        self::TYPE_ATTENDANCE => 'مكافأة انضباط',
        self::TYPE_OVERTIME => 'أجر إضافي',
        self::TYPE_EID => 'مكافأة عيد',
        self::TYPE_ANNUAL => 'مكافأة سنوية',
        self::TYPE_PROJECT => 'مكافأة مشروع',
        self::TYPE_OTHER => 'مكافأة أخرى',
    ];

    protected $fillable = [
        'company_code',
        'branch_id',
        'employee_id',
        'bonus_type',
        'custom_name',
        'amount',
        'bonus_date',
        'reason',
        'is_paid',
        'paid_in_payroll_id',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'bonus_date' => 'date',
        'is_paid' => 'boolean',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'paid_in_payroll_id');
    }

    // ============ Scopes ============

    public function scopePending($query)
    {
        return $query->where('is_paid', false);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('bonus_date', $year)
            ->whereMonth('bonus_date', $month);
    }

    // ============ Accessors ============

    public function getTypeNameAttribute(): string
    {
        if ($this->bonus_type === self::TYPE_OTHER && $this->custom_name) {
            return $this->custom_name;
        }
        return self::TYPES[$this->bonus_type] ?? $this->bonus_type;
    }

    // ============ Methods ============

    /**
     * تعليم المكافأة كمدفوعة
     */
    public function markAsPaid($payrollId): void
    {
        $this->update([
            'is_paid' => true,
            'paid_in_payroll_id' => $payrollId,
        ]);
    }
}
