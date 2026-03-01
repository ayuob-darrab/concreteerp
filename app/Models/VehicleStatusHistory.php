<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'vehicle_status_history';
    public $timestamps = false;

    // الحالات
    const STATUS_AVAILABLE = 'available';
    const STATUS_RESERVED = 'reserved';
    const STATUS_IN_MAINTENANCE = 'in_maintenance';
    const STATUS_OUT_OF_SERVICE = 'out_of_service';
    const STATUS_SCRAPPED = 'scrapped';

    const STATUSES = [
        self::STATUS_AVAILABLE => 'متاحة',
        self::STATUS_RESERVED => 'محجوزة',
        self::STATUS_IN_MAINTENANCE => 'في الصيانة',
        self::STATUS_OUT_OF_SERVICE => 'خارج الخدمة',
        self::STATUS_SCRAPPED => 'مشطوبة',
    ];

    protected $fillable = [
        'vehicle_id',
        'old_status',
        'new_status',
        'reason',
        'related_type',
        'related_id',
        'changed_by',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    // العلاقات
    public function vehicle()
    {
        return $this->belongsTo(Cars::class, 'vehicle_id');
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Scopes
    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('changed_at', 'desc');
    }

    // الدوال المساعدة
    public function getOldStatusLabelAttribute()
    {
        return $this->old_status ? (self::STATUSES[$this->old_status] ?? $this->old_status) : '-';
    }

    public function getNewStatusLabelAttribute()
    {
        return self::STATUSES[$this->new_status] ?? $this->new_status;
    }

    // تسجيل تغيير الحالة
    public static function logChange($vehicleId, $oldStatus, $newStatus, $reason = null, $relatedType = null, $relatedId = null)
    {
        return self::create([
            'vehicle_id' => $vehicleId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'reason' => $reason,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);
    }

    // الحصول على سجل الآلية
    public static function getVehicleHistory($vehicleId, $limit = 50)
    {
        return self::forVehicle($vehicleId)
            ->recent()
            ->with('changer')
            ->limit($limit)
            ->get();
    }
}
