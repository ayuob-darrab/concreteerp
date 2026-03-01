<?php

namespace App\Services;

use App\Models\Advance;
use App\Models\AdvancePayment;
use App\Models\AdvanceSetting;
use App\Models\Employee;
use App\Models\Supplier;
use App\Models\Contractor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class AdvanceService
{
    /**
     * إنشاء سلفة جديدة
     */
    public function createAdvance(array $data): Advance
    {
        return DB::transaction(function () use ($data) {
            // الحصول على إعدادات السلف
            $settings = AdvanceSetting::getSettings($data['company_code'], $data['branch_id']);

            // التحقق من الحد الأقصى
            $maxAdvance = $settings->getMaxAdvance($data['beneficiary_type']);
            if ($maxAdvance > 0 && $data['amount'] > $maxAdvance) {
                throw new Exception("المبلغ يتجاوز الحد الأقصى المسموح ({$maxAdvance})");
            }

            // التحقق من عدم وجود سلفة نشطة للمستفيد
            $existingAdvance = Advance::forBeneficiary($data['beneficiary_type'], $data['beneficiary_id'])
                ->whereIn('status', [Advance::STATUS_PENDING, Advance::STATUS_APPROVED, Advance::STATUS_ACTIVE])
                ->first();

            if ($existingAdvance && !($data['allow_multiple'] ?? false)) {
                throw new Exception("يوجد سلفة نشطة للمستفيد رقم: {$existingAdvance->advance_number}");
            }

            // توليد رقم السلفة
            $branchCode = $data['branch_code'] ?? 'BR001';
            $advanceNumber = Advance::generateAdvanceNumber($branchCode);

            // إنشاء السلفة
            $advance = Advance::create([
                'company_code' => $data['company_code'],
                'branch_id' => $data['branch_id'],
                'advance_number' => $advanceNumber,
                'beneficiary_type' => $data['beneficiary_type'],
                'beneficiary_id' => $data['beneficiary_id'],
                'amount' => $data['amount'],
                'remaining_amount' => $data['amount'],
                'deduction_type' => $data['deduction_type'] ?? Advance::DEDUCTION_PERCENTAGE,
                'deduction_value' => $data['deduction_value'] ?? $settings->getDefaultDeduction($data['beneficiary_type']),
                'auto_deduction' => $data['auto_deduction'] ?? true,
                'status' => Advance::STATUS_PENDING,
                'reason' => $data['reason'] ?? null,
                'notes' => $data['notes'] ?? null,
                'requested_by' => Auth::id(),
                'requested_at' => now(),
                'created_by' => Auth::id(),
            ]);

            return $advance;
        });
    }

    /**
     * الموافقة على السلفة
     */
    public function approveAdvance(Advance $advance, array $data = []): Advance
    {
        if (!$advance->canBeApproved()) {
            throw new Exception("لا يمكن الموافقة على هذه السلفة");
        }

        return DB::transaction(function () use ($advance, $data) {
            $advance->update([
                'status' => Advance::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_notes' => $data['notes'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            // تفعيل السلفة مباشرة
            $advance->update([
                'status' => Advance::STATUS_ACTIVE,
            ]);

            return $advance->fresh();
        });
    }

    /**
     * رفض السلفة
     */
    public function rejectAdvance(Advance $advance, string $reason): Advance
    {
        if (!$advance->canBeApproved()) {
            throw new Exception("لا يمكن رفض هذه السلفة");
        }

        $advance->update([
            'status' => Advance::STATUS_CANCELLED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $reason,
            'updated_by' => Auth::id(),
        ]);

        return $advance->fresh();
    }

    /**
     * تسديد دفعة
     */
    public function makePayment(Advance $advance, array $data): AdvancePayment
    {
        if (!$advance->canAcceptPayment()) {
            throw new Exception("لا يمكن تسديد دفعات على هذه السلفة");
        }

        // التحقق من إعدادات الدفع الزائد
        $settings = AdvanceSetting::getSettings($advance->company_code, $advance->branch_id);
        if (!$settings->allow_overpayment && $data['amount'] > $advance->remaining_amount) {
            throw new Exception("المبلغ يتجاوز المتبقي من السلفة");
        }

        return DB::transaction(function () use ($advance, $data) {
            $balanceBefore = $advance->remaining_amount;
            $balanceAfter = $balanceBefore - $data['amount'];

            // إنشاء الدفعة
            $payment = AdvancePayment::create([
                'advance_id' => $advance->id,
                'payment_number' => AdvancePayment::generatePaymentNumber($advance->id),
                'payment_type' => $data['payment_type'] ?? AdvancePayment::TYPE_MANUAL,
                'amount' => $data['amount'],
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'notes' => $data['notes'] ?? null,
                'paid_at' => $data['paid_at'] ?? now(),
                'paid_by' => Auth::id(),
            ]);

            // تحديث رصيد السلفة
            $advance->update([
                'remaining_amount' => $balanceAfter,
                'updated_by' => Auth::id(),
            ]);

            // التحقق من اكتمال السلفة
            if ($balanceAfter <= 0) {
                $advance->update([
                    'status' => Advance::STATUS_COMPLETED,
                    'completed_at' => now(),
                ]);
            }

            return $payment;
        });
    }

    /**
     * استقطاع تلقائي من الراتب
     */
    public function autoDeductFromSalary(Employee $employee, $payrollId): ?AdvancePayment
    {
        // البحث عن سلفة نشطة للموظف
        $advance = Advance::forBeneficiary(Advance::BENEFICIARY_EMPLOYEE, $employee->id)
            ->active()
            ->where('auto_deduction', true)
            ->first();

        if (!$advance) {
            return null;
        }

        // حساب مبلغ الاستقطاع
        $deductAmount = $advance->calculateDeductionAmount($employee->salary);
        $deductAmount = min($deductAmount, $advance->remaining_amount);

        if ($deductAmount <= 0) {
            return null;
        }

        return $this->makePayment($advance, [
            'amount' => $deductAmount,
            'payment_type' => AdvancePayment::TYPE_SALARY_DEDUCTION,
            'reference_type' => 'payroll',
            'reference_id' => $payrollId,
            'notes' => "استقطاع تلقائي من راتب شهر " . now()->format('Y-m'),
        ]);
    }

    /**
     * استقطاع تلقائي من فاتورة مورد
     */
    public function autoDeductFromSupplierInvoice(Supplier $supplier, $invoiceId, $invoiceAmount): ?AdvancePayment
    {
        // البحث عن سلفة نشطة للمورد
        $advance = Advance::forBeneficiary(Advance::BENEFICIARY_SUPPLIER, $supplier->id)
            ->active()
            ->where('auto_deduction', true)
            ->first();

        if (!$advance) {
            return null;
        }

        // حساب مبلغ الاستقطاع (نسبة من الفاتورة)
        $deductAmount = $advance->calculateDeductionAmount($invoiceAmount);
        $deductAmount = min($deductAmount, $advance->remaining_amount);

        if ($deductAmount <= 0) {
            return null;
        }

        return $this->makePayment($advance, [
            'amount' => $deductAmount,
            'payment_type' => AdvancePayment::TYPE_INVOICE_DEDUCTION,
            'reference_type' => 'supplier_invoice',
            'reference_id' => $invoiceId,
            'notes' => "استقطاع تلقائي من فاتورة مورد",
        ]);
    }

    /**
     * تفعيل/تعطيل الاستقطاع التلقائي
     */
    public function toggleAutoDeduction(Advance $advance): Advance
    {
        $advance->update([
            'auto_deduction' => !$advance->auto_deduction,
            'updated_by' => Auth::id(),
        ]);

        return $advance->fresh();
    }

    /**
     * الحصول على رصيد المستفيد
     */
    public function getBalance(string $beneficiaryType, int $beneficiaryId): array
    {
        $advances = Advance::forBeneficiary($beneficiaryType, $beneficiaryId)
            ->whereIn('status', [Advance::STATUS_ACTIVE, Advance::STATUS_COMPLETED])
            ->get();

        return [
            'total_advances' => $advances->sum('amount'),
            'total_paid' => $advances->sum('paid_amount'),
            'total_remaining' => $advances->where('status', Advance::STATUS_ACTIVE)->sum('remaining_amount'),
            'active_count' => $advances->where('status', Advance::STATUS_ACTIVE)->count(),
            'completed_count' => $advances->where('status', Advance::STATUS_COMPLETED)->count(),
        ];
    }

    /**
     * الحصول على السلف النشطة
     */
    public function getActiveAdvances($companyCode, $branchId = null)
    {
        $query = Advance::forCompany($companyCode)
            ->active()
            ->with(['branch', 'requester']);

        if ($branchId) {
            $query->forBranch($branchId);
        }

        return $query->orderBy('requested_at', 'desc')->get();
    }

    /**
     * الحصول على السلف المعلقة
     */
    public function getPendingAdvances($companyCode, $branchId = null)
    {
        $query = Advance::forCompany($companyCode)
            ->pending()
            ->with(['branch', 'requester']);

        if ($branchId) {
            $query->forBranch($branchId);
        }

        return $query->orderBy('requested_at', 'desc')->get();
    }

    /**
     * إلغاء السلفة
     */
    public function cancelAdvance(Advance $advance, string $reason): Advance
    {
        if (!$advance->canBeCancelled()) {
            throw new Exception("لا يمكن إلغاء هذه السلفة");
        }

        $advance->update([
            'status' => Advance::STATUS_CANCELLED,
            'notes' => $advance->notes . "\n[إلغاء]: " . $reason,
            'updated_by' => Auth::id(),
        ]);

        return $advance->fresh();
    }

    /**
     * إحصائيات السلف
     */
    public function getStatistics($companyCode, $branchId = null): array
    {
        $query = Advance::forCompany($companyCode);

        if ($branchId) {
            $query->forBranch($branchId);
        }

        $advances = $query->get();

        return [
            'total_count' => $advances->count(),
            'pending_count' => $advances->where('status', Advance::STATUS_PENDING)->count(),
            'active_count' => $advances->where('status', Advance::STATUS_ACTIVE)->count(),
            'completed_count' => $advances->where('status', Advance::STATUS_COMPLETED)->count(),
            'cancelled_count' => $advances->where('status', Advance::STATUS_CANCELLED)->count(),
            'total_amount' => $advances->sum('amount'),
            'total_remaining' => $advances->where('status', Advance::STATUS_ACTIVE)->sum('remaining_amount'),
            'by_type' => [
                'employee' => $advances->where('beneficiary_type', Advance::BENEFICIARY_EMPLOYEE)->count(),
                'agent' => $advances->where('beneficiary_type', Advance::BENEFICIARY_AGENT)->count(),
                'supplier' => $advances->where('beneficiary_type', Advance::BENEFICIARY_SUPPLIER)->count(),
                'contractor' => $advances->where('beneficiary_type', Advance::BENEFICIARY_CONTRACTOR)->count(),
            ],
        ];
    }
}
