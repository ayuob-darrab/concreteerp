<?php

namespace App\Http\Controllers;

use App\Models\WorkLoss;
use App\Models\WorkJob;
use App\Models\WorkShipment;
use App\Models\Cars;
use App\Services\LossService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LossController extends Controller
{
    protected $lossService;

    public function __construct(LossService $lossService)
    {
        $this->lossService = $lossService;
    }

    /**
     * قائمة الخسائر
     */
    public function index(Request $request)
    {
        $query = WorkLoss::with(['job', 'shipment', 'vehicle', 'reportedBy', 'branch'])
            ->company(Auth::user()->company_code);

        // تصفية حسب الفرع
        if ($request->filled('branch_id')) {
            $query->branch($request->branch_id);
        }

        // تصفية حسب النوع
        if ($request->filled('loss_type')) {
            $query->ofType($request->loss_type);
        }

        // تصفية حسب الحالة
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // تصفية حسب التاريخ
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->reportedBetween($request->date_from, $request->date_to);
        }

        $losses = $query->orderBy('reported_at', 'desc')->paginate(20);

        // الإحصائيات
        $statistics = $this->lossService->getStatistics(
            Auth::user()->company_code,
            $request->branch_id,
            $request->date_from,
            $request->date_to
        );

        return view('work-jobs.losses.index', compact('losses', 'statistics'));
    }

    /**
     * نموذج تسجيل خسارة
     */
    public function create(Request $request)
    {
        $jobId = $request->job_id;
        $shipmentId = $request->shipment_id;

        $job = $jobId ? WorkJob::find($jobId) : null;
        $shipment = $shipmentId ? WorkShipment::find($shipmentId) : null;

        // الآليات
        $vehicles = Cars::where('company_code', Auth::user()->company_code)->get();

        return view('work-jobs.losses.create', compact('job', 'shipment', 'vehicles'));
    }

    /**
     * حفظ خسارة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'loss_type' => 'required|in:' . implode(',', array_keys(WorkLoss::TYPES)),
            'description' => 'required|string|min:10',
            'quantity_lost' => 'nullable|numeric|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
            'job_id' => 'nullable|exists:work_jobs,id',
            'shipment_id' => 'nullable|exists:work_shipments,id',
            'vehicle_id' => 'nullable|exists:cars,id',
            'location_description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);

        try {
            $data = $request->all();
            $data['company_code'] = Auth::user()->company_code;

            $loss = $this->lossService->report($data);

            return redirect()
                ->route('losses.show', $loss)
                ->with('success', 'تم تسجيل الخسارة بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل الخسارة
     */
    public function show(WorkLoss $loss)
    {
        $loss->load(['job', 'shipment', 'vehicle', 'reportedBy', 'investigatedBy', 'branch']);

        return view('work-jobs.losses.show', compact('loss'));
    }

    /**
     * بدء التحقيق
     */
    public function startInvestigation(Request $request, WorkLoss $loss)
    {
        try {
            $this->lossService->startInvestigation($loss, $request->notes);

            return back()->with('success', 'تم بدء التحقيق');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * تحديث ملاحظات التحقيق
     */
    public function updateInvestigation(Request $request, WorkLoss $loss)
    {
        $request->validate([
            'notes' => 'required|string|min:10',
        ]);

        $this->lossService->updateInvestigation($loss, $request->notes);

        return back()->with('success', 'تم تحديث ملاحظات التحقيق');
    }

    /**
     * حل الخسارة
     */
    public function resolve(Request $request, WorkLoss $loss)
    {
        $request->validate([
            'resolution' => 'required|string|min:10',
            'actual_cost' => 'nullable|numeric|min:0',
            'resolution_date' => 'nullable|date',
        ]);

        try {
            $this->lossService->resolve($loss, $request->only([
                'resolution',
                'actual_cost',
                'resolution_date'
            ]));

            return back()->with('success', 'تم حل الخسارة');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * إغلاق الخسارة
     */
    public function close(WorkLoss $loss)
    {
        try {
            $this->lossService->close($loss);

            return back()->with('success', 'تم إغلاق الخسارة');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * إضافة مرفق
     */
    public function addAttachment(Request $request, WorkLoss $loss)
    {
        $request->validate([
            'attachment' => 'required|file|max:10240',
        ]);

        try {
            $this->lossService->addAttachment($loss, $request->file('attachment'));

            return back()->with('success', 'تم إضافة المرفق');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * إحصائيات الخسائر
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $statistics = $this->lossService->getStatistics(
            Auth::user()->company_code,
            $request->branch_id,
            $dateFrom,
            $dateTo
        );

        return view('work-jobs.losses.statistics', compact('statistics', 'dateFrom', 'dateTo'));
    }

    /**
     * التقرير الشهري
     */
    public function monthlyReport(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;

        $report = $this->lossService->getMonthlyReport(
            Auth::user()->company_code,
            $year,
            str_pad($month, 2, '0', STR_PAD_LEFT),
            $request->branch_id
        );

        return view('work-jobs.losses.monthly-report', compact('report', 'year', 'month'));
    }

    /**
     * طباعة تقرير الخسارة
     */
    public function print(WorkLoss $loss)
    {
        $loss->load(['job', 'shipment', 'vehicle', 'reportedBy', 'investigatedBy', 'branch']);

        return view('work-jobs.losses.print', compact('loss'));
    }
}
