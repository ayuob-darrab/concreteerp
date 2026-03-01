<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\Contractor;
use App\Models\ContractorInvoice;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * قائمة الفواتير
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // ========================
        // واجهة المقاول: تقرير طلباته
        // ========================
        if ($user->account_code === 'cont') {
            $contractorId = optional($user->contractor)->id;
            if (!$contractorId) {
                $orders = collect();
            } else {
                $orders = WorkOrder::with(['concreteMix', 'branch'])
                    ->withCount('customerPayments')
                    ->where('company_code', $user->company_code)
                    ->where('branch_id', $user->branch_id)
                    ->where('sender_type', 'cont')
                    ->where('sender_id', $user->id)
                    ->whereIn('status_code', ['in_progress', 'completed'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            $stats = [
                'total_orders' => $orders->count(),
                'total_amount' => $orders->sum(function ($order) {
                    return (float) ($order->price ?? 0);
                }),
                'total_paid' => $orders->sum(function ($order) {
                    return (float) ($order->paid_amount ?? 0);
                }),
                'total_remaining' => $orders->sum(function ($order) {
                    $total = (float) ($order->price ?? 0);
                    $paid = (float) ($order->paid_amount ?? 0);
                    return max($total - $paid, 0);
                }),
            ];

            $contractors = [];
            $branches = [];

            return view('contractors.invoices.index', [
                'mode' => 'contractor-orders',
                'orders' => $orders,
                'invoices' => collect(),
                'contractors' => $contractors,
                'branches' => $branches,
                'stats' => $stats,
            ]);
        }

        // ========================
        // واجهة الشركة: فواتير المقاولين
        // ========================
        $query = ContractorInvoice::with(['contractor', 'branch', 'createdBy'])
            ->where('company_code', $user->company_code);

        // تصفية حسب المقاول
        if ($user->account_code !== 'cont' && $request->filled('contractor_id')) {
            $query->where('contractor_id', $request->contractor_id);
        }

        // تصفية حسب الفرع (لغير المقاول)
        if ($user->account_code !== 'cont' && $request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // تصفية حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // تصفية حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        // إحصائيات مبسطة لاستخدامها في الصفحة
        $statsQuery = clone $query;
        $stats = [
            'total_invoices' => $statsQuery->count(),
            'total_amount' => (clone $query)->sum('total'),
            'total_paid' => (clone $query)->sum('paid_amount'),
            'total_remaining' => (clone $query)
                ->selectRaw('SUM(total - paid_amount) as remaining')
                ->value('remaining') ?? 0,
        ];

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);

        $contractors = [];
        $branches = [];
        if ($user->account_code !== 'cont') {
            $contractors = Contractor::where('company_code', $user->company_code)
                ->where('status', 'active')
                ->get();
            $branches = \App\Models\Branch::where('company_code', $user->company_code)
                ->where('is_active', true)
                ->get();
        }

        return view('contractors.invoices.index', compact('invoices', 'contractors', 'branches', 'stats'));
    }

    /**
     * نموذج إنشاء فاتورة
     */
    public function create()
    {
        $contractors = Contractor::where('company_code', Auth::user()->company_code)
            ->where('status', 'active')
            ->get();

        return view('contractors.invoices.create', compact('contractors'));
    }

    /**
     * حفظ فاتورة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'contractor_id' => 'required|exists:contractors,id',
            'branch_id' => 'required|exists:branches,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'description' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // حساب الإجماليات
            $subtotal = collect($request->items)->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $taxRate = $request->tax_rate ?? 0;
            $taxAmount = $subtotal * ($taxRate / 100);
            $total = $subtotal + $taxAmount - ($request->discount ?? 0);

            $invoice = ContractorInvoice::create([
                'company_code' => Auth::user()->company_code,
                'branch_id' => $request->branch_id,
                'contractor_id' => $request->contractor_id,
                'invoice_number' => $this->generateInvoiceNumber(),
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'description' => $request->description,
                'items' => $request->items,
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'discount' => $request->discount ?? 0,
                'total' => $total,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('contractor-invoices.show', $invoice)
                ->with('success', 'تم إنشاء الفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل الفاتورة
     */
    public function show(ContractorInvoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load(['contractor', 'branch', 'createdBy', 'payments']);

        return view('contractors.invoices.show', compact('invoice'));
    }

    /**
     * نموذج تعديل الفاتورة
     */
    public function edit(ContractorInvoice $invoice)
    {
        $this->authorize('update', $invoice);

        if ($invoice->status !== 'draft') {
            return back()->with('error', 'لا يمكن تعديل فاتورة صادرة');
        }

        $contractors = Contractor::where('company_code', Auth::user()->company_code)
            ->where('status', 'active')
            ->get();

        return view('contractors.invoices.edit', compact('invoice', 'contractors'));
    }

    /**
     * تحديث الفاتورة
     */
    public function update(Request $request, ContractorInvoice $invoice)
    {
        $this->authorize('update', $invoice);

        if ($invoice->status !== 'draft') {
            return back()->with('error', 'لا يمكن تعديل فاتورة صادرة');
        }

        $request->validate([
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'description' => 'required|string',
            'items' => 'required|array|min:1',
        ]);

        // إعادة حساب الإجماليات
        $subtotal = collect($request->items)->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        $taxRate = $request->tax_rate ?? 0;
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount - ($request->discount ?? 0);

        $invoice->update([
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'description' => $request->description,
            'items' => $request->items,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'discount' => $request->discount ?? 0,
            'total' => $total,
        ]);

        return redirect()->route('contractor-invoices.show', $invoice)
            ->with('success', 'تم تحديث الفاتورة بنجاح');
    }

    /**
     * حذف الفاتورة
     */
    public function destroy(ContractorInvoice $invoice)
    {
        $this->authorize('delete', $invoice);

        if ($invoice->status !== 'draft') {
            return back()->with('error', 'لا يمكن حذف فاتورة صادرة');
        }

        $invoice->delete();

        return redirect()->route('contractor-invoices.index')
            ->with('success', 'تم حذف الفاتورة بنجاح');
    }

    /**
     * إصدار الفاتورة
     */
    public function issue(ContractorInvoice $invoice)
    {
        $this->authorize('update', $invoice);

        if ($invoice->status !== 'draft') {
            return back()->with('error', 'الفاتورة صادرة بالفعل');
        }

        $invoice->update([
            'status' => 'issued',
            'issued_at' => now(),
        ]);

        // تحديث رصيد المقاول
        $invoice->contractor->increment('current_balance', $invoice->total);

        return back()->with('success', 'تم إصدار الفاتورة بنجاح');
    }

    /**
     * إلغاء الفاتورة
     */
    public function cancel(Request $request, ContractorInvoice $invoice)
    {
        $this->authorize('update', $invoice);

        if ($invoice->status === 'cancelled') {
            return back()->with('error', 'الفاتورة ملغاة بالفعل');
        }

        if ($invoice->paid_amount > 0) {
            return back()->with('error', 'لا يمكن إلغاء فاتورة تم سداد جزء منها');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        // إعادة الرصيد إذا كانت صادرة
        if ($invoice->status === 'issued') {
            $invoice->contractor->decrement('current_balance', $invoice->total);
        }

        $invoice->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم إلغاء الفاتورة بنجاح');
    }

    /**
     * طباعة الفاتورة
     */
    public function print(ContractorInvoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load(['contractor', 'branch', 'createdBy']);

        return view('contractors.invoices.print', compact('invoice'));
    }

    /**
     * تحميل الفاتورة PDF
     */
    public function download(ContractorInvoice $invoice)
    {
        $this->authorize('view', $invoice);

        // TODO: تنفيذ تحميل PDF
        return back()->with('info', 'ميزة التحميل قيد التطوير');
    }

    /**
     * إنشاء فاتورة من أمر عمل
     */
    public function createFromWorkOrder(WorkOrder $workOrder)
    {
        if (!$workOrder->contractor_id) {
            return back()->with('error', 'أمر العمل غير مرتبط بمقاول');
        }

        $invoice = ContractorInvoice::create([
            'company_code' => Auth::user()->company_code,
            'branch_id' => $workOrder->branch_id,
            'contractor_id' => $workOrder->contractor_id,
            'work_order_id' => $workOrder->id,
            'invoice_number' => $this->generateInvoiceNumber(),
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'description' => 'فاتورة أمر العمل رقم ' . $workOrder->order_number,
            'items' => [[
                'description' => $workOrder->description,
                'quantity' => $workOrder->executed_quantity ?? $workOrder->quantity,
                'unit_price' => $workOrder->final_price ?? $workOrder->initial_price,
            ]],
            'subtotal' => ($workOrder->executed_quantity ?? $workOrder->quantity) * ($workOrder->final_price ?? $workOrder->initial_price),
            'total' => ($workOrder->executed_quantity ?? $workOrder->quantity) * ($workOrder->final_price ?? $workOrder->initial_price),
            'status' => 'draft',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('contractor-invoices.edit', $invoice)
            ->with('success', 'تم إنشاء الفاتورة من أمر العمل');
    }

    /**
     * قائمة الفواتير المتأخرة
     */
    public function overdueList()
    {
        $invoices = ContractorInvoice::with(['contractor', 'branch'])
            ->where('company_code', Auth::user()->company_code)
            ->where('status', 'issued')
            ->where('due_date', '<', now())
            ->whereColumn('paid_amount', '<', 'total')
            ->orderBy('due_date')
            ->paginate(20);

        return view('contractors.invoices.overdue', compact('invoices'));
    }

    /**
     * إحصائيات الفواتير
     */
    public function statistics()
    {
        $companyCode = Auth::user()->company_code;

        $statistics = [
            'total_invoices' => ContractorInvoice::where('company_code', $companyCode)->count(),
            'draft_invoices' => ContractorInvoice::where('company_code', $companyCode)->where('status', 'draft')->count(),
            'issued_invoices' => ContractorInvoice::where('company_code', $companyCode)->where('status', 'issued')->count(),
            'paid_invoices' => ContractorInvoice::where('company_code', $companyCode)->where('status', 'paid')->count(),
            'overdue_invoices' => ContractorInvoice::where('company_code', $companyCode)
                ->where('status', 'issued')
                ->where('due_date', '<', now())
                ->whereColumn('paid_amount', '<', 'total')
                ->count(),
            'total_amount' => ContractorInvoice::where('company_code', $companyCode)
                ->whereIn('status', ['issued', 'paid', 'partial'])
                ->sum('total'),
            'total_paid' => ContractorInvoice::where('company_code', $companyCode)
                ->whereIn('status', ['issued', 'paid', 'partial'])
                ->sum('paid_amount'),
            'total_outstanding' => ContractorInvoice::where('company_code', $companyCode)
                ->whereIn('status', ['issued', 'partial'])
                ->selectRaw('SUM(total - paid_amount) as outstanding')
                ->value('outstanding') ?? 0,
        ];

        return view('contractors.invoices.statistics', compact('statistics'));
    }

    /**
     * توليد رقم فاتورة
     */
    private function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = ContractorInvoice::where('company_code', Auth::user()->company_code)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastInvoice ? (intval(substr($lastInvoice->invoice_number, -5)) + 1) : 1;

        return 'INV-' . $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }
}
