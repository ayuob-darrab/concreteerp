<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_number',
        'company_code',
        'branch_id',
        'work_order_id',
        'customer_name',
        'customer_phone',
        'payment_type',
        'payment_method',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'company_payment_card_id',
        'reference_number',
        'receipt_number',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public static $paymentTypes = [
        'cash' => 'كاش (دفع فوري)',
        'deferred' => 'آجل (دفع لاحقاً)',
    ];

    public static $paymentMethods = [
        'cash' => 'نقدي',
        'bank_transfer' => 'تحويل بنكي',
        'check' => 'شيك',
        'online' => 'دفع إلكتروني',
    ];

    public static $statuses = [
        'unpaid' => 'غير مدفوع',
        'partial' => 'مدفوع جزئياً',
        'paid' => 'مدفوع بالكامل',
    ];

    // ==================== العلاقات ====================

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function paymentCard()
    {
        return $this->belongsTo(CompanyPaymentCard::class, 'company_payment_card_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function records()
    {
        return $this->hasMany(CustomerPaymentRecord::class);
    }

    // ==================== المساعدات ====================

    public static function generatePaymentNumber($companyCode)
    {
        $year = date('Y');
        $month = date('m');
        $prefix = 'CP-' . $companyCode . '-' . $year . $month;

        $lastPayment = self::where('payment_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPayment && preg_match('/-(\d+)$/', $lastPayment->payment_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusTextAttribute()
    {
        return self::$statuses[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'paid' => 'success',
            'partial' => 'warning',
            'unpaid' => 'danger',
            default => 'secondary',
        };
    }

    public function getPaymentTypeTextAttribute()
    {
        return self::$paymentTypes[$this->payment_type] ?? $this->payment_type;
    }

    public function getPaymentMethodTextAttribute()
    {
        return self::$paymentMethods[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * تسجيل دفعة جديدة
     */
    public function recordPayment($amount, $paymentMethod, $cardId = null, $referenceNumber = null, $notes = null)
    {
        $balanceBefore = $this->remaining_amount;
        $balanceAfter = max(0, $balanceBefore - $amount);

        // إنشاء سجل الدفعة
        $record = $this->records()->create([
            'record_number' => CustomerPaymentRecord::generateRecordNumber($this->company_code),
            'company_code' => $this->company_code,
            'branch_id' => $this->branch_id,
            'payment_method' => $paymentMethod,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'company_payment_card_id' => $cardId,
            'reference_number' => $referenceNumber,
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);

        // تحديث المبلغ المدفوع والمتبقي
        $this->paid_amount += $amount;
        $this->remaining_amount = $balanceAfter;
        $this->payment_method = $paymentMethod;

        // تحديث الحالة
        if ($this->remaining_amount <= 0) {
            $this->status = 'paid';
        } else {
            $this->status = 'partial';
        }

        $this->updated_by = auth()->id();
        $this->save();

        // إذا كان الدفع بالبطاقة، تحديث رصيد البطاقة
        if ($cardId && $paymentMethod === 'online') {
            $card = CompanyPaymentCard::find($cardId);
            if ($card) {
                $card->deposit($amount, 'دفعة طلب #' . $this->work_order_id . ' - ' . $this->customer_name, 'order_payment', $this->id, $this->branch_id);
            }
        }

        // تحديث حالة الدفع في الطلب
        $this->updateWorkOrderPayment();

        return $record;
    }

    /**
     * تحديث حالة الدفع في الطلب
     */
    protected function updateWorkOrderPayment()
    {
        $workOrder = $this->workOrder;
        if ($workOrder) {
            $workOrder->payment_status = $this->status;
            $workOrder->paid_amount = $this->paid_amount;
            $workOrder->payment_method = $this->payment_method;
            if ($this->status === 'paid') {
                $workOrder->paid_at = now();
                $workOrder->paid_by = auth()->id();
            }
            $workOrder->save();
        }
    }

    // ==================== Scopes ====================

    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['unpaid', 'partial']);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
