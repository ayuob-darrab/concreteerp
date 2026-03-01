<?php

namespace App\Services;

use App\Models\PaymentReceipt;
use App\Models\FinancialTransaction;
use App\Models\AccountBalance;
use App\Models\DailyCashSummary;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReceiptService
{
    protected $currencyService;
    protected $accountBalanceService;

    public function __construct(CurrencyService $currencyService, AccountBalanceService $accountBalanceService)
    {
        $this->currencyService = $currencyService;
        $this->accountBalanceService = $accountBalanceService;
    }

    /**
     * Create a new payment receipt
     */
    public function create(array $data): PaymentReceipt
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();
            $companyCode = $user->company_code;
            $branchId = $data['branch_id'];

            // Generate receipt number
            $receiptNumber = PaymentReceipt::generateReceiptNumber($companyCode, $branchId);

            // Calculate amount in default currency
            $currency = $data['currency_code'] ?? 'IQD';
            $exchangeRate = Currency::getByCode($currency)?->exchange_rate ?? 1;
            $amountInDefault = $data['amount'] * $exchangeRate;

            // Generate amount in words
            $amountInWords = $this->currencyService->toWords($data['amount'], $currency);

            // Create financial transaction
            $transaction = FinancialTransaction::create([
                'company_code' => $companyCode,
                'branch_id' => $branchId,
                'transaction_type' => 'payment_received',
                'amount' => $amountInDefault,
                'description' => $data['description'],
                'reference_type' => 'payment_receipt',
                'created_by' => $user->id,
            ]);

            // Create receipt
            $receipt = PaymentReceipt::create([
                'receipt_number' => $receiptNumber,
                'company_code' => $companyCode,
                'branch_id' => $branchId,
                'transaction_id' => $transaction->id,
                'payer_type' => $data['payer_type'],
                'payer_id' => $data['payer_id'] ?? null,
                'payer_name' => $data['payer_name'],
                'payer_phone' => $data['payer_phone'] ?? null,
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
                'status' => PaymentReceipt::STATUS_CONFIRMED,
                'received_by' => $user->id,
                'received_at' => now(),
            ]);

            // Update transaction with receipt reference
            $transaction->update([
                'reference_id' => $receipt->id,
            ]);

            // Update account balance
            if ($data['payer_type'] && $data['payer_id']) {
                $this->accountBalanceService->addCredit(
                    $companyCode,
                    $branchId,
                    $data['payer_type'],
                    $data['payer_id'],
                    $amountInDefault,
                    $transaction->id
                );
            }

            // Update daily cash summary
            $this->updateDailySummary($companyCode, $branchId, $amountInDefault, 'receipt');

            return $receipt;
        });
    }

    /**
     * Cancel a receipt
     */
    public function cancel(PaymentReceipt $receipt, string $reason): PaymentReceipt
    {
        if (!$receipt->canCancel()) {
            throw new \Exception('لا يمكن إلغاء هذا الإيصال');
        }

        return DB::transaction(function () use ($receipt, $reason) {
            // Reverse account balance
            if ($receipt->payer_type && $receipt->payer_id) {
                $this->accountBalanceService->addDebit(
                    $receipt->company_code,
                    $receipt->branch_id,
                    $receipt->payer_type,
                    $receipt->payer_id,
                    $receipt->amount_in_default,
                    null,
                    'إلغاء إيصال: ' . $receipt->receipt_number
                );
            }

            // Update daily summary
            $this->updateDailySummary(
                $receipt->company_code,
                $receipt->branch_id,
                -$receipt->amount_in_default,
                'receipt'
            );

            // Update receipt status
            $receipt->update([
                'status' => PaymentReceipt::STATUS_CANCELLED,
                'cancelled_reason' => $reason,
            ]);

            return $receipt;
        });
    }

    /**
     * Mark check as bounced
     */
    public function markBounced(PaymentReceipt $receipt, string $reason): PaymentReceipt
    {
        if ($receipt->payment_method !== 'check') {
            throw new \Exception('هذا الإيصال ليس شيكاً');
        }

        return DB::transaction(function () use ($receipt, $reason) {
            // Reverse account balance
            if ($receipt->payer_type && $receipt->payer_id) {
                $this->accountBalanceService->addDebit(
                    $receipt->company_code,
                    $receipt->branch_id,
                    $receipt->payer_type,
                    $receipt->payer_id,
                    $receipt->amount_in_default,
                    null,
                    'شيك مرتجع: ' . $receipt->check_number
                );
            }

            $receipt->update([
                'status' => PaymentReceipt::STATUS_BOUNCED,
                'cancelled_reason' => $reason,
            ]);

            return $receipt;
        });
    }

    /**
     * Update daily cash summary
     */
    protected function updateDailySummary(string $companyCode, int $branchId, float $amount, string $type): void
    {
        $summary = DailyCashSummary::getOrCreateToday($companyCode, $branchId);

        if ($type === 'receipt') {
            if ($amount > 0) {
                $summary->addReceipt($amount);
            } else {
                // Adjustment for cancellation
                $summary->total_receipts += $amount;
                $summary->receipts_count = max(0, $summary->receipts_count - 1);
                $summary->closing_balance = $summary->opening_balance + $summary->total_receipts - $summary->total_payments;
                $summary->save();
            }
        }
    }

    /**
     * Get receipts report
     */
    public function getReport(string $companyCode, array $filters = []): array
    {
        $query = PaymentReceipt::where('company_code', $companyCode)
            ->confirmed();

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('received_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('received_at', '<=', $filters['to_date']);
        }

        if (!empty($filters['payer_type'])) {
            $query->where('payer_type', $filters['payer_type']);
        }

        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        $receipts = $query->orderBy('received_at', 'desc')->get();

        return [
            'receipts' => $receipts,
            'total_amount' => $receipts->sum('amount_in_default'),
            'total_count' => $receipts->count(),
            'by_payment_method' => $receipts->groupBy('payment_method')
                ->map(fn($group) => [
                    'count' => $group->count(),
                    'total' => $group->sum('amount_in_default'),
                ]),
        ];
    }
}
