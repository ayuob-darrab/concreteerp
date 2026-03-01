<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'receipt_number',
        'receipt_type',
        'account_id',
        'invoice_id',
        'check_id',
        'company_code',
        'branch_id',
        'party_name',
        'amount',
        'currency',
        'exchange_rate',
        'payment_method',
        'payment_reference',
        'bank_name',
        'receipt_date',
        'description',
        'notes',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'receipt_date' => 'date',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // أنواع السندات
    const TYPE_RECEIPT = 'receipt';     // سند قبض
    const TYPE_PAYMENT = 'payment';     // سند صرف

    // طرق الدفع
    const METHOD_CASH = 'cash';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_CHECK = 'check';
    const METHOD_CREDIT_CARD = 'credit_card';
    const METHOD_MOBILE_PAYMENT = 'mobile_payment';

    // حالات السند
    const STATUS_DRAFT = 'draft';
    const STATUS_APPROVED = 'approved';
    const STATUS_CANCELLED = 'cancelled';

    // ═══════════════════════════════════════════════════════════════
    // Boot
    // ═══════════════════════════════════════════════════════════════

    protected static function booted()
    {
        static::creating(function (Receipt $receipt) {
            if (empty($receipt->receipt_number)) {
                $receipt->receipt_number = self::generateNumber(
                    $receipt->company_code,
                    $receipt->receipt_type
                );
            }
            if (empty($receipt->receipt_date)) {
                $receipt->receipt_date = today();
            }
        });
    }

    // ═══════════════════════════════════════════════════════════════
    // العلاقات
    // ═══════════════════════════════════════════════════════════════

    /**
     * الحساب
     */
    public function account()
    {
        return $this->belongsTo(ContractorAccount::class, 'account_id');
    }

    /**
     * المقاول عبر الحساب
     */
    public function contractor()
    {
        return $this->hasOneThrough(
            Contractor::class,
            ContractorAccount::class,
            'id',
            'id',
            'account_id',
            'contractor_id'
        );
    }

    /**
     * الفاتورة
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * الشيك
     */
    public function check()
    {
        return $this->belongsTo(Check::class);
    }

    /**
     * من أنشأ السند
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * من وافق على السند
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ═══════════════════════════════════════════════════════════════
    // Scopes
    // ═══════════════════════════════════════════════════════════════

    /**
     * حسب الشركة
     */
    public function scopeForCompany($query, string $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    /**
     * حسب الفرع
     */
    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * سندات القبض
     */
    public function scopeReceipts($query)
    {
        return $query->where('receipt_type', self::TYPE_RECEIPT);
    }

    /**
     * سندات الصرف
     */
    public function scopePayments($query)
    {
        return $query->where('receipt_type', self::TYPE_PAYMENT);
    }

    /**
     * حسب الحالة
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * المعتمدة
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * حسب الفترة
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('receipt_date', [$startDate, $endDate]);
    }

    // ═══════════════════════════════════════════════════════════════
    // Accessors
    // ═══════════════════════════════════════════════════════════════

    /**
     * نص النوع
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->receipt_type) {
            self::TYPE_RECEIPT => 'سند قبض',
            self::TYPE_PAYMENT => 'سند صرف',
            default => 'غير معروف',
        };
    }

    /**
     * نص طريقة الدفع
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            self::METHOD_CASH => 'نقداً',
            self::METHOD_BANK_TRANSFER => 'تحويل بنكي',
            self::METHOD_CHECK => 'شيك',
            self::METHOD_CREDIT_CARD => 'بطاقة ائتمان',
            self::METHOD_MOBILE_PAYMENT => 'دفع إلكتروني',
            default => 'غير محدد',
        };
    }

    /**
     * نص الحالة
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_APPROVED => 'معتمد',
            self::STATUS_CANCELLED => 'ملغي',
            default => 'غير معروف',
        };
    }

    /**
     * لون الحالة
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_CANCELLED => 'red',
            default => 'gray',
        };
    }

    // ═══════════════════════════════════════════════════════════════
    // Methods
    // ═══════════════════════════════════════════════════════════════

    /**
     * توليد رقم السند
     */
    public static function generateNumber(string $companyCode, string $type): string
    {
        $prefix = $type === self::TYPE_RECEIPT ? 'RCV' : 'PAY';
        $year = date('Y');

        $lastReceipt = self::where('company_code', $companyCode)
            ->where('receipt_type', $type)
            ->where('receipt_number', 'like', "{$prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastReceipt
            ? (int)substr($lastReceipt->receipt_number, -6) + 1
            : 1;

        return sprintf('%s-%s-%06d', $prefix, $year, $nextNumber);
    }

    /**
     * اعتماد السند
     */
    public function approve(): bool
    {
        if ($this->status !== self::STATUS_DRAFT) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // تحديث رصيد الحساب
        if ($this->account) {
            $transactionType = $this->receipt_type === self::TYPE_RECEIPT ? 'credit' : 'debit';
            $this->account->addTransaction(
                $transactionType,
                $this->amount,
                'receipt',
                $this->description ?? "سند رقم: {$this->receipt_number}"
            );
        }

        // تحديث الفاتورة إن وجدت
        if ($this->invoice && $this->receipt_type === self::TYPE_RECEIPT) {
            $this->invoice->recordPayment($this->amount);
        }

        return true;
    }

    /**
     * إلغاء السند
     */
    public function cancel(string $reason): bool
    {
        // إذا كان معتمد نحتاج عكس العملية
        if ($this->status === self::STATUS_APPROVED && $this->account) {
            $transactionType = $this->receipt_type === self::TYPE_RECEIPT ? 'debit' : 'credit';
            $this->account->addTransaction(
                $transactionType,
                $this->amount,
                'receipt_reversal',
                "إلغاء سند رقم: {$this->receipt_number}"
            );
        }

        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }
}
