<?php

namespace App\Services;

use App\Models\AccountBalance;
use App\Models\FinancialTransaction;
use Illuminate\Database\Eloquent\Collection;

class AccountBalanceService
{
    /**
     * Get account balance
     */
    public function getBalance(
        string $companyCode,
        ?int $branchId,
        string $accountType,
        int $accountId,
        string $currencyCode = 'IQD'
    ): AccountBalance {
        return AccountBalance::getOrCreate($companyCode, $branchId, $accountType, $accountId, $currencyCode);
    }

    /**
     * Add debit to account (دفعنا له - له علينا)
     */
    public function addDebit(
        string $companyCode,
        ?int $branchId,
        string $accountType,
        int $accountId,
        float $amount,
        ?int $transactionId = null,
        string $currencyCode = 'IQD'
    ): AccountBalance {
        $balance = $this->getBalance($companyCode, $branchId, $accountType, $accountId, $currencyCode);
        $balance->addDebit($amount, $transactionId);
        return $balance;
    }

    /**
     * Add credit to account (استلمنا منه - لنا عليه)
     */
    public function addCredit(
        string $companyCode,
        ?int $branchId,
        string $accountType,
        int $accountId,
        float $amount,
        ?int $transactionId = null,
        string $currencyCode = 'IQD'
    ): AccountBalance {
        $balance = $this->getBalance($companyCode, $branchId, $accountType, $accountId, $currencyCode);
        $balance->addCredit($amount, $transactionId);
        return $balance;
    }

    /**
     * Get account statement
     */
    public function getStatement(
        string $companyCode,
        string $accountType,
        int $accountId,
        ?string $fromDate = null,
        ?string $toDate = null,
        ?int $branchId = null
    ): array {
        $query = FinancialTransaction::where('company_code', $companyCode)
            ->where(function ($q) use ($accountType, $accountId) {
                // Look for transactions related to this account
                $q->where('account_type', $accountType)
                    ->where('account_id', $accountId);
            })
            ->orderBy('created_at', 'asc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $transactions = $query->get();

        // Get balance
        $balance = AccountBalance::where('company_code', $companyCode)
            ->where('account_type', $accountType)
            ->where('account_id', $accountId);

        if ($branchId) {
            $balance->where('branch_id', $branchId);
        }

        $balance = $balance->first();

        // Calculate running balance
        $runningBalance = $balance ? $balance->opening_balance : 0;
        $statement = [];

        foreach ($transactions as $transaction) {
            if ($transaction->entry_type === 'debit') {
                $runningBalance += $transaction->amount;
            } else {
                $runningBalance -= $transaction->amount;
            }

            $statement[] = [
                'date' => $transaction->created_at->format('Y-m-d'),
                'description' => $transaction->description,
                'reference' => $transaction->reference_number,
                'debit' => $transaction->entry_type === 'debit' ? $transaction->amount : null,
                'credit' => $transaction->entry_type === 'credit' ? $transaction->amount : null,
                'balance' => $runningBalance,
            ];
        }

        return [
            'account_type' => $accountType,
            'account_id' => $accountId,
            'opening_balance' => $balance ? $balance->opening_balance : 0,
            'total_debits' => $balance ? $balance->total_debits : 0,
            'total_credits' => $balance ? $balance->total_credits : 0,
            'closing_balance' => $runningBalance,
            'balance_type' => $runningBalance >= 0 ? 'debit' : 'credit',
            'transactions' => $statement,
        ];
    }

    /**
     * Get all balances of a type
     */
    public function getBalancesByType(
        string $companyCode,
        string $accountType,
        ?int $branchId = null
    ): Collection {
        $query = AccountBalance::where('company_code', $companyCode)
            ->where('account_type', $accountType)
            ->withBalance();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    /**
     * Get balances summary
     */
    public function getSummary(string $companyCode, ?int $branchId = null): array
    {
        $query = AccountBalance::where('company_code', $companyCode);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $balances = $query->get();

        $summary = [];
        foreach (AccountBalance::ACCOUNT_TYPES as $type => $label) {
            $typeBalances = $balances->where('account_type', $type);

            $summary[$type] = [
                'label' => $label,
                'count' => $typeBalances->count(),
                'total_debits' => $typeBalances->sum('total_debits'),
                'total_credits' => $typeBalances->sum('total_credits'),
                'total_balance' => $typeBalances->sum('current_balance'),
                'debit_count' => $typeBalances->where('balance_type', 'debit')->count(),
                'credit_count' => $typeBalances->where('balance_type', 'credit')->count(),
            ];
        }

        return $summary;
    }

    /**
     * Recalculate balance from transactions
     */
    public function recalculate(
        string $companyCode,
        string $accountType,
        int $accountId,
        ?int $branchId = null
    ): AccountBalance {
        $balance = $this->getBalance($companyCode, $branchId, $accountType, $accountId);

        // Get all transactions
        $transactions = FinancialTransaction::where('company_code', $companyCode)
            ->where('account_type', $accountType)
            ->where('account_id', $accountId);

        if ($branchId) {
            $transactions->where('branch_id', $branchId);
        }

        $transactions = $transactions->get();

        $totalDebits = $transactions->where('entry_type', 'debit')->sum('amount');
        $totalCredits = $transactions->where('entry_type', 'credit')->sum('amount');
        $currentBalance = $balance->opening_balance + $totalDebits - $totalCredits;

        $balance->update([
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'current_balance' => $currentBalance,
            'balance_type' => $currentBalance >= 0 ? 'debit' : 'credit',
            'last_transaction_at' => $transactions->last()?->created_at,
            'last_transaction_id' => $transactions->last()?->id,
        ]);

        return $balance;
    }

    /**
     * Set opening balance
     */
    public function setOpeningBalance(
        string $companyCode,
        ?int $branchId,
        string $accountType,
        int $accountId,
        float $amount,
        string $currencyCode = 'IQD'
    ): AccountBalance {
        $balance = $this->getBalance($companyCode, $branchId, $accountType, $accountId, $currencyCode);

        $balance->update([
            'opening_balance' => $amount,
            'current_balance' => $amount + $balance->total_debits - $balance->total_credits,
            'balance_type' => ($amount + $balance->total_debits - $balance->total_credits) >= 0 ? 'debit' : 'credit',
        ]);

        return $balance;
    }
}
