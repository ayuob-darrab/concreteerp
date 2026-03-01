<?php

namespace App\Http\Controllers;

use App\Models\WorkJob;
use App\Models\WorkOrder;
use App\Models\Employee;
use App\Models\Cars;
use App\Models\ConcreteMix;
use App\Services\WorkJobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkJobController extends Controller
{
    protected $workJobService;

    public function __construct(WorkJobService $workJobService)
    {
        $this->workJobService = $workJobService;
    }

    /**
     * قائمة أوامر العمل
     */
    public function index(Request $request)
    {
        $query = WorkJob::with(['branch', 'concreteType', 'supervisor', 'order'])
            ->company(Auth::user()->company_code);

        // تصفية حسب الفرع
        if ($request->filled('branch_id')) {
            $query->branch($request->branch_id);
        }

        // تصفية حسب الحالة
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // تصفية حسب التاريخ
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->scheduledBetween($request->date_from, $request->date_to);
        } elseif ($request->filled('date')) {
            $query->scheduledOn($request->date);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('job_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('location_address', 'like', "%{$search}%");
            });
        }

        $jobs = $query->orderBy('scheduled_date', 'desc')
            ->orderBy('scheduled_time', 'desc')
            ->paginate(20);

        $statistics = $this->workJobService->getStatistics(
            Auth::user()->company_code,
            $request->branch_id,
            $request->date_from,
            $request->date_to
        );

        return view('work-jobs.index', compact('jobs', 'statistics'));
    }

    /**
     * أوامر اليوم
     */
    public function daily(Request $request)
    {
        $branchId = $request->branch_id;
        $jobs = $this->workJobService->getTodayJobs($branchId);

        return view('work-jobs.daily', compact('jobs'));
    }

    /**
     * إنشاء أمر عمل جديد
     */
    public function create(Request $request)
    {
        $orderId = $request->order_id;
        $order = null;

        if ($orderId) {
            $order = WorkOrder::findOrFail($orderId);
        }

        $supervisors = Employee::where('company_code', Auth::user()->company_code)
            ->where('position', 'like', '%مشرف%')
            ->orWhere('position', 'like', '%supervisor%')
            ->get();

        $concreteTypes = ConcreteMix::where('company_code', Auth::user()->company_code)
            ->where('is_active', true)
            ->get();

        return view('work-jobs.create', compact('order', 'supervisors', 'concreteTypes'));
    }

    /**
     * حفظ أمر العمل
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:work_orders,id',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'supervisor_id' => 'nullable|exists:employees,id',
            'notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        try {
            $order = WorkOrder::findOrFail($request->order_id);

            $job = $this->workJobService->createFromOrder($order, $request->only([
                'scheduled_date',
                'scheduled_time',
                'supervisor_id',
                'notes',
                'internal_notes',
                'latitude',
                'longitude',
            ]));

            return redirect()
                ->route('work-jobs.show', $job)
                ->with('success', 'تم إنشاء أمر العمل بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل أمر العمل
     */
    public function show(WorkJob $workJob)
    {
        $workJob->load([
            'branch',
            'concreteType',
            'supervisor',
            'order',
            'shipments.mixer',
            'shipments.truck',
            'shipments.pump',
            'shipments.mixerDriver',
            'shipments.events',
            'materialReservations.material',
            'losses',
        ]);

        // الآليات المتاحة للشحنات الجديدة
        $availableVehicles = Cars::where('company_code', Auth::user()->company_code)
            ->where('operational_status', 'available')
            ->get()
            ->groupBy('car_type');

        // السائقين المتاحين
        $availableDrivers = Employee::where('company_code', Auth::user()->company_code)
            ->where('is_driver', true)
            ->where('status', 'active')
            ->get();

        return view('work-jobs.show', compact('workJob', 'availableVehicles', 'availableDrivers'));
    }

    /**
     * حجز المواد
     */
    public function reserveMaterials(WorkJob $workJob)
    {
        try {
            $this->workJobService->reserveMaterials($workJob);

            return back()->with('success', 'تم حجز المواد بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * إكمال أمر العمل
     */
    public function complete(Request $request, WorkJob $workJob)
    {
        try {
            $this->workJobService->complete($workJob, $request->only('notes'));

            return back()->with('success', 'تم إكمال أمر العمل بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * إلغاء أمر العمل
     */
    public function cancel(Request $request, WorkJob $workJob)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        try {
            $this->workJobService->cancel($workJob, $request->reason);

            return back()->with('success', 'تم إلغاء أمر العمل');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * تعليق أمر العمل
     */
    public function hold(Request $request, WorkJob $workJob)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        try {
            $this->workJobService->hold($workJob, $request->reason);

            return back()->with('success', 'تم تعليق أمر العمل');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * استئناف أمر العمل
     */
    public function resume(WorkJob $workJob)
    {
        try {
            $this->workJobService->resume($workJob);

            return back()->with('success', 'تم استئناف أمر العمل');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * تعيين مشرف
     */
    public function assignSupervisor(Request $request, WorkJob $workJob)
    {
        $request->validate([
            'supervisor_id' => 'required|exists:employees,id',
        ]);

        $this->workJobService->assignSupervisor($workJob, $request->supervisor_id);

        return back()->with('success', 'تم تعيين المشرف');
    }

    /**
     * طباعة أمر العمل
     */
    public function print(WorkJob $workJob)
    {
        $workJob->load([
            'branch',
            'concreteType',
            'supervisor',
            'order',
            'shipments',
        ]);

        return view('work-jobs.prints.work-order', compact('workJob'));
    }

    /**
     * طباعة إذن التسليم
     */
    public function printDeliveryNote(WorkJob $workJob)
    {
        $workJob->load(['branch', 'concreteType', 'shipments']);

        return view('work-jobs.prints.delivery-note', compact('workJob'));
    }

    /**
     * الخط الزمني
     */
    public function timeline(WorkJob $workJob)
    {
        $events = collect();

        // جمع الأحداث من الشحنات
        foreach ($workJob->shipments as $shipment) {
            foreach ($shipment->events as $event) {
                $events->push([
                    'type' => 'shipment_event',
                    'shipment_number' => $shipment->shipment_number,
                    'event_type' => $event->event_type,
                    'event_label' => $event->type_label,
                    'description' => $event->description,
                    'time' => $event->recorded_at,
                    'icon' => $event->type_icon,
                    'color' => $event->type_color,
                ]);
            }
        }

        // ترتيب حسب الوقت
        $events = $events->sortBy('time')->values();

        return view('work-jobs.timeline', compact('workJob', 'events'));
    }

    /**
     * إحصائيات
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $statistics = $this->workJobService->getStatistics(
            Auth::user()->company_code,
            $request->branch_id,
            $dateFrom,
            $dateTo
        );

        return view('work-jobs.statistics', compact('statistics', 'dateFrom', 'dateTo'));
    }
}
