<?php

namespace App\Http\Controllers;

use App\Models\PaymentVoucher;
use App\Models\Contractor;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Currency;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentVoucherController extends Controller
{
    protected $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    /**
     * Display a listing of vouchers
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;

        $query = PaymentVoucher::where('company_code', $companyCode)
            ->with(['branch', 'creator', 'approver', 'payer'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payee_type')) {
            $query->where('payee_type', $request->payee_type);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('voucher_number', 'like', "%{$search}%")
                    ->orWhere('payee_name', 'like', "%{$search}%");
            });
        }

        $vouchers = $query->paginate(20);

        // Statistics
        $statistics = [
            'pending_approval' => PaymentVoucher::where('company_code', $companyCode)
                ->pendingApproval()->count(),
            'total_today' => PaymentVoucher::where('company_code', $companyCode)
                ->paid()->today()->sum('amount_in_default'),
            'total_month' => PaymentVoucher::where('company_code', $companyCode)
                ->paid()->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)->sum('amount_in_default'),
        ];

        $branches = $user->branches ?? collect();

        return view('financial.vouchers.index', compact('vouchers', 'statistics', 'branches'));
    }

    /**
     * Show pending approval vouchers
     */
    public function pendingApproval()
    {
        $user = Auth::user();
        $companyCode = $user->company_code;

        $vouchers = $this->voucherService->getPendingApproval($companyCode);

        return view('financial.vouchers.approval', compact('vouchers'));
    }

    /**
     * Show the form for creating a new voucher
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $branches = $user->branches ?? collect();
        $currencies = Currency::active()->get();

        // Pre-fill from query parameters
        $prefill = [];
        if ($request->filled('payee_type')) {
            $prefill['payee_type'] = $request->payee_type;
        }
        if ($request->filled('payee_id')) {
            $prefill['payee_id'] = $request->payee_id;

            switch ($request->payee_type) {
                case 'contractor':
                    $entity = Contractor::find($request->payee_id);
                    break;
                case 'supplier':
                    $entity = Supplier::find($request->payee_id);
                    break;
                case 'employee':
                    $entity = Employee::find($request->payee_id);
                    break;
            }

            if (isset($entity)) {
                $prefill['payee_name'] = $entity->name;
            }
        }

        return view('financial.vouchers.create', compact('branches', 'currencies', 'prefill'));
    }

    /**
     * Store a newly created voucher
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'payee_type' => 'required|in:supplier,contractor,employee,other',
            'payee_id' => 'nullable|integer',
            'payee_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'currency_code' => 'required|string|max:3',
            'payment_method' => 'required|in:cash,bank_transfer,check,card,other',
            'reference_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'check_number' => 'nullable|required_if:payment_method,check|string|max:50',
            'check_date' => 'nullable|required_if:payment_method,check|date',
            'description' => 'required|string|min:5',
            'related_type' => 'nullable|string|max:50',
            'related_id' => 'nullable|integer',
            'requires_approval' => 'nullable|boolean',
        ]);

        try {
            $voucher = $this->voucherService->create($validated);

            return redirect()
                ->route('vouchers.show', $voucher)
                ->with('success', 'تم إنشاء سند الصرف بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified voucher
     */
    public function show(PaymentVoucher $voucher)
    {
        $this->authorizeVoucher($voucher);

        $voucher->load(['branch', 'creator', 'approver', 'payer', 'transaction', 'currency']);

        return view('financial.vouchers.show', compact('voucher'));
    }

    /**
     * Print voucher
     */
    public function print(PaymentVoucher $voucher)
    {
        $this->authorizeVoucher($voucher);

        $voucher->load(['branch', 'creator', 'approver', 'payer', 'currency']);

        return view('financial.prints.voucher-template', compact('voucher'));
    }

    /**
     * Submit for approval
     */
    public function submitForApproval(PaymentVoucher $voucher)
    {
        $this->authorizeVoucher($voucher);

        try {
            $this->voucherService->submitForApproval($voucher);

            return redirect()
                ->route('vouchers.show', $voucher)
                ->with('success', 'تم إرسال السند للموافقة');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Approve voucher
     */
    public function approve(PaymentVoucher $voucher)
    {
        $this->authorizeVoucher($voucher);

        try {
            $this->voucherService->approve($voucher);

            return redirect()
                ->route('vouchers.show', $voucher)
                ->with('success', 'تمت الموافقة على السند');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject voucher
     */
    public function reject(Request $request, PaymentVoucher $voucher)
    {
        $this->authorizeVoucher($voucher);

        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        try {
            $this->voucherService->reject($voucher, $request->reason);

            return redirect()
                ->route('vouchers.index')
                ->with('success', 'تم رفض السند');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Pay voucher
     */
    public function pay(PaymentVoucher $voucher)
    {
        $this->authorizeVoucher($voucher);

        try {
            $this->voucherService->pay($voucher);

            return redirect()
                ->route('vouchers.show', $voucher)
                ->with('success', 'تم صرف السند بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel voucher
     */
    public function cancel(Request $request, PaymentVoucher $voucher)
    {
        $this->authorizeVoucher($voucher);

        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        try {
            $this->voucherService->cancel($voucher, $request->reason);

            return redirect()
                ->route('vouchers.index')
                ->with('success', 'تم إلغاء السند');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Authorize access to voucher
     */
    protected function authorizeVoucher(PaymentVoucher $voucher): void
    {
        if ($voucher->company_code !== Auth::user()->company_code) {
            abort(403);
        }
    }
}
