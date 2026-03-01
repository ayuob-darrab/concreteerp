<?php

namespace App\Services;

use App\Models\FinancialTransaction;
use App\Models\FinancialAccount;
use App\Models\Payment;
use App\Models\CashRegister;
use Illuminate\Support\Facades\DB;

class FinancialTransactionService
{
    /**
     * تسجيل فاتورة مبيعات
     */
    public function recordSaleInvoice($accountId, $amount, $data = []): FinancialTransaction
    {
        return $this->recordTransaction($accountId, 'sale_invoice', $amount, $data);
    }

    /**
     * تسجيل فاتورة مشتريات
     */
    public function recordPurchaseInvoice($accountId, $amount, $data = []): FinancialTransaction
    {
        return $this->recordTransaction($accountId, 'purchase_invoice', $amount, $data);
    }

    /**
     * تسجيل راتب
     */
    public function recordSalary($accountId, $amount, $data = []): FinancialTransaction
    {
        return $this->recordTransaction($accountId, 'salary', $amount, $data);
    }

    /**
     * تسجيل عمولة
     */
    public function recordCommission($accountId, $amount, $data = []): FinancialTransaction
    {
        return $this->recordTransaction($accountId, 'commission', $amount, $data);
    }

    /**
     * تسجيل مصروف
     */
    public function recordExpense($accountId, $amount, $data = []): FinancialTransaction
    {
        return $this->recordTransaction($accountId, 'expense', $amount, $data);
    }

    /**
     * تسجيل معاملة
     */
    public function recordTransaction($accountId, $type, $amount, $data = []): FinancialTransaction
    {
        return DB::transaction(function () use ($accountId, $type, $amount, $data) {
            $account = FinancialAccount::findOrFail($accountId);

            return $account->addTransaction($amount, $type, array_merge([
                'created_by' => auth()->id(),
                'status' => $data['auto_approve'] ?? false ? 'approved' : 'pending',
            ], $data));
        });
    }

    /**
     * تسجيل دفعة مستلمة
     */
    public function receivePayment($accountId, $amount, $paymentMethod = 'cash', $data = []): Payment
    {
        return DB::transaction(function () use ($accountId, $amount, $paymentMethod, $data) {
            $account = FinancialAccount::findOrFail($accountId);

            // توليد رقم إيصال
            $receiptNumber = Payment::generateReceiptNumber($account->company_code, 'in');

            // إنشاء الدفعة
            $payment = Payment::create(array_merge([
                'company_code' => $account->company_code,
                'account_id' => $accountId,
                'direction' => 'in',
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'receipt_number' => $receiptNumber,
                'received_at' => now(),
                'received_by' => auth()->id(),
                'created_by' => auth()->id(),
            ], $data));

            // تحديث رصيد الحساب
            $account->current_balance -= $amount;
            $account->save();

            // تسجيل المعاملة المالية
            $account->addTransaction($amount, 'payment_received', [
                'reference_type' => Payment::class,
                'reference_id' => $payment->id,
                'payment_method' => $paymentMethod,
                'description' => "دفعة مستلمة - إيصال رقم: {$receiptNumber}",
                'created_by' => auth()->id(),
                'status' => 'approved',
            ]);

            // تسجيل في الصندوق إذا كانت نقداً
            if ($paymentMethod === 'cash' && !empty($data['branch_id'])) {
                CashRegister::addEntry($data['branch_id'], 'cash_in', $amount, [
                    'company_code' => $account->company_code,
                    'payment_id' => $payment->id,
                    'description' => "دفعة من حساب: {$account->account_name}",
                    'handled_by' => auth()->id(),
                ]);
            }

            return $payment;
        });
    }

    /**
     * تسجيل دفعة صادرة
     */
    public function makePayment($accountId, $amount, $paymentMethod = 'cash', $data = []): Payment
    {
        return DB::transaction(function () use ($accountId, $amount, $paymentMethod, $data) {
            $account = FinancialAccount::findOrFail($accountId);

            // توليد رقم إيصال
            $receiptNumber = Payment::generateReceiptNumber($account->company_code, 'out');

            // إنشاء الدفعة
            $payment = Payment::create(array_merge([
                'company_code' => $account->company_code,
                'account_id' => $accountId,
                'direction' => 'out',
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'receipt_number' => $receiptNumber,
                'received_at' => now(),
                'received_by' => auth()->id(),
                'created_by' => auth()->id(),
            ], $data));

            // تحديث رصيد الحساب
            $account->current_balance += $amount;
            $account->save();

            // تسجيل المعاملة المالية
            $account->addTransaction($amount, 'payment_made', [
                'reference_type' => Payment::class,
                'reference_id' => $payment->id,
                'payment_method' => $paymentMethod,
                'description' => "دفعة صادرة - سند رقم: {$receiptNumber}",
                'created_by' => auth()->id(),
                'status' => 'approved',
            ]);

            // تسجيل في الصندوق إذا كانت نقداً
            if ($paymentMethod === 'cash' && !empty($data['branch_id'])) {
                CashRegister::addEntry($data['branch_id'], 'cash_out', $amount, [
                    'company_code' => $account->company_code,
                    'payment_id' => $payment->id,
                    'description' => "دفعة لحساب: {$account->account_name}",
                    'handled_by' => auth()->id(),
                ]);
            }

            return $payment;
        });
    }

