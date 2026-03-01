<?php

namespace App\Services;

use App\Models\PaymentVoucher;
use App\Models\FinancialTransaction;
use App\Models\DailyCashSummary;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VoucherService
{
    protected $currencyService;
    protected $accountBalanceService;

    public function __construct(CurrencyService $currencyService, AccountBalanceService $accountBalanceService)
    {
        $this->currencyService = $currencyService;
        $this->accountBalanceService = $accountBalanceService;
    }

    /**
     * Create a new payment voucher
     */
    public function create(array $data): PaymentVoucher
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();
            $companyCode = $user->company_code;
            $branchId = $data['branch_id'];

            // Generate voucher number
            $voucherNumber = PaymentVoucher::generateVoucherNumber($companyCode, $branchId);

            // Calculate amount in default currency
            $currency = $data['currency_code'] ?? 'IQD';
            $exchangeRate = Currency::getByCode($currency)?->exchange_rate ?? 1;
            $amountInDefault = $data['amount'] * $exchangeRate;

            // Generate amount in words
            $amountInWords = $this->currencyService->toWords($data['amount'], $currency);

            // Determine initial status
            $requiresApproval = $data['requires_approval'] ?? false;
            $status = $requiresApproval ? PaymentVoucher::STATUS_PENDING_APPROVAL : PaymentVoucher::STATUS_DRAFT;

            // Create voucher
            $voucher = PaymentVoucher::create([
                'voucher_number' => $voucherNumber,
                'company_code' => $companyCode,
                'branch_id' => $branchId,
                'payee_type' => $data['payee_type'],
                'payee_id' => $data['payee_id'] ?? null,
                'payee_name' => $data['payee_name'],
                'amount' => $data['amount'],
                'currency_code' => $currency,
                'exchange_rate' => $exchangeRate,
                'amount_in_default' => $amountInDefault,
                'amount_in_words' => $amountInWords,
                'payment_method' => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'check_number' => $data['check_number'] ?? null,
                'check_date' => $data['check_date'] ?? null,
                'description' => $data['description'],
                'related_type' => $data['related_type'] ?? null,
                'related_id' => $data['related_id'] ?? null,
                'requires_approval' => $requiresApproval,
                'status' => $status,
                'created_by' => $user->id,
            ]);

            return $voucher;
        });
    }

    /**
     * Submit voucher for approval
     */
    public function submitForApproval(PaymentVoucher $voucher): PaymentVoucher
    {
        if ($voucher->status !== PaymentVoucher::STATUS_DRAFT) {
            throw new \Exception('السند ليس في حالة مسودة');
        }

        $voucher->update([
            'status' => PaymentVoucher::STATUS_PENDING_APPROVAL,
            'requires_approval' => true,
        ]);

        return $voucher;
    }

    /**
     * Approve a voucher
     */
    public function approve(PaymentVoucher $voucher): PaymentVoucher
    {
        if (!$voucher->canApprove()) {
            throw new \Exception('لا يمكن الموافقة على هذا السند');
        }

        $voucher->update([
            'status' => PaymentVoucher::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return $voucher;
    }

    /**
     * Reject a voucher
     */
    public function reject(PaymentVoucher $voucher, string $reason): PaymentVoucher
    {
        if ($voucher->status !== PaymentVoucher::STATUS_PENDING_APPROVAL) {
            throw new \Exception('السند ليس بانتظار الموافقة');
        }

        $voucher->update([
            'status' => PaymentVoucher::STATUS_CANCELLED,
            'cancelled_reason' => $reason,
        ]);

        return $voucher;
    }

    /**
     * Pay a voucher
     */
    public function pay(PaymentVoucher $voucher): PaymentVoucher
    {
        if (!$voucher->canPay()) {
            throw new \Exception('لا يمكن صرف هذا السند');
        }

        return DB::transaction(function () use ($voucher) {
            $user = Auth::user();

            // Create financial transaction
            $transaction = FinancialTransaction::create([
                'company_code' => $voucher->company_code,
                'branch_id' => $voucher->branch_id,
                'transaction_type' => 'payment_made',
                'amount' => $voucher->amount_in_default,
                'description' => $voucher->description,
                'reference_type' => 'payment_voucher',
                'reference_id' => $voucher->id,
                'created_by' => $user->id,
            ]);

            // Update voucher
            $voucher->update([
                'transaction_id' => $transaction->id,
                'status' => PaymentVoucher::STATUS_PAID,
                'paid_by' => $user->id,
                'paid_at' => now(),
            ]);

            // Update account balance
            if ($voucher->payee_type && $voucher->payee_id) {
                $this->accountBalanceService->addDebit(
                    $voucher->company_code,
                    $voucher->branch_id,
                    $voucher->payee_type,
                    $voucher->payee_id,
                    $voucher->amount_in_default,
                    $transaction->id
                );
            }

            // Update daily cash summary
            $summary = DailyCashSummary::getOrCreateToday($voucher->company_code, $voucher->branch_id);
            $summary->addPayment($voucher->amount_in_default);

            return $voucher;
        });
    }

    /**
     * Cancel a voucher
     */
    public function cancel(PaymentVoucher $voucher, string $reason): PaymentVoucher
    {
        if (!$voucher->canCancel()) {
            throw new \Exception('لا يمكن إلغاء هذا السند');
        }

        $voucher->update([
            'status' => PaymentVoucher::STATUS_CANCELLED,
            'cancelled_reason' => $reason,
        ]);

        return $voucher;
    }

    /**
     * Get pending approval vouchers
     */
    public function getPendingApproval(string $companyCode, ?int $branchId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = PaymentVoucher::where('company_code', $companyCode)
            ->pendingApproval()
            ->orderBy('created_at', 'asc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    /**
     * Get vouchers report
     */
    public function getReport(string $companyCode, array $filters = []): array
    {
        $query = PaymentVoucher::where('company_code', $companyCode)
            ->paid();

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('paid_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('paid_at', '<=', $filters['to_date']);
        }

        if (!empty($filters['payee_type'])) {
            $query->where('payee_type', $filters['payee_type']);
        }

        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        $vouchers = $query->orderBy('paid_at', 'desc')->get();

        return [
            'vouchers' => $vouchers,
            'total_amount' => $vouchers->sum('amount_in_default'),
            'total_count' => $vouchers->count(),
            'by_payee_type' => $vouchers->groupBy('payee_type')
                ->map(fn($group) => [
                    'count' => $group->count(),
                    'total' => $group->sum('amount_in_default'),
                ]),
        ];
    }
}
