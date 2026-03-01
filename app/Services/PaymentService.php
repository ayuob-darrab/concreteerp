<?php

namespace App\Services;

use App\Models\Receipt;
use App\Models\Invoice;
use App\Models\Check;
use App\Models\ContractorAccount;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentService
{
    /**
     * الحصول على قائمة السندات
     */
    public function getReceipts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Receipt::query()
            ->forCompany(auth()->user()->company_code)
            ->with(['account.contractor', 'invoice', 'creator']);

        // فلتر النوع
        if (!empty($filters['type'])) {
            $filters['type'] === 'receipt'
                ? $query->receipts()
                : $query->payments();
        }

        // فلتر الحالة
        if (!empty($filters['status'])) {
            $query->status($filters['status']);
        }

        // فلتر الفرع
        if (!empty($filters['branch_id'])) {
            $query->forBranch($filters['branch_id']);
        }

        // فلتر التاريخ
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $query->betweenDates($filters['from_date'], $filters['to_date']);
        }

        // فلتر طريقة الدفع
        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        // الترتيب
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * إنشاء سند قبض
     */
    public function createReceipt(array $data): Receipt
    {
        return DB::transaction(function () use ($data) {
            $data['company_code'] = auth()->user()->company_code;
            $data['branch_id'] = $data['branch_id'] ?? auth()->user()->branch_id;
            $data['created_by'] = auth()->id();
            $data['receipt_type'] = Receipt::TYPE_RECEIPT;
            $data['status'] = Receipt::STATUS_DRAFT;

            // إذا كانت طريقة الدفع شيك، إنشاء الشيك أولاً
            if (($data['payment_method'] ?? null) === 'check' && !empty($data['check_number'])) {
                $check = $this->createCheckFromPayment($data);
                $data['check_id'] = $check->id;
            }

            $receipt = Receipt::create($data);

            Log::info('تم إنشاء سند قبض', [
                'receipt_id' => $receipt->id,
                'receipt_number' => $receipt->receipt_number,
                'amount' => $receipt->amount,
                'created_by' => auth()->id(),
            ]);

            return $receipt->fresh(['account.contractor', 'invoice', 'check']);
        });
    }

    /**
     * إنشاء سند صرف
     */
    public function createPayment(array $data): Receipt
    {
        return DB::transaction(function () use ($data) {
            $data['company_code'] = auth()->user()->company_code;
            $data['branch_id'] = $data['branch_id'] ?? auth()->user()->branch_id;
            $data['created_by'] = auth()->id();
            $data['receipt_type'] = Receipt::TYPE_PAYMENT;
            $data['status'] = Receipt::STATUS_DRAFT;

            $receipt = Receipt::create($data);

            Log::info('تم إنشاء سند صرف', [
                'receipt_id' => $receipt->id,
                'receipt_number' => $receipt->receipt_number,
                'amount' => $receipt->amount,
                'created_by' => auth()->id(),
            ]);

            return $receipt->fresh(['account.contractor', 'invoice']);
        });
    }

    /**
     * اعتماد سند
     */
    public function approveReceipt(Receipt $receipt): Receipt
    {
        if ($receipt->status !== Receipt::STATUS_DRAFT) {
            throw new \Exception('لا يمكن اعتماد هذا السند');
        }

        $receipt->approve();

        Log::info('تم اعتماد السند', [
            'receipt_id' => $receipt->id,
            'receipt_number' => $receipt->receipt_number,
            'approved_by' => auth()->id(),
        ]);

        return $receipt->fresh();
    }

    /**
     * إلغاء سند
     */
    public function cancelReceipt(Receipt $receipt, string $reason): Receipt
    {
        $receipt->cancel($reason);

        Log::warning('تم إلغاء السند', [
            'receipt_id' => $receipt->id,
            'receipt_number' => $receipt->receipt_number,
            'reason' => $reason,
            'cancelled_by' => auth()->id(),
        ]);

        return $receipt->fresh();
    }

    /**
     * إنشاء شيك من بيانات الدفع
     */
    protected function createCheckFromPayment(array $data): Check
    {
        return Check::create([
            'check_type' => Check::TYPE_INCOMING,
            'account_id' => $data['account_id'],
            'company_code' => $data['company_code'],
            'branch_id' => $data['branch_id'],
            'check_number' => $data['check_number'],
            'bank_name' => $data['bank_name'] ?? '',
            'drawer_name' => $data['drawer_name'] ?? '',
            'beneficiary_name' => auth()->user()->company->name ?? '',
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'SAR',
            'issue_date' => $data['receipt_date'],
            'due_date' => $data['check_due_date'] ?? $data['receipt_date'],
            'status' => Check::STATUS_PENDING,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * الحصول على ملخص المدفوعات لفترة
     */
    public function getPaymentsSummary(string $companyCode, ?string $fromDate = null, ?string $toDate = null): array
    {
        $query = Receipt::forCompany($companyCode)->approved();

        if ($fromDate && $toDate) {
            $query->betweenDates($fromDate, $toDate);
        }

        return [
            'total_receipts' => (clone $query)->receipts()->sum('amount'),
            'total_payments' => (clone $query)->payments()->sum('amount'),
            'receipts_count' => (clone $query)->receipts()->count(),
            'payments_count' => (clone $query)->payments()->count(),
            'by_payment_method' => [
                'cash' => (clone $query)->where('payment_method', 'cash')->sum('amount'),
                'bank_transfer' => (clone $query)->where('payment_method', 'bank_transfer')->sum('amount'),
                'check' => (clone $query)->where('payment_method', 'check')->sum('amount'),
                'credit_card' => (clone $query)->where('payment_method', 'credit_card')->sum('amount'),
            ],
        ];
    }

    /**
     * تسوية فاتورة
     */
    public function settleInvoice(Invoice $invoice, float $amount, string $paymentMethod, array $additionalData = []): Receipt
    {
        if ($amount > $invoice->remaining_amount) {
            throw new \Exception('المبلغ أكبر من المتبقي على الفاتورة');
        }

        $data = array_merge([
            'account_id' => $invoice->account_id,
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'receipt_date' => today(),
            'description' => "دفعة للفاتورة رقم: {$invoice->invoice_number}",
        ], $additionalData);

        $receipt = $this->createReceipt($data);

        // اعتماد السند تلقائياً
        $this->approveReceipt($receipt);

        return $receipt;
    }
}
