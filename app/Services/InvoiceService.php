<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ContractorAccount;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    /**
     * الحصول على قائمة الفواتير
     */
    public function getInvoices(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Invoice::query()
            ->forCompany(auth()->user()->company_code)
            ->with(['account.contractor', 'workOrder', 'creator', 'items']);

        // البحث
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('invoice_number', 'like', "%{$filters['search']}%")
                    ->orWhere('party_name', 'like', "%{$filters['search']}%");
            });
        }

        // فلتر الحالة
        if (!empty($filters['status'])) {
            $query->status($filters['status']);
        }

        // فلتر الفرع
        if (!empty($filters['branch_id'])) {
            $query->forBranch($filters['branch_id']);
        }

        // فلتر التاريخ
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $query->betweenDates($filters['from_date'], $filters['to_date']);
        }

        // الفواتير المتأخرة فقط
        if (!empty($filters['overdue'])) {
            $query->overdue();
        }

        // الفواتير غير المدفوعة
        if (!empty($filters['unpaid'])) {
            $query->unpaid();
        }

        // الترتيب
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * إنشاء فاتورة جديدة
     */
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $data['company_code'] = auth()->user()->company_code;
            $data['branch_id'] = $data['branch_id'] ?? auth()->user()->branch_id;
            $data['created_by'] = auth()->id();
            $data['status'] = Invoice::STATUS_DRAFT;

            // حساب الإجماليات
            $totals = $this->calculateTotals($data['items'], $data);
            $data = array_merge($data, $totals);

            // إنشاء الفاتورة
            $items = $data['items'];
            unset($data['items']);

            $invoice = Invoice::create($data);

            // إنشاء البنود
            foreach ($items as $index => $item) {
                $item['invoice_id'] = $invoice->id;
                $item['sort_order'] = $index + 1;
                InvoiceItem::create($item);
            }

            Log::info('تم إنشاء فاتورة جديدة', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'total_amount' => $invoice->total_amount,
                'created_by' => auth()->id(),
            ]);

            return $invoice->fresh(['account.contractor', 'items', 'workOrder']);
        });
    }

    /**
     * تحديث فاتورة
     */
    public function updateInvoice(Invoice $invoice, array $data): Invoice
    {
        if ($invoice->status !== Invoice::STATUS_DRAFT) {
            throw new \Exception('لا يمكن تعديل فاتورة غير مسودة');
        }

        return DB::transaction(function () use ($invoice, $data) {
            // تحديث البنود
            if (isset($data['items'])) {
                // حذف البنود القديمة
                $invoice->items()->delete();

                // حساب الإجماليات
                $totals = $this->calculateTotals($data['items'], $data);
                $data = array_merge($data, $totals);

                $items = $data['items'];
                unset($data['items']);

                // إنشاء البنود الجديدة
                foreach ($items as $index => $item) {
                    $item['invoice_id'] = $invoice->id;
                    $item['sort_order'] = $index + 1;
                    InvoiceItem::create($item);
                }
            }

            $invoice->update($data);

            Log::info('تم تحديث الفاتورة', [
                'invoice_id' => $invoice->id,
                'updated_by' => auth()->id(),
            ]);

            return $invoice->fresh(['account.contractor', 'items', 'workOrder']);
        });
    }

    /**
     * إصدار فاتورة
     */
    public function issueInvoice(Invoice $invoice): Invoice
    {
        if ($invoice->status !== Invoice::STATUS_DRAFT) {
            throw new \Exception('لا يمكن إصدار هذه الفاتورة');
        }

        $invoice->issue();

        // تسجيل في حساب المقاول
        if ($invoice->account) {
            $invoice->account->addTransaction(
                'debit',
                $invoice->total_amount,
                'invoice',
                "فاتورة رقم: {$invoice->invoice_number}"
            );
        }

        Log::info('تم إصدار الفاتورة', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'issued_by' => auth()->id(),
        ]);

        return $invoice->fresh();
    }

    /**
     * إلغاء فاتورة
     */
    public function cancelInvoice(Invoice $invoice, string $reason): Invoice
    {
        if (in_array($invoice->status, [Invoice::STATUS_CANCELLED, Invoice::STATUS_PAID])) {
            throw new \Exception('لا يمكن إلغاء هذه الفاتورة');
        }

        // عكس العملية إذا كانت مصدرة
        if ($invoice->status !== Invoice::STATUS_DRAFT && $invoice->account) {
            $invoice->account->addTransaction(
                'credit',
                $invoice->total_amount - $invoice->paid_amount,
                'invoice_cancellation',
                "إلغاء فاتورة رقم: {$invoice->invoice_number}"
            );
        }

        $invoice->cancel($reason);

        Log::warning('تم إلغاء الفاتورة', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'reason' => $reason,
            'cancelled_by' => auth()->id(),
        ]);

        return $invoice->fresh();
    }

    /**
     * حساب الإجماليات
     */
    protected function calculateTotals(array $items, array $invoiceData = []): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $itemSubtotal = ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
            $itemDiscount = $itemSubtotal * (($item['discount_percentage'] ?? 0) / 100);
            $itemAfterDiscount = $itemSubtotal - $itemDiscount;
            $itemTax = $itemAfterDiscount * (($item['tax_percentage'] ?? 0) / 100);

            $subtotal += $itemSubtotal;
        }

        // حساب الخصم الإجمالي
        $discountPercentage = $invoiceData['discount_percentage'] ?? 0;
        $discountAmount = $invoiceData['discount_amount'] ?? ($subtotal * ($discountPercentage / 100));
        $afterDiscount = $subtotal - $discountAmount;

        // حساب الضريبة الإجمالية
        $taxPercentage = $invoiceData['tax_percentage'] ?? 15;
        $taxAmount = $afterDiscount * ($taxPercentage / 100);

        $totalAmount = $afterDiscount + $taxAmount;

        return [
            'subtotal' => round($subtotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'total_amount' => round($totalAmount, 2),
            'remaining_amount' => round($totalAmount, 2),
            'paid_amount' => 0,
        ];
    }

    /**
     * إنشاء فاتورة من طلب عمل
     */
    public function createFromWorkOrder(WorkOrder $workOrder): Invoice
    {
        $items = [];
        foreach ($workOrder->items as $item) {
            $items[] = [
                'item_type' => 'concrete',
                'description' => $item->product_name ?? 'خرسانة',
                'work_order_item_id' => $item->id,
                'quantity' => $item->delivered_quantity ?? $item->quantity,
                'unit' => $item->unit ?? 'm³',
                'unit_price' => $item->unit_price ?? 0,
                'discount_percentage' => 0,
                'tax_percentage' => 15,
            ];
        }

        $data = [
            'invoice_type' => 'sales',
            'account_id' => $workOrder->contractor?->account?->id,
            'work_order_id' => $workOrder->id,
            'party_name' => $workOrder->contractor?->contractor_name ?? $workOrder->customer_name,
            'party_phone' => $workOrder->contractor?->phone ?? $workOrder->customer_phone,
            'party_address' => $workOrder->delivery_address,
            'party_tax_number' => $workOrder->contractor?->tax_number,
            'invoice_date' => today(),
            'due_date' => today()->addDays($workOrder->contractor?->payment_terms ?? 30),
            'items' => $items,
        ];

        return $this->createInvoice($data);
    }

    /**
     * طباعة الفاتورة PDF
     */
    public function generatePdf(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $invoice->load(['account.contractor', 'items', 'workOrder', 'creator', 'approver']);

        $pdf = Pdf::loadView('invoices.print', [
            'invoice' => $invoice,
            'company' => auth()->user()->company,
        ]);

        $pdf->setPaper('a4');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf;
    }

    /**
     * تحديث حالات الفواتير المتأخرة
     */
    public function updateOverdueInvoices(): int
    {
        $count = 0;

        Invoice::where('due_date', '<', today())
            ->where('remaining_amount', '>', 0)
            ->whereNotIn('status', [Invoice::STATUS_OVERDUE, Invoice::STATUS_CANCELLED, Invoice::STATUS_PAID])
            ->chunk(100, function ($invoices) use (&$count) {
                foreach ($invoices as $invoice) {
                    $invoice->update(['status' => Invoice::STATUS_OVERDUE]);
                    $count++;
                }
            });

        if ($count > 0) {
            Log::info("تم تحديث {$count} فاتورة متأخرة");
        }

        return $count;
    }

    /**
     * الحصول على إحصائيات الفواتير
     */
    public function getStatistics(string $companyCode, ?string $fromDate = null, ?string $toDate = null): array
    {
        $query = Invoice::forCompany($companyCode);

        if ($fromDate && $toDate) {
            $query->betweenDates($fromDate, $toDate);
        }

        return [
            'total_invoices' => $query->count(),
            'total_amount' => $query->sum('total_amount'),
            'total_paid' => $query->sum('paid_amount'),
            'total_remaining' => $query->sum('remaining_amount'),
            'by_status' => [
                'draft' => (clone $query)->status(Invoice::STATUS_DRAFT)->count(),
                'issued' => (clone $query)->status(Invoice::STATUS_ISSUED)->count(),
                'partially_paid' => (clone $query)->status(Invoice::STATUS_PARTIALLY_PAID)->count(),
                'paid' => (clone $query)->status(Invoice::STATUS_PAID)->count(),
                'overdue' => (clone $query)->status(Invoice::STATUS_OVERDUE)->count(),
                'cancelled' => (clone $query)->status(Invoice::STATUS_CANCELLED)->count(),
            ],
            'overdue_count' => (clone $query)->overdue()->count(),
            'overdue_amount' => (clone $query)->overdue()->sum('remaining_amount'),
        ];
    }
}
