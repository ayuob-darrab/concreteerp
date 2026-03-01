<?php

namespace App\Http\Controllers;

use App\Models\VehicleReservation;
use App\Models\Cars;
use App\Models\Employee;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleReservationController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * عرض قائمة الحجوزات
     */
    public function index(Request $request)
    {
        $query = VehicleReservation::with(['vehicle', 'driver', 'order', 'reserver']);

        // فلترة حسب الآلية
        if ($request->filled('vehicle_id')) {
            $query->forVehicle($request->vehicle_id);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // فلترة حسب التاريخ
        if ($request->filled('date')) {
            $query->whereDate('reserved_from', '<=', $request->date)
                ->whereDate('reserved_to', '>=', $request->date);
        }

        $reservations = $query->orderBy('reserved_from', 'desc')->paginate(15);
        $vehicles = Cars::where('isactive', true)->get();

        return view('vehicles.reservations.index', compact('reservations', 'vehicles'));
    }

    /**
     * عرض نموذج إنشاء حجز
     */
    public function create(Request $request)
    {
        $vehicles = Cars::where('isactive', true)
            ->where('operational_status', 'available')
            ->get();

        $drivers = Employee::where('is_active', true)
            ->whereHas('employeeType', function ($q) {
                $q->where('code', 'driver');
            })
            ->get();

        $selectedVehicle = null;
        if ($request->filled('vehicle_id')) {
            $selectedVehicle = Cars::find($request->vehicle_id);
        }

        return view('vehicles.reservations.create', compact('vehicles', 'drivers', 'selectedVehicle'));
    }

    /**
     * حفظ حجز جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:cars,id',
            'reserved_from' => 'required|date|after_or_equal:now',
            'reserved_to' => 'required|date|after:reserved_from',
            'driver_id' => 'nullable|exists:employees,id',
            'order_id' => 'nullable|exists:work_orders,id',
            'purpose' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $reservation = $this->reservationService->create($request->all());

            return redirect()->route('vehicle-reservations.index')
                ->with('success', 'تم إنشاء الحجز بنجاح');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل الحجز
     */
    public function show(VehicleReservation $vehicleReservation)
    {
        $vehicleReservation->load(['vehicle', 'driver', 'order', 'reserver']);

        return view('vehicles.reservations.show', compact('vehicleReservation'));
    }

    /**
     * تأكيد الحجز
     */
    public function confirm(VehicleReservation $vehicleReservation)
    {
        try {
            $this->reservationService->confirm($vehicleReservation);

            return back()->with('success', 'تم تأكيد الحجز بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * بدء الاستخدام
     */
    public function start(VehicleReservation $vehicleReservation)
    {
        try {
            $this->reservationService->startUsage($vehicleReservation);

            return back()->with('success', 'تم بدء استخدام الآلية');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * إكمال الحجز
     */
    public function complete(VehicleReservation $vehicleReservation)
    {
        try {
            $this->reservationService->complete($vehicleReservation);

            return back()->with('success', 'تم إكمال الحجز بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * إلغاء الحجز
     */
    public function cancel(Request $request, VehicleReservation $vehicleReservation)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $this->reservationService->cancel($vehicleReservation, $request->reason);

            return back()->with('success', 'تم إلغاء الحجز');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * عرض التقويم
     */
    public function calendar(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $vehicleId = $request->get('vehicle_id');

        $reservations = $this->reservationService->getCalendar($month, $year, $vehicleId);
        $vehicles = Cars::where('isactive', true)->get();

        return view('vehicles.reservations.calendar', compact('reservations', 'vehicles', 'month', 'year'));
    }

    /**
     * البحث عن آليات متاحة (AJAX)
     */
    public function findAvailable(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after:from',
            'type' => 'nullable|string',
        ]);

        $vehicles = $this->reservationService->getAvailableVehicles(
            $request->from,
            $request->to,
            $request->type,
            Auth::user()->branch_id
        );

        return response()->json([
            'success' => true,
            'vehicles' => $vehicles
        ]);
    }

    /**
     * الحصول على حجوزات آلية (AJAX)
     */
    public function vehicleReservations(Request $request, Cars $vehicle)
    {
        $reservations = $this->reservationService->getVehicleReservations(
            $vehicle->id,
            $request->get('upcoming', true)
        );

        return response()->json([
            'success' => true,
            'reservations' => $reservations
        ]);
    }

    /**
     * إحصائيات الحجوزات
     */
    public function statistics(Request $request)
    {
        $statistics = $this->reservationService->getStatistics(
            $request->get('branch_id', Auth::user()->branch_id),
            $request->get('start_date'),
            $request->get('end_date')
        );

        if ($request->wantsJson()) {
            return response()->json($statistics);
        }

        return view('vehicles.reservations.statistics', compact('statistics'));
    }
}
