<?php

namespace App\Services;

use App\Models\MaterialReservation;
use App\Models\WorkJob;
use App\Models\Material;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MaterialReservationService
{
    /**
     * حجز مادة لأمر العمل
     */
    public function reserve(WorkJob $job, int $materialId, float $quantity, int $inventoryId = null)
    {
        return DB::transaction(function () use ($job, $materialId, $quantity, $inventoryId) {
            // إذا لم يتم تحديد المخزن، ابحث عن أفضل مخزن
            if (!$inventoryId) {
                $inventoryId = $this->findBestInventory($job->branch_id, $materialId, $quantity);
            }

            if (!$inventoryId) {
                throw new \Exception("لا يوجد مخزون كافٍ للمادة المطلوبة");
            }

            // التحقق من توفر الكمية
            $available = $this->getAvailableQuantity($inventoryId, $materialId);
            if ($available < $quantity) {
                throw new \Exception("الكمية المتاحة ({$available}) أقل من المطلوبة ({$quantity})");
            }

            // إنشاء الحجز
            $reservation = MaterialReservation::create([
                'job_id' => $job->id,
                'material_id' => $materialId,
                'inventory_id' => $inventoryId,
                'quantity_reserved' => $quantity,
                'status' => MaterialReservation::STATUS_RESERVED,
                'reserved_by' => Auth::id(),
            ]);

            // تحديث المخزون (حجز الكمية)
            $this->updateInventoryReserved($inventoryId, $materialId, $quantity);

            return $reservation;
        });
    }

    /**
     * استخدام كمية من الحجز
     */
    public function use(MaterialReservation $reservation, float $quantity)
    {
        if ($quantity > $reservation->remaining_quantity) {
            throw new \Exception("الكمية المطلوبة أكبر من المتاح في الحجز");
        }

        $reservation->useQuantity($quantity);

        return $reservation;
    }

    /**
     * إفراج الحجز
     */
    public function release(MaterialReservation $reservation)
    {
        return DB::transaction(function () use ($reservation) {
            $remainingQuantity = $reservation->remaining_quantity;

            if ($remainingQuantity > 0) {
                // إعادة الكمية المتبقية للمخزون
                $this->updateInventoryReserved(
                    $reservation->inventory_id,
                    $reservation->material_id,
                    -$remainingQuantity
                );
            }

            $reservation->release();

            return $reservation;
        });
    }

    /**
     * إفراج جميع الحجوزات لأمر العمل
     */
    public function releaseAll(WorkJob $job)
    {
        $reservations = $job->materialReservations()->active()->get();

        foreach ($reservations as $reservation) {
            $this->release($reservation);
        }

        return $reservations->count();
    }

    /**
     * البحث عن أفضل مخزن للمادة
     */
    protected function findBestInventory(int $branchId, int $materialId, float $quantity)
    {
        // البحث في مخازن الفرع أولاً
        $inventory = Inventory::where('branch_id', $branchId)
            ->whereHas('items', function ($q) use ($materialId, $quantity) {
                $q->where('material_id', $materialId)
                    ->whereRaw('(quantity - COALESCE(reserved_quantity, 0)) >= ?', [$quantity]);
            })
            ->first();

        if ($inventory) {
            return $inventory->id;
        }

        // إذا لم يوجد، ابحث في أي مخزن للشركة
        // يمكن توسيع هذا المنطق حسب الحاجة
        return null;
    }

    /**
     * الحصول على الكمية المتاحة
     */
    public function getAvailableQuantity(int $inventoryId, int $materialId)
    {
        // هذا يعتمد على بنية جدول inventory_items
        // يجب تعديله حسب التصميم الفعلي
        return DB::table('inventory_items')
            ->where('inventory_id', $inventoryId)
            ->where('material_id', $materialId)
            ->selectRaw('COALESCE(quantity, 0) - COALESCE(reserved_quantity, 0) as available')
            ->value('available') ?? 0;
    }

    /**
     * تحديث الكمية المحجوزة في المخزون
     */
    protected function updateInventoryReserved(int $inventoryId, int $materialId, float $quantity)
    {
        DB::table('inventory_items')
            ->where('inventory_id', $inventoryId)
            ->where('material_id', $materialId)
            ->increment('reserved_quantity', $quantity);
    }

    /**
     * الحصول على ملخص حجوزات أمر العمل
     */
    public function getJobReservationsSummary(WorkJob $job)
    {
        return $job->materialReservations()
            ->with(['material', 'inventory'])
            ->get()
            ->map(function ($reservation) {
                return [
                    'material_name' => $reservation->material->name ?? 'Unknown',
                    'inventory_name' => $reservation->inventory->name ?? 'Unknown',
                    'quantity_reserved' => $reservation->quantity_reserved,
                    'quantity_used' => $reservation->quantity_used,
                    'quantity_remaining' => $reservation->remaining_quantity,
                    'usage_percentage' => $reservation->usage_percentage,
                    'status' => $reservation->status_label,
                ];
            });
    }

    /**
     * التحقق من توفر جميع المواد
     */
    public function checkMaterialsAvailability(WorkJob $job, array $materials)
    {
        $results = [];

        foreach ($materials as $material) {
            $available = $this->findBestInventory(
                $job->branch_id,
                $material['material_id'],
                $material['quantity']
            );

            $results[] = [
                'material_id' => $material['material_id'],
                'quantity_needed' => $material['quantity'],
                'available' => $available !== null,
                'inventory_id' => $available,
            ];
        }

        return $results;
    }
}
