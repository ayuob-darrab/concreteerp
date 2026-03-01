<?php

namespace App\Services;

use App\Models\LocationLog;
use App\Models\WorkShipment;
use App\Models\ShipmentEvent;
use Illuminate\Support\Collection;

class LocationTrackingService
{
    /**
     * تسجيل موقع جديد
     */
    public function logLocation(WorkShipment $shipment, float $latitude, float $longitude, array $data = [])
    {
        // التحقق من أن الشحنة نشطة
        if (!$shipment->is_active) {
            return null;
        }

        $log = LocationLog::create([
            'shipment_id' => $shipment->id,
            'vehicle_id' => $data['vehicle_id'] ?? $shipment->mixer_id ?? $shipment->truck_id,
            'driver_id' => $data['driver_id'] ?? $shipment->mixer_driver_id ?? $shipment->truck_driver_id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'speed' => $data['speed'] ?? null,
            'heading' => $data['heading'] ?? null,
            'accuracy' => $data['accuracy'] ?? null,
        ]);

        return $log;
    }

    /**
     * الحصول على مسار الشحنة
     */
    public function getTrack(WorkShipment $shipment)
    {
        return $shipment->locationLogs()
            ->orderBy('recorded_at')
            ->get()
            ->map(function ($log) {
                return [
                    'lat' => (float) $log->latitude,
                    'lng' => (float) $log->longitude,
                    'time' => $log->recorded_at->format('H:i:s'),
                    'speed' => $log->speed,
                ];
            });
    }

    /**
     * الحصول على آخر موقع للشحنة
     */
    public function getCurrentLocation(WorkShipment $shipment)
    {
        $lastLog = $shipment->getLastLocation();

        if (!$lastLog) {
            return null;
        }

        return [
            'lat' => (float) $lastLog->latitude,
            'lng' => (float) $lastLog->longitude,
            'recorded_at' => $lastLog->recorded_at,
            'speed' => $lastLog->speed,
            'heading' => $lastLog->heading,
            'heading_direction' => $lastLog->heading_direction,
            'minutes_ago' => $lastLog->recorded_at->diffInMinutes(now()),
        ];
    }

    /**
     * الحصول على المواقع الحالية لجميع الشحنات النشطة
     */
    public function getActiveShipmentsLocations($branchId = null)
    {
        $query = WorkShipment::with(['job', 'mixer', 'mixerDriver'])
            ->active();

        if ($branchId) {
            $query->whereHas('job', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        return $query->get()->map(function ($shipment) {
            $location = $this->getCurrentLocation($shipment);

            return [
                'shipment_id' => $shipment->id,
                'job_number' => $shipment->job->job_number,
                'shipment_number' => $shipment->shipment_number,
                'status' => $shipment->status,
                'status_label' => $shipment->status_label,
                'vehicle' => $shipment->mixer->plate_number ?? 'N/A',
                'driver' => $shipment->mixerDriver->name ?? 'N/A',
                'location' => $location,
                'destination' => [
                    'address' => $shipment->job->location_address,
                    'lat' => (float) $shipment->job->latitude,
                    'lng' => (float) $shipment->job->longitude,
                ],
            ];
        });
    }

    /**
     * الحصول على سجل المواقع لفترة زمنية
     */
    public function getLocationHistory(WorkShipment $shipment, $from = null, $to = null)
    {
        $query = $shipment->locationLogs()->orderBy('recorded_at');

        if ($from) {
            $query->where('recorded_at', '>=', $from);
        }

        if ($to) {
            $query->where('recorded_at', '<=', $to);
        }

        return $query->get();
    }

    /**
     * حساب المسافة الإجمالية المقطوعة
     */
    public function calculateTotalDistance(WorkShipment $shipment)
    {
        $logs = $shipment->locationLogs()->orderBy('recorded_at')->get();

        if ($logs->count() < 2) {
            return 0;
        }

        $totalDistance = 0;
        $previousLog = null;

        foreach ($logs as $log) {
            if ($previousLog) {
                $totalDistance += $log->distanceTo($previousLog->latitude, $previousLog->longitude);
            }
            $previousLog = $log;
        }

        return round($totalDistance, 2); // كم
    }

    /**
     * حساب متوسط السرعة
     */
    public function calculateAverageSpeed(WorkShipment $shipment)
    {
        $avgSpeed = $shipment->locationLogs()
            ->whereNotNull('speed')
            ->where('speed', '>', 0)
            ->avg('speed');

        return round($avgSpeed ?? 0, 1);
    }

    /**
     * التحقق من اقتراب الشحنة من الوجهة
     */
    public function isNearDestination(WorkShipment $shipment, float $thresholdKm = 0.5)
    {
        $location = $this->getCurrentLocation($shipment);

        if (!$location || !$shipment->job->latitude || !$shipment->job->longitude) {
            return false;
        }

        $lastLog = $shipment->getLastLocation();
        $distance = $lastLog->distanceTo($shipment->job->latitude, $shipment->job->longitude);

        return $distance <= $thresholdKm;
    }

    /**
     * الحصول على تقرير الرحلة
     */
    public function getTripReport(WorkShipment $shipment)
    {
        return [
            'shipment_id' => $shipment->id,
            'job_number' => $shipment->job->job_number,
            'shipment_number' => $shipment->shipment_number,
            'departure_time' => $shipment->departure_time?->format('Y-m-d H:i'),
            'arrival_time' => $shipment->arrival_time?->format('Y-m-d H:i'),
            'return_time' => $shipment->return_time?->format('Y-m-d H:i'),
            'total_distance' => $this->calculateTotalDistance($shipment) . ' كم',
            'average_speed' => $this->calculateAverageSpeed($shipment) . ' كم/س',
            'trip_duration' => $shipment->total_trip_duration ? $shipment->total_trip_duration . ' دقيقة' : null,
            'work_duration' => $shipment->work_duration ? $shipment->work_duration . ' دقيقة' : null,
            'location_points' => $shipment->locationLogs()->count(),
            'track' => $this->getTrack($shipment),
        ];
    }

    /**
     * تنظيف السجلات القديمة
     */
    public function cleanOldLogs(int $daysToKeep = 90)
    {
        $cutoffDate = now()->subDays($daysToKeep);

        return LocationLog::where('recorded_at', '<', $cutoffDate)->delete();
    }
}
