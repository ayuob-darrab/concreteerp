<?php

namespace App\Services;

use App\Models\Cars;
use App\Models\VehicleDriver;
use App\Models\VehicleStatusHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleService
{
    /**
     * تغيير حالة الآلية
     */
    public function changeStatus(Cars $vehicle, string $newStatus, string $reason = null, $relatedType = null, $relatedId = null)
    {
        $oldStatus = $vehicle->operational_status;

        // تسجيل في السجل
        VehicleStatusHistory::logChange(
            $vehicle->id,
            $oldStatus,
            $newStatus,
            $reason,
            $relatedType,
            $relatedId
        );

        // تحديث الآلية
        $vehicle->update([
            'operational_status' => $newStatus,
            'status_reason' => $reason,
            'status_changed_at' => now(),
            'status_changed_by' => Auth::id(),
        ]);

        return $vehicle;
    }

    /**
     * تعيين سائق للآلية
     */
    public function assignDriver(Cars $vehicle, int $driverId, string $type = 'primary', $startDate = null, $endDate = null)
    {
        // إذا كان أساسي، إنهاء التعيين السابق
        if ($type === VehicleDriver::TYPE_PRIMARY) {
            VehicleDriver::forVehicle($vehicle->id)
                ->primary()
                ->active()
                ->update([
                    'is_active' => false,
                    'end_date' => now()->subDay(),
                ]);
        }

        return VehicleDriver::create([
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driverId,
            'assignment_type' => $type,
            'start_date' => $startDate ?? now(),
            'end_date' => $endDate,
            'is_active' => true,
            'assigned_by' => Auth::id(),
        ]);
    }

    /**
     * إلغاء تعيين سائق
     */
    public function unassignDriver(VehicleDriver $assignment)
    {
        $assignment->update([
            'is_active' => false,
            'end_date' => now(),
        ]);

        return $assignment;
    }

    /**
     * الحصول على الآليات المتاحة
     */
    public function getAvailable($type = null, $branchId = null)
    {
        $query = Cars::where('operational_status', 'available')
            ->where('isactive', true);

        if ($type) {
            $query->where('car_type', $type);
        }

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->with(['primaryDriver.driver'])->get();
    }

    /**
     * الحصول على الآليات التي تحتاج صيانة
     */
    public function getDueForMaintenance($daysAhead = 7)
    {
        return Cars::where('isactive', true)
            ->where('operational_status', '!=', 'scrapped')
            ->where(function ($q) use ($daysAhead) {
                $q->whereNull('next_maintenance_date')
                    ->orWhere('next_maintenance_date', '<=', now()->addDays($daysAhead));
            })
            ->orderBy('next_maintenance_date')
            ->get();
    }

    /**
     * تحديث قراءات الآلية
     */
    public function updateReadings(Cars $vehicle, array $data)
    {
        $updates = [];

        if (isset($data['odometer_reading'])) {
            $updates['odometer_reading'] = $data['odometer_reading'];
        }

        if (isset($data['working_hours'])) {
            $updates['working_hours'] = $data['working_hours'];
        }

        if (!empty($updates)) {
            $vehicle->update($updates);
        }

        return $vehicle;
    }

    /**
     * جدولة الصيانة التالية
     */
    public function scheduleNextMaintenance(Cars $vehicle, $days = null)
    {
        $days = $days ?? $vehicle->maintenance_interval_days ?? 30;

        $vehicle->update([
            'last_maintenance_date' => now(),
            'next_maintenance_date' => now()->addDays($days),
        ]);

        return $vehicle;
    }

    /**
     * إحصائيات الآليات
     */
    public function getStatistics($companyCode = null, $branchId = null)
    {
        $query = Cars::query();

        if ($companyCode) {
            $query->where('company_code', $companyCode);
        }

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $total = (clone $query)->count();

        return [
            'total' => $total,
            'available' => (clone $query)->where('operational_status', 'available')->count(),
            'reserved' => (clone $query)->where('operational_status', 'reserved')->count(),
            'in_maintenance' => (clone $query)->where('operational_status', 'in_maintenance')->count(),
            'out_of_service' => (clone $query)->where('operational_status', 'out_of_service')->count(),
            'scrapped' => (clone $query)->where('operational_status', 'scrapped')->count(),
            'due_maintenance' => Cars::where('next_maintenance_date', '<=', now()->addDays(7))->count(),
            'by_type' => Cars::select('car_type', DB::raw('count(*) as count'))
                ->groupBy('car_type')
                ->pluck('count', 'car_type')
                ->toArray(),
        ];
    }

    /**
     * البحث عن آليات
     */
    public function search(array $filters)
    {
        $query = Cars::query();

        if (!empty($filters['company_code'])) {
            $query->where('company_code', $filters['company_code']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('car_type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('operational_status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        return $query->with(['primaryDriver.driver', 'branch'])
            ->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }
}
