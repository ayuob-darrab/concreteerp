<?php

namespace App\Services;

use App\Models\Check;
use App\Models\CheckStatusLog;
use App\Models\ContractorAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CheckService
{
    /**
     * الحصول على قائمة الشيكات
     */
    public function getChecks(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Check::query()
            ->forCompany(auth()->user()->company_code)
            ->with(['account.contractor', 'invoice', 'creator', 'statusLogs']);

        // البحث
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('check_number', 'like', "%{$filters['search']}%")
                    ->orWhere('drawer_name', 'like', "%{$filters['search']}%")
                    ->orWhere('beneficiary_name', 'like', "%{$filters['search']}%")
                    ->orWhere('bank_name', 'like', "%{$filters['search']}%");
            });
        }

        // فلتر النوع
        if (!empty($filters['type'])) {
            $filters['type'] === 'incoming'
                ? $query->incoming()
                : $query->outgoing();
        }

        // فلتر الحالة
        if (!empty($filters['status'])) {
            $query->status($filters['status']);
        }

        // فلتر الفرع
        if (!empty($filters['branch_id'])) {
            $query->forBranch($filters['branch_id']);
        }

        // فلتر تاريخ الاستحقاق
        if (!empty($filters['due_from']) && !empty($filters['due_to'])) {
            $query->whereBetween('due_date', [$filters['due_from'], $filters['due_to']]);
        }

        // الشيكات المستحقة خلال أيام
        if (!empty($filters['due_within_days'])) {
            $query->dueWithinDays((int) $filters['due_within_days']);
        }

        // الشيكات المتأخرة
        if (!empty($filters['overdue'])) {
            $query->overdue();
        }

        // الترتيب
        $sortField = $filters['sort_by'] ?? 'due_date';
        $sortDirection = $filters['sort_direction'] ?? 'asc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * إنشاء شيك جديد
     */
    public function createCheck(array $data): Check
    {
        return DB::transaction(function () use ($data) {
            $data['company_code'] = auth()->user()->company_code;
            $data['branch_id'] = $data['branch_id'] ?? auth()->user()->branch_id;
            $data['created_by'] = auth()->id();
            $data['status'] = Check::STATUS_PENDING;

            // رفع صور الشيك
            if (isset($data['image_front']) && $data['image_front']) {
                $data['image_front'] = $this->uploadCheckImage($data['image_front'], 'front');
            }
            if (isset($data['image_back']) && $data['image_back']) {
                $data['image_back'] = $this->uploadCheckImage($data['image_back'], 'back');
            }

            $check = Check::create($data);

            // تسجيل الحالة الأولية
            $check->statusLogs()->create([
                'status' => Check::STATUS_PENDING,
                'notes' => 'تم إنشاء الشيك',
                'created_by' => auth()->id(),
            ]);

            Log::info('تم إنشاء شيك جديد', [
                'check_id' => $check->id,
                'check_number' => $check->check_number,
                'amount' => $check->amount,
                'type' => $check->check_type,
                'created_by' => auth()->id(),
            ]);

            return $check->fresh(['account.contractor', 'statusLogs']);
        });
    }

    /**
     * إيداع شيك
     */
    public function depositCheck(Check $check): Check
    {
        if (!$check->deposit()) {
            throw new \Exception('لا يمكن إيداع هذا الشيك');
        }

        Log::info('تم إيداع الشيك', [
            'check_id' => $check->id,
            'check_number' => $check->check_number,
            'deposited_by' => auth()->id(),
        ]);

        return $check->fresh();
    }

    /**
     * تحصيل شيك
     */
    public function collectCheck(Check $check): Check
    {
        if (!$check->collect()) {
            throw new \Exception('لا يمكن تحصيل هذا الشيك');
        }

        Log::info('تم تحصيل الشيك', [
            'check_id' => $check->id,
            'check_number' => $check->check_number,
            'amount' => $check->amount,
            'collected_by' => auth()->id(),
        ]);

        return $check->fresh();
    }

    /**
     * رفض شيك
     */
    public function rejectCheck(Check $check, string $reason): Check
    {
        $check->reject($reason);

        Log::warning('تم رفض الشيك', [
            'check_id' => $check->id,
            'check_number' => $check->check_number,
            'reason' => $reason,
            'rejected_by' => auth()->id(),
        ]);

        // إشعار المعنيين
        // TODO: إرسال إشعار

        return $check->fresh();
    }

    /**
     * إرجاع شيك
     */
    public function returnCheck(Check $check, string $reason = null): Check
    {
        $check->returnCheck($reason);

        Log::info('تم إرجاع الشيك', [
            'check_id' => $check->id,
            'check_number' => $check->check_number,
            'reason' => $reason,
            'returned_by' => auth()->id(),
        ]);

        return $check->fresh();
    }

    /**
     * إلغاء شيك
     */
    public function cancelCheck(Check $check, string $reason = null): Check
    {
        $check->cancel($reason);

        Log::warning('تم إلغاء الشيك', [
            'check_id' => $check->id,
            'check_number' => $check->check_number,
            'reason' => $reason,
            'cancelled_by' => auth()->id(),
        ]);

        return $check->fresh();
    }

    /**
     * تظهير شيك
     */
    public function endorseCheck(Check $check, string $newHolder, string $notes = null): Check
    {
        if (!$check->endorse($newHolder, $notes)) {
            throw new \Exception('لا يمكن تظهير هذا الشيك');
        }

        Log::info('تم تظهير الشيك', [
            'check_id' => $check->id,
            'check_number' => $check->check_number,
            'new_holder' => $newHolder,
            'endorsed_by' => auth()->id(),
        ]);

        return $check->fresh();
    }

    /**
     * رفع صورة الشيك
     */
    protected function uploadCheckImage($file, string $side): string
    {
        $filename = uniqid("check_{$side}_") . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('checks', $filename, 'public');
        return $path;
    }

    /**
     * الحصول على الشيكات المستحقة اليوم
     */
    public function getDueToday(string $companyCode): Collection
    {
        return Check::forCompany($companyCode)
            ->dueToday()
            ->with(['account.contractor'])
            ->get();
    }

    /**
     * الحصول على الشيكات المستحقة خلال فترة
     */
    public function getDueWithinDays(string $companyCode, int $days): Collection
    {
        return Check::forCompany($companyCode)
            ->dueWithinDays($days)
            ->with(['account.contractor'])
            ->orderBy('due_date')
            ->get();
    }

    /**
     * الحصول على الشيكات المتأخرة
     */
    public function getOverdue(string $companyCode): Collection
    {
        return Check::forCompany($companyCode)
            ->overdue()
            ->with(['account.contractor'])
            ->orderBy('due_date')
            ->get();
    }

    /**
     * الحصول على إحصائيات الشيكات
     */
    public function getStatistics(string $companyCode): array
    {
        $incoming = Check::forCompany($companyCode)->incoming();
        $outgoing = Check::forCompany($companyCode)->outgoing();

        return [
            'incoming' => [
                'total' => $incoming->count(),
                'total_amount' => $incoming->sum('amount'),
                'pending' => (clone $incoming)->status(Check::STATUS_PENDING)->sum('amount'),
                'deposited' => (clone $incoming)->status(Check::STATUS_DEPOSITED)->sum('amount'),
                'collected' => (clone $incoming)->status(Check::STATUS_COLLECTED)->sum('amount'),
                'rejected' => (clone $incoming)->status(Check::STATUS_REJECTED)->sum('amount'),
            ],
            'outgoing' => [
                'total' => $outgoing->count(),
                'total_amount' => $outgoing->sum('amount'),
                'pending' => (clone $outgoing)->status(Check::STATUS_PENDING)->sum('amount'),
                'collected' => (clone $outgoing)->status(Check::STATUS_COLLECTED)->sum('amount'),
            ],
            'due_today' => Check::forCompany($companyCode)->dueToday()->count(),
            'due_this_week' => Check::forCompany($companyCode)->dueWithinDays(7)->count(),
            'overdue' => Check::forCompany($companyCode)->overdue()->count(),
            'overdue_amount' => Check::forCompany($companyCode)->overdue()->sum('amount'),
        ];
    }

    /**
     * تصدير الشيكات المستحقة
     */
    public function exportDueChecks(string $companyCode, int $days = 30): Collection
    {
        return Check::forCompany($companyCode)
            ->dueWithinDays($days)
            ->with(['account.contractor', 'invoice'])
            ->orderBy('due_date')
            ->get()
            ->map(function ($check) {
                return [
                    'رقم الشيك' => $check->check_number,
                    'البنك' => $check->bank_name,
                    'الساحب' => $check->drawer_name,
                    'المستفيد' => $check->beneficiary_name,
                    'المبلغ' => $check->amount,
                    'تاريخ الاستحقاق' => $check->due_date->format('Y-m-d'),
                    'أيام للاستحقاق' => $check->days_until_due,
                    'الحالة' => $check->status_label,
                ];
            });
    }
}