    /**
     * تحويل بين حسابين
     */
    public function transfer($fromAccountId, $toAccountId, $amount, $data = []): array
    {
        return DB::transaction(function () use ($fromAccountId, $toAccountId, $amount, $data) {
            $fromAccount = FinancialAccount::findOrFail($fromAccountId);
            $toAccount = FinancialAccount::findOrFail($toAccountId);

            // خصم من الحساب المصدر
            $fromTransaction = $fromAccount->addTransaction($amount, 'transfer', array_merge([
                'description' => "تحويل إلى حساب: {$toAccount->account_name}",
                'created_by' => auth()->id(),
                'status' => 'approved',
            ], $data));

            // إضافة للحساب الهدف
            $toTransaction = $toAccount->addTransaction($amount, 'transfer', array_merge([
                'description' => "تحويل من حساب: {$fromAccount->account_name}",
                'created_by' => auth()->id(),
                'status' => 'approved',
            ], $data));

            return [
                'from_transaction' => $fromTransaction,
                'to_transaction' => $toTransaction,
            ];
        });
    }

    /**
     * تسوية حساب
     */
    public function adjustAccount($accountId, $newBalance, $reason = null): FinancialTransaction
    {
        return DB::transaction(function () use ($accountId, $newBalance, $reason) {
            $account = FinancialAccount::findOrFail($accountId);
            $difference = $newBalance - $account->current_balance;

            $transaction = $account->addTransaction(abs($difference), 'adjustment', [
                'description' => $reason ?? 'تسوية رصيد',
                'notes' => "الرصيد القديم: {$account->current_balance}, الرصيد الجديد: {$newBalance}",
                'created_by' => auth()->id(),
                'status' => 'approved',
            ]);

            $account->current_balance = $newBalance;
            $account->save();

            return $transaction;
        });
    }

    /**
     * الحصول على ملخص المعاملات
     */
    public function getTransactionsSummary($companyCode, $from = null, $to = null): array
    {
        $query = FinancialTransaction::where('company_code', $companyCode)
            ->approved();

        if ($from && $to) {
            $query->between($from, $to);
        }

        $transactions = $query->get();

        return [
            'total_count' => $transactions->count(),
            'total_amount' => $transactions->sum('amount'),
            'by_type' => $transactions->groupBy('transaction_type')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount'),
                ];
            }),
            'by_payment_method' => $transactions->groupBy('payment_method')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount'),
                ];
            }),
        ];
    }

    /**
     * الحصول على المعاملات المعلقة
     */
    public function getPendingTransactions($companyCode): \Illuminate\Database\Eloquent\Collection
    {
        return FinancialTransaction::where('company_code', $companyCode)
            ->pending()
            ->with(['account', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * اعتماد معاملات متعددة
     */
    public function bulkApprove(array $transactionIds, $userId = null): int
    {
        $userId = $userId ?? auth()->id();
        $count = 0;

        foreach ($transactionIds as $id) {
            $transaction = FinancialTransaction::find($id);
            if ($transaction && $transaction->status === 'pending') {
                $transaction->approve($userId);
                $count++;
            }
        }

        return $count;
    }

    /**
     * تقرير يومي
     */
    public function getDailyReport($companyCode, $date = null): array
    {
        $date = $date ?? today();

        $transactions = FinancialTransaction::where('company_code', $companyCode)
            ->whereDate('created_at', $date)
            ->approved()
            ->with(['account', 'creator'])
            ->get();

        $payments = Payment::where('company_code', $companyCode)
            ->whereDate('created_at', $date)
            ->with(['account'])
            ->get();

        return [
            'date' => $date->format('Y-m-d'),
            'transactions' => [
                'count' => $transactions->count(),
                'total' => $transactions->sum('amount'),
                'items' => $transactions,
            ],
            'payments' => [
                'in' => [
                    'count' => $payments->where('direction', 'in')->count(),
                    'total' => $payments->where('direction', 'in')->sum('amount'),
                ],
                'out' => [
                    'count' => $payments->where('direction', 'out')->count(),
                    'total' => $payments->where('direction', 'out')->sum('amount'),
                ],
            ],
        ];
    }

    /**
     * كشف حساب
     */
    public function getAccountStatement($accountId, $from = null, $to = null): array
    {
        $account = FinancialAccount::with('company', 'branch')->findOrFail($accountId);

        $query = $account->transactions()->orderBy('created_at', 'asc');

        if ($from) {
            $query->where('created_at', '>=', $from);
        }
        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        $transactions = $query->get();

        // حساب الرصيد الافتتاحي
        $openingBalance = $account->opening_balance;
        if ($from) {
            $priorTransactions = $account->transactions()
                ->where('created_at', '<', $from)
                ->get();

            foreach ($priorTransactions as $t) {
                if ($t->is_debit) {
                    $openingBalance += $t->amount;
                } else {
                    $openingBalance -= $t->amount;
                }
            }
        }

        return [
            'account' => $account,
            'period' => [
                'from' => $from,
                'to' => $to,
            ],
            'opening_balance' => $openingBalance,
            'closing_balance' => $account->current_balance,
            'total_debit' => $transactions->filter(fn($t) => $t->is_debit)->sum('amount'),
            'total_credit' => $transactions->filter(fn($t) => $t->is_credit)->sum('amount'),
            'transactions' => $transactions,
        ];
    }
}
