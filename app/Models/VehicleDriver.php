<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDriver extends Model
{
    use HasFactory;

    protected $table = 'vehicle_drivers';

    // أنواع التعيين
    const TYPE_PRIMARY = 'primary';
    const TYPE_BACKUP = 'backup';

    const ASSIGNMENT_TYPES = [
        self::TYPE_PRIMARY => 'أساسي',
        self::TYPE_BACKUP => 'احتياطي',
    ];

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'assignment_type',
        'start_date',
        'end_date',
        'is_active',
        'assigned_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // العلاقات
    public function vehicle()
    {
        return $this->belongsTo(Cars::class, 'vehicle_id');
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePrimary($query)
    {
        return $query->where('assignment_type', self::TYPE_PRIMARY);
    }

    public function scopeBackup($query)
    {
        return $query->where('assignment_type', self::TYPE_BACKUP);
    }

    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    // الدوال المساعدة
    public function getAssignmentTypeLabelAttribute()
    {
        return self::ASSIGNMENT_TYPES[$this->assignment_type] ?? $this->assignment_type;
    }

    public function isExpired()
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function isCurrent()
    {
        if (!$this->is_active) return false;
        if ($this->start_date->isFuture()) return false;
        if ($this->end_date && $this->end_date->isPast()) return false;
        return true;
    }

    // الحصول على السائق الأساسي للآلية
    public static function getPrimaryDriver($vehicleId)
    {
        return self::forVehicle($vehicleId)
            ->primary()
            ->current()
            ->first();
    }

    // الحصول على الآليات المعينة للسائق
    public static function getDriverVehicles($driverId)
    {
        return self::forDriver($driverId)
            ->current()
            ->with('vehicle')
            ->get();
    }
}
