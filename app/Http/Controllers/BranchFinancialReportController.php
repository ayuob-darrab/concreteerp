<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CarMaintenance;
use App\Models\CompanyPaymentCard;
use App\Models\CompanyPaymentCardTransaction;
use App\Models\CustomerPaymentRecord;
use App\Models\InventoryLoss;
use App\Models\SupplierPayment;
use App\Models\WorkLoss;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * تقرير مالي لمدير الفرع: بطاقات الدفع، الإيداعات، السحوبات، وملخصات ذات صلة.
 */
class BranchFinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $companyCode = $user->company_code;
        $branchId = null;
        $reportBackUrl = url('/home');

        if ($user->isBranchManager()) {
            if (!$user->branch_id) {
                abort(403, 'لا يوجد فرع مرتبط بحسابك.');
            }
            $branchId = (int) $user->branch_id;
        } elseif (method_exists($user, 'isAccountantEmployee') && $user->isAccountantEmployee()) {
            if (!$user->branch_id) {
                abort(403, 'لا يوجد فرع مرتبط بحسابك.');
            }
            // المحاسب يرى بيانات فرعه فقط حتى لو تم تمرير branch_id في الرابط.
            $branchId = (int) $user->branch_id;
            $reportBackUrl = route('branch.payments.branches-report');
        } elseif ($user->isCompanyManager()) {
            $requested = (int) $request->query('branch_id', 0);
            if ($requested < 1) {
                return redirect()
                    ->route('branch.payments.branches-report')
                    ->with('info', 'اختر فرعاً من «تقرير الفروع» لفتح التقرير المالي التفصيلي.');
            }
            $branch = Branch::where('id', $requested)->where('company_code', $companyCode)->first();
            if (!$branch) {
                abort(404, 'الفرع غير موجود أو لا يتبع شركتك.');
            }
            $branchId = (int) $branch->id;
            $reportBackUrl = route('branch.payments.branches-report');
        } elseif ($user->isSuperAdmin()) {
            $requested = (int) $request->query('branch_id', 0);
            if ($requested < 1) {
                abort(403, 'حدد معرف الفرع (branch_id) لعرض التقرير.');
            }
            $branch = Branch::where('id', $requested)->first();
            if (!$branch) {
                abort(404);
            }
            $branchId = (int) $branch->id;
            $companyCode = $branch->company_code;
            $reportBackUrl = url('/home');
        } else {
            abort(403, 'غير مصرح لك بعرض هذا التقرير.');
        }

        $from = $request->filled('from_date') ? $request->from_date : null;
        $to = $request->filled('to_date') ? $request->to_date : null;

        $branch = Branch::where('id', $branchId)->where('company_code', $companyCode)->first();

        $cards = CompanyPaymentCard::query()
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->orderBy('card_name')
            ->get();

        $cardIds = $cards->pluck('id')->all();

        $transactionsInPeriod = function () use ($companyCode, $branchId, $from, $to) {
            return CompanyPaymentCardTransaction::query()
                ->where('company_code', $companyCode)
                ->where('branch_id', $branchId)
                ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to));
        };

        $totalDeposits = (float) $transactionsInPeriod()->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = (float) $transactionsInPeriod()->where('type', 'withdrawal')->sum('amount');

        $depositsByRef = $transactionsInPeriod()
            ->where('type', 'deposit')
            ->select('reference_type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('reference_type')
            ->orderByDesc('total')
            ->get();

        $withdrawalsByRef = $transactionsInPeriod()
            ->where('type', 'withdrawal')
            ->select('reference_type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('reference_type')
            ->orderByDesc('total')
            ->get();

        $perCardPeriod = collect();
        if ($cardIds !== []) {
            $perCardPeriod = CompanyPaymentCardTransaction::query()
                ->whereIn('company_payment_card_id', $cardIds)
                ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
                ->select(
                    'company_payment_card_id',
                    'type',
                    DB::raw('SUM(amount) as total'),
                    DB::raw('COUNT(*) as cnt')
                )
                ->groupBy('company_payment_card_id', 'type')
                ->get()
                ->groupBy('company_payment_card_id');
        }

        $cardsWithPeriod = $cards->map(function (CompanyPaymentCard $card) use ($perCardPeriod) {
            $rows = $perCardPeriod->get($card->id, collect());
            $dep = $rows->firstWhere('type', 'deposit');
            $wdr = $rows->firstWhere('type', 'withdrawal');

            return [
                'card' => $card,
                'period_deposits' => (float) ($dep->total ?? 0),
                'period_withdrawals' => (float) ($wdr->total ?? 0),
                'period_tx_count' => (int) (($dep->cnt ?? 0) + ($wdr->cnt ?? 0)),
            ];
        });

        $recentTransactions = CompanyPaymentCardTransaction::query()
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->with(['paymentCard', 'creator'])
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->orderByDesc('created_at')
            ->limit(150)
            ->get();

        $customerCollections = CustomerPaymentRecord::query()
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as cnt')
            ->groupBy('payment_method')
            ->get();

        $supplierPaymentsTotal = (float) SupplierPayment::query()
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->sum('amount');

        $maintenanceCostsTotal = (float) CarMaintenance::query()
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->where('status', 'completed')
            ->when($from, fn ($q) => $q->whereDate('maintenance_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('maintenance_date', '<=', $to))
            ->sum('total_cost');

        $inventoryLossesQuery = InventoryLoss::query()
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->when($from, fn ($q) => $q->whereDate('reported_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('reported_at', '<=', $to));

        $inventoryLossesTotal = (float) (clone $inventoryLossesQuery)->sum('total_cost');
        $inventoryLosses = (clone $inventoryLossesQuery)
            ->with(['creator'])
            ->orderBy('reported_at', 'desc')
            ->limit(200)
            ->get();

        $shipmentConcreteLossesQuery = WorkLoss::query()
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->whereNotNull('shipment_id')
            ->when($from, fn ($q) => $q->whereDate('reported_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('reported_at', '<=', $to));

        $shipmentConcreteLossesTotal = (float) (clone $shipmentConcreteLossesQuery)
            ->selectRaw('COALESCE(SUM(COALESCE(actual_cost, estimated_cost)), 0) as aggregate')
            ->value('aggregate');

        $shipmentConcreteLosses = (clone $shipmentConcreteLossesQuery)
            ->with(['job.concreteType', 'shipment', 'reportedBy'])
            ->orderByDesc('reported_at')
            ->limit(200)
            ->get();

        $summary = [
            'cards_total' => $cards->count(),
            'cards_active' => $cards->where('is_active', true)->count(),
            'total_current_balance' => (float) $cards->sum('current_balance'),
            'total_opening_balance' => (float) $cards->sum('opening_balance'),
            'period_deposits' => (float) $totalDeposits,
            'period_withdrawals' => (float) $totalWithdrawals,
            'net_card_movement' => (float) $totalDeposits - (float) $totalWithdrawals,
            'supplier_payments' => $supplierPaymentsTotal,
            'maintenance_costs' => $maintenanceCostsTotal,
            'customer_collections_total' => (float) $customerCollections->sum('total'),
            'inventory_losses_total' => $inventoryLossesTotal,
            'shipment_concrete_losses_total' => $shipmentConcreteLossesTotal,
        ];

        return view('company-branch.financial-report', [
            'branch' => $branch,
            'from' => $from,
            'to' => $to,
            'summary' => $summary,
            'cardsWithPeriod' => $cardsWithPeriod,
            'depositsByRef' => $depositsByRef,
            'withdrawalsByRef' => $withdrawalsByRef,
            'recentTransactions' => $recentTransactions,
            'customerCollections' => $customerCollections,
            'inventoryLosses' => $inventoryLosses,
            'shipmentConcreteLosses' => $shipmentConcreteLosses,
            'reportBackUrl' => $reportBackUrl,
            'financialBranchId' => $branchId,
        ]);
    }
}
