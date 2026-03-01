<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Services\WorkOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkOrderController extends Controller
{
    protected $workOrderService;

    public function __construct(WorkOrderService $workOrderService)
    {
        $this->workOrderService = $workOrderService;
    }

    /**
     * عرض قائمة الطلبات
     */
    public function index(Request $request)
    {
        $query = WorkOrder::with(['company', 'branch', 'concreteMix', 'creator', 'latestStage']);

        // فلترة حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الشركة
        if ($request->has('company_code')) {
            $query->forCompany($request->company_code);
        }

        // فلترة حسب الفرع
        if ($request->has('branch_id')) {
            $query->forBranch($request->branch_id);
        }

        // فلترة حسب التاريخ
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->betweenDates($request->from_date, $request->to_date);
        }

        // الطلبات النشطة فقط
        if ($request->has('active_only')) {
            $query->active();
        }

        $orders = $query->latest()->paginate(20);

        return view('work_orders.index', compact('orders'));
    }

    /**
     * عرض نموذج إنشاء طلب جديد
     */
    public function create()
    {
        return view('work_orders.create');
    }

    /**
     * حفظ طلب جديد
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_type' => 'required|string',
            'classification' => 'required|exists:concrete_mixes,id',
            'company_code' => 'required|exists:companies,code',
            'branch_id' => 'required|exists:branches,id',
            'quantity' => 'required|numeric|min:0.01',
            'delivery_datetime' => 'nullable|date',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'initial_price' => 'nullable|numeric|min:0',
            'location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $result = $this->workOrderService->createOrder($request->all());

        if ($result['success']) {
            return redirect()
                ->route('work-orders.show', $result['order']->id)
                ->with('success', 'تم إنشاء الطلب بنجاح');
        }

        return back()
            ->withErrors(['error' => $result['message']])
            ->withInput();
    }

    /**
     * عرض تفاصيل الطلب
     */
    public function show($id)
    {
        $order = WorkOrder::with([
            'company',
            'branch',
            'concreteMix',
            'creator',
            'stages.user',
            'histories.user',
            'executions.car',
            'executions.driver',
            'priceChanges.changer',
        ])->findOrFail($id);

        // الحصول على التقرير الشامل
        $report = $this->workOrderService->getOrderReport($order);

        return view('work_orders.show', compact('order', 'report'));
    }

    /**
     * الموافقة على الطلب
     */
    public function approve(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $result = $this->workOrderService->changeStatus($order, 'approved', [
            'notes' => $request->notes
        ]);

        if ($result['success']) {
            return back()->with('success', 'تمت الموافقة على الطلب وتم خصم المواد من المخزن');
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * رفض الطلب
     */
    public function reject(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $result = $this->workOrderService->changeStatus($order, 'rejected', [
            'notes' => $request->notes
        ]);

        if ($result['success']) {
            return back()->with('success', 'تم رفض الطلب');
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * تحويل إلى قيد المراجعة
     */
    public function review($id)
    {
        $order = WorkOrder::findOrFail($id);

        $result = $this->workOrderService->changeStatus($order, 'under_review', [
            'notes' => 'تم تحويل الطلب للمراجعة'
        ]);

        if ($result['success']) {
            return back()->with('success', 'تم تحويل الطلب للمراجعة');
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * جدولة الطلب
     */
    public function schedule(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'delivery_datetime' => 'required|date|after:now',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // تحديث موعد التسليم
        $order->update(['delivery_datetime' => $request->delivery_datetime]);

        $result = $this->workOrderService->changeStatus($order, 'scheduled', [
            'notes' => "تم جدولة التسليم للموعد: {$request->delivery_datetime}",
            'metadata' => ['scheduled_datetime' => $request->delivery_datetime]
        ]);

        if ($result['success']) {
            return back()->with('success', 'تم جدولة الطلب بنجاح');
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * تغيير السعر
     */
    public function changePrice(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'new_price' => 'required|numeric|min:0',
            'change_type' => 'required|in:customer_request,quantity_change,market_change,discount,surcharge,correction,management,final_approval',
            'reason' => 'required|string',
            'requires_approval' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $result = $this->workOrderService->changePrice(
            $order,
            $request->new_price,
            $request->change_type,
            $request->reason,
            $request->requires_approval ?? false
        );

        if ($result['success']) {
            return back()->with('success', 'تم تغيير السعر بنجاح');
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * إضافة تنفيذ جزئي
     */
    public function addExecution(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:0.01',
            'car_id' => 'nullable|exists:cars,id',
            'driver_id' => 'nullable|exists:employees,id',
            'execution_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $result = $this->workOrderService->addExecution($order, $request->all());

        if ($result['success']) {
            return back()->with('success', 'تم إضافة التنفيذ بنجاح');
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * تحديث حالة التنفيذ
     */
    public function updateExecutionStatus(Request $request, $orderId, $executionId)
    {
        $execution = \App\Models\OrderExecution::where('work_order_id', $orderId)
            ->findOrFail($executionId);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:scheduled,loading,in_transit,pouring,completed,returned,cancelled',
            'departure_time' => 'nullable|date',
            'arrival_time' => 'nullable|date',
            'pour_start_time' => 'nullable|date',
            'pour_end_time' => 'nullable|date',
            'return_time' => 'nullable|date',
            'temperature' => 'nullable|numeric',
            'slump' => 'nullable|numeric',
            'quality_status' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $data = $request->only([
            'departure_time',
            'arrival_time',
            'pour_start_time',
            'pour_end_time',
            'return_time',
            'temperature',
            'slump',
            'quality_status',
            'notes',
        ]);

        $result = $this->workOrderService->updateExecutionStatus(
            $execution,
            $request->status,
            $data
        );

        if ($result['success']) {
            return back()->with('success', 'تم تحديث حالة التنفيذ بنجاح');
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * إلغاء الطلب
     */
    public function cancel(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $result = $this->workOrderService->cancelOrder($order, $request->reason);

        if ($result['success']) {
            return back()->with('success', 'تم إلغاء الطلب وإرجاع المواد للمخزن');
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * طباعة الطلب
     */
    public function print($id)
    {
        $order = WorkOrder::with([
            'company',
            'branch',
            'concreteMix',
            'executions.car',
            'executions.driver',
        ])->findOrFail($id);

        $report = $this->workOrderService->getOrderReport($order);

        return view('work_orders.print', compact('order', 'report'));
    }

    /**
     * تصدير تقرير Excel
     */
    public function export(Request $request)
    {
        // يمكن استخدام Laravel Excel
        // return Excel::download(new WorkOrdersExport($request->all()), 'work_orders.xlsx');
    }

    /**
     * API: الحصول على إحصائيات
     */
    public function statistics(Request $request)
    {
        $statistics = [
            'total' => WorkOrder::count(),
            'new' => WorkOrder::new()->count(),
            'under_review' => WorkOrder::underReview()->count(),
            'approved' => WorkOrder::approved()->count(),
            'in_progress' => WorkOrder::inProgress()->count(),
            'completed' => WorkOrder::completed()->count(),
            'cancelled' => WorkOrder::where('status', 'cancelled')->count(),
            'active' => WorkOrder::active()->count(),
        ];

        if ($request->has('company_code')) {
            $statistics['company_total'] = WorkOrder::forCompany($request->company_code)->count();
        }

        if ($request->has('branch_id')) {
            $statistics['branch_total'] = WorkOrder::forBranch($request->branch_id)->count();
        }

        return response()->json($statistics);
    }
}
