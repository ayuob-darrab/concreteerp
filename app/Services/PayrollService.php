<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeBonus;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeLeave;
use App\Models\Payroll;
use App\Models\Advance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class PayrollService
{
    protected $advanceService;

    public function __construct(AdvanceService $advanceService)
    {
        $this->advanceService = $advanceService;
    }

    /**
     * إنشاء كشف راتب لموظف
     */
    public function generatePayroll(Employee $employee, int $year, int $month): Payroll
    {
        // التحقق من عدم وجود كشف سابق
        $existing = Payroll::where('employee_id', $employee->id)
            ->where('payroll_year', $year)
            ->where('payroll_month', $month)
            ->first();

        if ($existing) {
            throw new Exception("يوجد كشف راتب للموظف عن هذا الشهر");
        }

        return DB::transaction(function () use ($employee, $year, $month) {
            // جمع البدلات
            $allowances = $this->calculateAllowances($employee, $year, $month);

            // جمع المكافآت
            $bonuses = $this->calculateBonuses($employee, $year, $month);

            // جمع الخصومات
            $deductions = $this->calculateDeductions($employee, $year, $month);

            // حساب استقطاع السلف
            $advanceDeduction = $this->calculateAdvanceDeduction($employee);

            // حساب خصم الغياب
            $absenceData = $this->calculateAbsenceDeduction($employee, $year, $month);

            // حساب الراتب الإجمالي
            $grossSalary = $employee->salary + $allowances['total'] + $bonuses['total'];

            // حساب الصافي
            $totalDeductions = $deductions['total'] + $advanceDeduction['amount'] + $absenceData['deduction'];
            $netSalary = $grossSalary - $totalDeductions;

            // إنشاء كشف الراتب
            $payroll = Payroll::create([
                'company_code' => $employee->company_code,
                'branch_id' => $employee->branch_id,
                'employee_id' => $employee->id,
                'payroll_year' => $year,
                'payroll_month' => $month,
                'basic_salary' => $employee->salary,
                'allowances_total' => $allowances['total'],
                'allowances_details' => $allowances['details'],
                'bonuses_total' => $bonuses['total'],
                'bonuses_details' => $bonuses['details'],
                'deductions_total' => $deductions['total'],
                'deductions_details' => $deductions['details'],
                'advances_deducted' => $advanceDeduction['amount'],
                'advances_details' => $advanceDeduction['details'],
                'absence_deduction' => $absenceData['deduction'],
                'absence_days' => $absenceData['days'],
                'gross_salary' => $grossSalary,
                'net_salary' => $netSalary,
                'status' => Payroll::STATUS_DRAFT,
                'created_by' => Auth::id(),
            ]);

            return $payroll;
        });
    }

    /**
     * حساب البدلات
     */
    protected function calculateAllowances(Employee $employee, int $year, int $month): array
    {
        $allowances = EmployeeAllowance::where('employee_id', $employee->id)
            ->active()
            ->validForDate(now()->setYear($year)->setMonth($month)->endOfMonth())
            ->get();

        $details = [];
        $total = 0;

        foreach ($allowances as $allowance) {
            $details[] = [
                'id' => $allowance->id,
                'type' => $allowance->allowance_type,
                'name' => $allowance->type_name,
                'amount' => $allowance->amount,
            ];
            $total += $allowance->amount;
        }

        return ['total' => $total, 'details' => $details];
    }

    /**
     * حساب المكافآت
     */
    protected function calculateBonuses(Employee $employee, int $year, int $month): array
    {
        $bonuses = EmployeeBonus::where('employee_id', $employee->id)
            ->pending()
            ->forMonth($year, $month)
            ->get();

        $details = [];
        $total = 0;

        foreach ($bonuses as $bonus) {
            $details[] = [
                'id' => $bonus->id,
                'type' => $bonus->bonus_type,
                'name' => $bonus->type_name,
                'amount' => $bonus->amount,
                'reason' => $bonus->reason,
            ];
            $total += $bonus->amount;
        }

        return ['total' => $total, 'details' => $details, 'items' => $bonuses];
    }

    /**
     * حساب الخصومات
     */
    protected function calculateDeductions(Employee $employee, int $year, int $month): array
    {
        $deductions = EmployeeDeduction::where('employee_id', $employee->id)
            ->pending()
            ->forMonth($year, $month)
            ->get();

        $details = [];
        $total = 0;

        foreach ($deductions as $deduction) {
            $details[] = [
                'id' => $deduction->id,
                'type' => $deduction->deduction_type,
                'name' => $deduction->type_name,
                'amount' => $deduction->amount,
                'reason' => $deduction->reason,
            ];
            $total += $deduction->amount;
        }

        return ['total' => $total, 'details' => $details, 'items' => $deductions];
    }

    /**
     * حساب استقطاع السلف
     */
    protected function calculateAdvanceDeduction(Employee $employee): array
    {
        $advance = Advance::forBeneficiary(Advance::BENEFICIARY_EMPLOYEE, $employee->id)
            ->active()
            ->where('auto_deduction', true)
            ->first();

        if (!$advance) {
            return ['amount' => 0, 'details' => []];
        }

        $deductAmount = $advance->calculateDeductionAmount($employee->salary);
        $deductAmount = min($deductAmount, $advance->remaining_amount);

        return [
            'amount' => $deductAmount,
            'details' => [
                [
                    'advance_id' => $advance->id,
                    'advance_number' => $advance->advance_number,
                    'amount' => $deductAmount,
                    'remaining_before' => $advance->remaining_amount,
                ]
            ],
        ];
    }

    /**
     * حساب خصم الغياب
     */
    protected function calculateAbsenceDeduction(Employee $employee, int $year, int $month): array
    {
        // حساب الإجازات بدون راتب
        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = now()->setYear($year)->setMonth($month)->endOfMonth();

        $unpaidLeaves = EmployeeLeave::where('employee_id', $employee->id)
            ->approved()
            ->where('leave_type', EmployeeLeave::TYPE_UNPAID)
            ->inDateRange($startDate, $endDate)
            ->get();

        $totalDays = $unpaidLeaves->sum('days_count');

        // حساب قيمة اليوم
        $dailyRate = $employee->salary / 30;
        $deduction = $totalDays * $dailyRate;

        return [
            'days' => $totalDays,
            'deduction' => $deduction,
        ];
    }

    /**
     * اعتماد كشف الراتب وتنفيذ العمليات
     */
    public function approvePayroll(Payroll $payroll): Payroll
    {
        if (!$payroll->canBeApproved()) {
            throw new Exception("لا يمكن اعتماد هذا الكشف");
        }

        return DB::transaction(function () use ($payroll) {
            // تحديث حالة المكافآت
            if ($payroll->bonuses_details) {
                foreach ($payroll->bonuses_details as $bonus) {
                    EmployeeBonus::find($bonus['id'])?->markAsPaid($payroll->id);
                }
            }

            // تحديث حالة الخصومات
            if ($payroll->deductions_details) {
                foreach ($payroll->deductions_details as $deduction) {
                    EmployeeDeduction::find($deduction['id'])?->markAsDeducted($payroll->id);
                }
            }

            // تنفيذ استقطاع السلف
            if ($payroll->advances_deducted > 0 && $payroll->advances_details) {
                foreach ($payroll->advances_details as $advanceData) {
                    $this->advanceService->autoDeductFromSalary(
                        $payroll->employee,
                        $payroll->id
                    );
                }
            }

            // اعتماد الكشف
            $payroll->approve(Auth::id());

            return $payroll->fresh();
        });
    }

    /**
     * صرف الراتب
     */
    public function payPayroll(Payroll $payroll, string $paymentMethod, ?string $reference = null): Payroll
    {
        if (!$payroll->canBePaid()) {
            throw new Exception("لا يمكن صرف هذا الراتب - يجب اعتماده أولاً");
        }

        $payroll->markAsPaid(Auth::id(), $paymentMethod, $reference);

        return $payroll->fresh();
    }

    /**
     * إنشاء كشوفات رواتب جماعية
     */
    public function generateBulkPayroll(string $companyCode, ?int $branchId, int $year, int $month): array
    {
        $query = Employee::where('company_code', $companyCode)
            ->where('status', 'active');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $employees = $query->get();

        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($employees as $employee) {
            try {
                $payroll = $this->generatePayroll($employee, $year, $month);
                $results['success'][] = [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                    'payroll_id' => $payroll->id,
                ];
            } catch (Exception $e) {
                $results['failed'][] = [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * إحصائيات الرواتب
     */
    public function getPayrollStatistics(string $companyCode, int $year, int $month, ?int $branchId = null): array
    {
        $query = Payroll::forCompany($companyCode)->forMonth($year, $month);

        if ($branchId) {
            $query->forBranch($branchId);
        }

        $payrolls = $query->get();

        return [
            'total_count' => $payrolls->count(),
            'draft_count' => $payrolls->where('status', Payroll::STATUS_DRAFT)->count(),
            'approved_count' => $payrolls->where('status', Payroll::STATUS_APPROVED)->count(),
            'paid_count' => $payrolls->where('status', Payroll::STATUS_PAID)->count(),
            'total_basic' => $payrolls->sum('basic_salary'),
            'total_allowances' => $payrolls->sum('allowances_total'),
            'total_bonuses' => $payrolls->sum('bonuses_total'),
            'total_deductions' => $payrolls->sum('deductions_total'),
            'total_advances' => $payrolls->sum('advances_deducted'),
            'total_gross' => $payrolls->sum('gross_salary'),
            'total_net' => $payrolls->sum('net_salary'),
        ];
    }
}
