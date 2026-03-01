<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CheckResource;
use App\Models\Check;
use App\Models\Contractor;
use App\Services\CheckService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CheckApiController extends Controller
{
    protected CheckService $checkService;

    public function __construct(CheckService $checkService)
    {
        $this->checkService = $checkService;
    }

    /**
     * شيكات مقاول معين
     */
    public function contractorChecks(Request $request, Contractor $contractor): JsonResponse
    {
        $checks = $contractor->checks()
            ->with(['drawer', 'receivedBy', 'statusLogs'])
            ->orderBy('due_date', 'asc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => CheckResource::collection($checks),
            'meta' => [
                'current_page' => $checks->currentPage(),
                'last_page' => $checks->lastPage(),
                'per_page' => $checks->perPage(),
                'total' => $checks->total(),
            ]
        ]);
    }

    /**
     * عرض شيك
     */
    public function show(Check $check): JsonResponse
    {
        $check->load(['contractor', 'invoice', 'drawer', 'receivedBy', 'statusLogs.changedBy', 'endorsedTo']);

        return response()->json([
            'success' => true,
            'data' => new CheckResource($check)
        ]);
    }

    /**
     * الشيكات المستحقة اليوم
     */
    public function dueToday(Request $request): JsonResponse
    {
        $checks = $this->checkService->getDueToday();

        return response()->json([
            'success' => true,
            'data' => CheckResource::collection($checks),
            'count' => $checks->count()
        ]);
    }

    /**
     * الشيكات المستحقة هذا الأسبوع
     */
    public function dueThisWeek(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;

        $checks = Check::where('company_id', $companyId)
            ->dueThisWeek()
            ->with(['contractor', 'drawer'])
            ->orderBy('due_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => CheckResource::collection($checks),
            'count' => $checks->count()
        ]);
    }

    /**
     * الشيكات المتأخرة
     */
    public function overdue(Request $request): JsonResponse
    {
        $checks = $this->checkService->getOverdue();

        return response()->json([
            'success' => true,
            'data' => CheckResource::collection($checks),
            'count' => $checks->count()
        ]);
    }

    /**
     * إحصائيات الشيكات
     */
    public function statistics(): JsonResponse
    {
        $stats = $this->checkService->getStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
