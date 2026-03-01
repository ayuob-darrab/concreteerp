<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'shipment_id',
        'event_type',
        'description',
        'latitude',
        'longitude',
        'metadata',
        'recorded_by',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'metadata' => 'array',
        'recorded_at' => 'datetime',
    ];

    // =====================
    // الثوابت - Constants
    // =====================

    const TYPE_CREATED = 'created';
    const TYPE_PREPARED = 'prepared';
    const TYPE_DEPARTED = 'departed';
    const TYPE_ARRIVED = 'arrived';
    const TYPE_WORK_STARTED = 'work_started';
    const TYPE_WORK_ENDED = 'work_ended';
    const TYPE_RETURNED = 'returned';
    const TYPE_CANCELLED = 'cancelled';
    const TYPE_ISSUE_REPORTED = 'issue_reported';
    const TYPE_LOCATION_UPDATED = 'location_updated';

    const TYPES = [
        self::TYPE_CREATED => 'إنشاء',
        self::TYPE_PREPARED => 'تم التحضير',
        self::TYPE_DEPARTED => 'انطلاق',
        self::TYPE_ARRIVED => 'وصول',
        self::TYPE_WORK_STARTED => 'بدء العمل',
        self::TYPE_WORK_ENDED => 'انتهاء العمل',
        self::TYPE_RETURNED => 'عودة',
        self::TYPE_CANCELLED => 'إلغاء',
        self::TYPE_ISSUE_REPORTED => 'تقرير مشكلة',
        self::TYPE_LOCATION_UPDATED => 'تحديث الموقع',
    ];

    const TYPE_ICONS = [
        self::TYPE_CREATED => 'fa-plus-circle',
        self::TYPE_PREPARED => 'fa-check-circle',
        self::TYPE_DEPARTED => 'fa-truck',
        self::TYPE_ARRIVED => 'fa-map-marker-alt',
        self::TYPE_WORK_STARTED => 'fa-play-circle',
        self::TYPE_WORK_ENDED => 'fa-stop-circle',
        self::TYPE_RETURNED => 'fa-home',
        self::TYPE_CANCELLED => 'fa-times-circle',
        self::TYPE_ISSUE_REPORTED => 'fa-exclamation-triangle',
        self::TYPE_LOCATION_UPDATED => 'fa-location-arrow',
    ];

    const TYPE_COLORS = [
        self::TYPE_CREATED => 'info',
        self::TYPE_PREPARED => 'secondary',
        self::TYPE_DEPARTED => 'primary',
        self::TYPE_ARRIVED => 'info',
        self::TYPE_WORK_STARTED => 'warning',
        self::TYPE_WORK_ENDED => 'success',
        self::TYPE_RETURNED => 'dark',
        self::TYPE_CANCELLED => 'danger',
        self::TYPE_ISSUE_REPORTED => 'danger',
        self::TYPE_LOCATION_UPDATED => 'muted',
    ];

    // =====================
    // العلاقات - Relationships
    // =====================

    public function shipment()
    {
        return $this->belongsTo(WorkShipment::class, 'shipment_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // =====================
    // Accessors
    // =====================

    public function getTypeLabelAttribute()
    {
        return self::TYPES[$this->event_type] ?? $this->event_type;
    }

    public function getTypeIconAttribute()
    {
        return self::TYPE_ICONS[$this->event_type] ?? 'fa-circle';
    }

    public function getTypeColorAttribute()
    {
        return self::TYPE_COLORS[$this->event_type] ?? 'secondary';
    }

    public function getHasLocationAttribute()
    {
        return $this->latitude && $this->longitude;
    }

    // =====================
    // Scopes
    // =====================

    public function scopeOfType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeWithLocation($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    // =====================
    // Boot
    // =====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (!$event->recorded_at) {
                $event->recorded_at = now();
            }
        });
    }
}
