<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CustomerPayment;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // معلومات المرسل
        'sender_type',
        'sender_id',

        // معلومات الطلب
        'classification',
        'company_code',
        'branch_id',

        // الحالة الموحدة (دعم كلا الاسمين)
        'status',
        'status_code',

        // موافقات
        'approved_price',
        'approved_by',
        'approved_at',
        'approved_note',
        'client_approved',
        'client_approved_at',
        'final_price',
        'execution_date',
        'execution_time',
        'cancellation_reason',

        // موافقة الفرع
        'branch_approval_status',
        'branch_approval_user_id',
        'branch_approval_date',
        'branch_approval_note',
        'price',

        // موافقة المقاول/صاحب الطلب
        'requester_approval_status',
        'requester_approval_date',
        'requester_approval_note',

        // الموافقة النهائية
        'accept_user',
        'accept_date',
        'accept_note',
        'completed_at',
        'completed_by',

        // تفاصيل الطلب الأساسية
        'request_type',
        'quantity',
        'executed_quantity',
        'location',
        'location_map_url',
        'location_lat',
        'location_lng',
        'delivery_datetime',
        'customer_name',
        'customer_phone',

        // الأسعار
        'initial_price',
        'final_price',
        'price_approved',

        // الدفع
        'payment_status',
        'paid_amount',
        'payment_method',
        'payment_note',
        'paid_at',
        'paid_by',

        // تواريخ
        'request_date',
        'created_by',
        'updated_by',

        // ملاحظات عامة (دعم كلا الاسمين)
        'notes',
        'note',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'executed_quantity' => 'decimal:2',
        'initial_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'price_approved' => 'boolean',
        'paid_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'request_date' => 'datetime',
        'delivery_datetime' => 'datetime',
        'approved_at' => 'datetime',
        'client_approved_at' => 'datetime',
        'execution_date' => 'date',
    ];

    // ==================== العلاقات الأساسية ====================

    /**
     * خلطة الكونكريت المطلوبة
     */
    public function concreteMix()
    {
        return $this->belongsTo(ConcreteMix::class, 'classification', 'id');
    }

    /**
     * نوع المرسل
     */
    public function senderType()
    {
        return $this->belongsTo(accountsType::class, 'sender_type', 'code');
    }

    /**
     * المرسل (المستخدم)
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    /**
     * الشركة
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    /**
     * الفرع
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    /**
     * منشئ الطلب
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * آخر من عدّل الطلب
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ==================== العلاقات مع الجداول الفرعية ====================

    /**
     * مراحل الطلب
     */
    public function stages()
    {
        return $this->hasMany(OrderStage::class)->orderBy('created_at', 'desc');
    }

    /**
     * آخر مرحلة
     */
    public function latestStage()
    {
        return $this->hasOne(OrderStage::class)->latestOfMany();
    }

    /**
     * السجل التاريخي الكامل
     */
    public function histories()
    {
        return $this->hasMany(OrderHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * عمليات التنفيذ
     */
    public function executions()
    {
        return $this->hasMany(OrderExecution::class)->orderBy('execution_date', 'desc');
    }

    /**
     * التنفيذات المكتملة
     */
    public function completedExecutions()
    {
        return $this->hasMany(OrderExecution::class)->where('status', 'completed');
    }

    /**
     * تغييرات الأسعار
     */
    public function priceChanges()
    {
        return $this->hasMany(OrderPriceChange::class)->orderBy('created_at', 'desc');
    }

    /**
     * دفعات الزبون على هذا الطلب
     */
    public function customerPayments()
    {
        return $this->hasMany(CustomerPayment::class);
    }

    /**
     * آخر تغيير سعر معتمد
     */
    public function latestApprovedPriceChange()
    {
        return $this->hasOne(OrderPriceChange::class)
            ->where('is_approved', true)
            ->latestOfMany();
    }

    /**
     * مفاوضات الطلب
     */
    public function negotiations()
    {
        return $this->hasMany(OrderNegotiation::class, 'order_id')->orderBy('created_at', 'desc');
    }

    /**
     * آخر مفاوضة
     */
    public function latestNegotiation()
    {
        return $this->hasOne(OrderNegotiation::class, 'order_id')->latestOfMany();
    }

    /**
     * الخط الزمني للطلب
     */
    public function timeline()
    {
        return $this->hasMany(OrderTimeline::class, 'order_id')->orderBy('created_at', 'asc');
    }

    /**
     * أوامر العمل المرتبطة بالطلب
     */
    public function workJobs()
    {
        return $this->hasMany(WorkJob::class, 'order_id');
    }

    // ==================== Scopes ====================

    /**
     * الطلبات الجديدة
     */
    public function scopeNew($query)
    {
        return $query->where('status_code', 'new');
    }

    /**
     * الطلبات قيد المراجعة
     */
    public function scopeUnderReview($query)
    {
        return $query->where('status_code', 'under_review');
    }

    /**
     * الطلبات المعتمدة
     */
    public function scopeApproved($query)
    {
        return $query->where('status_code', 'approved');
    }

    /**
     * الطلبات المرفوضة
     */
    public function scopeRejected($query)
    {
        return $query->where('status_code', 'rejected');
    }

    /**
     * الطلبات قيد التنفيذ
     */
    public function scopeInProgress($query)
    {
        return $query->where('status_code', 'in_progress');
    }

    /**
     * الطلبات المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('status_code', 'completed');
    }

    /**
     * الطلبات النشطة (غير المكتملة والملغاة)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status_code', ['completed', 'cancelled']);
    }

    /**
     * الطلبات لشركة معينة
     */
    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    /**
     * الطلبات لفرع معين
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * الطلبات في فترة زمنية
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('request_date', [$startDate, $endDate]);
    }

    // ==================== Attributes & Helpers ====================

    /**
     * Accessor للحالة - قراءة من status_code
     */
    public function getStatusAttribute()
    {
        return $this->attributes['status_code'] ?? null;
    }

    /**
     * Mutator للحالة - حفظ في status_code
     */
    public function setStatusAttribute($value)
    {
        $this->attributes['status_code'] = $value;
    }

    /**
     * Accessor للملاحظات - قراءة من note
     */
    public function getNotesAttribute()
    {
        return $this->attributes['note'] ?? null;
    }

    /**
     * Mutator للملاحظات - حفظ في note
     */
    public function setNotesAttribute($value)
    {
        $this->attributes['note'] = $value;
    }

    /**
     * نسبة التنفيذ
     */
    public function getExecutionPercentageAttribute()
    {
        if ($this->quantity <= 0) {
            return 0;
        }
        return round(($this->executed_quantity / $this->quantity) * 100, 2);
    }

    /**
     * الكمية المتبقية
     */
    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - $this->executed_quantity;
    }

    /**
     * هل الطلب مكتمل التنفيذ؟
     */
    public function isFullyExecuted()
    {
        return $this->executed_quantity >= $this->quantity;
    }

    /**
     * هل يمكن تعديل الطلب؟
     */
    public function canBeModified()
    {
        $status = $this->attributes['status_code'] ?? null;
        return in_array($status, ['new', 'under_review', 'waiting_customer']);
    }

    /**
     * هل يمكن إلغاء الطلب؟
     */
    public function canBeCancelled()
    {
        $status = $this->attributes['status_code'] ?? null;
        return !in_array($status, ['completed', 'cancelled']);
    }

    /**
     * الحصول على السعر الفعلي (النهائي إن وُجد، وإلا المبدئي)
     */
    public function getEffectivePriceAttribute()
    {
        return $this->final_price ?? $this->initial_price;
    }

    /**
     * إجمالي المبلغ
     */
    public function getTotalAmountAttribute()
    {
        return $this->quantity * ($this->effective_price ?? 0);
    }

    /**
     * المبلغ المنفذ
     */
    public function getExecutedAmountAttribute()
    {
        return $this->executed_quantity * ($this->effective_price ?? 0);
    }

    /**
     * المبلغ المتبقي
     */
    public function getRemainingAmountAttribute()
    {
        return $this->remaining_quantity * ($this->effective_price ?? 0);
    }

    // ==================== Boot ====================

    protected static function boot()
    {
        parent::boot();

        // عند إنشاء طلب جديد
        static::creating(function ($workOrder) {
            // تعيين created_by تلقائياً
            if (!$workOrder->created_by) {
                $workOrder->created_by = auth()->id();
            }

            // تعيين request_date تلقائياً
            if (!$workOrder->request_date) {
                $workOrder->request_date = now();
            }
        });

        // عند التحديث
        static::updating(function ($workOrder) {
            // ملاحظة: العمود updated_by غير موجود في قاعدة البيانات حالياً
            // يمكن إضافته لاحقاً إذا لزم الأمر
            // $workOrder->updated_by = auth()->id();
        });

        // بعد إنشاء الطلب - تسجيل في المراحل والتاريخ
        static::created(function ($workOrder) {
            // إضافة المرحلة الأولى
            $status = $workOrder->attributes['status_code'] ?? $workOrder->attributes['status'] ?? 'new';
            OrderStage::create([
                'work_order_id' => $workOrder->id,
                'stage' => $status,
                'user_id' => $workOrder->created_by,
                'notes' => 'تم إنشاء الطلب',
            ]);

            // إضافة سجل في التاريخ
            OrderHistory::logChange($workOrder->id, 'created', [
                'description' => 'تم إنشاء طلب عمل جديد',
            ]);

            // تسجيل السعر المبدئي إن وُجد
            if ($workOrder->initial_price) {
                OrderPriceChange::logPriceChange(
                    $workOrder->id,
                    null,
                    $workOrder->initial_price,
                    'initial',
                    'السعر المبدئي للطلب'
                );
            }
        });
    }
}
