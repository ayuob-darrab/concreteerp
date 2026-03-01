<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_code',
        'branch_id',
        'advance_number',
        'beneficiary_type',
        'beneficiary_id',
        'amount',
        'remaining_amount',
        'deduction_type',
        'deduction_value',
        'auto_deduction',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'approval_notes',
        'reason',
        'notes',
        'requested_at',
        'completed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'deduction_value' => 'decimal:2',
        'auto_deduction' => 'boolean',
        'approved_at' => 'datetime',
        'requested_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // ==================== الثوابت ====================

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const BENEFICIARY_EMPLOYEE = 'employee';
    const BENEFICIARY_AGENT = 'agent';
    const BENEFICIARY_SUPPLIER = 'supplier';
    const BENEFICIARY_CONTRACTOR = 'contractor';

    const DEDUCTION_PERCENTAGE = 'percentage';
    const DEDUCTION_FIXED = 'fixed';

    const STATUSES = [
        self::STATUS_PENDING => 'قيد الانتظار',
        self::STATUS_APPROVED => 'موافق عليها',
        self::STATUS_ACTIVE => 'نشطة',
        self::STATUS_COMPLETED => 'مكتملة',
        self::STATUS_CANCELLED => 'ملغية',
    ];

    const BENEFICIARY_TYPES = [
        self::BENEFICIARY_EMPLOYEE => 'موظف',
        self::BENEFICIARY_AGENT => 'مندوب',
        self::BENEFICIARY_SUPPLIER => 'مورد',
        self::BENEFICIARY_CONTRACTOR => 'مقاول',
    ];

    // ==================== العلاقات ====================

    /**
     * الشركة
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    /**
     * الفرع
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * المستفيد (polymorphic)
     */
    public function beneficiary()
    {
        return $this->morphTo(__FUNCTION__, 'beneficiary_type', 'beneficiary_id');
    }

    /**
     * الموظف المستفيد
     */
    public function employeeBeneficiary()
    {
        return $this->belongsTo(Employee::class, 'beneficiary_id');
    }

    /**
     * المورد المستفيد
     */
    public function supplierBeneficiary()
    {
        return $this->belongsTo(Supplier::class, 'beneficiary_id');
    }

    /**
     * المقاول المستفيد
     */
    public function contractorBeneficiary()
    {
        return $this->belongsTo(Contractor::class, 'beneficiary_id');
    }

    /**
     * من طلب السلفة
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * من وافق على السلفة
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * من أنشأ السجل
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * من حدّث السجل
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * الدفعات
     */
    public function payments()
    {
        return $this->hasMany(AdvancePayment::class)->orderBy('paid_at', 'desc');
    }

    // ==================== Scopes ====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForBeneficiary($query, $type, $id)
    {
        return $query->where('beneficiary_type', $type)
            ->where('beneficiary_id', $id);
    }

    // ==================== Accessors ====================

    /**
     * نسبة الإنجاز
     */
    public function getCompletionPercentageAttribute()
    {
        if ($this->amount == 0) return 100;
        $paid = $this->amount - $this->remaining_amount;
        return round(($paid / $this->amount) * 100, 2);
    }

    /**
     * المبلغ المسدد
     */
    public function getPaidAmountAttribute()
    {
        return $this->amount - $this->remaining_amount;
    }

    /**
     * اسم المستفيد
     */
    public function getBeneficiaryNameAttribute()
    {
        switch ($this->beneficiary_type) {
            case self::BENEFICIARY_EMPLOYEE:
                return $this->employeeBeneficiary?->fullname ?? 'غير معروف';
            case self::BENEFICIARY_SUPPLIER:
                return $this->supplierBeneficiary?->name ?? 'غير معروف';
            case self::BENEFICIARY_CONTRACTOR:
                return $this->contractorBeneficiary?->contract_name ?? 'غير معروف';
            default:
                return 'غير معروف';
        }
    }

    /**
     * نص الحالة
     */
    public function getStatusTextAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * نص نوع المستفيد
     */
    public function getBeneficiaryTypeTextAttribute()
    {
        return self::BENEFICIARY_TYPES[$this->beneficiary_type] ?? $this->beneficiary_type;
    }

    // ==================== Methods ====================

    /**
     * توليد رقم السلفة
     */
    public static function generateAdvanceNumber($branchCode)
    {
        $prefix = 'ADV-' . $branchCode . '-' . date('Ym') . '-';
        $lastAdvance = self::where('advance_number', 'like', $prefix . '%')
            ->orderBy('advance_number', 'desc')
            ->first();

        if ($lastAdvance) {
            $lastNumber = (int) substr($lastAdvance->advance_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * هل يمكن الموافقة
     */
    public function canBeApproved()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * هل يمكن الدفع
     */
    public function canAcceptPayment()
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_ACTIVE])
            && $this->remaining_amount > 0;
    }

    /**
     * هل يمكن الإلغاء
     */
    public function canBeCancelled()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    /**
     * حساب قيمة الاستقطاع
     */
    public function calculateDeductionAmount($baseSalary = null)
    {
        if ($this->deduction_type === self::DEDUCTION_PERCENTAGE && $baseSalary) {
            return ($baseSalary * $this->deduction_value) / 100;
        }
        return $this->deduction_value;
    }
}
