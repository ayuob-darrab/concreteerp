<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleReservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehicle_reservations';

    // حالات الحجز
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_IN_USE = 'in_use';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING => 'معلق',
        self::STATUS_CONFIRMED => 'مؤكد',
        self::STATUS_IN_USE => 'قيد الاستخدام',
        self::STATUS_COMPLETED => 'مكتمل',
        self::STATUS_CANCELLED => 'ملغي',
    ];

    protected $fillable = [
        'vehicle_id',
        'order_id',
        'job_id',
        'reserved_from',
        'reserved_to',
        'driver_id',
        'status',
        'purpose',
        'notes',
        'reserved_by',
    ];

    protected $casts = [
        'reserved_from' => 'datetime',
        'reserved_to' => 'datetime',
    ];

    // العلاقات
    public function vehicle()
    {
        return $this->belongsTo(Cars::class, 'vehicle_id');
    }

    public function order()
    {
        return $this->belongsTo(WorkOrder::class, 'order_id');
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    public function reserver()
    {
        return $this->belongsTo(User::class, 'reserved_by');
    }

    // Scopes
    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeForOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_IN_USE]);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('reserved_from', '>', now())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function scopeCurrent($query)
    {
        return $query->where('reserved_from', '<=', now())
            ->where('reserved_to', '>=', now())
            ->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_IN_USE]);
    }

    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->where(function ($q) use ($from, $to) {
            $q->whereBetween('reserved_from', [$from, $to])
                ->orWhereBetween('reserved_to', [$from, $to])
                ->orWhere(function ($q2) use ($from, $to) {
                    $q2->where('reserved_from', '<=', $from)
                        ->where('reserved_to', '>=', $to);
                });
        });
    }

    // الدوال المساعدة
    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_IN_USE => 'primary',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger',
        ];
        return $colors[$this->status] ?? 'secondary';
    }

    public function getDurationHoursAttribute()
    {
        return $this->reserved_from->diffInHours($this->reserved_to);
    }

    public function isActive()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_IN_USE]);
    }

    public function isCurrent()
    {
        return $this->reserved_from->isPast() && $this->reserved_to->isFuture()
            && in_array($this->status, [self::STATUS_CONFIRMED, self::STATUS_IN_USE]);
    }

    public function canBeConfirmed()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeCancelled()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    // التحقق من تعارض الحجز
    public static function hasConflict($vehicleId, $from, $to, $excludeId = null)
    {
        $query = self::forVehicle($vehicleId)
            ->active()
            ->betweenDates($from, $to);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    // الحصول على الحجوزات لتاريخ معين
    public static function getForDate($date, $vehicleId = null)
    {
        $query = self::whereDate('reserved_from', '<=', $date)
            ->whereDate('reserved_to', '>=', $date)
            ->active();

        if ($vehicleId) {
            $query->forVehicle($vehicleId);
        }

        return $query->with(['vehicle', 'driver', 'order'])->get();
    }
}
