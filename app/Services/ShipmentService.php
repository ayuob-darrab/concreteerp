<?php

namespace App\Services;

use App\Models\WorkShipment;
use App\Models\WorkJob;
use App\Models\ShipmentEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ShipmentService
{
    /**
     * إنشاء شحنة جديدة
     */
    public function create(WorkJob $job, array $data)
    {
        return DB::transaction(function () use ($job, $data) {
            // التأكد من إمكانية إضافة شحنة
            if (!$job->can_add_shipment) {
                throw new \Exception('لا يمكن إضافة شحنات لأمر عمل مكتمل أو ملغي');
            }

            // حساب رقم الشحنة التالي
            $nextShipmentNumber = $job->shipments()->max('shipment_number') + 1;

            // إنشاء الشحنة
            $shipment = WorkShipment::create([
                'job_id' => $job->id,
                'shipment_number' => $nextShipmentNumber,
                'planned_quantity' => $data['planned_quantity'],
                'mixer_id' => $data['mixer_id'] ?? null,
                'truck_id' => $data['truck_id'] ?? null,
                'pump_id' => $data['pump_id'] ?? null,
                'mixer_driver_id' => $data['mixer_driver_id'] ?? null,
                'truck_driver_id' => $data['truck_driver_id'] ?? null,
                'pump_driver_id' => $data['pump_driver_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => WorkShipment::STATUS_PLANNED,
                'created_by' => Auth::id(),
            ]);

            // تسجيل حدث الإنشاء
            $shipment->recordEvent(ShipmentEvent::TYPE_CREATED, 'تم إنشاء الشحنة');

            // تحديث حالة أمر العمل إذا كان في حالة انتظار
            if ($job->status === WorkJob::STATUS_PENDING || $job->status === WorkJob::STATUS_MATERIALS_RESERVED) {
                $job->update(['status' => WorkJob::STATUS_IN_PROGRESS]);
                if (!$job->actual_start_date) {
                    $job->update(['actual_start_date' => now()->toDateString()]);
                }
            }

            // تحديث عدد الشحنات
            $job->update(['total_shipments' => $job->shipments()->count()]);

            return $shipment;
        });
    }

    /**
     * تعيين الآليات للشحنة
     */
    public function assignVehicles(WorkShipment $shipment, array $vehicles)
    {
        $shipment->update([
            'mixer_id' => $vehicles['mixer_id'] ?? $shipment->mixer_id,
            'truck_id' => $vehicles['truck_id'] ?? $shipment->truck_id,
            'pump_id' => $vehicles['pump_id'] ?? $shipment->pump_id,
        ]);

        return $shipment;
    }

    /**
     * تعيين السائقين للشحنة
     */
    public function assignDrivers(WorkShipment $shipment, array $drivers)
    {
        $shipment->update([
            'mixer_driver_id' => $drivers['mixer_driver_id'] ?? $shipment->mixer_driver_id,
            'truck_driver_id' => $drivers['truck_driver_id'] ?? $shipment->truck_driver_id,
            'pump_driver_id' => $drivers['pump_driver_id'] ?? $shipment->pump_driver_id,
        ]);

        return $shipment;
    }

    /**
     * تسجيل الانطلاق
     */
    public function recordDeparture(WorkShipment $shipment, $latitude = null, $longitude = null)
    {
        if (!$shipment->can_depart) {
            throw new \Exception('لا يمكن تسجيل الانطلاق في هذه الحالة');
        }

        $shipment->update([
            'status' => WorkShipment::STATUS_DEPARTED,
            'departure_time' => now(),
        ]);

        $shipment->recordEvent(
            ShipmentEvent::TYPE_DEPARTED,
            'انطلقت الشحنة',
            $latitude,
            $longitude
        );

        return $shipment;
    }

    /**
     * تسجيل الوصول
     */
    public function recordArrival(WorkShipment $shipment, $latitude = null, $longitude = null)
    {
        if (!$shipment->can_arrive) {
            throw new \Exception('لا يمكن تسجيل الوصول في هذه الحالة');
        }

        $shipment->update([
            'status' => WorkShipment::STATUS_ARRIVED,
            'arrival_time' => now(),
        ]);

        $shipment->recordEvent(
            ShipmentEvent::TYPE_ARRIVED,
            'وصلت الشحنة للموقع',
            $latitude,
            $longitude
        );

        return $shipment;
    }

    /**
     * تسجيل بدء العمل
     */
    public function recordWorkStart(WorkShipment $shipment, $latitude = null, $longitude = null)
    {
        if (!$shipment->can_start_work) {
            throw new \Exception('لا يمكن بدء العمل في هذه الحالة');
        }

        $shipment->update([
            'status' => WorkShipment::STATUS_WORKING,
            'work_start_time' => now(),
        ]);

        $shipment->recordEvent(
            ShipmentEvent::TYPE_WORK_STARTED,
            'بدأ العمل (الصب)',
            $latitude,
            $longitude
        );

        return $shipment;
    }

    /**
     * تسجيل انتهاء العمل
     */
    public function recordWorkEnd(WorkShipment $shipment, float $actualQuantity, $latitude = null, $longitude = null, $notes = null)
    {
        if (!$shipment->can_end_work) {
            throw new \Exception('لا يمكن إنهاء العمل في هذه الحالة');
        }

        $shipment->update([
            'status' => WorkShipment::STATUS_COMPLETED,
            'work_end_time' => now(),
            'actual_quantity' => $actualQuantity,
            'driver_notes' => $notes,
        ]);

        $shipment->recordEvent(
            ShipmentEvent::TYPE_WORK_ENDED,
            "انتهى العمل - الكمية المنفذة: {$actualQuantity} م³",
            $latitude,
            $longitude,
            ['actual_quantity' => $actualQuantity]
        );

        // تحديث تقدم أمر العمل
        $shipment->job->updateProgress();

        return $shipment;
    }

    /**
     * تسجيل العودة للمقر
     */
    public function recordReturn(WorkShipment $shipment, $latitude = null, $longitude = null)
    {
        if (!$shipment->can_return) {
            throw new \Exception('لا يمكن تسجيل العودة في هذه الحالة');
        }

        $shipment->update([
            'status' => WorkShipment::STATUS_RETURNED,
            'return_time' => now(),
        ]);

        $shipment->recordEvent(
            ShipmentEvent::TYPE_RETURNED,
            'عادت الشحنة للمقر',
            $latitude,
            $longitude
        );

        return $shipment;
    }

    /**
     * إلغاء الشحنة
     */
    public function cancel(WorkShipment $shipment, string $reason)
    {
        if (in_array($shipment->status, [WorkShipment::STATUS_COMPLETED, WorkShipment::STATUS_RETURNED])) {
            throw new \Exception('لا يمكن إلغاء شحنة مكتملة');
        }

        $shipment->update([
            'status' => WorkShipment::STATUS_CANCELLED,
            'notes' => ($shipment->notes ? $shipment->notes . "\n" : '') . "سبب الإلغاء: {$reason}",
        ]);

        $shipment->recordEvent(
            ShipmentEvent::TYPE_CANCELLED,
            "تم إلغاء الشحنة: {$reason}"
        );

        return $shipment;
    }

    /**
     * تسجيل مشكلة
     */
    public function reportIssue(WorkShipment $shipment, string $description, $latitude = null, $longitude = null)
    {
        $shipment->recordEvent(
            ShipmentEvent::TYPE_ISSUE_REPORTED,
            $description,
            $latitude,
            $longitude
        );

        return $shipment;
    }

    /**
     * الحصول على الشحنات النشطة
     */
    public function getActiveShipments($branchId = null)
    {
        $query = WorkShipment::with(['job', 'mixer', 'truck', 'pump', 'mixerDriver', 'truckDriver', 'pumpDriver'])
            ->active();

        if ($branchId) {
            $query->whereHas('job', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        return $query->get();
    }

    /**
     * الحصول على شحنات السائق
     */
    public function getDriverShipments($driverId, $status = null)
    {
        $query = WorkShipment::with(['job', 'mixer', 'truck', 'pump'])
            ->forDriver($driverId);

        if ($status) {
            $query->status($status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * الحصول على الشحنة الحالية للسائق
     */
    public function getCurrentShipmentForDriver($driverId)
    {
        return WorkShipment::with(['job', 'mixer', 'truck', 'pump', 'events'])
            ->forDriver($driverId)
            ->active()
            ->first();
    }

    /**
     * الحصول على إحصائيات الشحنات
     */
    public function getStatistics($jobId = null, $dateFrom = null, $dateTo = null)
    {
        $query = WorkShipment::query();

        if ($jobId) {
            $query->where('job_id', $jobId);
        }

        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }

        return [
            'total' => (clone $query)->count(),
            'planned' => (clone $query)->status(WorkShipment::STATUS_PLANNED)->count(),
            'active' => (clone $query)->active()->count(),
            'completed' => (clone $query)->completed()->count(),
            'cancelled' => (clone $query)->status(WorkShipment::STATUS_CANCELLED)->count(),
            'total_planned_quantity' => (clone $query)->sum('planned_quantity'),
            'total_actual_quantity' => (clone $query)->sum('actual_quantity'),
        ];
    }
}
