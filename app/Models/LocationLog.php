<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'shipment_id',
        'vehicle_id',
        'driver_id',
        'latitude',
        'longitude',
        'speed',
        'heading',
        'accuracy',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'speed' => 'decimal:2',
        'heading' => 'decimal:2',
        'accuracy' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    // =====================
    // العلاقات - Relationships
    // =====================

    public function shipment()
    {
        return $this->belongsTo(WorkShipment::class, 'shipment_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Cars::class, 'vehicle_id');
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    // =====================
    // Accessors
    // =====================

    public function getCoordinatesAttribute()
    {
        return [
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude,
        ];
    }

    public function getSpeedKmhAttribute()
    {
        return $this->speed ? round($this->speed, 1) . ' كم/س' : null;
    }

    public function getHeadingDirectionAttribute()
    {
        if (!$this->heading) return null;

        $directions = ['شمال', 'شمال شرق', 'شرق', 'جنوب شرق', 'جنوب', 'جنوب غرب', 'غرب', 'شمال غرب'];
        $index = round($this->heading / 45) % 8;
        return $directions[$index];
    }

    // =====================
    // Scopes
    // =====================

    public function scopeForShipment($query, $shipmentId)
    {
        return $query->where('shipment_id', $shipmentId);
    }

    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeBetween($query, $from, $to)
    {
        return $query->whereBetween('recorded_at', [$from, $to]);
    }

    public function scopeRecent($query, $minutes = 60)
    {
        return $query->where('recorded_at', '>=', now()->subMinutes($minutes));
    }

    // =====================
    // Methods
    // =====================

    public function distanceTo($lat, $lng)
    {
        // حساب المسافة بالكيلومتر باستخدام صيغة Haversine
        $earthRadius = 6371; // km

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }

    // =====================
    // Boot
    // =====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            if (!$log->recorded_at) {
                $log->recorded_at = now();
            }
        });
    }
}
