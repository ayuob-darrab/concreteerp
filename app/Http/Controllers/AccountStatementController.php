<?php

namespace App\Http\Controllers;

use App\Models\AccountBalance;
use App\Models\Contractor;
use App\Models\Supplier;
use App\Models\Employee;
use App\Services\AccountBalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountStatementController extends Controller
{
    protected $accountBalanceService;

    public function __construct(AccountBalanceService $accountBalanceService)
    {
        $this->accountBalanceService = $accountBalanceService;
    }

    /**
     * Display balances index
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;

        $query = AccountBalance::where('company_code', $companyCode)
            ->withBalance()
            ->orderBy('account_type')
            ->orderByDesc('current_balance');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('account_type')) {
            $query->where('account_type', $request->account_type);
        }

        if ($request->filled('balance_type')) {
            $query->where('balance_type', $request->balance_type);
        }

        $balances = $query->paginate(20);

        // Summary
        $summary = $this->accountBalanceService->getSummary($companyCode, $request->branch_id);

        $branches = $user->branches ?? collect();

        return view('financial.balances.index', compact('balances', 'summary', 'branches'));
    }

    /**
     * Show balance summary
     */
    public function summary(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;

        $summary = $this->accountBalanceService->getSummary($companyCode, $request->branch_id);

        $branches = $user->branches ?? collect();

        return view('financial.balances.summary', compact('summary', 'branches'));
    }

    /**
     * Show contractor statement
     */
    public function contractorStatement(Request $request, Contractor $contractor)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;

        if ($contractor->company_code !== $companyCode) {
            abort(403);
        }

        $statement = $this->accountBalanceService->getStatement(
            $companyCode,
            AccountBalance::TYPE_CONTRACTOR,
            $contractor->id,
            $request->from_date,
            $request->to_date,
            $request->branch_id
        );

        return view('financial.statements.contractor', compact('contractor', 'statement'));
    }

    /**
     * Show supplier statement
     */
    public function supplierStatement(Request $request, Supplier $supplier)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;

        if ($supplier->company_code !== $companyCode) {
            abort(403);
        }

        $statement = $this->accountBalanceService->getStatement(
            $companyCode,
            AccountBalance::TYPE_SUPPLIER,
            $supplier->id,
            $request->from_date,
            $request->to_date,
            $request->branch_id
        );

        return view('financial.statements.supplier', compact('supplier', 'statement'));
    }

    /**
     * Show employee statement
     */
    public function employeeStatement(Request $request, Employee $employee)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;

        if ($employee->company_code !== $companyCode) {
            abort(403);
        }

        $statement = $this->accountBalanceService->getStatement(
            $companyCode,
            AccountBalance::TYPE_EMPLOYEE,
            $employee->id,
            $request->from_date,
            $request->to_date,
            $request->branch_id
        );

        return view('financial.statements.employee', compact('employee', 'statement'));
    }

    /**
     * Print statement
     */
    public function printStatement(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;

        $accountType = $request->account_type;
        $accountId = $request->account_id;

        // Get account info
        $accountName = 'حساب';
        switch ($accountType) {
            case 'contractor':
                $entity = Contractor::find($accountId);
                break;
            case 'supplier':
                $entity = Supplier::find($accountId);
                break;
            case 'employee':
                $entity = Employee::find($accountId);
                break;
        }

        if (isset($entity)) {
            $accountName = $entity->name;
        }

        $statement = $this->accountBalanceService->getStatement(
            $companyCode,
            $accountType,
            $accountId,
            $request->from_date,
            $request->to_date,
            $request->branch_id
        );

        return view('financial.prints.statement-template', compact('statement', 'accountName', 'accountType'));
    }

    /**
     * Set opening balance
     */
    public function setOpeningBalance(Request $request)
    {
        $validated = $request->validate([
            'account_type' => 'required|in:contractor,supplier,employee,customer',
            'account_id' => 'required|integer',
            'opening_balance' => 'required|numeric',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $user = Auth::user();
        $companyCode = $user->company_code;

        $this->accountBalanceService->setOpeningBalance(
            $companyCode,
            $validated['branch_id'],
            $validated['account_type'],
            $validated['account_id'],
            $validated['opening_balance']
        );

        return back()->with('success', 'تم تعديل الرصيد الافتتاحي');
    }

    /**
     * Recalculate balance
     */
    public function recalculate(Request $request)
    {
        $validated = $request->validate([
            'account_type' => 'required|in:contractor,supplier,employee,customer',
            'account_id' => 'required|integer',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $user = Auth::user();
        $companyCode = $user->company_code;

        $this->accountBalanceService->recalculate(
            $companyCode,
            $validated['account_type'],
            $validated['account_id'],
            $validated['branch_id']
        );

        return back()->with('success', 'تم إعادة حساب الرصيد');
    }
}
