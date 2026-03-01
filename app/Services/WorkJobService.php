<?php

namespace App\Services;

use App\Models\WorkJob;
use App\Models\WorkOrder;
use App\Models\MaterialReservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WorkJobService
{
    protected $materialReservationService;

    public function __construct(MaterialReservationService $materialReservationService)
    {
        $this->materialReservationService = $materialReservationService;
    }

    /**
     * إنشاء أمر عمل من طلب موافق عليه
     */
    public function createFromOrder(WorkOrder $order, array $data = [])
    {
        return DB::transaction(function () use ($order, $data) {
            // التأكد من أن الطلب في حالة جاهزة للتنفيذ
            if (!in_array($order->status, ['approved', 'ready_for_execution'])) {
                throw new \Exception('الطلب غير جاهز للتنفيذ');
            }

            // إنشاء أمر العمل
            $job = WorkJob::create([
                'job_number' => WorkJob::generateJobNumber($order->branch->code),
                'company_code' => $order->company_code,
                'branch_id' => $order->branch_id,
                'order_id' => $order->id,
                'customer_type' => $order->customer_type ?? 'direct_customer',
                'customer_id' => $order->customer_id,
                'customer_name' => $data['customer_name'] ?? $order->customer_name,
                'customer_phone' => $data['customer_phone'] ?? $order->customer_phone,
                'concrete_type_id' => $order->concrete_type_id,
                'total_quantity' => $order->quantity,
                'unit_price' => $order->unit_price,
                'total_price' => $order->total_price,
                'discount_amount' => $order->discount_amount ?? 0,
                'final_price' => $order->final_price ?? $order->total_price,
                'location_address' => $order->delivery_address,
                'location_map_url' => $order->map_url ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'scheduled_date' => $data['scheduled_date'] ?? $order->delivery_date,
                'scheduled_time' => $data['scheduled_time'] ?? $order->delivery_time,
                'supervisor_id' => $data['supervisor_id'] ?? null,
                'notes' => $data['notes'] ?? $order->notes,
                'internal_notes' => $data['internal_notes'] ?? null,
                'status' => WorkJob::STATUS_PENDING,
                'created_by' => Auth::id(),
            ]);

            // تحديث حالة الطلب الأصلي
            $order->update([
                'status' => 'in_execution',
                'job_id' => $job->id,
            ]);

            return $job;
        });
    }

    /**
     * حجز المواد لأمر العمل
     */
    public function reserveMaterials(WorkJob $job)
    {
        return DB::transaction(function () use ($job) {
            // حساب المواد المطلوبة
            $materialsNeeded = $this->calculateMaterialsNeeded($job);

            // حجز كل مادة
            foreach ($materialsNeeded as $material) {
                $this->materialReservationService->reserve(
                    $job,
                    $material['material_id'],
                    $material['quantity'],
                    $material['inventory_id'] ?? null
                );
            }

            // تحديث حالة أمر العمل
            $job->update(['status' => WorkJob::STATUS_MATERIALS_RESERVED]);

            return $job->fresh()->materialReservations;
        });
    }

    /**
     * حساب المواد المطلوبة لأمر العمل
     */
    public function calculateMaterialsNeeded(WorkJob $job)
    {
        $materials = [];
        $mix = $job->concreteType;

        if (!$mix || !$mix->components) {
            return $materials;
        }

        foreach ($mix->components as $component) {
            $materials[] = [
                'material_id' => $component->material_id,
                'quantity' => $job->total_quantity * $component->ratio,
                'unit' => $component->unit,
                'material_name' => $component->material->name ?? 'Unknown',
            ];
        }

        return $materials;
    }

    /**
     * تحديث تقدم أمر العمل
     */
    public function updateProgress(WorkJob $job)
    {
        $job->updateProgress();
        return $job;
    }

    /**
     * إكمال أمر العمل
     */
    public function complete(WorkJob $job, array $data = [])
    {
        return DB::transaction(function () use ($job, $data) {
            // التأكد من إمكانية الإكمال
            if ($job->status === WorkJob::STATUS_COMPLETED) {
                throw new \Exception('أمر العمل مكتمل بالفعل');
            }

            // تحديث البيانات النهائية
            $job->update([
                'status' => WorkJob::STATUS_COMPLETED,
                'actual_end_date' => now()->toDateString(),
                'notes' => $data['notes'] ?? $job->notes,
                'updated_by' => Auth::id(),
            ]);

            // تحديث حالة الطلب الأصلي
            if ($job->order) {
                $job->order->update(['status' => 'completed']);
            }

            // إفراج المواد المتبقية
            foreach ($job->materialReservations()->active()->get() as $reservation) {
                $reservation->release();
            }

            return $job;
        });
    }

    /**
     * إلغاء أمر العمل
     */
    public function cancel(WorkJob $job, string $reason)
    {
        return DB::transaction(function () use ($job, $reason) {
            // التأكد من إمكانية الإلغاء
            if ($job->status === WorkJob::STATUS_COMPLETED) {
                throw new \Exception('لا يمكن إلغاء أمر عمل مكتمل');
            }

            // إلغاء الشحنات المعلقة
            $job->shipments()
                ->whereNotIn('status', ['completed', 'returned', 'cancelled'])
                ->update(['status' => 'cancelled']);

            // إفراج المواد المحجوزة
            foreach ($job->materialReservations()->active()->get() as $reservation) {
                $reservation->release();
            }

            // تحديث أمر العمل
            $job->update([
                'status' => WorkJob::STATUS_CANCELLED,
                'internal_notes' => ($job->internal_notes ? $job->internal_notes . "\n" : '') . "سبب الإلغاء: {$reason}",
                'updated_by' => Auth::id(),
            ]);

            // تحديث الطلب الأصلي
            if ($job->order) {
                $job->order->update(['status' => 'cancelled']);
            }

            return $job;
        });
    }

    /**
     * تعليق أمر العمل
     */
    public function hold(WorkJob $job, string $reason)
    {
        $job->update([
            'status' => WorkJob::STATUS_ON_HOLD,
            'internal_notes' => ($job->internal_notes ? $job->internal_notes . "\n" : '') . "سبب التعليق: {$reason}",
            'updated_by' => Auth::id(),
        ]);

        return $job;
    }

    /**
     * استئناف أمر عمل معلق
     */
    public function resume(WorkJob $job)
    {
        if ($job->status !== WorkJob::STATUS_ON_HOLD) {
            throw new \Exception('أمر العمل ليس معلقاً');
        }

        $newStatus = $job->executed_quantity > 0
            ? WorkJob::STATUS_PARTIALLY_COMPLETED
            : ($job->materialReservations()->exists() ? WorkJob::STATUS_MATERIALS_RESERVED : WorkJob::STATUS_PENDING);

        $job->update([
            'status' => $newStatus,
            'updated_by' => Auth::id(),
        ]);

        return $job;
    }

    /**
     * تعيين مشرف لأمر العمل
     */
    public function assignSupervisor(WorkJob $job, int $supervisorId)
    {
        $job->update([
            'supervisor_id' => $supervisorId,
            'updated_by' => Auth::id(),
        ]);

        return $job;
    }

    /**
     * الحصول على أوامر العمل لليوم
     */
    public function getTodayJobs($branchId = null)
    {
        $query = WorkJob::with(['branch', 'concreteType', 'supervisor', 'shipments'])
            ->today()
            ->orderBy('scheduled_time');

        if ($branchId) {
            $query->branch($branchId);
        }

        return $query->get();
    }

    /**
     * الحصول على إحصائيات أوامر العمل
     */
    public function getStatistics($companyCode, $branchId = null, $dateFrom = null, $dateTo = null)
    {
        $query = WorkJob::company($companyCode);

        if ($branchId) {
            $query->branch($branchId);
        }

        if ($dateFrom && $dateTo) {
            $query->scheduledBetween($dateFrom, $dateTo);
        }

        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->status(WorkJob::STATUS_PENDING)->count(),
            'in_progress' => (clone $query)->inProgress()->count(),
            'completed' => (clone $query)->completed()->count(),
            'cancelled' => (clone $query)->status(WorkJob::STATUS_CANCELLED)->count(),
            'total_quantity' => (clone $query)->sum('total_quantity'),
            'executed_quantity' => (clone $query)->sum('executed_quantity'),
            'total_revenue' => (clone $query)->completed()->sum('final_price'),
        ];
    }
}
