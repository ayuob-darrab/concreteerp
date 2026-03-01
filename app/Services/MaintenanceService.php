<?php

namespace App\Services;

use App\Models\Cars;
use App\Models\MaintenanceRecord;
use App\Models\VehicleStatusHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaintenanceService
{
    protected $vehicleService;

    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    /**
     * إنشاء سجل صيانة جديد
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $vehicle = Cars::findOrFail($data['vehicle_id']);

            // إنشاء سجل الصيانة
            $record = MaintenanceRecord::create([
                'company_code' => $data['company_code'] ?? $vehicle->company_code,
                'branch_id' => $data['branch_id'] ?? $vehicle->branch_id,
                'vehicle_id' => $data['vehicle_id'],
                'maintenance_type' => $data['maintenance_type'],
                'description' => $data['description'],
                'odometer_before' => $vehicle->odometer_reading,
                'working_hours_before' => $vehicle->working_hours,
                'labor_cost' => $data['labor_cost'] ?? 0,
                'parts_cost' => $data['parts_cost'] ?? 0,
                'parts_used' => $data['parts_used'] ?? null,
                'started_at' => $data['started_at'] ?? now(),
                'performed_by' => $data['performed_by'] ?? null,
                'external_workshop' => $data['external_workshop'] ?? false,
                'workshop_name' => $data['workshop_name'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // تغيير حالة الآلية إلى "في الصيانة"
            $this->vehicleService->changeStatus(
                $vehicle,
                'in_maintenance',
                'بدء صيانة: ' . $data['description'],
                'maintenance',
                $record->id
            );

            return $record;
        });
    }

    /**
     * إكمال الصيانة
     */
    public function complete(MaintenanceRecord $record, array $data)
    {
        return DB::transaction(function () use ($record, $data) {
            $vehicle = $record->vehicle;

            // تحديث سجل الصيانة
            $record->update([
                'completed_at' => $data['completed_at'] ?? now(),
                'odometer_after' => $data['odometer_after'] ?? $vehicle->odometer_reading,
                'working_hours_after' => $data['working_hours_after'] ?? $vehicle->working_hours,
                'labor_cost' => $data['labor_cost'] ?? $record->labor_cost,
                'parts_cost' => $data['parts_cost'] ?? $record->parts_cost,
                'parts_used' => $data['parts_used'] ?? $record->parts_used,
                'notes' => $data['notes'] ?? $record->notes,
                'next_maintenance_notes' => $data['next_maintenance_notes'] ?? null,
            ]);

            // تحديث قراءات الآلية
            if (isset($data['odometer_after'])) {
                $vehicle->odometer_reading = $data['odometer_after'];
            }
            if (isset($data['working_hours_after'])) {
                $vehicle->working_hours = $data['working_hours_after'];
            }

            // جدولة الصيانة التالية
            $this->vehicleService->scheduleNextMaintenance(
                $vehicle,
                $data['next_maintenance_days'] ?? null
            );

            // إعادة الآلية للخدمة
            $this->vehicleService->changeStatus(
                $vehicle,
                'available',
                'اكتمال الصيانة: ' . $record->description,
                'maintenance',
                $record->id
            );

            return $record;
        });
    }

    /**
     * إلغاء الصيانة
     */
    public function cancel(MaintenanceRecord $record, string $reason = null)
    {
        return DB::transaction(function () use ($record, $reason) {
            $vehicle = $record->vehicle;

            // حذف السجل (soft delete)
            $record->delete();

            // إعادة الآلية للخدمة
            $this->vehicleService->changeStatus(
                $vehicle,
                'available',
                'إلغاء الصيانة: ' . ($reason ?? 'بدون سبب'),
                'maintenance',
                $record->id
            );

            return true;
        });
    }

    /**
     * الحصول على سجلات صيانة الآلية
     */
    public function getVehicleHistory($vehicleId, $limit = null)
    {
        $query = MaintenanceRecord::forVehicle($vehicleId)
            ->with('creator')
            ->orderBy('started_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * الحصول على الصيانات المعلقة
     */
    public function getPending($branchId = null)
    {
        $query = MaintenanceRecord::pending()
            ->with(['vehicle', 'creator']);

        if ($branchId) {
            $query->forBranch($branchId);
        }

        return $query->orderBy('started_at', 'asc')->get();
    }

    /**
     * إحصائيات الصيانة
     */
    public function getStatistics($companyCode = null, $branchId = null, $startDate = null, $endDate = null)
    {
        $query = MaintenanceRecord::query();

        if ($companyCode) {
            $query->forCompany($companyCode);
        }

        if ($branchId) {
            $query->forBranch($branchId);
        }

        if ($startDate && $endDate) {
            $query->betweenDates($startDate, $endDate);
        }

        $records = $query->get();

        return [
            'total_records' => $records->count(),
            'completed' => $records->where('completed_at', '!=', null)->count(),
            'pending' => $records->whereNull('completed_at')->count(),
            'total_cost' => $records->sum('total_cost'),
            'labor_cost' => $records->sum('labor_cost'),
            'parts_cost' => $records->sum('parts_cost'),
            'by_type' => [
                'scheduled' => $records->where('maintenance_type', 'scheduled')->count(),
                'preventive' => $records->where('maintenance_type', 'preventive')->count(),
                'corrective' => $records->where('maintenance_type', 'corrective')->count(),
                'emergency' => $records->where('maintenance_type', 'emergency')->count(),
            ],
            'avg_duration' => $records->whereNotNull('completed_at')->avg(function ($r) {
                return $r->duration;
            }),
        ];
    }

    /**
     * تقرير الصيانة
     */
    public function generateReport($vehicleId, $startDate, $endDate)
    {
        $records = MaintenanceRecord::forVehicle($vehicleId)
            ->betweenDates($startDate, $endDate)
            ->orderBy('started_at', 'asc')
            ->get();

        $vehicle = Cars::find($vehicleId);

        return [
            'vehicle' => $vehicle,
            'period' => ['from' => $startDate, 'to' => $endDate],
            'records' => $records,
            'summary' => [
                'total_records' => $records->count(),
                'total_cost' => $records->sum('total_cost'),
                'total_downtime_hours' => $records->whereNotNull('completed_at')->sum(function ($r) {
                    return $r->duration ?? 0;
                }),
                'avg_cost' => $records->count() > 0 ? $records->avg('total_cost') : 0,
            ],
        ];
    }
}
