<?php

namespace App\Http\Controllers;

use App\Models\WorkShipment;
use App\Models\WorkJob;
use App\Models\Cars;
use App\Models\Employee;
use App\Services\ShipmentService;
use App\Services\LocationTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    protected $shipmentService;
    protected $locationService;

    public function __construct(ShipmentService $shipmentService, LocationTrackingService $locationService)
    {
        $this->shipmentService = $shipmentService;
        $this->locationService = $locationService;
    }

    /**
     * إنشاء شحنة جديدة
     */
    public function create(WorkJob $workJob)
    {
        // الآليات المتاحة
        $vehicles = Cars::where('company_code', Auth::user()->company_code)
            ->where('branch_id', $workJob->branch_id)
            ->where('operational_status', 'available')
            ->get()
            ->groupBy('car_type');

        // السائقين المتاحين
        $drivers = Employee::where('company_code', Auth::user()->company_code)
            ->where('branch_id', $workJob->branch_id)
            ->where('is_driver', true)
            ->where('status', 'active')
            ->get();

        return view('work-jobs.shipments.create', compact('workJob', 'vehicles', 'drivers'));
    }

    /**
     * حفظ شحنة جديدة
     */
    public function store(Request $request, WorkJob $workJob)
    {
        $request->validate([
            'planned_quantity' => 'required|numeric|min:0.1',
            'mixer_id' => 'nullable|exists:cars,id',
            'truck_id' => 'nullable|exists:cars,id',
            'pump_id' => 'nullable|exists:cars,id',
            'mixer_driver_id' => 'nullable|exists:employees,id',
            'truck_driver_id' => 'nullable|exists:employees,id',
            'pump_driver_id' => 'nullable|exists:employees,id',
            'notes' => 'nullable|string',
        ]);

        try {
            $shipment = $this->shipmentService->create($workJob, $request->all());

            return redirect()
                ->route('work-jobs.show', $workJob)
                ->with('success', 'تم إنشاء الشحنة رقم ' . $shipment->shipment_number);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل الشحنة
     */
    public function show(WorkShipment $shipment)
    {
        $shipment->load([
            'job.branch',
            'job.concreteType',
            'mixer',
            'truck',
            'pump',
            'mixerDriver',
            'truckDriver',
            'pumpDriver',
            'events',
            'locationLogs',
        ]);

        // معلومات الموقع الحالي
        $currentLocation = $this->locationService->getCurrentLocation($shipment);

        // تقرير الرحلة
        $tripReport = $this->locationService->getTripReport($shipment);

        return view('work-jobs.shipments.show', compact('shipment', 'currentLocation', 'tripReport'));
    }

    /**
     * تعيين الآليات
     */
    public function assignVehicles(Request $request, WorkShipment $shipment)
    {
        $request->validate([
            'mixer_id' => 'nullable|exists:cars,id',
            'truck_id' => 'nullable|exists:cars,id',
            'pump_id' => 'nullable|exists:cars,id',
        ]);

        $this->shipmentService->assignVehicles($shipment, $request->only([
            'mixer_id',
            'truck_id',
            'pump_id'
        ]));

        return back()->with('success', 'تم تعيين الآليات');
    }

    /**
     * تعيين السائقين
     */
    public function assignDrivers(Request $request, WorkShipment $shipment)
    {
        $request->validate([
            'mixer_driver_id' => 'nullable|exists:employees,id',
            'truck_driver_id' => 'nullable|exists:employees,id',
            'pump_driver_id' => 'nullable|exists:employees,id',
        ]);

        $this->shipmentService->assignDrivers($shipment, $request->only([
            'mixer_driver_id',
            'truck_driver_id',
            'pump_driver_id'
        ]));

        return back()->with('success', 'تم تعيين السائقين');
    }

    /**
     * تسجيل الانطلاق
     */
    public function depart(Request $request, WorkShipment $shipment)
    {
        try {
            $this->shipmentService->recordDeparture(
                $shipment,
                $request->latitude,
                $request->longitude
            );

            return back()->with('success', 'تم تسجيل الانطلاق');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * تسجيل الوصول
     */
    public function arrive(Request $request, WorkShipment $shipment)
    {
        try {
            $this->shipmentService->recordArrival(
                $shipment,
                $request->latitude,
                $request->longitude
            );

            return back()->with('success', 'تم تسجيل الوصول');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * تسجيل بدء العمل
     */
    public function startWork(Request $request, WorkShipment $shipment)
    {
        try {
            $this->shipmentService->recordWorkStart(
                $shipment,
                $request->latitude,
                $request->longitude
            );

            return back()->with('success', 'تم تسجيل بدء العمل');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * تسجيل انتهاء العمل
     */
    public function endWork(Request $request, WorkShipment $shipment)
    {
        $request->validate([
            'actual_quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $this->shipmentService->recordWorkEnd(
                $shipment,
                $request->actual_quantity,
                $request->latitude,
                $request->longitude,
                $request->notes
            );

            return back()->with('success', 'تم تسجيل انتهاء العمل');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * تسجيل العودة
     */
    public function returnToBase(Request $request, WorkShipment $shipment)
    {
        try {
            $this->shipmentService->recordReturn(
                $shipment,
                $request->latitude,
                $request->longitude
            );

            return back()->with('success', 'تم تسجيل العودة');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * إلغاء الشحنة
     */
    public function cancel(Request $request, WorkShipment $shipment)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        try {
            $this->shipmentService->cancel($shipment, $request->reason);

            return back()->with('success', 'تم إلغاء الشحنة');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * تسجيل مشكلة
     */
    public function reportIssue(Request $request, WorkShipment $shipment)
    {
        $request->validate([
            'description' => 'required|string|min:10',
        ]);

        $this->shipmentService->reportIssue(
            $shipment,
            $request->description,
            $request->latitude,
            $request->longitude
        );

        return back()->with('success', 'تم تسجيل المشكلة');
    }

    /**
     * تتبع مباشر
     */
    public function tracking(WorkShipment $shipment)
    {
        $shipment->load(['job', 'mixer', 'mixerDriver']);

        $currentLocation = $this->locationService->getCurrentLocation($shipment);
        $track = $this->locationService->getTrack($shipment);

        return view('work-jobs.shipments.tracking', compact('shipment', 'currentLocation', 'track'));
    }

    /**
     * الخريطة المباشرة (API)
     */
    public function liveMap(Request $request)
    {
        $branchId = $request->branch_id;
        $locations = $this->locationService->getActiveShipmentsLocations($branchId);

        return response()->json($locations);
    }

    /**
     * استقبال تحديث الموقع (API)
     */
    public function updateLocation(Request $request, WorkShipment $shipment)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric',
        ]);

        $log = $this->locationService->logLocation($shipment, $request->latitude, $request->longitude, [
            'speed' => $request->speed,
            'heading' => $request->heading,
            'accuracy' => $request->accuracy,
        ]);

        return response()->json([
            'success' => true,
            'log_id' => $log?->id,
        ]);
    }

    /**
     * سجل المواقع
     */
    public function locationHistory(WorkShipment $shipment)
    {
        $shipment->load(['job', 'mixer', 'mixerDriver']);

        $history = $this->locationService->getLocationHistory($shipment);
        $tripReport = $this->locationService->getTripReport($shipment);

        return view('work-jobs.tracking.history', compact('shipment', 'history', 'tripReport'));
    }
}
