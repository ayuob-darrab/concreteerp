<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Models\OrderNegotiation;
use App\Models\OrderTimeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * إنشاء طلب جديد
     */
    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = Auth::id();
            $data['status_code'] = 'new';
            $data['request_date'] = now();

            $order = WorkOrder::create($data);

            // إضافة مفاوضة مبدئية
            OrderNegotiation::create([
                'order_id' => $order->id,
                'stage' => OrderNegotiation::STAGE_INITIAL,
                'offered_price' => $data['initial_price'] ?? null,
                'offered_quantity' => $data['quantity'] ?? null,
                'offered_concrete_type' => $data['classification'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
                'created_by_type' => 'employee',
            ]);

            // تسجيل في الخط الزمني
            OrderTimeline::addEvent(
                $order->id,
                OrderTimeline::EVENT_CREATED,
                'تم إنشاء الطلب',
                'طلب جديد من ' . ($data['customer_name'] ?? 'غير محدد')
            );

            return $order;
        });
    }

    /**
     * مراجعة الفرع للطلب
     */
    public function branchReview(WorkOrder $order, array $data)
    {
        return DB::transaction(function () use ($order, $data) {
            $oldStatus = $order->status_code;

            $order->update([
                'branch_reviewed' => true,
                'branch_reviewed_at' => now(),
                'branch_reviewed_by' => Auth::id(),
                'branch_notes' => $data['notes'] ?? null,
                'branch_approved' => $data['approved'] ?? false,
                'status_code' => $data['approved'] ? 'branch_approved' : 'branch_rejected',
            ]);

            // تسجيل الحدث
            OrderTimeline::addEvent(
                $order->id,
                OrderTimeline::EVENT_BRANCH_REVIEWED,
                $data['approved'] ? 'تمت الموافقة من الفرع' : 'تم الرفض من الفرع',
                $data['notes'] ?? null,
                ['status' => $oldStatus],
                ['status' => $order->status_code]
            );

            return $order;
        });
    }

    /**
     * إرسال عرض سعر من الفرع
     */
    public function sendBranchOffer(WorkOrder $order, array $data)
    {
        return DB::transaction(function () use ($order, $data) {
            // تحديث الطلب
            $order->update([
                'branch_price' => $data['price'],
                'branch_offer_sent_at' => now(),
                'status_code' => 'waiting_customer',
            ]);

            // إضافة مفاوضة
            $negotiation = OrderNegotiation::create([
                'order_id' => $order->id,
                'stage' => OrderNegotiation::STAGE_BRANCH_OFFER,
                'offered_price' => $data['price'],
                'offered_quantity' => $data['quantity'] ?? $order->quantity,
                'offered_concrete_type' => $data['concrete_type'] ?? $order->classification,
                'offered_pump_type' => $data['pump_type'] ?? null,
                'offered_delivery_date' => $data['delivery_date'] ?? null,
                'offered_delivery_time' => $data['delivery_time'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
                'created_by_type' => 'employee',
            ]);

            // تسجيل الحدث
            OrderTimeline::addEvent(
                $order->id,
                OrderTimeline::EVENT_OFFER_SENT,
                'تم إرسال عرض سعر',
                'السعر المعروض: ' . number_format($data['price'], 2) . ' د.ع'
            );

            return $negotiation;
        });
    }

    /**
     * رد الطالب على العرض - قبول
     */
    public function acceptOffer(WorkOrder $order, array $data = [])
    {
        return DB::transaction(function () use ($order, $data) {
            $latestOffer = OrderNegotiation::getLatestOffer($order->id);

            // تحديث الطلب
            $order->update([
                'requester_response' => 'accepted',
                'requester_response_at' => now(),
                'final_price' => $latestOffer ? $latestOffer->offered_price : $order->branch_price,
                'status_code' => 'customer_approved',
            ]);

            // إضافة مفاوضة القبول
            $negotiation = OrderNegotiation::create([
                'order_id' => $order->id,
                'stage' => OrderNegotiation::STAGE_REQUESTER_ACCEPT,
                'offered_price' => $order->final_price,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
                'created_by_type' => $data['requester_type'] ?? 'customer',
            ]);

            // تسجيل الحدث
            OrderTimeline::addEvent(
                $order->id,
                OrderTimeline::EVENT_OFFER_ACCEPTED,
                'تم قبول العرض',
                'السعر النهائي: ' . number_format($order->final_price, 2) . ' د.ع'
            );

            return $negotiation;
        });
    }

    /**
     * رد الطالب على العرض - رفض
     */
    public function rejectOffer(WorkOrder $order, array $data)
    {
        return DB::transaction(function () use ($order, $data) {
            // تحديث الطلب
            $order->update([
                'requester_response' => 'rejected',
                'requester_response_at' => now(),
                'status_code' => 'negotiation',
            ]);

            // إضافة مفاوضة الرفض
            $negotiation = OrderNegotiation::create([
                'order_id' => $order->id,
                'stage' => OrderNegotiation::STAGE_REQUESTER_REJECT,
                'rejection_reason' => $data['reason'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
                'created_by_type' => $data['requester_type'] ?? 'customer',
            ]);

            // تسجيل الحدث
            OrderTimeline::addEvent(
                $order->id,
                OrderTimeline::EVENT_OFFER_REJECTED,
                'تم رفض العرض',
                $data['reason'] ?? null
            );

            return $negotiation;
        });
    }

    /**
     * رد الطالب على العرض - عرض مضاد
     */
    public function counterOffer(WorkOrder $order, array $data)
    {
        return DB::transaction(function () use ($order, $data) {
            // تحديث الطلب
            $order->update([
                'requester_response' => 'counter',
                'requester_response_at' => now(),
                'requester_price' => $data['price'],
                'status_code' => 'negotiation',
            ]);

            // إضافة مفاوضة العرض المضاد
            $negotiation = OrderNegotiation::create([
                'order_id' => $order->id,
                'stage' => OrderNegotiation::STAGE_REQUESTER_COUNTER,
                'offered_price' => $data['price'],
                'offered_quantity' => $data['quantity'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
                'created_by_type' => $data['requester_type'] ?? 'customer',
            ]);

            // تسجيل الحدث
            OrderTimeline::addEvent(
                $order->id,
                OrderTimeline::EVENT_COUNTER_OFFER,
                'عرض مضاد من الطالب',
                'السعر المقترح: ' . number_format($data['price'], 2) . ' د.ع'
            );

            return $negotiation;
        });
    }

    /**
     * الموافقة النهائية
     */
    public function finalApproval(WorkOrder $order, array $data)
    {
        return DB::transaction(function () use ($order, $data) {
            $order->update([
                'final_approved' => true,
                'final_approved_at' => now(),
                'final_approved_by' => Auth::id(),
                'final_price' => $data['final_price'] ?? $order->final_price,
                'approved_price' => $data['final_price'] ?? $order->final_price,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approved_note' => $data['notes'] ?? null,
                'status_code' => 'approved',
                'execution_date' => $data['execution_date'] ?? null,
                'execution_time' => $data['execution_time'] ?? null,
            ]);

            // إضافة مفاوضة الاتفاق النهائي
            OrderNegotiation::create([
                'order_id' => $order->id,
                'stage' => OrderNegotiation::STAGE_FINAL,
                'offered_price' => $order->final_price,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
                'created_by_type' => 'employee',
            ]);

            // تسجيل الحدث
            OrderTimeline::addEvent(
                $order->id,
                OrderTimeline::EVENT_FINAL_APPROVAL,
                'تمت الموافقة النهائية',
                'السعر النهائي المعتمد: ' . number_format($order->final_price, 2) . ' د.ع'
            );

            return $order;
        });
    }

    /**
     * إلغاء الطلب
     */
    public function cancelOrder(WorkOrder $order, array $data)
    {
        return DB::transaction(function () use ($order, $data) {
            $oldStatus = $order->status_code;

            $order->update([
                'status_code' => 'cancelled',
                'cancellation_reason' => $data['reason'] ?? null,
            ]);

            // إضافة مفاوضة الإلغاء
            OrderNegotiation::create([
                'order_id' => $order->id,
                'stage' => OrderNegotiation::STAGE_CANCELLED,
                'rejection_reason' => $data['reason'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
                'created_by_type' => 'employee',
            ]);

            // تسجيل الحدث
            OrderTimeline::addEvent(
                $order->id,
                OrderTimeline::EVENT_CANCELLED,
                'تم إلغاء الطلب',
                $data['reason'] ?? null,
                ['status' => $oldStatus],
                ['status' => 'cancelled']
            );

            return $order;
        });
    }

    /**
     * تعيين سائق وسيارة
     */
    public function assignDriver(WorkOrder $order, array $data)
    {
        return DB::transaction(function () use ($order, $data) {
            $order->update([
                'driver_id' => $data['driver_id'],
                'vehicle_id' => $data['vehicle_id'],
                'assigned_at' => now(),
                'assigned_by' => Auth::id(),
                'status_code' => $order->status_code === 'approved' ? 'assigned' : $order->status_code,
            ]);

            // تسجيل الحدث
            OrderTimeline::addEvent(
                $order->id,
                OrderTimeline::EVENT_ASSIGNED,
                'تم تعيين سائق وسيارة',
                'تم تعيين السائق والسيارة للطلب'
            );

            return $order;
        });
    }

    /**
     * إرسال للتنفيذ
     */
    public function dispatch(WorkOrder $order, array $data = [])
    {
        return DB::transaction(function () use ($order, $data) {
            $order->update([
                'status_code' => 'in_progress',
                'dispatched_at' => now(),
                'dispatched_by' => Auth::id(),
            ]);

            // تسجيل الحدث
            OrderTimeline::addEvent(
                $order->id,
                OrderTimeline::EVENT_DISPATCHED,
                'تم إرسال الطلب للتنفيذ',
                $data['notes'] ?? null,
                null,
                null,
                isset($data['lat']) && isset($data['lng']) ? ['lat' => $data['lat'], 'lng' => $data['lng']] : null
            );

            return $order;
        });
    }

    /**
     * اكتمال الطلب
     */
    public function complete(WorkOrder $order, array $data)
    {
        return DB::transaction(function () use ($order, $data) {
            $order->update([
                'status_code' => 'completed',
                'executed_quantity' => $data['executed_quantity'] ?? $order->quantity,
                'completed_at' => now(),
                'completed_by' => Auth::id(),
            ]);

            // تسجيل الحدث
            OrderTimeline::addEvent(
                $order->id,
                OrderTimeline::EVENT_COMPLETED,
                'تم اكتمال الطلب',
                'الكمية المنفذة: ' . ($data['executed_quantity'] ?? $order->quantity),
                null,
                null,
                isset($data['lat']) && isset($data['lng']) ? ['lat' => $data['lat'], 'lng' => $data['lng']] : null
            );

            return $order;
        });
    }

    /**
     * إضافة ملاحظة للطلب
     */
    public function addNote(WorkOrder $order, string $note)
    {
        OrderTimeline::addEvent(
            $order->id,
            OrderTimeline::EVENT_NOTE_ADDED,
            'ملاحظة جديدة',
            $note
        );

        return true;
    }

    /**
     * الحصول على سجل المفاوضات
     */
    public function getNegotiationHistory(WorkOrder $order)
    {
        return OrderNegotiation::getNegotiationHistory($order->id);
    }

    /**
     * الحصول على الخط الزمني
     */
    public function getTimeline(WorkOrder $order)
    {
        return OrderTimeline::getTimeline($order->id);
    }

    /**
     * الطلبات المعلقة للمراجعة
     */
    public function getPendingReview($branchId = null)
    {
        $query = WorkOrder::where('status_code', 'new')
            ->where('branch_reviewed', false);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    /**
     * الطلبات المعلقة لرد العميل
     */
    public function getPendingCustomerResponse($branchId = null)
    {
        $query = WorkOrder::where('status_code', 'waiting_customer');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('branch_offer_sent_at', 'asc')->get();
    }

    /**
     * الطلبات في التفاوض
     */
    public function getInNegotiation($branchId = null)
    {
        $query = WorkOrder::where('status_code', 'negotiation');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('updated_at', 'desc')->get();
    }

    /**
     * الطلبات الجاهزة للموافقة النهائية
     */
    public function getReadyForFinalApproval($branchId = null)
    {
        $query = WorkOrder::where('status_code', 'customer_approved')
            ->where('final_approved', false);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('requester_response_at', 'asc')->get();
    }

    /**
     * إحصائيات الطلبات
     */
    public function getOrderStatistics($branchId = null, $startDate = null, $endDate = null)
    {
        $query = WorkOrder::query();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return [
            'total' => (clone $query)->count(),
            'new' => (clone $query)->where('status_code', 'new')->count(),
            'in_negotiation' => (clone $query)->whereIn('status_code', ['waiting_customer', 'negotiation'])->count(),
            'approved' => (clone $query)->where('status_code', 'approved')->count(),
            'in_progress' => (clone $query)->where('status_code', 'in_progress')->count(),
            'completed' => (clone $query)->where('status_code', 'completed')->count(),
            'cancelled' => (clone $query)->where('status_code', 'cancelled')->count(),
            'total_value' => (clone $query)->whereNotNull('final_price')->sum(DB::raw('quantity * final_price')),
            'executed_value' => (clone $query)->where('status_code', 'completed')->sum(DB::raw('executed_quantity * final_price')),
        ];
    }
}
