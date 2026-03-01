<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'shipment_number',
        'planned_quantity',
        'actual_quantity',
        'mixer_id',
        'truck_id',
        'pump_id',
        'mixer_driver_id',
        'truck_driver_id',
        'pump_driver_id',
        'departure_time',
        'arrival_time',
        'work_start_time',
        'work_end_time',
        'return_time',
        'status',
        'notes',
        'driver_notes',
        'created_by',
    ];

    protected $casts = [
        'planned_quantity' => 'decimal:2',
        'actual_quantity' => 'decimal:2',
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'work_start_time' => 'datetime',
        'work_end_time' => 'datetime',
        'return_time' => 'datetime',
    ];

    // =====================
    // الثوابت - Constants
    // =====================

    const STATUS_PLANNED = 'planned';
    const STATUS_PREPARING = 'preparing';
    const STATUS_DEPARTED = 'departed';
    const STATUS_ARRIVED = 'arrived';
    const STATUS_WORKING = 'working';
    const STATUS_COMPLETED = 'completed';
    const STATUS_RETURNED = 'returned';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PLANNED => 'مخطط',
        self::STATUS_PREPARING => 'جاري التحضير',
        self::STATUS_DEPARTED => 'انطلق',
        self::STATUS_ARRIVED => 'وصل',
        self::STATUS_WORKING => 'يعمل',
        self::STATUS_COMPLETED => 'أكمل',
        self::STATUS_RETURNED => 'عاد',
        self::STATUS_CANCELLED => 'ملغي',
    ];

    const STATUS_BADGES = [
        self::STATUS_PLANNED => 'secondary',
        self::STATUS_PREPARING => 'info',
        self::STATUS_DEPARTED => 'primary',
        self::STATUS_ARRIVED => 'info',
        self::STATUS_WORKING => 'warning',
        self::STATUS_COMPLETED => 'success',
        self::STATUS_RETURNED => 'dark',
        self::STATUS_CANCELLED => 'danger',
    ];

    const STATUS_ICONS = [
        self::STATUS_PLANNED => 'fa-clock',
        self::STATUS_PREPARING => 'fa-cogs',
        self::STATUS_DEPARTED => 'fa-truck',
        self::STATUS_ARRIVED => 'fa-map-marker-alt',
        self::STATUS_WORKING => 'fa-hard-hat',
        self::STATUS_COMPLETED => 'fa-check-circle',
        self::STATUS_RETURNED => 'fa-home',
        self::STATUS_CANCELLED => 'fa-times-circle',
    ];

    // =====================
    // العلاقات - Relationships
    // =====================

    public function job()
    {
        return $this->belongsTo(WorkJob::class, 'job_id');
    }

    public function mixer()
    {
        return $this->belongsTo(Cars::class, 'mixer_id');
    }

    public function truck()
    {
        return $this->belongsTo(Cars::class, 'truck_id');
    }

    public function pump()
    {
        return $this->belongsTo(Cars::class, 'pump_id');
    }

    public function mixerDriver()
    {
        return $this->belongsTo(Employee::class, 'mixer_driver_id');
    }

    public function truckDriver()
    {
        return $this->belongsTo(Employee::class, 'truck_driver_id');
    }

    public function pumpDriver()
    {
        return $this->belongsTo(Employee::class, 'pump_driver_id');
    }

    public function events()
    {
        return $this->hasMany(ShipmentEvent::class, 'shipment_id');
    }

    public function locationLogs()
    {
        return $this->hasMany(LocationLog::class, 'shipment_id');
    }

    public function losses()
    {
        return $this->hasMany(WorkLoss::class, 'shipment_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    public function getStatusIconAttribute()
    {
        return self::STATUS_ICONS[$this->status] ?? 'fa-circle';
    }

    public function getWorkDurationAttribute()
    {
        if (!$this->work_start_time || !$this->work_end_time) {
            return null;
        }
        return $this->work_start_time->diffInMinutes($this->work_end_time);
    }

    public function getTotalTripDurationAttribute()
    {
        if (!$this->departure_time || !$this->return_time) {
            return null;
        }
        return $this->departure_time->diffInMinutes($this->return_time);
    }

    public function getIsActiveAttribute()
    {
        return in_array($this->status, [
            self::STATUS_DEPARTED,
            self::STATUS_ARRIVED,
            self::STATUS_WORKING
        ]);
    }

    public function getCanDepartAttribute()
    {
        return in_array($this->status, [self::STATUS_PLANNED, self::STATUS_PREPARING]);
    }

    public function getCanArriveAttribute()
    {
        return $this->status === self::STATUS_DEPARTED;
    }

    public function getCanStartWorkAttribute()
    {
        return $this->status === self::STATUS_ARRIVED;
    }

    public function getCanEndWorkAttribute()
    {
        return $this->status === self::STATUS_WORKING;
    }

    public function getCanReturnAttribute()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    // =====================
    // Scopes
    // =====================

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_DEPARTED,
            self::STATUS_ARRIVED,
            self::STATUS_WORKING
        ]);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', [self::STATUS_COMPLETED, self::STATUS_RETURNED]);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_PLANNED, self::STATUS_PREPARING]);
    }

    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where(function ($q) use ($vehicleId) {
            $q->where('mixer_id', $vehicleId)
                ->orWhere('truck_id', $vehicleId)
                ->orWhere('pump_id', $vehicleId);
        });
    }

    public function scopeForDriver($query, $driverId)
    {
        return $query->where(function ($q) use ($driverId) {
            $q->where('mixer_driver_id', $driverId)
                ->orWhere('truck_driver_id', $driverId)
                ->orWhere('pump_driver_id', $driverId);
        });
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // =====================
    // Methods
    // =====================

    public function recordEvent($eventType, $description = null, $latitude = null, $longitude = null, $metadata = null)
    {
        return $this->events()->create([
            'event_type' => $eventType,
            'description' => $description,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'metadata' => $metadata,
            'recorded_by' => auth()->id(),
        ]);
    }

    public function getNextStatus()
    {
        $statusFlow = [
            self::STATUS_PLANNED => self::STATUS_PREPARING,
            self::STATUS_PREPARING => self::STATUS_DEPARTED,
            self::STATUS_DEPARTED => self::STATUS_ARRIVED,
            self::STATUS_ARRIVED => self::STATUS_WORKING,
            self::STATUS_WORKING => self::STATUS_COMPLETED,
            self::STATUS_COMPLETED => self::STATUS_RETURNED,
        ];

        return $statusFlow[$this->status] ?? null;
    }

    public function getLastLocation()
    {
        return $this->locationLogs()->latest('recorded_at')->first();
    }
}
