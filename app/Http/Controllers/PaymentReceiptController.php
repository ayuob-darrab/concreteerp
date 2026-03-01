<?php

namespace App\Http\Controllers;

use App\Models\PaymentReceipt;
use App\Models\Contractor;
use App\Models\Currency;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentReceiptController extends Controller
{
    protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    /**
     * Display a listing of receipts
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;

        $query = PaymentReceipt::where('company_code', $companyCode)
            ->with(['branch', 'receiver'])
            ->orderBy('received_at', 'desc');

        // Apply filters
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payer_type')) {
            $query->where('payer_type', $request->payer_type);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('received_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('received_at', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                    ->orWhere('payer_name', 'like', "%{$search}%");
            });
        }

        $receipts = $query->paginate(20);

        // Statistics
        $statistics = [
            'total_today' => PaymentReceipt::where('company_code', $companyCode)
                ->confirmed()->today()->sum('amount_in_default'),
            'count_today' => PaymentReceipt::where('company_code', $companyCode)
                ->confirmed()->today()->count(),
            'total_month' => PaymentReceipt::where('company_code', $companyCode)
                ->confirmed()->whereMonth('received_at', now()->month)
                ->whereYear('received_at', now()->year)->sum('amount_in_default'),
        ];

        $branches = $user->branches ?? collect();

        return view('financial.receipts.index', compact('receipts', 'statistics', 'branches'));
    }

    /**
     * Show the form for creating a new receipt
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $branches = $user->branches ?? collect();
        $currencies = Currency::active()->get();

        // Pre-fill from query parameters
        $prefill = [];
        if ($request->filled('payer_type')) {
            $prefill['payer_type'] = $request->payer_type;
        }
        if ($request->filled('payer_id')) {
            $prefill['payer_id'] = $request->payer_id;

            if ($request->payer_type === 'contractor') {
                $contractor = Contractor::find($request->payer_id);
                if ($contractor) {
                    $prefill['payer_name'] = $contractor->name;
                    $prefill['payer_phone'] = $contractor->phone;
                }
            }
        }

        return view('financial.receipts.create', compact('branches', 'currencies', 'prefill'));
    }

    /**
     * Store a newly created receipt
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'payer_type' => 'required|in:contractor,customer,other',
            'payer_id' => 'nullable|integer',
            'payer_name' => 'required|string|max:255',
            'payer_phone' => 'nullable|string|max:20',
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
        ]);

        try {
            $receipt = $this->receiptService->create($validated);

            return redirect()
                ->route('receipts.show', $receipt)
                ->with('success', 'تم إنشاء إيصال القبض بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified receipt
     */
    public function show(PaymentReceipt $receipt)
    {
        $this->authorizeReceipt($receipt);

        $receipt->load(['branch', 'receiver', 'transaction', 'currency']);

        return view('financial.receipts.show', compact('receipt'));
    }

    /**
     * Print receipt
     */
    public function print(PaymentReceipt $receipt)
    {
        $this->authorizeReceipt($receipt);

        $receipt->load(['branch', 'receiver', 'currency']);

        return view('financial.prints.receipt-template', compact('receipt'));
    }

    /**
     * Cancel receipt
     */
    public function cancel(Request $request, PaymentReceipt $receipt)
    {
        $this->authorizeReceipt($receipt);

        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        try {
            $this->receiptService->cancel($receipt, $request->reason);

            return redirect()
                ->route('receipts.index')
                ->with('success', 'تم إلغاء الإيصال بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mark check as bounced
     */
    public function markBounced(Request $request, PaymentReceipt $receipt)
    {
        $this->authorizeReceipt($receipt);

        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        try {
            $this->receiptService->markBounced($receipt, $request->reason);

            return redirect()
                ->route('receipts.show', $receipt)
                ->with('success', 'تم تسجيل الشيك كمرتجع');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get report
     */
    public function report(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;

        $filters = $request->only(['branch_id', 'from_date', 'to_date', 'payer_type', 'payment_method']);
        $report = $this->receiptService->getReport($companyCode, $filters);

        $branches = $user->branches ?? collect();

        return view('financial.receipts.report', compact('report', 'branches', 'filters'));
    }

    /**
     * Authorize access to receipt
     */
    protected function authorizeReceipt(PaymentReceipt $receipt): void
    {
        if ($receipt->company_code !== Auth::user()->company_code) {
            abort(403);
        }
    }
}
