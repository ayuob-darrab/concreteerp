<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'maintenance_records';

    // أنواع الصيانة
    const TYPE_SCHEDULED = 'scheduled';
    const TYPE_PREVENTIVE = 'preventive';
    const TYPE_CORRECTIVE = 'corrective';
    const TYPE_EMERGENCY = 'emergency';

    const MAINTENANCE_TYPES = [
        self::TYPE_SCHEDULED => 'دورية مجدولة',
        self::TYPE_PREVENTIVE => 'وقائية',
        self::TYPE_CORRECTIVE => 'تصحيحية (إصلاح)',
        self::TYPE_EMERGENCY => 'طارئة',
    ];

    protected $fillable = [
        'company_code',
        'branch_id',
        'vehicle_id',
        'maintenance_type',
        'description',
        'odometer_before',
        'odometer_after',
        'working_hours_before',
        'working_hours_after',
        'labor_cost',
        'parts_cost',
        'total_cost',
        'parts_used',
        'started_at',
        'completed_at',
        'performed_by',
        'external_workshop',
        'workshop_name',
        'attachments',
        'notes',
        'next_maintenance_notes',
        'created_by',
    ];

    protected $casts = [
        'labor_cost' => 'decimal:2',
        'parts_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'parts_used' => 'array',
        'attachments' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'external_workshop' => 'boolean',
    ];

    // العلاقات
    public function vehicle()
    {
        return $this->belongsTo(Cars::class, 'vehicle_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeType($query, $type)
    {
        return $query->where('maintenance_type', $type);
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('completed_at');
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('started_at', [$startDate, $endDate]);
    }

    // الدوال المساعدة
    public function getTypeLabelAttribute()
    {
        return self::MAINTENANCE_TYPES[$this->maintenance_type] ?? $this->maintenance_type;
    }

    public function isCompleted()
    {
        return !is_null($this->completed_at);
    }

    public function isPending()
    {
        return is_null($this->completed_at);
    }

    public function getDurationAttribute()
    {
        if (!$this->completed_at) {
            return null;
        }
        return $this->started_at->diffInHours($this->completed_at);
    }

    public function getOdometerDifferenceAttribute()
    {
        if (!$this->odometer_before || !$this->odometer_after) {
            return null;
        }
        return $this->odometer_after - $this->odometer_before;
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($record) {
            // حساب التكلفة الإجمالية
            $record->total_cost = ($record->labor_cost ?? 0) + ($record->parts_cost ?? 0);
        });

        static::updating(function ($record) {
            // إعادة حساب التكلفة الإجمالية
            $record->total_cost = ($record->labor_cost ?? 0) + ($record->parts_cost ?? 0);
        });
    }
}
