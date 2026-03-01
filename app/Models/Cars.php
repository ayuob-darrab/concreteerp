<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cars extends Model
{
    use HasFactory;
    protected $table = 'cars';

    protected $fillable = [
        'id',
        'company_code',
        'branch_id',
        'car_type_id',
        'car_name',
        'car_number',
        'car_model',
        'mixer_capacity',
        'is_active',
        'driver_name',
        'driver_id',
        'backup_driver_id',
        'add_date',
        'note',
        // حقول الحالة التشغيلية
        'operational_status',
        'status_reason',
        'status_changed_at',
        'status_changed_by',
        'last_maintenance_date',
        'next_maintenance_date',
        'odometer_reading',
    ];

    // علاقة بسيارات النوع
    public function carType()
    {
        return $this->belongsTo(CarsType::class, 'car_type_id');
    }

    // علاقة بالقسم
    public function BranchName()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    // علاقة بالسائق الرئيسي
    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    // علاقة بالسائق الاحتياطي
    public function backupDriver()
    {
        return $this->belongsTo(Employee::class, 'backup_driver_id');
    }

    // ============================================
    // علاقات جدول car_drivers الجديد (دعم عدة شفتات)
    // ============================================

    /**
     * جميع تكليفات السائقين لهذه السيارة
     */
    public function carDrivers()
    {
        return $this->hasMany(CarDriver::class, 'car_id');
    }

    /**
     * تكليفات السائقين النشطة فقط
     */
    public function activeCarDrivers()
    {
        return $this->hasMany(CarDriver::class, 'car_id')->where('is_active', true);
    }

    /**
     * السائقين الرئيسيين النشطين (لجميع الشفتات)
     */
    public function primaryDrivers()
    {
        return $this->hasMany(CarDriver::class, 'car_id')
            ->where('is_active', true)
            ->where('driver_type', CarDriver::TYPE_PRIMARY);
    }

    /**
     * السائقين الاحتياطيين النشطين (لجميع الشفتات)
     */
    public function backupDrivers()
    {
        return $this->hasMany(CarDriver::class, 'car_id')
            ->where('is_active', true)
            ->where('driver_type', CarDriver::TYPE_BACKUP);
    }

    /**
     * الحصول على سائقي شفت معين
     */
    public function getDriversByShift($shiftId)
    {
        return $this->activeCarDrivers()
            ->where('shift_id', $shiftId)
            ->with('driver')
            ->get();
    }

    /**
     * الحصول على السائق الرئيسي لشفت معين
     */
    public function getPrimaryDriverForShift($shiftId)
    {
        return $this->activeCarDrivers()
            ->where('shift_id', $shiftId)
            ->where('driver_type', CarDriver::TYPE_PRIMARY)
            ->with('driver')
            ->first();
    }

    /**
     * الحصول على السائق الاحتياطي لشفت معين
     */
    public function getBackupDriverForShift($shiftId)
    {
        return $this->activeCarDrivers()
            ->where('shift_id', $shiftId)
            ->where('driver_type', CarDriver::TYPE_BACKUP)
            ->with('driver')
            ->first();
    }

    /**
     * الحصول على جميع الشفتات المعينة لهذه السيارة
     */
    public function getAssignedShiftsAttribute()
    {
        return $this->activeCarDrivers()
            ->with('shift')
            ->get()
            ->pluck('shift')
            ->unique('id');
    }

    /**
     * الشحنات النشطة لهذه السيارة
     * تستخدم للتحقق من أن السيارة مشغولة
     */
    public function activeShipments()
    {
        return $this->hasMany(WorkShipment::class, 'mixer_id')
            ->whereIn('status', [
                WorkShipment::STATUS_DEPARTED,
                WorkShipment::STATUS_ARRIVED,
                WorkShipment::STATUS_WORKING
            ]);
    }

    /**
     * جميع الشحنات لهذه السيارة (كخلاطة)
     */
    public function mixerShipments()
    {
        return $this->hasMany(WorkShipment::class, 'mixer_id');
    }

    /**
     * هل السيارة مشغولة حالياً؟
     */
    public function getIsBusyAttribute()
    {
        return $this->activeShipments()->exists();
    }

    /**
     * هل السيارة متاحة للحجز؟ (ليست مشغولة وليست في الصيانة)
     */
    public function getIsAvailableAttribute()
    {
        return !$this->is_busy && $this->operational_status !== 'in_maintenance';
    }

    /**
     * هل السيارة في الصيانة؟
     */
    public function getIsInMaintenanceAttribute()
    {
        return $this->operational_status === 'in_maintenance';
    }

    /**
     * Scope للسيارات المتاحة (غير مشغولة وليست في الصيانة)
     */
    public function scopeAvailable($query)
    {
        return $query->whereDoesntHave('activeShipments')
            ->where(function ($q) {
                $q->whereNull('operational_status')
                    ->orWhere('operational_status', '!=', 'in_maintenance');
            });
    }

    /**
     * Scope للسيارات المشغولة
     */
    public function scopeBusy($query)
    {
        return $query->whereHas('activeShipments');
    }

    /**
     * Scope للسيارات في الصيانة
     */
    public function scopeInMaintenance($query)
    {
        return $query->where('operational_status', 'in_maintenance');
    }

    /**
     * سجل صيانات السيارة
     */
    public function maintenances()
    {
        return $this->hasMany(CarMaintenance::class, 'car_id');
    }

    /**
     * آخر صيانة للسيارة
     */
    public function lastMaintenance()
    {
        return $this->hasOne(CarMaintenance::class, 'car_id')
            ->where('status', 'completed')
            ->orderBy('maintenance_date', 'desc');
    }

    /**
     * الصيانات القادمة المجدولة
     */
    public function scheduledMaintenances()
    {
        return $this->hasMany(CarMaintenance::class, 'car_id')
            ->where('status', 'scheduled')
            ->orderBy('maintenance_date', 'asc');
    }

    /**
     * إجمالي تكاليف الصيانة
     */
    public function getTotalMaintenanceCostAttribute()
    {
        return $this->maintenances()
            ->where('status', 'completed')
            ->sum('total_cost');
    }
}
