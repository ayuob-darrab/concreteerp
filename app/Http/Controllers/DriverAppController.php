<?php

namespace App\Http\Controllers;

use App\Models\WorkShipment;
use App\Models\Employee;
use App\Services\ShipmentService;
use App\Services\LocationTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverAppController extends Controller
{
    protected $shipmentService;
    protected $locationService;

    public function __construct(ShipmentService $shipmentService, LocationTrackingService $locationService)
    {
        $this->shipmentService = $shipmentService;
        $this->locationService = $locationService;
    }

    /**
     * لوحة تحكم السائق
     */
    public function dashboard()
    {
        $driverId = $this->getDriverId();

        if (!$driverId) {
            return view('driver.no-driver-profile');
        }

        // الشحنة الحالية
        $currentShipment = $this->shipmentService->getCurrentShipmentForDriver($driverId);

        // شحنات اليوم
        $todayShipments = WorkShipment::forDriver($driverId)
            ->today()
            ->with(['job'])
            ->orderBy('created_at')
            ->get();

        // إحصائيات اليوم
        $todayStats = [
            'total' => $todayShipments->count(),
            'completed' => $todayShipments->where('status', WorkShipment::STATUS_RETURNED)->count(),
            'total_quantity' => $todayShipments->sum('actual_quantity'),
        ];

        return view('driver.dashboard', compact('currentShipment', 'todayShipments', 'todayStats'));
    }

    /**
     * الشحنة الحالية
     */
    public function currentShipment()
    {
        $driverId = $this->getDriverId();
        $shipment = $this->shipmentService->getCurrentShipmentForDriver($driverId);

        if (!$shipment) {
            return redirect()->route('driver.dashboard')->with('info', 'لا توجد شحنة حالية');
        }

        $shipment->load(['job.branch', 'job.concreteType', 'events']);

        return view('driver.current-shipment', compact('shipment'));
    }

    /**
     * تحديث حالة الشحنة
     */
    public function updateStatus(Request $request, WorkShipment $shipment)
    {
        $request->validate([
            'action' => 'required|in:depart,arrive,start_work,end_work,return',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'actual_quantity' => 'required_if:action,end_work|nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        try {
            switch ($request->action) {
                case 'depart':
                    $this->shipmentService->recordDeparture($shipment, $request->latitude, $request->longitude);
                    $message = 'تم تسجيل الانطلاق';
                    break;

                case 'arrive':
                    $this->shipmentService->recordArrival($shipment, $request->latitude, $request->longitude);
                    $message = 'تم تسجيل الوصول';
                    break;

                case 'start_work':
                    $this->shipmentService->recordWorkStart($shipment, $request->latitude, $request->longitude);
                    $message = 'تم بدء العمل';
                    break;

                case 'end_work':
                    $this->shipmentService->recordWorkEnd(
                        $shipment,
                        $request->actual_quantity,
                        $request->latitude,
                        $request->longitude,
                        $request->notes
                    );
                    $message = 'تم إنهاء العمل';
                    break;

                case 'return':
                    $this->shipmentService->recordReturn($shipment, $request->latitude, $request->longitude);
                    $message = 'تم تسجيل العودة';
                    break;

                default:
                    throw new \Exception('إجراء غير صالح');
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * تقرير مشكلة
     */
    public function reportIssue(Request $request, WorkShipment $shipment)
    {
        $request->validate([
            'description' => 'required|string|min:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
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
     * تحديث الموقع
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'shipment_id' => 'required|exists:work_shipments,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric',
        ]);

        $shipment = WorkShipment::findOrFail($request->shipment_id);

        $log = $this->locationService->logLocation($shipment, $request->latitude, $request->longitude, [
            'driver_id' => $this->getDriverId(),
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
     * سجل الشحنات
     */
    public function history(Request $request)
    {
        $driverId = $this->getDriverId();

        // الأساس: جميع شحنات السائق
        $baseQuery = WorkShipment::forDriver($driverId)
            ->with(['job']);

        // تطبيق فلاتر التاريخ إن وُجدت
        if ($request->filled('date_from')) {
            $baseQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $baseQuery->whereDate('created_at', '<=', $request->date_to);
        }

        // نسخة للإحصائيات (بدون paginate)
        $allShipments = (clone $baseQuery)->get();

        $totalShipments = $allShipments->count();

        // استخدام الكمية الفعلية، وإن لم توجد نستخدم المخططة
        $totalQuantity = $allShipments->sum(function ($s) {
            return $s->actual_quantity ?? $s->planned_quantity;
        });

        // حساب عدد الأيام المختلفة في السجل (لتقدير متوسط/يوم)
        $distinctDays = max(
            1,
            $allShipments
                ->groupBy(fn($s) => optional($s->created_at)->format('Y-m-d'))
                ->keys()
                ->filter()
                ->count()
        );

        $stats = [
            'total' => $totalShipments,
            'total_quantity' => $totalQuantity,
            'avg_per_day' => $distinctDays > 0 ? round($totalQuantity / $distinctDays, 1) : 0,
        ];

        // الشحنات مع ترقيم الصفحات للعرض
        $shipments = $baseQuery
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('driver.history', compact('shipments', 'stats'));
    }

    /**
     * تفاصيل شحنة
     */
    public function shipmentDetails(WorkShipment $shipment)
    {
        $shipment->load(['job.branch', 'job.concreteType', 'events']);

        return view('driver.shipment-details', compact('shipment'));
    }

    /**
     * الحصول على معرف السائق
     */
    protected function getDriverId()
    {
        $user = Auth::user();

        // البحث عن موظف مرتبط بالمستخدم
        $employee = Employee::where('user_id', $user->id)->first();

        if ($employee && $employee->is_driver) {
            return $employee->id;
        }

        return null;
    }

    /**
     * API: الشحنات المتاحة للسائق
     */
    public function apiGetShipments()
    {
        $driverId = $this->getDriverId();

        $shipments = WorkShipment::forDriver($driverId)
            ->whereIn('status', [
                WorkShipment::STATUS_PLANNED,
                WorkShipment::STATUS_PREPARING,
                WorkShipment::STATUS_DEPARTED,
                WorkShipment::STATUS_ARRIVED,
                WorkShipment::STATUS_WORKING,
                WorkShipment::STATUS_COMPLETED,
            ])
            ->with(['job'])
            ->get();

        return response()->json([
            'success' => true,
            'shipments' => $shipments->map(function ($s) {
                return [
                    'id' => $s->id,
                    'job_number' => $s->job->job_number,
                    'shipment_number' => $s->shipment_number,
                    'status' => $s->status,
                    'status_label' => $s->status_label,
                    'planned_quantity' => $s->planned_quantity,
                    'destination' => $s->job->location_address,
                    'customer' => $s->job->customer_name,
                    'can_depart' => $s->can_depart,
                    'can_arrive' => $s->can_arrive,
                    'can_start_work' => $s->can_start_work,
                    'can_end_work' => $s->can_end_work,
                    'can_return' => $s->can_return,
                ];
            }),
        ]);
    }

    /**
     * API: تحديث الحالة
     */
    public function apiUpdateStatus(Request $request)
    {
        $request->validate([
            'shipment_id' => 'required|exists:work_shipments,id',
            'action' => 'required|in:depart,arrive,start_work,end_work,return',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'actual_quantity' => 'required_if:action,end_work|nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        try {
            $shipment = WorkShipment::findOrFail($request->shipment_id);

            switch ($request->action) {
                case 'depart':
                    $this->shipmentService->recordDeparture($shipment, $request->latitude, $request->longitude);
                    break;
                case 'arrive':
                    $this->shipmentService->recordArrival($shipment, $request->latitude, $request->longitude);
                    break;
                case 'start_work':
                    $this->shipmentService->recordWorkStart($shipment, $request->latitude, $request->longitude);
                    break;
                case 'end_work':
                    $this->shipmentService->recordWorkEnd($shipment, $request->actual_quantity, $request->latitude, $request->longitude, $request->notes);
                    break;
                case 'return':
                    $this->shipmentService->recordReturn($shipment, $request->latitude, $request->longitude);
                    break;
            }

            return response()->json([
                'success' => true,
                'status' => $shipment->fresh()->status,
                'status_label' => $shipment->fresh()->status_label,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
