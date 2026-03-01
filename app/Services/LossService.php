<?php

namespace App\Services;

use App\Models\WorkLoss;
use App\Models\WorkJob;
use App\Models\WorkShipment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LossService
{
    /**
     * تسجيل خسارة جديدة
     */
    public function report(array $data)
    {
        return DB::transaction(function () use ($data) {
            // معالجة المرفقات
            $attachments = [];
            if (isset($data['attachments']) && is_array($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('losses/' . date('Y/m'), 'public');
                        $attachments[] = [
                            'path' => $path,
                            'name' => $file->getClientOriginalName(),
                            'type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                        ];
                    }
                }
            }

            // إنشاء سجل الخسارة
            $loss = WorkLoss::create([
                'company_code' => $data['company_code'],
                'branch_id' => $data['branch_id'],
                'job_id' => $data['job_id'] ?? null,
                'shipment_id' => $data['shipment_id'] ?? null,
                'vehicle_id' => $data['vehicle_id'] ?? null,
                'loss_type' => $data['loss_type'],
                'quantity_lost' => $data['quantity_lost'] ?? null,
                'estimated_cost' => $data['estimated_cost'] ?? 0,
                'description' => $data['description'],
                'location_description' => $data['location_description'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'attachments' => !empty($attachments) ? $attachments : null,
                'status' => WorkLoss::STATUS_REPORTED,
                'reported_by' => Auth::id(),
            ]);

            return $loss;
        });
    }

    /**
     * بدء التحقيق في الخسارة
     */
    public function startInvestigation(WorkLoss $loss, string $notes = null)
    {
        if ($loss->status !== WorkLoss::STATUS_REPORTED) {
            throw new \Exception('الخسارة ليست في حالة تم الإبلاغ');
        }

        $loss->update([
            'status' => WorkLoss::STATUS_INVESTIGATING,
            'investigation_notes' => $notes,
            'investigated_by' => Auth::id(),
            'investigated_at' => now(),
        ]);

        return $loss;
    }

    /**
     * تحديث ملاحظات التحقيق
     */
    public function updateInvestigation(WorkLoss $loss, string $notes)
    {
        $existingNotes = $loss->investigation_notes ?? '';
        $timestamp = now()->format('Y-m-d H:i');
        $userName = Auth::user()->name ?? 'Unknown';

        $loss->update([
            'investigation_notes' => $existingNotes . "\n[{$timestamp}] ({$userName}): {$notes}",
        ]);

        return $loss;
    }

    /**
     * حل الخسارة
     */
    public function resolve(WorkLoss $loss, array $data)
    {
        if (!in_array($loss->status, [WorkLoss::STATUS_REPORTED, WorkLoss::STATUS_INVESTIGATING])) {
            throw new \Exception('لا يمكن حل خسارة في هذه الحالة');
        }

        $loss->update([
            'status' => WorkLoss::STATUS_RESOLVED,
            'resolution' => $data['resolution'],
            'resolution_date' => $data['resolution_date'] ?? now()->toDateString(),
            'actual_cost' => $data['actual_cost'] ?? $loss->estimated_cost,
        ]);

        return $loss;
    }

    /**
     * إغلاق الخسارة
     */
    public function close(WorkLoss $loss)
    {
        if ($loss->status !== WorkLoss::STATUS_RESOLVED) {
            throw new \Exception('يجب حل الخسارة أولاً قبل الإغلاق');
        }

        $loss->update([
            'status' => WorkLoss::STATUS_CLOSED,
        ]);

        return $loss;
    }

    /**
     * إضافة مرفق
     */
    public function addAttachment(WorkLoss $loss, $file)
    {
        if (!$file->isValid()) {
            throw new \Exception('الملف غير صالح');
        }

        $path = $file->store('losses/' . date('Y/m'), 'public');
        $attachment = [
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_at' => now()->toIso8601String(),
        ];

        $attachments = $loss->attachments ?? [];
        $attachments[] = $attachment;

        $loss->update(['attachments' => $attachments]);

        return $loss;
    }

    /**
     * الحصول على إحصائيات الخسائر
     */
    public function getStatistics($companyCode, $branchId = null, $dateFrom = null, $dateTo = null)
    {
        $query = WorkLoss::company($companyCode);

        if ($branchId) {
            $query->branch($branchId);
        }

        if ($dateFrom && $dateTo) {
            $query->reportedBetween($dateFrom, $dateTo);
        }

        $losses = $query->get();

        return [
            'total_count' => $losses->count(),
            'open_count' => $losses->where('status', WorkLoss::STATUS_REPORTED)->count() +
                $losses->where('status', WorkLoss::STATUS_INVESTIGATING)->count(),
            'resolved_count' => $losses->where('status', WorkLoss::STATUS_RESOLVED)->count() +
                $losses->where('status', WorkLoss::STATUS_CLOSED)->count(),
            'total_quantity_lost' => $losses->sum('quantity_lost'),
            'total_estimated_cost' => $losses->sum('estimated_cost'),
            'total_actual_cost' => $losses->sum('actual_cost'),
            'by_type' => $losses->groupBy('loss_type')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'quantity' => $group->sum('quantity_lost'),
                    'cost' => $group->sum('actual_cost') ?: $group->sum('estimated_cost'),
                ];
            }),
            'by_status' => $losses->groupBy('status')->map->count(),
        ];
    }

    /**
     * الحصول على الخسائر المفتوحة
     */
    public function getOpenLosses($companyCode, $branchId = null)
    {
        $query = WorkLoss::company($companyCode)
            ->open()
            ->with(['job', 'shipment', 'vehicle', 'reportedBy'])
            ->orderBy('reported_at', 'desc');

        if ($branchId) {
            $query->branch($branchId);
        }

        return $query->get();
    }

    /**
     * الحصول على تقرير الخسائر الشهري
     */
    public function getMonthlyReport($companyCode, $year, $month, $branchId = null)
    {
        $startDate = "{$year}-{$month}-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $query = WorkLoss::company($companyCode)
            ->reportedBetween($startDate, $endDate);

        if ($branchId) {
            $query->branch($branchId);
        }

        $losses = $query->get();

        return [
            'period' => "{$year}/{$month}",
            'summary' => $this->getStatistics($companyCode, $branchId, $startDate, $endDate),
            'details' => $losses->map(function ($loss) {
                return [
                    'id' => $loss->id,
                    'date' => $loss->reported_at->format('Y-m-d'),
                    'type' => $loss->type_label,
                    'description' => $loss->description,
                    'quantity' => $loss->quantity_lost,
                    'cost' => $loss->total_cost,
                    'status' => $loss->status_label,
                    'job' => $loss->job?->job_number,
                ];
            }),
        ];
    }
}
