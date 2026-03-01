<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\WorkOrder;
use App\Services\InvoiceService;
use App\Http\Requests\StoreInvoiceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
        $this->middleware('auth');
    }

    /**
     * عرض قائمة الفواتير
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'search',
            'status',
            'branch_id',
            'from_date',
            'to_date',
            'overdue',
            'unpaid',
            'sort_by',
            'sort_direction'
        ]);

        $invoices = $this->invoiceService->getInvoices($filters, 15);
        $statistics = $this->invoiceService->getStatistics(auth()->user()->company_code);

        return view('invoices.index', compact('invoices', 'statistics', 'filters'));
    }

    /**
     * عرض نموذج إنشاء فاتورة
     */
    public function create(Request $request): View
    {
        $workOrder = null;
        if ($request->has('work_order_id')) {
            $workOrder = WorkOrder::with(['contractor', 'items'])->find($request->work_order_id);
        }

        return view('invoices.create', compact('workOrder'));
    }

    /**
     * حفظ فاتورة جديدة
     */
    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        try {
            $invoice = $this->invoiceService->createInvoice($request->validated());

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('success', 'تم إنشاء الفاتورة بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل فاتورة
     */
    public function show(Invoice $invoice): View
    {
        $invoice->load([
            'account.contractor',
            'items',
            'workOrder',
            'transactions',
            'creator',
            'approver',
        ]);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * عرض نموذج تعديل فاتورة
     */
    public function edit(Invoice $invoice): View
    {
        if ($invoice->status !== Invoice::STATUS_DRAFT) {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', 'لا يمكن تعديل فاتورة غير مسودة');
        }

        $invoice->load(['items', 'account.contractor', 'workOrder']);

        return view('invoices.edit', compact('invoice'));
    }

    /**
     * تحديث فاتورة
     */
    public function update(StoreInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        try {
            $this->invoiceService->updateInvoice($invoice, $request->validated());

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('success', 'تم تحديث الفاتورة بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * إصدار فاتورة
     */
    public function issue(Invoice $invoice): RedirectResponse
    {
        try {
            $this->invoiceService->issueInvoice($invoice);

            return back()->with('success', 'تم إصدار الفاتورة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * إلغاء فاتورة
     */
    public function cancel(Request $request, Invoice $invoice): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->invoiceService->cancelInvoice($invoice, $request->reason);

            return back()->with('success', 'تم إلغاء الفاتورة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * طباعة الفاتورة
     */
    public function print(Invoice $invoice)
    {
        $pdf = $this->invoiceService->generatePdf($invoice);

        return $pdf->stream("فاتورة_{$invoice->invoice_number}.pdf");
    }

    /**
     * تنزيل الفاتورة PDF
     */
    public function download(Invoice $invoice)
    {
        $pdf = $this->invoiceService->generatePdf($invoice);

        return $pdf->download("فاتورة_{$invoice->invoice_number}.pdf");
    }

    /**
     * إنشاء فاتورة من طلب عمل
     */
    public function createFromWorkOrder(WorkOrder $workOrder): RedirectResponse
    {
        try {
            $invoice = $this->invoiceService->createFromWorkOrder($workOrder);

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('success', 'تم إنشاء الفاتورة من طلب العمل بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * عرض الفواتير المتأخرة
     */
    public function overdue(Request $request): View
    {
        $filters = array_merge(
            $request->only(['search', 'branch_id', 'sort_by', 'sort_direction']),
            ['overdue' => true]
        );

        $invoices = $this->invoiceService->getInvoices($filters, 15);

        return view('invoices.overdue', compact('invoices', 'filters'));
    }
}
