<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAllowance extends Model
{
    use SoftDeletes;

    protected $table = 'employee_allowances';

    // أنواع البدلات
    const TYPE_TRANSPORTATION = 'transportation';
    const TYPE_HOUSING = 'housing';
    const TYPE_MEALS = 'meals';
    const TYPE_PHONE = 'phone';
    const TYPE_OTHER = 'other';

    // تسميات البدلات
    const TYPES = [
        self::TYPE_TRANSPORTATION => 'بدل مواصلات',
        self::TYPE_HOUSING => 'بدل سكن',
        self::TYPE_MEALS => 'بدل طعام',
        self::TYPE_PHONE => 'بدل هاتف',
        self::TYPE_OTHER => 'بدل آخر',
    ];

    protected $fillable = [
        'company_code',
        'branch_id',
        'employee_id',
        'allowance_type',
        'custom_name',
        'amount',
        'is_recurring',
        'start_date',
        'end_date',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
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

    // ============ Scopes ============

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeValidForDate($query, $date = null)
    {
        $date = $date ?? now();
        return $query->where(function ($q) use ($date) {
            $q->whereNull('start_date')
                ->orWhere('start_date', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('end_date')
                ->orWhere('end_date', '>=', $date);
        });
    }

    // ============ Accessors ============

    public function getTypeNameAttribute(): string
    {
        if ($this->allowance_type === self::TYPE_OTHER && $this->custom_name) {
            return $this->custom_name;
        }
        return self::TYPES[$this->allowance_type] ?? $this->allowance_type;
    }

    // ============ Methods ============

    /**
     * التحقق إذا كان البدل ساري المفعول
     */
    public function isValidNow(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now()->toDateString();

        if ($this->start_date && $this->start_date > $now) {
            return false;
        }

        if ($this->end_date && $this->end_date < $now) {
            return false;
        }

        return true;
    }
}
