<?php

namespace App\Services;

use App\Models\DailyCashSummary;
use App\Models\PaymentReceipt;
use App\Models\PaymentVoucher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class CashSummaryService
{
    /**
     * Open day
     */
    public function openDay(string $companyCode, int $branchId, string $currencyCode = 'IQD'): DailyCashSummary
    {
        return DailyCashSummary::getOrCreateToday($companyCode, $branchId, $currencyCode);
    }

    /**
     * Close day
     */
    public function closeDay(
        string $companyCode,
        int $branchId,
        ?string $notes = null,
        string $currencyCode = 'IQD'
    ): DailyCashSummary {
        $summary = DailyCashSummary::getOrCreateToday($companyCode, $branchId, $currencyCode);

        if (!$summary->is_open) {
            throw new \Exception('اليوم مغلق مسبقاً');
        }

        // Recalculate before closing
        $summary->recalculate();

        // Close
        $summary->close(Auth::id(), $notes);

        return $summary;
    }

    /**
     * Get today's summary
     */
    public function getTodaySummary(string $companyCode, int $branchId, string $currencyCode = 'IQD'): DailyCashSummary
    {
        return DailyCashSummary::getOrCreateToday($companyCode, $branchId, $currencyCode);
    }

    /**
     * Get summary for a specific date
     */
    public function getSummaryForDate(
        string $companyCode,
        int $branchId,
        string $date,
        string $currencyCode = 'IQD'
    ): ?DailyCashSummary {
        return DailyCashSummary::where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->where('currency_code', $currencyCode)
            ->whereDate('summary_date', $date)
            ->first();
    }

    /**
     * Get daily details (receipts and vouchers)
     */
    public function getDailyDetails(string $companyCode, int $branchId, string $date): array
    {
        $receipts = PaymentReceipt::where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->whereDate('received_at', $date)
            ->confirmed()
            ->orderBy('received_at', 'asc')
            ->get();

        $vouchers = PaymentVoucher::where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->whereDate('paid_at', $date)
            ->paid()
            ->orderBy('paid_at', 'asc')
            ->get();

        return [
            'receipts' => $receipts,
            'vouchers' => $vouchers,
            'total_receipts' => $receipts->sum('amount_in_default'),
            'total_vouchers' => $vouchers->sum('amount_in_default'),
            'net_change' => $receipts->sum('amount_in_default') - $vouchers->sum('amount_in_default'),
        ];
    }

    /**
     * Get period summary
     */
    public function getPeriodSummary(
        string $companyCode,
        int $branchId,
        string $fromDate,
        string $toDate,
        string $currencyCode = 'IQD'
    ): array {
        $summaries = DailyCashSummary::where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->where('currency_code', $currencyCode)
            ->whereBetween('summary_date', [$fromDate, $toDate])
            ->orderBy('summary_date', 'asc')
            ->get();

        return [
            'summaries' => $summaries,
            'total_receipts' => $summaries->sum('total_receipts'),
            'total_payments' => $summaries->sum('total_payments'),
            'net_change' => $summaries->sum('total_receipts') - $summaries->sum('total_payments'),
            'days_count' => $summaries->count(),
            'open_days' => $summaries->where('status', DailyCashSummary::STATUS_OPEN)->count(),
            'closed_days' => $summaries->where('status', DailyCashSummary::STATUS_CLOSED)->count(),
        ];
    }

    /**
     * Get monthly report
     */
    public function getMonthlyReport(
        string $companyCode,
        int $branchId,
        int $year,
        int $month,
        string $currencyCode = 'IQD'
    ): array {
        $fromDate = "{$year}-{$month}-01";
        $toDate = date('Y-m-t', strtotime($fromDate));

        return $this->getPeriodSummary($companyCode, $branchId, $fromDate, $toDate, $currencyCode);
    }

    /**
     * Check if day is closed
     */
    public function isDayClosed(string $companyCode, int $branchId, string $date, string $currencyCode = 'IQD'): bool
    {
        $summary = $this->getSummaryForDate($companyCode, $branchId, $date, $currencyCode);
        return $summary && !$summary->is_open;
    }

    /**
     * Reopen day (admin only)
     */
    public function reopenDay(
        string $companyCode,
        int $branchId,
        string $date,
        string $currencyCode = 'IQD'
    ): DailyCashSummary {
        $summary = $this->getSummaryForDate($companyCode, $branchId, $date, $currencyCode);

        if (!$summary) {
            throw new \Exception('لا يوجد ملخص لهذا اليوم');
        }

        if ($summary->is_open) {
            throw new \Exception('اليوم مفتوح مسبقاً');
        }

        $summary->update([
            'status' => DailyCashSummary::STATUS_OPEN,
            'closed_by' => null,
            'closed_at' => null,
        ]);

        return $summary;
    }

    /**
     * Recalculate summary
     */
    public function recalculate(DailyCashSummary $summary): DailyCashSummary
    {
        $summary->recalculate();
        return $summary;
    }

    /**
     * Get unclosed days
     */
    public function getUnclosedDays(string $companyCode, ?int $branchId = null): Collection
    {
        $query = DailyCashSummary::where('company_code', $companyCode)
            ->open()
            ->whereDate('summary_date', '<', today())
            ->orderBy('summary_date', 'asc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }
}
