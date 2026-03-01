<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDeduction extends Model
{
    protected $table = 'employee_deductions';

    // أنواع الخصومات
    const TYPE_ABSENCE = 'absence';
    const TYPE_LATE = 'late';
    const TYPE_EARLY_LEAVE = 'early_leave';
    const TYPE_VIOLATION = 'violation';
    const TYPE_DAMAGE = 'damage';
    const TYPE_OTHER = 'other';

    // تسميات الخصومات
    const TYPES = [
        self::TYPE_ABSENCE => 'غياب',
        self::TYPE_LATE => 'تأخير',
        self::TYPE_EARLY_LEAVE => 'خروج مبكر',
        self::TYPE_VIOLATION => 'مخالفة',
        self::TYPE_DAMAGE => 'تلفيات',
        self::TYPE_OTHER => 'خصم آخر',
    ];

    protected $fillable = [
        'company_code',
        'branch_id',
        'employee_id',
        'deduction_type',
        'custom_name',
        'amount',
        'deduction_date',
        'reason',
        'is_deducted',
        'deducted_in_payroll_id',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deduction_date' => 'date',
        'is_deducted' => 'boolean',
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
        return $this->belongsTo(Payroll::class, 'deducted_in_payroll_id');
    }

    // ============ Scopes ============

    public function scopePending($query)
    {
        return $query->where('is_deducted', false);
    }

    public function scopeDeducted($query)
    {
        return $query->where('is_deducted', true);
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
        return $query->whereYear('deduction_date', $year)
            ->whereMonth('deduction_date', $month);
    }

    // ============ Accessors ============

    public function getTypeNameAttribute(): string
    {
        if ($this->deduction_type === self::TYPE_OTHER && $this->custom_name) {
            return $this->custom_name;
        }
        return self::TYPES[$this->deduction_type] ?? $this->deduction_type;
    }

    // ============ Methods ============

    /**
     * تعليم الخصم كمخصوم
     */
    public function markAsDeducted($payrollId): void
    {
        $this->update([
            'is_deducted' => true,
            'deducted_in_payroll_id' => $payrollId,
        ]);
    }
}
