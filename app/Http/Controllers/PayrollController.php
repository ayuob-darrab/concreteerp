<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeBonus;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeLeave;
use App\Models\Payroll;
use App\Models\SalaryAdjustment;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class PayrollController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    /**
     * عرض كشوفات الرواتب
     */
    public function index(Request $request)
    {
        $companyCode = session('company_code');
        $branchId = session('branch_id');
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;

        $query = Payroll::forCompany($companyCode)
            ->forMonth($year, $month)
            ->with(['employee', 'branch']);

        if ($request->branch_id) {
            $query->forBranch($request->branch_id);
        } elseif ($branchId) {
            $query->forBranch($branchId);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->orderBy('created_at', 'desc')->paginate(20);

        // الإحصائيات
        $statistics = $this->payrollService->getPayrollStatistics($companyCode, $year, $month, $branchId);

        return view('payroll.index', compact('payrolls', 'statistics', 'year', 'month'));
    }

    /**
     * إنشاء كشف راتب فردي
     */
    public function create(Request $request)
    {
        $companyCode = session('company_code');

        $employees = Employee::where('company_code', $companyCode)
            ->where('status', 'active')
            ->get();

        return view('payroll.create', compact('employees'));
    }

    /**
     * حفظ كشف راتب
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer|min:2020|max:2050',
            'month' => 'required|integer|min:1|max:12',
        ]);

        try {
            $employee = Employee::findOrFail($request->employee_id);
            $payroll = $this->payrollService->generatePayroll(
                $employee,
                $request->year,
                $request->month
            );

            return redirect()
                ->route('payroll.show', $payroll)
                ->with('success', 'تم إنشاء كشف الراتب بنجاح');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * عرض كشف راتب
     */
    public function show(Payroll $payroll)
    {
        $payroll->load(['employee', 'branch', 'creator', 'approver', 'payer']);

        return view('payroll.show', compact('payroll'));
    }

    /**
     * اعتماد كشف الراتب
     */
    public function approve(Payroll $payroll)
    {
        try {
            $this->payrollService->approvePayroll($payroll);
            return back()->with('success', 'تم اعتماد كشف الراتب بنجاح');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * صرف الراتب
     */
    public function pay(Request $request, Payroll $payroll)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,bank_transfer,check',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        try {
            $this->payrollService->payPayroll(
                $payroll,
                $request->payment_method,
                $request->payment_reference
            );
            return back()->with('success', 'تم صرف الراتب بنجاح');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * إلغاء كشف الراتب
     */
    public function cancel(Payroll $payroll)
    {
        if ($payroll->status === Payroll::STATUS_PAID) {
            return back()->with('error', 'لا يمكن إلغاء كشف راتب مدفوع');
        }

        $payroll->cancel();
        return back()->with('success', 'تم إلغاء كشف الراتب');
    }

    /**
     * طباعة كشف الراتب
     */
    public function print(Payroll $payroll)
    {
        $payroll->load(['employee', 'branch']);
        return view('payroll.print', compact('payroll'));
    }

    /**
     * إنشاء كشوفات جماعية
     */
    public function generateBulk(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2050',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $companyCode = session('company_code');
        $branchId = $request->branch_id ?? session('branch_id');

        try {
            $results = $this->payrollService->generateBulkPayroll(
                $companyCode,
                $branchId,
                $request->year,
                $request->month
            );

            $successCount = count($results['success']);
            $failedCount = count($results['failed']);

            return back()->with('success', "تم إنشاء {$successCount} كشف راتب. فشل: {$failedCount}");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * تقرير الرواتب
     */
    public function report(Request $request)
    {
        $companyCode = session('company_code');
        $year = $request->year ?? now()->year;

        // إحصائيات سنوية
        $monthlyStats = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyStats[$month] = $this->payrollService->getPayrollStatistics(
                $companyCode,
                $year,
                $month
            );
        }

        return view('payroll.report', compact('monthlyStats', 'year'));
    }

    // ============ البدلات ============

    /**
     * عرض بدلات موظف
     */
    public function allowances(Employee $employee)
    {
        $allowances = EmployeeAllowance::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('payroll.allowances', compact('employee', 'allowances'));
    }

    /**
     * إضافة بدل
     */
    public function storeAllowance(Request $request, Employee $employee)
    {
        $request->validate([
            'allowance_type' => 'required|in:transportation,housing,meals,phone,other',
            'amount' => 'required|numeric|min:0',
            'is_recurring' => 'boolean',
        ]);

        EmployeeAllowance::create([
            'company_code' => $employee->company_code,
            'branch_id' => $employee->branch_id,
            'employee_id' => $employee->id,
            'allowance_type' => $request->allowance_type,
            'custom_name' => $request->custom_name,
            'amount' => $request->amount,
            'is_recurring' => $request->is_recurring ?? true,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم إضافة البدل بنجاح');
    }

    // ============ المكافآت ============

    /**
     * عرض مكافآت موظف
     */
    public function bonuses(Employee $employee)
    {
        $bonuses = EmployeeBonus::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('payroll.bonuses', compact('employee', 'bonuses'));
    }

    /**
     * إضافة مكافأة
     */
    public function storeBonus(Request $request, Employee $employee)
    {
        $request->validate([
            'bonus_type' => 'required',
            'amount' => 'required|numeric|min:0',
            'bonus_date' => 'required|date',
        ]);

        EmployeeBonus::create([
            'company_code' => $employee->company_code,
            'branch_id' => $employee->branch_id,
            'employee_id' => $employee->id,
            'bonus_type' => $request->bonus_type,
            'custom_name' => $request->custom_name,
            'amount' => $request->amount,
            'bonus_date' => $request->bonus_date,
            'reason' => $request->reason,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم إضافة المكافأة بنجاح');
    }

    // ============ الخصومات ============

    /**
     * عرض خصومات موظف
     */
    public function deductions(Employee $employee)
    {
        $deductions = EmployeeDeduction::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('payroll.deductions', compact('employee', 'deductions'));
    }

    /**
     * إضافة خصم
     */
    public function storeDeduction(Request $request, Employee $employee)
    {
        $request->validate([
            'deduction_type' => 'required',
            'amount' => 'required|numeric|min:0',
            'deduction_date' => 'required|date',
            'reason' => 'required|string',
        ]);

        EmployeeDeduction::create([
            'company_code' => $employee->company_code,
            'branch_id' => $employee->branch_id,
            'employee_id' => $employee->id,
            'deduction_type' => $request->deduction_type,
            'custom_name' => $request->custom_name,
            'amount' => $request->amount,
            'deduction_date' => $request->deduction_date,
            'reason' => $request->reason,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم إضافة الخصم بنجاح');
    }

    // ============ الإجازات ============

    /**
     * عرض الإجازات
     */
    public function leaves(Request $request)
    {
        $companyCode = session('company_code');

        $query = EmployeeLeave::forCompany($companyCode)
            ->with(['employee', 'approver']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('payroll.leaves', compact('leaves'));
    }

    /**
     * طلب إجازة
     */
    public function storeLeave(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $daysCount = EmployeeLeave::calculateDays($request->start_date, $request->end_date);

        EmployeeLeave::create([
            'company_code' => $employee->company_code,
            'branch_id' => $employee->branch_id,
            'employee_id' => $employee->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_count' => $daysCount,
            'reason' => $request->reason,
            'requested_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم تقديم طلب الإجازة');
    }

    /**
     * الموافقة على إجازة
     */
    public function approveLeave(EmployeeLeave $leave)
    {
        $leave->approve(Auth::id());
        return back()->with('success', 'تمت الموافقة على الإجازة');
    }

    /**
     * رفض إجازة
     */
    public function rejectLeave(Request $request, EmployeeLeave $leave)
    {
        $request->validate(['reason' => 'required|string']);
        $leave->reject(Auth::id(), $request->reason);
        return back()->with('success', 'تم رفض الإجازة');
    }

    // ============ تعديل الراتب ============

    /**
     * تعديل راتب موظف
     */
    public function adjustSalary(Request $request, Employee $employee)
    {
        $request->validate([
            'new_salary' => 'required|numeric|min:0',
            'reason' => 'required|string',
        ]);

        SalaryAdjustment::createAdjustment(
            $employee,
            $request->new_salary,
            $request->reason,
            Auth::id()
        );

        return back()->with('success', 'تم تعديل الراتب بنجاح');
    }
}
