<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Models\OrderStage;
use App\Models\OrderHistory;
use App\Models\OrderExecution;
use App\Models\OrderPriceChange;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\ConcreteMix;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * خدمة إدارة أوامر العمل
 * 
 * تحتوي على كل منطق الأعمال لإدارة الطلبات
 */
class WorkOrderService
{
    /**
     * إنشاء طلب جديد
     */
    public function createOrder(array $data)
    {
        DB::beginTransaction();
        try {
            // إنشاء الطلب
            $order = WorkOrder::create([
                'sender_type' => $data['sender_type'],
                'sender_id' => $data['sender_id'] ?? null,
                'classification' => $data['classification'],
                'company_code' => $data['company_code'],
                'branch_id' => $data['branch_id'],
                'status' => 'new',
                'request_type' => $data['request_type'] ?? 'Concrete',
                'quantity' => $data['quantity'],
                'location' => $data['location'] ?? null,
                'delivery_datetime' => $data['delivery_datetime'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'initial_price' => $data['initial_price'] ?? null,
                'request_date' => now(),
                'created_by' => auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);

            DB::commit();
            return ['success' => true, 'order' => $order];
        } catch (Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * تغيير حالة الطلب
     */
    public function changeStatus(WorkOrder $order, string $newStatus, array $options = [])
    {
        DB::beginTransaction();
        try {
            $oldStatus = $order->status;

            // التحقق من صحة الانتقال
            if (!$this->isValidStatusTransition($oldStatus, $newStatus)) {
                throw new Exception("لا يمكن الانتقال من {$oldStatus} إلى {$newStatus}");
            }

            // تحديث الحالة
            $order->update(['status' => $newStatus]);

            // إضافة مرحلة جديدة
            OrderStage::create([
                'work_order_id' => $order->id,
                'stage' => $newStatus,
                'user_id' => auth()->id(),
                'notes' => $options['notes'] ?? null,
                'metadata' => $options['metadata'] ?? null,
            ]);

            // تسجيل في التاريخ
            OrderHistory::logChange($order->id, 'status_changed', [
                'field_name' => 'status',
                'old_value' => $oldStatus,
                'new_value' => $newStatus,
                'description' => "تغيير الحالة من {$oldStatus} إلى {$newStatus}",
                'notes' => $options['notes'] ?? null,
            ]);

            // إجراءات خاصة حسب الحالة
            if ($newStatus === 'approved') {
                $this->handleApproval($order);
            }

            DB::commit();
            return ['success' => true, 'order' => $order->fresh()];
        } catch (Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * التحقق من صحة الانتقال بين الحالات
     */
    private function isValidStatusTransition($from, $to)
    {
        $validTransitions = [
            'new' => ['under_review', 'cancelled'],
            'under_review' => ['waiting_customer', 'approved', 'rejected', 'cancelled'],
            'waiting_customer' => ['under_review', 'approved', 'rejected', 'cancelled'],
            'approved' => ['scheduled', 'cancelled'],
            'rejected' => [],
            'scheduled' => ['in_progress', 'cancelled'],
            'in_progress' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        return in_array($to, $validTransitions[$from] ?? []);
    }

    /**
     * معالجة الموافقة على الطلب
     */
    private function handleApproval(WorkOrder $order)
    {
        // خصم المواد من المخزن
        $this->deductInventory($order);

        // تفعيل السعر النهائي
        if ($order->initial_price && !$order->final_price) {
            $order->update([
                'final_price' => $order->initial_price,
                'price_approved' => true,
            ]);
        }

        OrderHistory::logChange($order->id, 'approval_given', [
            'description' => 'تمت الموافقة على الطلب',
        ]);
    }

    /**
     * خصم المواد من المخزن
     */
    public function deductInventory(WorkOrder $order)
    {
        // الحصول على مكونات الخلطة
        $concreteMix = $order->concreteMix;
        if (!$concreteMix) {
            throw new Exception('لم يتم العثور على خلطة الكونكريت');
        }

        $quantity = $order->quantity;

        // خصم الإسمنت
        if ($concreteMix->cement_code) {
            $this->deductMaterial($concreteMix->cement_code, $concreteMix->cement * $quantity, $order->id, 'cement');
        }

        // خصم الرمل
        if ($concreteMix->sand_code) {
            $this->deductMaterial($concreteMix->sand_code, $concreteMix->sand * $quantity, $order->id, 'sand');
        }

        // خصم الحصى
        if ($concreteMix->gravel_code) {
            $this->deductMaterial($concreteMix->gravel_code, $concreteMix->gravel * $quantity, $order->id, 'gravel');
        }

        // خصم الماء
        if ($concreteMix->water_code) {
            $this->deductMaterial($concreteMix->water_code, $concreteMix->water * $quantity, $order->id, 'water');
        }

        // خصم المواد الكيميائية
        foreach ($concreteMix->chemicals as $chemical) {
            $this->deductMaterial(
                $chemical->code ?? $chemical->id,
                $chemical->pivot->quantity * $quantity,
                $order->id,
                'chemical'
            );
        }

        OrderHistory::logChange($order->id, 'inventory_deducted', [
            'description' => "تم خصم المواد من المخزن للكمية: {$quantity}",
        ]);
    }

    /**
     * خصم مادة من المخزن
     */
    private function deductMaterial($materialCode, $quantity, $orderId, $materialType)
    {
        $inventory = Inventory::where('code', $materialCode)->first();

        if (!$inventory) {
            throw new Exception("المادة {$materialCode} غير موجودة في المخزن");
        }

        if ($inventory->quantity < $quantity) {
            throw new Exception("الكمية المتاحة من {$inventory->name} غير كافية");
        }

        // خصم من المخزن
        $inventory->decrement('quantity', $quantity);

        // تسجيل في سجل المخزن
        InventoryHistory::create([
            'inventory_id' => $inventory->id,
            'type' => 'out',
            'quantity' => $quantity,
            'reference_type' => 'work_order',
            'reference_id' => $orderId,
            'notes' => "خصم لأمر عمل رقم {$orderId}",
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * تغيير السعر
     */
    public function changePrice(WorkOrder $order, $newPrice, $changeType, $reason = null, $requiresApproval = false)
    {
        DB::beginTransaction();
        try {
            $oldPrice = $order->initial_price ?? $order->final_price;

            // تسجيل تغيير السعر
            $priceChange = OrderPriceChange::logPriceChange(
                $order->id,
                $oldPrice,
                $newPrice,
                $changeType,
                $reason,
                $requiresApproval
            );

            // تحديث السعر في الطلب
            if ($changeType === 'final_approval' || !$requiresApproval) {
                $order->update([
                    'final_price' => $newPrice,
                    'price_approved' => true,
                ]);
            } else {
                $order->update(['initial_price' => $newPrice]);
            }

            // تسجيل في التاريخ
            OrderHistory::logChange($order->id, 'price_changed', [
                'field_name' => 'price',
                'old_value' => $oldPrice,
                'new_value' => $newPrice,
                'description' => "تغيير السعر من {$oldPrice} إلى {$newPrice}",
                'notes' => $reason,
            ]);

            DB::commit();
            return ['success' => true, 'price_change' => $priceChange];
        } catch (Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * إضافة تنفيذ جزئي
     */
    public function addExecution(WorkOrder $order, array $data)
    {
        DB::beginTransaction();
        try {
            // التحقق من أن الطلب معتمد
            if (!in_array($order->status, ['approved', 'scheduled', 'in_progress'])) {
                throw new Exception('لا يمكن إضافة تنفيذ لطلب غير معتمد');
            }

            // التحقق من عدم تجاوز الكمية
            $totalExecuted = $order->executed_quantity + $data['quantity'];
            if ($totalExecuted > $order->quantity) {
                throw new Exception('الكمية المنفذة تتجاوز الكمية المطلوبة');
            }

            // حساب السعر
            $unitPrice = $data['unit_price'] ?? $order->effective_price;
            $totalPrice = $data['quantity'] * $unitPrice;

            // إنشاء التنفيذ
            $execution = OrderExecution::create([
                'work_order_id' => $order->id,
                'quantity' => $data['quantity'],
                'car_id' => $data['car_id'] ?? null,
                'driver_id' => $data['driver_id'] ?? null,
                'execution_date' => $data['execution_date'] ?? now(),
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'location' => $data['location'] ?? $order->location,
                'status' => $data['status'] ?? 'scheduled',
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // تحديث الكمية المنفذة
            $order->increment('executed_quantity', $data['quantity']);

            // تسجيل في التاريخ
            OrderHistory::logChange($order->id, 'execution_added', [
                'description' => "تم إضافة تنفيذ بكمية {$data['quantity']}",
                'metadata' => ['execution_id' => $execution->id],
            ]);

            // إذا اكتملت الكمية، تغيير الحالة
            if ($order->isFullyExecuted() && $order->status !== 'completed') {
                $this->changeStatus($order, 'completed', [
                    'notes' => 'تم إكمال التنفيذ الكامل للطلب'
                ]);
            } elseif ($order->status === 'scheduled') {
                $this->changeStatus($order, 'in_progress');
            }

            DB::commit();
            return ['success' => true, 'execution' => $execution];
        } catch (Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * تحديث حالة التنفيذ
     */
    public function updateExecutionStatus(OrderExecution $execution, $newStatus, $data = [])
    {
        DB::beginTransaction();
        try {
            $execution->update(array_merge([
                'status' => $newStatus,
                'updated_by' => auth()->id(),
            ], $data));

            // إذا تم إكمال التنفيذ، خصم المخزن
            if ($newStatus === 'completed' && !$execution->inventory_deducted) {
                $this->deductInventoryForExecution($execution);
            }

            OrderHistory::logChange($execution->work_order_id, 'execution_added', [
                'description' => "تحديث حالة التنفيذ #{$execution->id} إلى {$newStatus}",
            ]);

            DB::commit();
            return ['success' => true, 'execution' => $execution->fresh()];
        } catch (Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * خصم المخزن لتنفيذ محدد
     */
    private function deductInventoryForExecution(OrderExecution $execution)
    {
        $order = $execution->workOrder;
        $concreteMix = $order->concreteMix;

        if (!$concreteMix) {
            throw new Exception('لم يتم العثور على خلطة الكونكريت');
        }

        $quantity = $execution->quantity;

        // خصم المواد حسب الكمية المنفذة
        if ($concreteMix->cement_code) {
            $this->deductMaterial($concreteMix->cement_code, $concreteMix->cement * $quantity, $order->id, 'cement');
        }

        if ($concreteMix->sand_code) {
            $this->deductMaterial($concreteMix->sand_code, $concreteMix->sand * $quantity, $order->id, 'sand');
        }

        if ($concreteMix->gravel_code) {
            $this->deductMaterial($concreteMix->gravel_code, $concreteMix->gravel * $quantity, $order->id, 'gravel');
        }

        if ($concreteMix->water_code) {
            $this->deductMaterial($concreteMix->water_code, $concreteMix->water * $quantity, $order->id, 'water');
        }

        // تحديث حالة الخصم
        $execution->update([
            'inventory_deducted' => true,
            'inventory_deducted_at' => now(),
            'inventory_deducted_by' => auth()->id(),
        ]);
    }

    /**
     * إلغاء الطلب
     */
    public function cancelOrder(WorkOrder $order, $reason = null)
    {
        DB::beginTransaction();
        try {
            if (!$order->canBeCancelled()) {
                throw new Exception('لا يمكن إلغاء هذا الطلب');
            }

            // إرجاع المخزن إذا كان تم الخصم
            if ($order->status === 'approved' || $order->status === 'scheduled') {
                $this->returnInventory($order);
            }

            // إلغاء التنفيذات الجارية
            $order->executions()
                ->whereIn('status', ['scheduled', 'loading'])
                ->update(['status' => 'cancelled']);

            // تحديث الحالة
            $this->changeStatus($order, 'cancelled', [
                'notes' => $reason ?? 'تم إلغاء الطلب'
            ]);

            DB::commit();
            return ['success' => true, 'message' => 'تم إلغاء الطلب بنجاح'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * إرجاع المواد للمخزن
     */
    private function returnInventory(WorkOrder $order)
    {
        $concreteMix = $order->concreteMix;
        if (!$concreteMix) return;

        $quantityToReturn = $order->quantity - $order->executed_quantity;
        if ($quantityToReturn <= 0) return;

        // إرجاع المواد
        if ($concreteMix->cement_code) {
            $this->returnMaterial($concreteMix->cement_code, $concreteMix->cement * $quantityToReturn, $order->id);
        }

        if ($concreteMix->sand_code) {
            $this->returnMaterial($concreteMix->sand_code, $concreteMix->sand * $quantityToReturn, $order->id);
        }

        if ($concreteMix->gravel_code) {
            $this->returnMaterial($concreteMix->gravel_code, $concreteMix->gravel * $quantityToReturn, $order->id);
        }

        if ($concreteMix->water_code) {
            $this->returnMaterial($concreteMix->water_code, $concreteMix->water * $quantityToReturn, $order->id);
        }

        OrderHistory::logChange($order->id, 'other', [
            'description' => "تم إرجاع المواد للمخزن للكمية: {$quantityToReturn}",
        ]);
    }

    /**
     * إرجاع مادة للمخزن
     */
    private function returnMaterial($materialCode, $quantity, $orderId)
    {
        $inventory = Inventory::where('code', $materialCode)->first();
        if (!$inventory) return;

        $inventory->increment('quantity', $quantity);

        InventoryHistory::create([
            'inventory_id' => $inventory->id,
            'type' => 'in',
            'quantity' => $quantity,
            'reference_type' => 'work_order_cancelled',
            'reference_id' => $orderId,
            'notes' => "إرجاع بسبب إلغاء أمر عمل رقم {$orderId}",
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * الحصول على تقرير الطلب
     */
    public function getOrderReport(WorkOrder $order)
    {
        return [
            'order' => $order->load(['company', 'branch', 'concreteMix']),
            'stages' => $order->stages,
            'histories' => $order->histories,
            'executions' => $order->executions,
            'price_changes' => $order->priceChanges,
            'statistics' => [
                'total_quantity' => $order->quantity,
                'executed_quantity' => $order->executed_quantity,
                'remaining_quantity' => $order->remaining_quantity,
                'execution_percentage' => $order->execution_percentage,
                'total_amount' => $order->total_amount,
                'executed_amount' => $order->executed_amount,
                'remaining_amount' => $order->remaining_amount,
            ],
        ];
    }
}
