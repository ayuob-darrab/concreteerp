<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\Contractor;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvoiceApiController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * فواتير مقاول معين
     */
    public function contractorInvoices(Request $request, Contractor $contractor): JsonResponse
    {
        $invoices = $contractor->invoices()
            ->with(['items', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => InvoiceResource::collection($invoices),
            'meta' => [
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'per_page' => $invoices->perPage(),
                'total' => $invoices->total(),
            ]
        ]);
    }

    /**
     * عرض فاتورة
     */
    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load(['contractor', 'items', 'workOrder', 'createdBy']);

        return response()->json([
            'success' => true,
            'data' => new InvoiceResource($invoice)
        ]);
    }

    /**
     * الفواتير المتأخرة
     */
    public function overdue(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;

        $invoices = Invoice::where('company_id', $companyId)
            ->overdue()
            ->with(['contractor', 'items'])
            ->orderBy('due_date', 'asc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => InvoiceResource::collection($invoices),
            'meta' => [
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'per_page' => $invoices->perPage(),
                'total' => $invoices->total(),
            ]
        ]);
    }

    /**
     * إحصائيات الفواتير
     */
    public function statistics(): JsonResponse
    {
        $stats = $this->invoiceService->getStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
