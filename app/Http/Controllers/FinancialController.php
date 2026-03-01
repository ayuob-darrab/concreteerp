<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\CashRegister;
use App\Services\AccountService;
use App\Services\FinancialTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FinancialController extends Controller
{
    protected $accountService;
    protected $transactionService;

    public function __construct(AccountService $accountService, FinancialTransactionService $transactionService)
    {
        $this->accountService = $accountService;
        $this->transactionService = $transactionService;
    }

    // ==================== الحسابات ====================

    /**
     * عرض قائمة الحسابات
     */
    public function accounts(Request $request)
    {
        $companyCode = auth()->user()->company_code;

        $query = FinancialAccount::where('company_code', $companyCode);

        // الفلترة
        if ($request->type) {
            $query->where('account_type', $request->type);
        }
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->search) {
            $query->search($request->search);
        }
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $accounts = $query->with(['branch', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $summary = $this->accountService->getAccountsSummary($companyCode);

        return view('financial.accounts.index', compact('accounts', 'summary'));
    }

    /**
     * إنشاء حساب جديد
     */
    public function createAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_type' => 'required|in:' . implode(',', array_keys(FinancialAccount::ACCOUNT_TYPES)),
            'account_name' => 'required|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'opening_balance' => 'nullable|numeric',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $account = $this->accountService->createAccount([
            'company_code' => auth()->user()->company_code,
            'branch_id' => $request->branch_id,
            'account_type' => $request->account_type,
            'account_name' => $request->account_name,
            'opening_balance' => $request->opening_balance ?? 0,
            'current_balance' => $request->opening_balance ?? 0,
            'credit_limit' => $request->credit_limit ?? 0,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('financial.accounts')
            ->with('success', 'تم إنشاء الحساب بنجاح');
    }

    /**
     * عرض تفاصيل حساب
     */
    public function showAccount($id)
    {
        $account = FinancialAccount::with(['company', 'branch', 'creator'])
            ->findOrFail($id);

        $this->authorizeCompany($account->company_code);

        $transactions = $account->transactions()
            ->with(['creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $payments = $account->payments()
            ->with(['creator', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('financial.accounts.show', compact('account', 'transactions', 'payments'));
    }

    /**
     * كشف حساب
     */
    public function accountStatement(Request $request, $id)
    {
        $account = FinancialAccount::findOrFail($id);
        $this->authorizeCompany($account->company_code);

        $from = $request->from ? \Carbon\Carbon::parse($request->from) : null;
        $to = $request->to ? \Carbon\Carbon::parse($request->to) : null;

        $statement = $this->transactionService->getAccountStatement($id, $from, $to);

        return view('financial.accounts.statement', compact('statement'));
    }

    // ==================== المعاملات المالية ====================

    /**
     * عرض المعاملات
     */
    public function transactions(Request $request)
    {
        $companyCode = auth()->user()->company_code;

        $query = FinancialTransaction::where('company_code', $companyCode);

        // الفلترة
        if ($request->type) {
            $query->where('transaction_type', $request->type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->account_id) {
            $query->where('account_id', $request->account_id);
        }
        if ($request->from && $request->to) {
            $query->between($request->from, $request->to);
        }

        $transactions = $query->with(['account', 'creator', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('financial.transactions.index', compact('transactions'));
    }

    /**
     * المعاملات المعلقة
     */
    public function pendingTransactions()
    {
        $companyCode = auth()->user()->company_code;
        $transactions = $this->transactionService->getPendingTransactions($companyCode);

        return view('financial.transactions.pending', compact('transactions'));
    }

    /**
     * اعتماد معاملة
     */
    public function approveTransaction($id)
    {
        $transaction = FinancialTransaction::findOrFail($id);
        $this->authorizeCompany($transaction->company_code);

        $transaction->approve(auth()->id());

        return back()->with('success', 'تم اعتماد المعاملة بنجاح');
    }

    /**
     * رفض معاملة
     */
    public function rejectTransaction(Request $request, $id)
    {
        $transaction = FinancialTransaction::findOrFail($id);
        $this->authorizeCompany($transaction->company_code);

        $transaction->reject(auth()->id(), $request->reason);

        return back()->with('success', 'تم رفض المعاملة');
    }

    // ==================== المدفوعات ====================

    /**
     * عرض المدفوعات
     */
    public function payments(Request $request)
    {
        $companyCode = auth()->user()->company_code;

        $query = Payment::where('company_code', $companyCode);

        // الفلترة
        if ($request->direction) {
            $query->where('direction', $request->direction);
        }
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->account_id) {
            $query->where('account_id', $request->account_id);
        }
        if ($request->from && $request->to) {
            $query->between($request->from, $request->to);
        }

        $payments = $query->with(['account', 'creator', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('financial.payments.index', compact('payments'));
    }

    /**
     * استلام دفعة
     */
    public function receivePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:financial_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:' . implode(',', array_keys(Payment::PAYMENT_METHODS)),
            'payment_type' => 'nullable|in:' . implode(',', array_keys(Payment::PAYMENT_TYPES)),
            'description' => 'nullable|string',
            'check_number' => 'nullable|string|required_if:payment_method,check',
            'check_date' => 'nullable|date|required_if:payment_method,check',
            'bank_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $account = FinancialAccount::findOrFail($request->account_id);
        $this->authorizeCompany($account->company_code);

        $payment = $this->transactionService->receivePayment(
            $request->account_id,
            $request->amount,
            $request->payment_method,
            [
                'payment_type' => $request->payment_type ?? 'full',
                'description' => $request->description,
                'check_number' => $request->check_number,
                'check_date' => $request->check_date,
                'bank_name' => $request->bank_name,
                'branch_id' => auth()->user()->branch_id,
            ]
        );

        return redirect()->route('financial.payments')
            ->with('success', "تم استلام الدفعة بنجاح - إيصال رقم: {$payment->receipt_number}");
    }

    /**
     * صرف دفعة
     */
    public function makePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:financial_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:' . implode(',', array_keys(Payment::PAYMENT_METHODS)),
            'payment_type' => 'nullable|in:' . implode(',', array_keys(Payment::PAYMENT_TYPES)),
            'description' => 'nullable|string',
            'check_number' => 'nullable|string|required_if:payment_method,check',
            'check_date' => 'nullable|date|required_if:payment_method,check',
            'bank_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $account = FinancialAccount::findOrFail($request->account_id);
        $this->authorizeCompany($account->company_code);

        $payment = $this->transactionService->makePayment(
            $request->account_id,
            $request->amount,
            $request->payment_method,
            [
                'payment_type' => $request->payment_type ?? 'full',
                'description' => $request->description,
                'check_number' => $request->check_number,
                'check_date' => $request->check_date,
                'bank_name' => $request->bank_name,
                'branch_id' => auth()->user()->branch_id,
            ]
        );

        return redirect()->route('financial.payments')
            ->with('success', "تم صرف الدفعة بنجاح - سند رقم: {$payment->receipt_number}");
    }

    // ==================== الصندوق ====================

    /**
     * عرض حركات الصندوق
     */
    public function cashRegister(Request $request)
    {
        $branchId = auth()->user()->branch_id;

        $query = CashRegister::where('branch_id', $branchId);

        if ($request->from && $request->to) {
            $query->between($request->from, $request->to);
        }

        $entries = $query->with(['handler', 'payment'])
            ->orderBy('handled_at', 'desc')
            ->paginate(20);

        $summary = CashRegister::getDailySummary($branchId);

        return view('financial.cash-register.index', compact('entries', 'summary'));
    }

    /**
     * إضافة حركة صندوق
     */
    public function addCashEntry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_type' => 'required|in:cash_in,cash_out',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $branchId = auth()->user()->branch_id;

        try {
            CashRegister::addEntry($branchId, $request->transaction_type, $request->amount, [
                'company_code' => auth()->user()->company_code,
                'description' => $request->description,
                'notes' => $request->notes,
                'handled_by' => auth()->id(),
            ]);

            return back()->with('success', 'تم إضافة الحركة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ==================== التقارير ====================

    /**
     * التقرير اليومي
     */
    public function dailyReport(Request $request)
    {
        $companyCode = auth()->user()->company_code;
        $date = $request->date ? \Carbon\Carbon::parse($request->date) : today();

        $report = $this->transactionService->getDailyReport($companyCode, $date);

        return view('financial.reports.daily', compact('report', 'date'));
    }

    // ==================== مساعدات ====================

    /**
     * التحقق من صلاحية الوصول للشركة
     */
    protected function authorizeCompany($companyCode)
    {
        if (auth()->user()->company_code !== $companyCode && !auth()->user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بالوصول لهذا المورد');
        }
    }
}
