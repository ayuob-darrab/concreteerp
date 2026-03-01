<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
use App\Models\Cars;
use App\Services\MaintenanceService;
use App\Services\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    protected $maintenanceService;
    protected $vehicleService;

    public function __construct(MaintenanceService $maintenanceService, VehicleService $vehicleService)
    {
        $this->maintenanceService = $maintenanceService;
        $this->vehicleService = $vehicleService;
    }

    /**
     * عرض قائمة سجلات الصيانة
     */
    public function index(Request $request)
    {
        $query = MaintenanceRecord::with(['vehicle', 'creator', 'branch']);

        // فلترة حسب الآلية
        if ($request->filled('vehicle_id')) {
            $query->forVehicle($request->vehicle_id);
        }

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->type($request->type);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->pending();
            } elseif ($request->status === 'completed') {
                $query->completed();
            }
        }

        // فلترة حسب التاريخ
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        $records = $query->orderBy('started_at', 'desc')->paginate(15);
        $vehicles = Cars::where('isactive', true)->get();

        return view('vehicles.maintenance.index', compact('records', 'vehicles'));
    }

    /**
     * عرض نموذج إضافة صيانة
     */
    public function create(Request $request)
    {
        $vehicle = null;
        if ($request->filled('vehicle_id')) {
            $vehicle = Cars::findOrFail($request->vehicle_id);
        }

        $vehicles = Cars::where('isactive', true)
            ->where('operational_status', '!=', 'scrapped')
            ->get();

        return view('vehicles.maintenance.create', compact('vehicles', 'vehicle'));
    }

    /**
     * حفظ سجل صيانة جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:cars,id',
            'maintenance_type' => 'required|in:scheduled,preventive,corrective,emergency',
            'description' => 'required|string|max:1000',
            'started_at' => 'required|date',
            'labor_cost' => 'nullable|numeric|min:0',
            'parts_cost' => 'nullable|numeric|min:0',
            'performed_by' => 'nullable|string|max:100',
            'external_workshop' => 'nullable|boolean',
            'workshop_name' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $record = $this->maintenanceService->create($request->all());

            return redirect()->route('maintenance.show', $record)
                ->with('success', 'تم إنشاء سجل الصيانة بنجاح');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل سجل الصيانة
     */
    public function show(MaintenanceRecord $maintenance)
    {
        $maintenance->load(['vehicle', 'creator', 'branch']);

        return view('vehicles.maintenance.show', compact('maintenance'));
    }

    /**
     * إكمال الصيانة
     */
    public function complete(Request $request, MaintenanceRecord $maintenance)
    {
        $request->validate([
            'completed_at' => 'required|date',
            'odometer_after' => 'nullable|integer|min:0',
            'working_hours_after' => 'nullable|integer|min:0',
            'labor_cost' => 'nullable|numeric|min:0',
            'parts_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'next_maintenance_days' => 'nullable|integer|min:1',
        ]);

        try {
            $this->maintenanceService->complete($maintenance, $request->all());

            return redirect()->route('maintenance.show', $maintenance)
                ->with('success', 'تم إكمال الصيانة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * إلغاء الصيانة
     */
    public function cancel(Request $request, MaintenanceRecord $maintenance)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $this->maintenanceService->cancel($maintenance, $request->reason);

            return redirect()->route('maintenance.index')
                ->with('success', 'تم إلغاء الصيانة');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * جدول الصيانة
     */
    public function schedule(Request $request)
    {
        $dueVehicles = $this->vehicleService->getDueForMaintenance(
            $request->get('days_ahead', 7)
        );

        $pendingMaintenance = $this->maintenanceService->getPending(
            Auth::user()->branch_id
        );

        return view('vehicles.maintenance.schedule', compact('dueVehicles', 'pendingMaintenance'));
    }

    /**
     * تقرير صيانة آلية
     */
    public function report(Request $request, Cars $vehicle)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->maintenanceService->generateReport(
            $vehicle->id,
            $request->start_date,
            $request->end_date
        );

        return view('vehicles.maintenance.report', compact('report'));
    }

    /**
     * إحصائيات الصيانة
     */
    public function statistics(Request $request)
    {
        $statistics = $this->maintenanceService->getStatistics(
            Auth::user()->company_code,
            $request->get('branch_id', Auth::user()->branch_id),
            $request->get('start_date'),
            $request->get('end_date')
        );

        if ($request->wantsJson()) {
            return response()->json($statistics);
        }

        return view('vehicles.maintenance.statistics', compact('statistics'));
    }

    /**
     * طباعة تقرير الصيانة
     */
    public function print(MaintenanceRecord $maintenance)
    {
        $maintenance->load(['vehicle', 'creator', 'branch']);

        return view('vehicles.prints.maintenance-report', compact('maintenance'));
    }
}
