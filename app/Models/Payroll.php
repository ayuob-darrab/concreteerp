<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $table = 'payroll';

    // الحالات
    const STATUS_DRAFT = 'draft';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_DRAFT => 'مسودة',
        self::STATUS_APPROVED => 'معتمد',
        self::STATUS_PAID => 'مدفوع',
        self::STATUS_CANCELLED => 'ملغي',
    ];

    // طرق الدفع
    const PAYMENT_CASH = 'cash';
    const PAYMENT_BANK = 'bank_transfer';
    const PAYMENT_CHECK = 'check';

    const PAYMENT_METHODS = [
        self::PAYMENT_CASH => 'نقداً',
        self::PAYMENT_BANK => 'تحويل بنكي',
        self::PAYMENT_CHECK => 'شيك',
    ];

    protected $fillable = [
        'company_code',
        'branch_id',
        'employee_id',
        'payroll_month',
        'payroll_year',
        'basic_salary',
        'allowances_total',
        'allowances_details',
        'bonuses_total',
        'bonuses_details',
        'overtime_amount',
        'overtime_hours',
        'deductions_total',
        'deductions_details',
        'advances_deducted',
        'advances_details',
        'absence_deduction',
        'absence_days',
        'insurance_deduction',
        'tax_deduction',
        'gross_salary',
        'net_salary',
        'status',
        'payment_method',
        'payment_reference',
        'paid_at',
        'paid_by',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'allowances_total' => 'decimal:2',
        'allowances_details' => 'array',
        'bonuses_total' => 'decimal:2',
        'bonuses_details' => 'array',
        'overtime_amount' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'deductions_total' => 'decimal:2',
        'deductions_details' => 'array',
        'advances_deducted' => 'decimal:2',
        'advances_details' => 'array',
        'absence_deduction' => 'decimal:2',
        'insurance_deduction' => 'decimal:2',
        'tax_deduction' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'paid_at' => 'datetime',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function deductions()
    {
        return $this->hasMany(EmployeeDeduction::class, 'deducted_in_payroll_id');
    }

    public function bonuses()
    {
        return $this->hasMany(EmployeeBonus::class, 'paid_in_payroll_id');
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

    public function scopeForMonth($query, $year, $month)
    {
        return $query->where('payroll_year', $year)->where('payroll_month', $month);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    // ============ Accessors ============

    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getPaymentMethodNameAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    public function getPeriodAttribute(): string
    {
        $months = [
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر'
        ];
        return $months[$this->payroll_month] . ' ' . $this->payroll_year;
    }

    public function getTotalAdditionsAttribute(): float
    {
        return $this->allowances_total + $this->bonuses_total + $this->overtime_amount;
    }

    public function getTotalDeductionsAttribute(): float
    {
        return $this->deductions_total + $this->advances_deducted + $this->absence_deduction +
            $this->insurance_deduction + $this->tax_deduction;
    }

    // ============ Methods ============

    /**
     * حساب المرتب
     */
    public function calculate(): void
    {
        $this->gross_salary = $this->basic_salary + $this->allowances_total +
            $this->bonuses_total + $this->overtime_amount;

        $totalDeductions = $this->deductions_total + $this->advances_deducted +
            $this->absence_deduction + $this->insurance_deduction +
            $this->tax_deduction;

        $this->net_salary = $this->gross_salary - $totalDeductions;
    }

    /**
     * اعتماد كشف الراتب
     */
    public function approve($userId): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    /**
     * صرف الراتب
     */
    public function markAsPaid($userId, $paymentMethod, $reference = null): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'payment_method' => $paymentMethod,
            'payment_reference' => $reference,
            'paid_at' => now(),
            'paid_by' => $userId,
        ]);
    }

    /**
     * إلغاء كشف الراتب
     */
    public function cancel(): void
    {
        // إعادة حالة الخصومات والمكافآت
        $this->deductions()->update(['is_deducted' => false, 'deducted_in_payroll_id' => null]);
        $this->bonuses()->update(['is_paid' => false, 'paid_in_payroll_id' => null]);

        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    /**
     * التحقق من إمكانية التعديل
     */
    public function canBeEdited(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * التحقق من إمكانية الاعتماد
     */
    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * التحقق من إمكانية الصرف
     */
    public function canBePaid(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
