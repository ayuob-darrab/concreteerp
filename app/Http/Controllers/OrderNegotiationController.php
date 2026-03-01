<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\OrderNegotiation;
use App\Models\OrderTimeline;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderNegotiationController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * عرض صفحة التفاوض للطلب
     */
    public function show(WorkOrder $order)
    {
        $order->load(['negotiations', 'timeline', 'branch', 'company']);

        $negotiations = $this->orderService->getNegotiationHistory($order);
        $timeline = $this->orderService->getTimeline($order);

        return view('orders.negotiation', compact('order', 'negotiations', 'timeline'));
    }

    /**
     * مراجعة الفرع
     */
    public function branchReview(Request $request, WorkOrder $order)
    {
        $request->validate([
            'approved' => 'required|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $this->orderService->branchReview($order, $request->all());

        return redirect()->route('orders.negotiation.show', $order)
            ->with('success', 'تم تسجيل مراجعة الفرع بنجاح');
    }

    /**
     * إرسال عرض سعر
     */
    public function sendOffer(Request $request, WorkOrder $order)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|numeric|min:0',
            'concrete_type' => 'nullable|string',
            'pump_type' => 'nullable|string',
            'delivery_date' => 'nullable|date',
            'delivery_time' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        $this->orderService->sendBranchOffer($order, $request->all());

        return redirect()->route('orders.negotiation.show', $order)
            ->with('success', 'تم إرسال العرض بنجاح');
    }

    /**
     * قبول العرض (من قبل العميل/المقاول/الوكيل)
     */
    public function acceptOffer(Request $request, WorkOrder $order)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $this->orderService->acceptOffer($order, $request->all());

        return redirect()->route('orders.negotiation.show', $order)
            ->with('success', 'تم قبول العرض بنجاح');
    }

    /**
     * رفض العرض
     */
    public function rejectOffer(Request $request, WorkOrder $order)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $this->orderService->rejectOffer($order, $request->all());

        return redirect()->route('orders.negotiation.show', $order)
            ->with('success', 'تم تسجيل الرفض بنجاح');
    }

    /**
     * عرض مضاد
     */
    public function counterOffer(Request $request, WorkOrder $order)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $this->orderService->counterOffer($order, $request->all());

        return redirect()->route('orders.negotiation.show', $order)
            ->with('success', 'تم إرسال العرض المضاد بنجاح');
    }

    /**
     * الموافقة النهائية
     */
    public function finalApproval(Request $request, WorkOrder $order)
    {
        $request->validate([
            'final_price' => 'nullable|numeric|min:0',
            'execution_date' => 'nullable|date',
            'execution_time' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        $this->orderService->finalApproval($order, $request->all());

        return redirect()->route('orders.negotiation.show', $order)
            ->with('success', 'تمت الموافقة النهائية بنجاح');
    }

    /**
     * إلغاء الطلب
     */
    public function cancel(Request $request, WorkOrder $order)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $this->orderService->cancelOrder($order, $request->all());

        return redirect()->route('orders.negotiation.show', $order)
            ->with('success', 'تم إلغاء الطلب بنجاح');
    }

    /**
     * تعيين سائق وسيارة
     */
    public function assign(Request $request, WorkOrder $order)
    {
        $request->validate([
            'driver_id' => 'required|exists:employees,id',
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $this->orderService->assignDriver($order, $request->all());

        return redirect()->route('orders.negotiation.show', $order)
            ->with('success', 'تم تعيين السائق والسيارة بنجاح');
    }

    /**
     * إرسال للتنفيذ
     */
    public function sendToExecution(Request $request, WorkOrder $order)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        $this->orderService->dispatch($order, $request->all());

        return redirect()->route('orders.negotiation.show', $order)
            ->with('success', 'تم إرسال الطلب للتنفيذ');
    }

    /**
     * اكتمال الطلب
     */
    public function complete(Request $request, WorkOrder $order)
    {
        $request->validate([
            'executed_quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        $this->orderService->complete($order, $request->all());

        return redirect()->route('orders.negotiation.show', $order)
            ->with('success', 'تم اكتمال الطلب بنجاح');
    }

    /**
     * إضافة ملاحظة
     */
    public function addNote(Request $request, WorkOrder $order)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $this->orderService->addNote($order, $request->note);

        return redirect()->route('orders.negotiation.show', $order)
            ->with('success', 'تمت إضافة الملاحظة بنجاح');
    }

    /**
     * عرض الخط الزمني (AJAX)
     */
    public function timeline(WorkOrder $order)
    {
        $timeline = $this->orderService->getTimeline($order);

        return response()->json([
            'success' => true,
            'timeline' => $timeline
        ]);
    }

    /**
     * قائمة الطلبات المعلقة للمراجعة
     */
    public function pendingReview(Request $request)
    {
        $branchId = $request->get('branch_id', Auth::user()->branch_id);
        $orders = $this->orderService->getPendingReview($branchId);

        return view('orders.pending-review', compact('orders'));
    }

    /**
     * قائمة الطلبات في انتظار رد العميل
     */
    public function pendingResponse(Request $request)
    {
        $branchId = $request->get('branch_id', Auth::user()->branch_id);
        $orders = $this->orderService->getPendingCustomerResponse($branchId);

        return view('orders.pending-response', compact('orders'));
    }

    /**
     * قائمة الطلبات في التفاوض
     */
    public function inNegotiation(Request $request)
    {
        $branchId = $request->get('branch_id', Auth::user()->branch_id);
        $orders = $this->orderService->getInNegotiation($branchId);

        return view('orders.in-negotiation', compact('orders'));
    }

    /**
     * قائمة الطلبات الجاهزة للموافقة النهائية
     */
    public function readyForApproval(Request $request)
    {
        $branchId = $request->get('branch_id', Auth::user()->branch_id);
        $orders = $this->orderService->getReadyForFinalApproval($branchId);

        return view('orders.ready-approval', compact('orders'));
    }

    /**
     * إحصائيات الطلبات
     */
    public function statistics(Request $request)
    {
        $branchId = $request->get('branch_id', Auth::user()->branch_id);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $statistics = $this->orderService->getOrderStatistics($branchId, $startDate, $endDate);

        if ($request->wantsJson()) {
            return response()->json($statistics);
        }

        return view('orders.statistics', compact('statistics'));
    }
}
