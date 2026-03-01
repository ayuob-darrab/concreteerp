<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Invoice;
use App\Services\PaymentService;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ReceiptController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->middleware('auth');
    }

    /**
     * عرض قائمة السندات
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'type',
            'status',
            'branch_id',
            'from_date',
            'to_date',
            'payment_method',
            'sort_by',
            'sort_direction'
        ]);

        $receipts = $this->paymentService->getReceipts($filters, 15);
        $summary = $this->paymentService->getPaymentsSummary(
            auth()->user()->company_code,
            $filters['from_date'] ?? null,
            $filters['to_date'] ?? null
        );

        return view('receipts.index', compact('receipts', 'summary', 'filters'));
    }

    /**
     * عرض نموذج إنشاء سند قبض
     */
    public function createReceipt(Request $request): View
    {
        $invoice = null;
        if ($request->has('invoice_id')) {
            $invoice = Invoice::with('account.contractor')->find($request->invoice_id);
        }

        return view('receipts.create-receipt', compact('invoice'));
    }

    /**
     * عرض نموذج إنشاء سند صرف
     */
    public function createPayment(): View
    {
        return view('receipts.create-payment');
    }

    /**
     * حفظ سند قبض جديد
     */
    public function storeReceipt(StorePaymentRequest $request): RedirectResponse
    {
        try {
            $receipt = $this->paymentService->createReceipt($request->validated());

            return redirect()
                ->route('receipts.show', $receipt)
                ->with('success', 'تم إنشاء سند القبض بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * حفظ سند صرف جديد
     */
    public function storePayment(StorePaymentRequest $request): RedirectResponse
    {
        try {
            $receipt = $this->paymentService->createPayment($request->validated());

            return redirect()
                ->route('receipts.show', $receipt)
                ->with('success', 'تم إنشاء سند الصرف بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل سند
     */
    public function show(Receipt $receipt): View
    {
        $receipt->load([
            'account.contractor',
            'invoice',
            'check',
            'creator',
            'approver',
        ]);

        return view('receipts.show', compact('receipt'));
    }

    /**
     * اعتماد سند
     */
    public function approve(Receipt $receipt): RedirectResponse
    {
        try {
            $this->paymentService->approveReceipt($receipt);

            return back()->with('success', 'تم اعتماد السند بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * إلغاء سند
     */
    public function cancel(Request $request, Receipt $receipt): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->paymentService->cancelReceipt($receipt, $request->reason);

            return back()->with('warning', 'تم إلغاء السند');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * طباعة السند
     */
    public function print(Receipt $receipt)
    {
        $receipt->load([
            'account.contractor',
            'invoice',
            'creator',
            'approver',
        ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('receipts.print', [
            'receipt' => $receipt,
            'company' => auth()->user()->company,
        ]);

        $typeName = $receipt->receipt_type === Receipt::TYPE_RECEIPT ? 'قبض' : 'صرف';

        return $pdf->stream("سند_{$typeName}_{$receipt->receipt_number}.pdf");
    }

    /**
     * تسوية فاتورة
     */
    public function settleInvoice(Request $request, Invoice $invoice): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->remaining_amount,
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card,mobile_payment',
        ]);

        try {
            $receipt = $this->paymentService->settleInvoice(
                $invoice,
                $request->amount,
                $request->payment_method,
                $request->only(['payment_reference', 'bank_name', 'notes'])
            );

            return redirect()
                ->route('receipts.show', $receipt)
                ->with('success', 'تم تسجيل الدفعة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * سندات القبض فقط
     */
    public function receipts(Request $request): View
    {
        $filters = array_merge(
            $request->only(['status', 'branch_id', 'from_date', 'to_date', 'payment_method']),
            ['type' => 'receipt']
        );

        $receipts = $this->paymentService->getReceipts($filters, 15);

        return view('receipts.receipts', compact('receipts', 'filters'));
    }

    /**
     * سندات الصرف فقط
     */
    public function payments(Request $request): View
    {
        $filters = array_merge(
            $request->only(['status', 'branch_id', 'from_date', 'to_date', 'payment_method']),
            ['type' => 'payment']
        );

        $receipts = $this->paymentService->getReceipts($filters, 15);

        return view('receipts.payments', compact('receipts', 'filters'));
    }
}
