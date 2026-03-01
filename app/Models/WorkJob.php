<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkJob extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_number',
        'company_code',
        'branch_id',
        'order_id',
        'customer_type',
        'customer_id',
        'customer_name',
        'customer_phone',
        'concrete_type_id',
        'total_quantity',
        'executed_quantity',
        'completion_percentage',
        'unit_price',
        'total_price',
        'discount_amount',
        'final_price',
        'location_address',
        'location_map_url',
        'latitude',
        'longitude',
        'scheduled_date',
        'scheduled_time',
        'actual_start_date',
        'actual_end_date',
        'status',
        'supervisor_id',
        'default_pump_id',
        'default_pump_driver_id',
        'pump_assigned_at',
        'pump_notes',
        'notes',
        'internal_notes',
        'total_shipments',
        'total_working_hours',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_quantity' => 'decimal:2',
        'executed_quantity' => 'decimal:2',
        'completion_percentage' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime:H:i',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'pump_assigned_at' => 'datetime',
        'total_working_hours' => 'decimal:2',
    ];

    // =====================
    // الثوابت - Constants
    // =====================

    // أنواع العملاء
    const CUSTOMER_TYPE_CONTRACTOR = 'contractor';
    const CUSTOMER_TYPE_AGENT = 'agent_customer';
    const CUSTOMER_TYPE_DIRECT = 'direct_customer';

    const CUSTOMER_TYPES = [
        self::CUSTOMER_TYPE_CONTRACTOR => 'مقاول',
        self::CUSTOMER_TYPE_AGENT => 'عميل وكيل',
        self::CUSTOMER_TYPE_DIRECT => 'عميل مباشر',
    ];

    // حالات أمر العمل
    const STATUS_PENDING = 'pending';
    const STATUS_MATERIALS_RESERVED = 'materials_reserved';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_PARTIALLY_COMPLETED = 'partially_completed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ON_HOLD = 'on_hold';

    const STATUSES = [
        self::STATUS_PENDING => 'بانتظار التنفيذ',
        self::STATUS_MATERIALS_RESERVED => 'تم حجز المواد',
        self::STATUS_IN_PROGRESS => 'قيد التنفيذ',
        self::STATUS_PARTIALLY_COMPLETED => 'منجز جزئياً',
        self::STATUS_COMPLETED => 'مكتمل',
        self::STATUS_CANCELLED => 'ملغي',
        self::STATUS_ON_HOLD => 'معلق',
    ];

    const STATUS_BADGES = [
        self::STATUS_PENDING => 'secondary',
        self::STATUS_MATERIALS_RESERVED => 'info',
        self::STATUS_IN_PROGRESS => 'primary',
        self::STATUS_PARTIALLY_COMPLETED => 'warning',
        self::STATUS_COMPLETED => 'success',
        self::STATUS_CANCELLED => 'danger',
        self::STATUS_ON_HOLD => 'dark',
    ];

    // =====================
    // العلاقات - Relationships
    // =====================

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function order()
    {
        return $this->belongsTo(WorkOrder::class, 'order_id');
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'order_id');
    }

    public function concreteType()
    {
        return $this->belongsTo(ConcreteMix::class, 'concrete_type_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    public function defaultPump()
    {
        return $this->belongsTo(Cars::class, 'default_pump_id');
    }

    public function defaultPumpDriver()
    {
        return $this->belongsTo(Employee::class, 'default_pump_driver_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function shipments()
    {
        return $this->hasMany(WorkShipment::class, 'job_id');
    }

    public function materialReservations()
    {
        return $this->hasMany(MaterialReservation::class, 'job_id');
    }

    public function losses()
    {
        return $this->hasMany(WorkLoss::class, 'job_id');
    }

    // العميل (مقاول أو وكيل)
    public function contractor()
    {
        return $this->belongsTo(Contractor::class, 'customer_id');
    }

    // =====================
    // Accessors
    // =====================

    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        return self::STATUS_BADGES[$this->status] ?? 'secondary';
    }

    public function getCustomerTypeLabelAttribute()
    {
        return self::CUSTOMER_TYPES[$this->customer_type] ?? $this->customer_type;
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->total_quantity - $this->executed_quantity;
    }

    public function getIsCompletedAttribute()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function getCanAddShipmentAttribute()
    {
        return !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    // =====================
    // Scopes
    // =====================

    public function scopeCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    public function scopeBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('status', [
            self::STATUS_IN_PROGRESS,
            self::STATUS_PARTIALLY_COMPLETED,
            self::STATUS_MATERIALS_RESERVED
        ]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeScheduledOn($query, $date)
    {
        return $query->whereDate('scheduled_date', $date);
    }

    public function scopeScheduledBetween($query, $from, $to)
    {
        return $query->whereBetween('scheduled_date', [$from, $to]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', today());
    }

    // =====================
    // Methods
    // =====================

    public function updateProgress()
    {
        $totalExecuted = $this->shipments()
            ->where('status', WorkShipment::STATUS_COMPLETED)
            ->sum('actual_quantity');

        $this->executed_quantity = $totalExecuted;
        $this->completion_percentage = $this->total_quantity > 0
            ? round(($totalExecuted / $this->total_quantity) * 100, 2)
            : 0;

        // تحديث الحالة بناءً على نسبة الإنجاز
        if ($this->completion_percentage >= 100) {
            $this->status = self::STATUS_COMPLETED;
            $this->actual_end_date = now()->toDateString();
        } elseif ($this->completion_percentage > 0) {
            $this->status = self::STATUS_PARTIALLY_COMPLETED;
        }

        $this->total_shipments = $this->shipments()->count();
        $this->save();
    }

    public static function generateJobNumber($branchCode, $date = null)
    {
        $date = $date ?: now();
        $yearMonth = $date->format('Ym');
        $prefix = "JOB-{$branchCode}-{$yearMonth}";

        $lastJob = self::where('job_number', 'like', "{$prefix}%")
            ->orderBy('job_number', 'desc')
            ->first();

        $sequence = 1;
        if ($lastJob) {
            $lastSequence = (int) substr($lastJob->job_number, -4);
            $sequence = $lastSequence + 1;
        }

        return $prefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
