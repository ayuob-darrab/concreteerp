<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialReservation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'job_id',
        'material_id',
        'inventory_id',
        'quantity_reserved',
        'quantity_used',
        'status',
        'reserved_at',
        'reserved_by',
    ];

    protected $casts = [
        'quantity_reserved' => 'decimal:4',
        'quantity_used' => 'decimal:4',
        'reserved_at' => 'datetime',
    ];

    // =====================
    // الثوابت - Constants
    // =====================

    const STATUS_RESERVED = 'reserved';
    const STATUS_PARTIALLY_USED = 'partially_used';
    const STATUS_FULLY_USED = 'fully_used';
    const STATUS_RELEASED = 'released';

    const STATUSES = [
        self::STATUS_RESERVED => 'محجوز',
        self::STATUS_PARTIALLY_USED => 'مستخدم جزئياً',
        self::STATUS_FULLY_USED => 'مستخدم بالكامل',
        self::STATUS_RELEASED => 'تم الإفراج',
    ];

    const STATUS_BADGES = [
        self::STATUS_RESERVED => 'info',
        self::STATUS_PARTIALLY_USED => 'warning',
        self::STATUS_FULLY_USED => 'success',
        self::STATUS_RELEASED => 'secondary',
    ];

    // =====================
    // العلاقات - Relationships
    // =====================

    public function job()
    {
        return $this->belongsTo(WorkJob::class, 'job_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function reservedBy()
    {
        return $this->belongsTo(User::class, 'reserved_by');
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

    public function getRemainingQuantityAttribute()
    {
        return $this->quantity_reserved - $this->quantity_used;
    }

    public function getUsagePercentageAttribute()
    {
        if ($this->quantity_reserved == 0) return 0;
        return round(($this->quantity_used / $this->quantity_reserved) * 100, 2);
    }

    // =====================
    // Scopes
    // =====================

    public function scopeForJob($query, $jobId)
    {
        return $query->where('job_id', $jobId);
    }

    public function scopeForMaterial($query, $materialId)
    {
        return $query->where('material_id', $materialId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_RESERVED,
            self::STATUS_PARTIALLY_USED
        ]);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', [
            self::STATUS_FULLY_USED,
            self::STATUS_RELEASED
        ]);
    }

    // =====================
    // Methods
    // =====================

    public function useQuantity($amount)
    {
        $this->quantity_used += $amount;

        if ($this->quantity_used >= $this->quantity_reserved) {
            $this->quantity_used = $this->quantity_reserved;
            $this->status = self::STATUS_FULLY_USED;
        } else {
            $this->status = self::STATUS_PARTIALLY_USED;
        }

        $this->save();

        return $this;
    }

    public function release()
    {
        $this->status = self::STATUS_RELEASED;
        $this->save();

        return $this;
    }

    // =====================
    // Boot
    // =====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            if (!$reservation->reserved_at) {
                $reservation->reserved_at = now();
            }
        });
    }
}
