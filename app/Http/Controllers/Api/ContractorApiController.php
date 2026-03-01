<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContractorResource;
use App\Http\Resources\ContractorAccountResource;
use App\Models\Contractor;
use App\Services\ContractorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContractorApiController extends Controller
{
    protected ContractorService $contractorService;

    public function __construct(ContractorService $contractorService)
    {
        $this->contractorService = $contractorService;
    }

    /**
     * الحصول على قائمة المقاولين
     */
    public function index(Request $request): JsonResponse
    {
        $contractors = $this->contractorService->getContractors($request->all());

        return response()->json([
            'success' => true,
            'data' => ContractorResource::collection($contractors),
            'meta' => [
                'current_page' => $contractors->currentPage(),
                'last_page' => $contractors->lastPage(),
                'per_page' => $contractors->perPage(),
                'total' => $contractors->total(),
            ]
        ]);
    }

    /**
     * البحث السريع عن المقاولين
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        $contractors = $this->contractorService->quickSearch($request->q);

        return response()->json([
            'success' => true,
            'data' => $contractors->map(function ($contractor) {
                return [
                    'id' => $contractor->id,
                    'name' => $contractor->name,
                    'phone' => $contractor->phone,
                    'balance' => $contractor->account?->balance ?? 0,
                ];
            })
        ]);
    }

    /**
     * عرض تفاصيل مقاول
     */
    public function show(Contractor $contractor): JsonResponse
    {
        $contractor->load(['account', 'orders', 'invoices', 'checks']);

        return response()->json([
            'success' => true,
            'data' => new ContractorResource($contractor)
        ]);
    }

    /**
     * عرض حساب المقاول
     */
    public function account(Contractor $contractor): JsonResponse
    {
        $contractor->load(['account.transactions']);

        if (!$contractor->account) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد حساب لهذا المقاول'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ContractorAccountResource($contractor->account)
        ]);
    }

    /**
     * إحصائيات المقاول
     */
    public function statistics(Contractor $contractor): JsonResponse
    {
        $stats = $this->contractorService->getContractorStatistics($contractor);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * حركات المقاول
     */
    public function transactions(Request $request, Contractor $contractor): JsonResponse
    {
        $perPage = $request->get('per_page', 20);

        $transactions = $contractor->account?->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        if (!$transactions) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد حساب لهذا المقاول'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transactions->items(),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ]
        ]);
    }
}
