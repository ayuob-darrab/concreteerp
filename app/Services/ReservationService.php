<?php

namespace App\Services;

use App\Models\Cars;
use App\Models\VehicleReservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    protected $vehicleService;

    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    /**
     * إنشاء حجز جديد
     */
    public function create(array $data)
    {
        // التحقق من عدم وجود تعارض
        if (VehicleReservation::hasConflict(
            $data['vehicle_id'],
            $data['reserved_from'],
            $data['reserved_to']
        )) {
            throw new \Exception('توجد حجوزات متعارضة لهذه الآلية في الفترة المحددة');
        }

        return DB::transaction(function () use ($data) {
            $reservation = VehicleReservation::create([
                'vehicle_id' => $data['vehicle_id'],
                'order_id' => $data['order_id'] ?? null,
                'job_id' => $data['job_id'] ?? null,
                'reserved_from' => $data['reserved_from'],
                'reserved_to' => $data['reserved_to'],
                'driver_id' => $data['driver_id'] ?? null,
                'status' => VehicleReservation::STATUS_PENDING,
                'purpose' => $data['purpose'] ?? null,
                'notes' => $data['notes'] ?? null,
                'reserved_by' => Auth::id(),
            ]);

            return $reservation;
        });
    }

    /**
     * تأكيد الحجز
     */
    public function confirm(VehicleReservation $reservation)
    {
        if (!$reservation->canBeConfirmed()) {
            throw new \Exception('لا يمكن تأكيد هذا الحجز');
        }

        return DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => VehicleReservation::STATUS_CONFIRMED]);

            // إذا كان الحجز يبدأ الآن، تغيير حالة الآلية
            if ($reservation->reserved_from->isPast() || $reservation->reserved_from->isToday()) {
                $this->vehicleService->changeStatus(
                    $reservation->vehicle,
                    'reserved',
                    'حجز مؤكد: ' . ($reservation->purpose ?? 'طلب #' . $reservation->order_id),
                    'reservation',
                    $reservation->id
                );
            }

            return $reservation;
        });
    }

    /**
     * بدء استخدام الحجز
     */
    public function startUsage(VehicleReservation $reservation)
    {
        return DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => VehicleReservation::STATUS_IN_USE]);

            $this->vehicleService->changeStatus(
                $reservation->vehicle,
                'reserved',
                'قيد الاستخدام: ' . ($reservation->purpose ?? 'طلب #' . $reservation->order_id),
                'reservation',
                $reservation->id
            );

            return $reservation;
        });
    }

    /**
     * إكمال الحجز
     */
    public function complete(VehicleReservation $reservation)
    {
        return DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => VehicleReservation::STATUS_COMPLETED]);

            // إعادة الآلية للخدمة إذا لم يكن هناك حجوزات أخرى نشطة
            $activeReservations = VehicleReservation::forVehicle($reservation->vehicle_id)
                ->where('id', '!=', $reservation->id)
                ->current()
                ->exists();

            if (!$activeReservations) {
                $this->vehicleService->changeStatus(
                    $reservation->vehicle,
                    'available',
                    'اكتمال الحجز',
                    'reservation',
                    $reservation->id
                );
            }

            return $reservation;
        });
    }

    /**
     * إلغاء الحجز
     */
    public function cancel(VehicleReservation $reservation, string $reason = null)
    {
        if (!$reservation->canBeCancelled()) {
            throw new \Exception('لا يمكن إلغاء هذا الحجز');
        }

        return DB::transaction(function () use ($reservation, $reason) {
            $reservation->update([
                'status' => VehicleReservation::STATUS_CANCELLED,
                'notes' => $reservation->notes . "\nسبب الإلغاء: " . ($reason ?? 'غير محدد'),
            ]);

            // إعادة الآلية للخدمة إذا كانت محجوزة لهذا الحجز
            if ($reservation->vehicle->operational_status === 'reserved') {
                $activeReservations = VehicleReservation::forVehicle($reservation->vehicle_id)
                    ->where('id', '!=', $reservation->id)
                    ->current()
                    ->exists();

                if (!$activeReservations) {
                    $this->vehicleService->changeStatus(
                        $reservation->vehicle,
                        'available',
                        'إلغاء الحجز: ' . ($reason ?? 'غير محدد'),
                        'reservation',
                        $reservation->id
                    );
                }
            }

            return $reservation;
        });
    }

    /**
     * الحصول على حجوزات الآلية
     */
    public function getVehicleReservations($vehicleId, $upcoming = true)
    {
        $query = VehicleReservation::forVehicle($vehicleId)
            ->with(['driver', 'order', 'reserver']);

        if ($upcoming) {
            $query->where('reserved_to', '>=', now());
        }

        return $query->orderBy('reserved_from', 'asc')->get();
    }

    /**
     * الحصول على الحجوزات لفترة معينة
     */
    public function getReservationsForPeriod($from, $to, $vehicleId = null)
    {
        $query = VehicleReservation::betweenDates($from, $to)
            ->active()
            ->with(['vehicle', 'driver', 'order']);

        if ($vehicleId) {
            $query->forVehicle($vehicleId);
        }

        return $query->orderBy('reserved_from', 'asc')->get();
    }

    /**
     * الحصول على التقويم
     */
    public function getCalendar($month, $year, $vehicleId = null)
    {
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return $this->getReservationsForPeriod($startDate, $endDate, $vehicleId);
    }

    /**
     * البحث عن آلية متاحة
     */
    public function findAvailableVehicle($type, $from, $to, $branchId = null)
    {
        $vehicles = $this->vehicleService->getAvailable($type, $branchId);

        foreach ($vehicles as $vehicle) {
            if (!VehicleReservation::hasConflict($vehicle->id, $from, $to)) {
                return $vehicle;
            }
        }

        return null;
    }

    /**
     * الحصول على الآليات المتاحة لفترة معينة
     */
    public function getAvailableVehicles($from, $to, $type = null, $branchId = null)
    {
        $vehicles = $this->vehicleService->getAvailable($type, $branchId);

        return $vehicles->filter(function ($vehicle) use ($from, $to) {
            return !VehicleReservation::hasConflict($vehicle->id, $from, $to);
        });
    }

    /**
     * إحصائيات الحجوزات
     */
    public function getStatistics($branchId = null, $startDate = null, $endDate = null)
    {
        $query = VehicleReservation::query();

        if ($branchId) {
            $query->whereHas('vehicle', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        if ($startDate && $endDate) {
            $query->betweenDates($startDate, $endDate);
        }

        $reservations = $query->get();

        return [
            'total' => $reservations->count(),
            'pending' => $reservations->where('status', 'pending')->count(),
            'confirmed' => $reservations->where('status', 'confirmed')->count(),
            'in_use' => $reservations->where('status', 'in_use')->count(),
            'completed' => $reservations->where('status', 'completed')->count(),
            'cancelled' => $reservations->where('status', 'cancelled')->count(),
            'total_hours' => $reservations->sum('duration_hours'),
            'avg_duration' => $reservations->count() > 0 ? $reservations->avg('duration_hours') : 0,
        ];
    }
}
