<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryAdjustment extends Model
{
    protected $table = 'salary_adjustments';

    // أنواع التعديل
    const TYPE_INCREASE = 'increase';
    const TYPE_DECREASE = 'decrease';

    const TYPES = [
        self::TYPE_INCREASE => 'زيادة',
        self::TYPE_DECREASE => 'تخفيض',
    ];

    protected $fillable = [
        'company_code',
        'branch_id',
        'employee_id',
        'adjustment_type',
        'old_salary',
        'new_salary',
        'difference',
        'effective_date',
        'reason',
        'approved_by',
    ];

    protected $casts = [
        'old_salary' => 'decimal:2',
        'new_salary' => 'decimal:2',
        'difference' => 'decimal:2',
        'effective_date' => 'date',
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

    // ============ Scopes ============

    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeIncreases($query)
    {
        return $query->where('adjustment_type', self::TYPE_INCREASE);
    }

    public function scopeDecreases($query)
    {
        return $query->where('adjustment_type', self::TYPE_DECREASE);
    }

    // ============ Accessors ============

    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->adjustment_type] ?? $this->adjustment_type;
    }

    public function getPercentageChangeAttribute(): float
    {
        if ($this->old_salary == 0) return 0;
        return round(($this->difference / $this->old_salary) * 100, 2);
    }

    // ============ Methods ============

    /**
     * إنشاء تعديل راتب وتحديث راتب الموظف
     */
    public static function createAdjustment(Employee $employee, float $newSalary, string $reason, int $approvedBy): self
    {
        $oldSalary = $employee->salary;
        $difference = $newSalary - $oldSalary;

        $adjustment = self::create([
            'company_code' => $employee->company_code,
            'branch_id' => $employee->branch_id,
            'employee_id' => $employee->id,
            'adjustment_type' => $difference >= 0 ? self::TYPE_INCREASE : self::TYPE_DECREASE,
            'old_salary' => $oldSalary,
            'new_salary' => $newSalary,
            'difference' => abs($difference),
            'effective_date' => now(),
            'reason' => $reason,
            'approved_by' => $approvedBy,
        ]);

        // تحديث راتب الموظف
        $employee->update(['salary' => $newSalary]);

        return $adjustment;
    }
}
