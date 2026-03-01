<?php

namespace App\Services;

use App\Models\Contractor;
use App\Models\ContractorAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ContractorService
{
    /**
     * الحصول على قائمة المقاولين مع الفلترة والبحث
     */
    public function getContractors(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Contractor::query()
            ->forCompany(auth()->user()->company_code)
            ->with(['account', 'branch']);

        // البحث
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // فلتر الحالة
        if (!empty($filters['status'])) {
            match ($filters['status']) {
                'active' => $query->active(),
                'inactive' => $query->inactive(),
                'blocked' => $query->blocked(),
                default => null,
            };
        }

        // فلتر التصنيف
        if (!empty($filters['classification'])) {
            $query->classification($filters['classification']);
        }

        // فلتر الفرع
        if (!empty($filters['branch_id'])) {
            $query->forBranch($filters['branch_id']);
        }

        // فلتر النوع
        if (!empty($filters['contractor_type'])) {
            $query->where('contractor_type', $filters['contractor_type']);
        }

        // الترتيب
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * إنشاء مقاول جديد
     */
    public function createContractor(array $data): Contractor
    {
        return DB::transaction(function () use ($data) {
            // إضافة بيانات الشركة والمستخدم
            $data['company_code'] = auth()->user()->company_code;
            $data['branch_id'] = $data['branch_id'] ?? auth()->user()->branch_id;
            $data['user_id'] = auth()->id();

            // توليد كود المقاول
            $data['code'] = Contractor::generateCode($data['company_code']);

            // إنشاء المقاول
            $contractor = Contractor::create($data);

            // إنشاء حساب مالي للمقاول
            $this->createContractorAccount($contractor);

            Log::info('تم إنشاء مقاول جديد', [
                'contractor_id' => $contractor->id,
                'code' => $contractor->code,
                'name' => $contractor->contractor_name,
                'created_by' => auth()->id(),
            ]);

            return $contractor->fresh(['account', 'branch']);
        });
    }

    /**
     * تحديث بيانات مقاول
     */
    public function updateContractor(Contractor $contractor, array $data): Contractor
    {
        return DB::transaction(function () use ($contractor, $data) {
            // التحقق من التغييرات المهمة
            $hadStatusChange = isset($data['status']) && $data['status'] !== $contractor->status;

            // تحديث البيانات
            $contractor->update($data);

            // معالجة تغيير الحالة
            if ($hadStatusChange) {
                $this->handleStatusChange($contractor, $data['status'], $data['block_reason'] ?? null);
            }

            Log::info('تم تحديث بيانات المقاول', [
                'contractor_id' => $contractor->id,
                'updated_by' => auth()->id(),
            ]);

            return $contractor->fresh(['account', 'branch']);
        });
    }

    /**
     * حذف مقاول (Soft Delete)
     */
    public function deleteContractor(Contractor $contractor): bool
    {
        return DB::transaction(function () use ($contractor) {
            // التحقق من عدم وجود طلبات نشطة
            if ($contractor->workOrders()->whereNotIn('status', ['completed', 'cancelled'])->exists()) {
                throw new \Exception('لا يمكن حذف المقاول لوجود طلبات نشطة');
            }

            // التحقق من عدم وجود رصيد
            if ($contractor->account && $contractor->account->current_balance != 0) {
                throw new \Exception('لا يمكن حذف المقاول لوجود رصيد مالي');
            }

            $contractor->delete();

            Log::info('تم حذف مقاول', [
                'contractor_id' => $contractor->id,
                'deleted_by' => auth()->id(),
            ]);

            return true;
        });
    }

    /**
     * حظر مقاول
     */
    public function blockContractor(Contractor $contractor, string $reason): Contractor
    {
        $contractor->block($reason);

        Log::warning('تم حظر مقاول', [
            'contractor_id' => $contractor->id,
            'reason' => $reason,
            'blocked_by' => auth()->id(),
        ]);

        return $contractor;
    }

    /**
     * رفع الحظر عن مقاول
     */
    public function unblockContractor(Contractor $contractor): Contractor
    {
        $contractor->unblock();

        Log::info('تم رفع الحظر عن مقاول', [
            'contractor_id' => $contractor->id,
            'unblocked_by' => auth()->id(),
        ]);

        return $contractor;
    }

    /**
     * إنشاء حساب مالي للمقاول
     */
    protected function createContractorAccount(Contractor $contractor): ContractorAccount
    {
        return ContractorAccount::create([
            'contractor_id' => $contractor->id,
            'company_code' => $contractor->company_code,
            'account_number' => ContractorAccount::generateAccountNumber($contractor->company_code),
            'account_name' => $contractor->contractor_name,
            'credit_limit' => $contractor->credit_limit ?? 0,
            'current_balance' => 0,
            'is_active' => true,
        ]);
    }

    /**
     * معالجة تغيير الحالة
     */
    protected function handleStatusChange(Contractor $contractor, string $newStatus, ?string $reason = null): void
    {
        match ($newStatus) {
            'blocked' => $contractor->block($reason ?? 'تم الحظر من قبل الإدارة'),
            'active' => $contractor->activate(),
            'inactive' => $contractor->deactivate(),
            default => null,
        };
    }

    /**
     * الحصول على إحصائيات المقاولين
     */
    public function getStatistics(string $companyCode): array
    {
        $contractors = Contractor::forCompany($companyCode);

        return [
            'total' => $contractors->count(),
            'active' => $contractors->clone()->active()->count(),
            'inactive' => $contractors->clone()->inactive()->count(),
            'blocked' => $contractors->clone()->blocked()->count(),
            'by_classification' => [
                'A' => $contractors->clone()->classification('A')->count(),
                'B' => $contractors->clone()->classification('B')->count(),
                'C' => $contractors->clone()->classification('C')->count(),
                'D' => $contractors->clone()->classification('D')->count(),
            ],
            'total_balance' => ContractorAccount::forCompany($companyCode)->sum('current_balance'),
            'total_credit_limit' => ContractorAccount::forCompany($companyCode)->sum('credit_limit'),
        ];
    }

    /**
     * البحث السريع عن مقاولين (للـ Autocomplete)
     */
    public function quickSearch(string $query, int $limit = 10): Collection
    {
        return Contractor::query()
            ->forCompany(auth()->user()->company_code)
            ->active()
            ->search($query)
            ->select(['id', 'code', 'contractor_name', 'phone'])
            ->limit($limit)
            ->get();
    }

    /**
     * تحديث تصنيف المقاول بناءً على الأداء
     */
    public function updateClassification(Contractor $contractor): string
    {
        $stats = [
            'total_orders' => $contractor->total_orders,
            'completed_orders' => $contractor->completed_orders,
            'total_purchases' => $contractor->total_purchases,
            'payment_delays' => $this->calculatePaymentDelays($contractor),
        ];

        $newClassification = $this->calculateClassification($stats);

        if ($newClassification !== $contractor->classification) {
            $contractor->update(['classification' => $newClassification]);

            Log::info('تم تحديث تصنيف المقاول', [
                'contractor_id' => $contractor->id,
                'old_classification' => $contractor->classification,
                'new_classification' => $newClassification,
            ]);
        }

        return $newClassification;
    }

    /**
     * حساب تأخيرات الدفع
     */
    protected function calculatePaymentDelays(Contractor $contractor): int
    {
        if (!$contractor->account) {
            return 0;
        }

        return $contractor->account->invoices()
            ->where('status', 'overdue')
            ->count();
    }

    /**
     * حساب التصنيف
     */
    protected function calculateClassification(array $stats): string
    {
        $score = 0;

        // نقاط حسب عدد الطلبات
        if ($stats['total_orders'] >= 100) $score += 30;
        elseif ($stats['total_orders'] >= 50) $score += 20;
        elseif ($stats['total_orders'] >= 20) $score += 10;

        // نقاط حسب نسبة الإنجاز
        if ($stats['total_orders'] > 0) {
            $completionRate = ($stats['completed_orders'] / $stats['total_orders']) * 100;
            if ($completionRate >= 90) $score += 30;
            elseif ($completionRate >= 70) $score += 20;
            elseif ($completionRate >= 50) $score += 10;
        }

        // نقاط حسب المشتريات
        if ($stats['total_purchases'] >= 1000000) $score += 20;
        elseif ($stats['total_purchases'] >= 500000) $score += 15;
        elseif ($stats['total_purchases'] >= 100000) $score += 10;

        // خصم حسب التأخيرات
        $score -= ($stats['payment_delays'] * 5);

        // تحديد التصنيف
        if ($score >= 70) return 'A';
        if ($score >= 50) return 'B';
        if ($score >= 30) return 'C';
        return 'D';
    }
}
