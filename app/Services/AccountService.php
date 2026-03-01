<?php

namespace App\Services;

use App\Models\FinancialAccount;
use App\Models\Company;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class AccountService
{
    /**
     * إنشاء حساب جديد
     */
    public function createAccount(array $data): FinancialAccount
    {
        // توليد رقم حساب إذا لم يتم توفيره
        if (empty($data['account_number'])) {
            $data['account_number'] = $this->generateAccountNumber(
                $data['company_code'],
                $data['account_type']
            );
        }

        return FinancialAccount::create($data);
    }

    /**
     * إنشاء حساب لمقاول
     */
    public function createContractorAccount($contractorId, $companyCode, $branchId = null): FinancialAccount
    {
        return $this->createAccount([
            'company_code' => $companyCode,
            'branch_id' => $branchId,
            'account_type' => 'contractor',
            'account_holder_type' => 'App\\Models\\User',
            'account_holder_id' => $contractorId,
            'account_name' => 'حساب مقاول #' . $contractorId,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * إنشاء حساب لمورد
     */
    public function createSupplierAccount($supplierId, $companyCode, $name = null): FinancialAccount
    {
        return $this->createAccount([
            'company_code' => $companyCode,
            'account_type' => 'supplier',
            'account_holder_type' => 'App\\Models\\Supplier',
            'account_holder_id' => $supplierId,
            'account_name' => $name ?? 'حساب مورد #' . $supplierId,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * إنشاء حساب لعميل مباشر
     */
    public function createClientAccount($clientId, $companyCode, $name = null): FinancialAccount
    {
        return $this->createAccount([
            'company_code' => $companyCode,
            'account_type' => 'direct_client',
            'account_holder_type' => 'App\\Models\\Client',
            'account_holder_id' => $clientId,
            'account_name' => $name ?? 'حساب عميل #' . $clientId,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * إنشاء حساب لموظف
     */
    public function createEmployeeAccount($employeeId, $companyCode, $branchId = null): FinancialAccount
    {
        return $this->createAccount([
            'company_code' => $companyCode,
            'branch_id' => $branchId,
            'account_type' => 'employee',
            'account_holder_type' => 'App\\Models\\User',
            'account_holder_id' => $employeeId,
            'account_name' => 'حساب موظف #' . $employeeId,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * الحصول على حساب أو إنشاؤه
     */
    public function getOrCreateAccount($holderType, $holderId, $companyCode, $accountType): FinancialAccount
    {
        $account = FinancialAccount::where('account_holder_type', $holderType)
            ->where('account_holder_id', $holderId)
            ->where('company_code', $companyCode)
            ->first();

        if (!$account) {
            $account = $this->createAccount([
                'company_code' => $companyCode,
                'account_type' => $accountType,
                'account_holder_type' => $holderType,
                'account_holder_id' => $holderId,
                'account_name' => "حساب {$accountType} #{$holderId}",
                'created_by' => auth()->id(),
            ]);
        }

        return $account;
    }

    /**
     * توليد رقم حساب
     */
    public function generateAccountNumber($companyCode, $type): string
    {
        $prefix = match ($type) {
            'contractor' => 'CON',
            'supplier' => 'SUP',
            'direct_client' => 'CLI',
            'employee' => 'EMP',
            'delegate' => 'DEL',
            'expense' => 'EXP',
            'revenue' => 'REV',
            'bank' => 'BNK',
            'cash' => 'CSH',
            default => 'ACC',
        };

        $lastAccount = FinancialAccount::where('company_code', $companyCode)
            ->where('account_type', $type)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastAccount
            ? (int)substr($lastAccount->account_number, -5) + 1
            : 1;

        return "{$prefix}-{$companyCode}-" . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    /**
     * الحصول على ملخص الحسابات
     */
    public function getAccountsSummary($companyCode, $branchId = null): array
    {
        $query = FinancialAccount::where('company_code', $companyCode)
            ->active();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $accounts = $query->get();

        return [
            'total_accounts' => $accounts->count(),
            'total_debit' => $accounts->where('current_balance', '>', 0)->sum('current_balance'),
            'total_credit' => abs($accounts->where('current_balance', '<', 0)->sum('current_balance')),
            'by_type' => $accounts->groupBy('account_type')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total_balance' => $group->sum('current_balance'),
                ];
            }),
        ];
    }

    /**
     * البحث عن حسابات
     */
    public function searchAccounts($companyCode, $term, $type = null, $limit = 20)
    {
        $query = FinancialAccount::where('company_code', $companyCode)
            ->active()
            ->search($term);

        if ($type) {
            $query->where('account_type', $type);
        }

        return $query->limit($limit)->get();
    }

    /**
     * الحسابات ذات الأرصدة المستحقة
     */
    public function getAccountsWithDueBalances($companyCode, $type = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = FinancialAccount::where('company_code', $companyCode)
            ->active()
            ->where('current_balance', '!=', 0);

        if ($type) {
            $query->where('account_type', $type);
        }

        return $query->orderBy('current_balance', 'desc')->get();
    }

    /**
     * تعطيل حساب
     */
    public function deactivateAccount($accountId): bool
    {
        $account = FinancialAccount::findOrFail($accountId);

        // التحقق من عدم وجود رصيد
        if ($account->current_balance != 0) {
            throw new \Exception('لا يمكن تعطيل حساب له رصيد غير صفري');
        }

        $account->is_active = false;
        return $account->save();
    }

    /**
     * إعادة حساب جميع أرصدة شركة
     */
    public function recalculateAllBalances($companyCode): int
    {
        $count = 0;

        FinancialAccount::where('company_code', $companyCode)
            ->chunk(100, function ($accounts) use (&$count) {
                foreach ($accounts as $account) {
                    $account->recalculateBalance();
                    $count++;
                }
            });

        return $count;
    }

    /**
     * تقرير أعمار الديون
     */
    public function getAgingReport($companyCode, $type = null): array
    {
        $query = FinancialAccount::where('company_code', $companyCode)
            ->active()
            ->withDebitBalance()
            ->with(['transactions' => function ($q) {
                $q->orderBy('created_at', 'desc')->limit(1);
            }]);

        if ($type) {
            $query->where('account_type', $type);
        }

        $accounts = $query->get();

        $aging = [
            'current' => [], // 0-30 يوم
            '30_60' => [],   // 31-60 يوم
            '60_90' => [],   // 61-90 يوم
            'over_90' => [], // أكثر من 90 يوم
        ];

        foreach ($accounts as $account) {
            $lastTransaction = $account->transactions->first();
            $days = $lastTransaction
                ? now()->diffInDays($lastTransaction->created_at)
                : 0;

            $category = match (true) {
                $days <= 30 => 'current',
                $days <= 60 => '30_60',
                $days <= 90 => '60_90',
                default => 'over_90',
            };

            $aging[$category][] = [
                'account' => $account,
                'days' => $days,
                'balance' => $account->current_balance,
            ];
        }

        return [
            'aging' => $aging,
            'totals' => [
                'current' => collect($aging['current'])->sum('balance'),
                '30_60' => collect($aging['30_60'])->sum('balance'),
                '60_90' => collect($aging['60_90'])->sum('balance'),
                'over_90' => collect($aging['over_90'])->sum('balance'),
            ],
        ];
    }
}
