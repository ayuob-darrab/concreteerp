<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_code',
        'branch_id',
        'max_advance_employee',
        'max_advance_agent',
        'max_advance_supplier',
        'max_advance_contractor',
        'default_deduction_employee',
        'default_deduction_agent',
        'default_deduction_supplier',
        'default_deduction_contractor',
        'auto_deduction_enabled',
        'allow_overpayment',
    ];

    protected $casts = [
        'max_advance_employee' => 'decimal:2',
        'max_advance_agent' => 'decimal:2',
        'max_advance_supplier' => 'decimal:2',
        'max_advance_contractor' => 'decimal:2',
        'default_deduction_employee' => 'decimal:2',
        'default_deduction_agent' => 'decimal:2',
        'default_deduction_supplier' => 'decimal:2',
        'default_deduction_contractor' => 'decimal:2',
        'auto_deduction_enabled' => 'boolean',
        'allow_overpayment' => 'boolean',
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

    // ==================== Methods ====================

    /**
     * الحصول على الإعدادات للفرع أو الشركة
     */
    public static function getSettings($companyCode, $branchId = null)
    {
        // التحقق من وجود company_code
        if (empty($companyCode)) {
            // إرجاع كائن إعدادات افتراضية بدون حفظه في قاعدة البيانات
            return new self([
                'company_code' => null,
                'branch_id' => null,
                'max_advance_employee' => 50000000,
                'max_advance_agent' => 100000000,
                'max_advance_supplier' => 200000000,
                'max_advance_contractor' => 200000000,
                'default_deduction_employee' => 10,
                'default_deduction_agent' => 10,
                'default_deduction_supplier' => 10,
                'default_deduction_contractor' => 10,
                'auto_deduction_enabled' => true,
                'allow_overpayment' => false,
            ]);
        }

        // أولاً: البحث عن إعدادات الفرع
        if ($branchId) {
            $settings = self::where('company_code', $companyCode)
                ->where('branch_id', $branchId)
                ->first();

            if ($settings) {
                return $settings;
            }
        }

        // ثانياً: البحث عن إعدادات الشركة
        $settings = self::where('company_code', $companyCode)
            ->whereNull('branch_id')
            ->first();

        // إذا لم توجد، إنشاء إعدادات افتراضية
        if (!$settings) {
            $settings = self::create([
                'company_code' => $companyCode,
                'branch_id' => null,
                'max_advance_employee' => 50000000,
                'max_advance_agent' => 100000000,
                'max_advance_supplier' => 200000000,
                'max_advance_contractor' => 200000000,
                'default_deduction_employee' => 10,
                'default_deduction_agent' => 10,
                'default_deduction_supplier' => 10,
                'default_deduction_contractor' => 10,
                'auto_deduction_enabled' => true,
                'allow_overpayment' => false,
            ]);
        }

        return $settings;
    }

    /**
     * الحصول على الحد الأقصى للسلفة حسب النوع
     */
    public function getMaxAdvance($beneficiaryType)
    {
        return match ($beneficiaryType) {
            'employee' => $this->max_advance_employee,
            'agent' => $this->max_advance_agent,
            'supplier' => $this->max_advance_supplier,
            'contractor' => $this->max_advance_contractor,
            default => 0,
        };
    }

    /**
     * الحصول على نسبة الاستقطاع الافتراضية حسب النوع
     */
    public function getDefaultDeduction($beneficiaryType)
    {
        return match ($beneficiaryType) {
            'employee' => $this->default_deduction_employee,
            'agent' => $this->default_deduction_agent,
            'supplier' => $this->default_deduction_supplier,
            'contractor' => $this->default_deduction_contractor,
            default => 0,
        };
    }
}
