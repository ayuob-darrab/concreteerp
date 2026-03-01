<?php

namespace App\Http\Controllers;

use App\Models\DailyCashSummary;
use App\Services\CashSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashSummaryController extends Controller
{
    protected $cashSummaryService;

    public function __construct(CashSummaryService $cashSummaryService)
    {
        $this->cashSummaryService = $cashSummaryService;
    }

    /**
     * Show daily cash summary
     */
    public function daily(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;
        $branchId = $request->branch_id ?? session('current_branch_id');

        if (!$branchId) {
            return redirect()->route('home')->with('error', 'الرجاء تحديد الفرع أولاً');
        }

        $date = $request->date ?? today()->format('Y-m-d');

        // Get or create summary
        $summary = $date == today()->format('Y-m-d')
            ? $this->cashSummaryService->getTodaySummary($companyCode, $branchId)
            : $this->cashSummaryService->getSummaryForDate($companyCode, $branchId, $date);

        // Get details
        $details = $this->cashSummaryService->getDailyDetails($companyCode, $branchId, $date);

        // Get unclosed days warning
        $unclosedDays = $this->cashSummaryService->getUnclosedDays($companyCode, $branchId);

        $branches = $user->branches ?? collect();

        return view('financial.cash.daily', compact('summary', 'details', 'unclosedDays', 'branches', 'date'));
    }

    /**
     * Close day
     */
    public function close(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;
        $branchId = $request->branch_id ?? session('current_branch_id');

        if (!$branchId) {
            return back()->with('error', 'الرجاء تحديد الفرع');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $this->cashSummaryService->closeDay($companyCode, $branchId, $validated['notes'] ?? null);

            return back()->with('success', 'تم إقفال اليوم بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show period report
     */
    public function periodReport(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;
        $branchId = $request->branch_id ?? session('current_branch_id');

        $fromDate = $request->from_date ?? now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        $report = [];
        if ($branchId) {
            $report = $this->cashSummaryService->getPeriodSummary($companyCode, $branchId, $fromDate, $toDate);
        }

        $branches = $user->branches ?? collect();

        return view('financial.cash.period-report', compact('report', 'branches', 'fromDate', 'toDate'));
    }

    /**
     * Show monthly report
     */
    public function monthlyReport(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;
        $branchId = $request->branch_id ?? session('current_branch_id');

        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;

        $report = [];
        if ($branchId) {
            $report = $this->cashSummaryService->getMonthlyReport($companyCode, $branchId, $year, $month);
        }

        $branches = $user->branches ?? collect();

        return view('financial.cash.monthly-report', compact('report', 'branches', 'year', 'month'));
    }

    /**
     * Reopen day (admin)
     */
    public function reopen(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'date' => 'required|date',
        ]);

        try {
            $this->cashSummaryService->reopenDay($companyCode, $validated['branch_id'], $validated['date']);

            return back()->with('success', 'تم إعادة فتح اليوم');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Recalculate summary
     */
    public function recalculate(Request $request, DailyCashSummary $summary)
    {
        $user = Auth::user();

        if ($summary->company_code !== $user->company_code) {
            abort(403);
        }

        $this->cashSummaryService->recalculate($summary);

        return back()->with('success', 'تم إعادة حساب ملخص اليوم');
    }

    /**
     * Print daily summary
     */
    public function print(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;
        $branchId = $request->branch_id ?? session('current_branch_id');
        $date = $request->date ?? today()->format('Y-m-d');

        $summary = $this->cashSummaryService->getSummaryForDate($companyCode, $branchId, $date);
        $details = $this->cashSummaryService->getDailyDetails($companyCode, $branchId, $date);

        return view('financial.prints.daily-summary', compact('summary', 'details', 'date'));
    }
}
