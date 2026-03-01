<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkLoss extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_code',
        'branch_id',
        'job_id',
        'shipment_id',
        'vehicle_id',
        'loss_type',
        'quantity_lost',
        'estimated_cost',
        'actual_cost',
        'description',
        'location_description',
        'latitude',
        'longitude',
        'attachments',
        'investigation_notes',
        'investigated_by',
        'investigated_at',
        'resolution',
        'resolution_date',
        'status',
        'reported_by',
        'reported_at',
    ];

    protected $casts = [
        'quantity_lost' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'attachments' => 'array',
        'investigated_at' => 'datetime',
        'resolution_date' => 'date',
        'reported_at' => 'datetime',
    ];

    // =====================
    // الثوابت - Constants
    // =====================

    // أنواع الخسائر
    const TYPE_ACCIDENT = 'accident';
    const TYPE_VEHICLE_BREAKDOWN = 'vehicle_breakdown';
    const TYPE_MATERIAL_SPOILAGE = 'material_spoilage';
    const TYPE_SPILLAGE = 'spillage';
    const TYPE_REJECTION = 'rejection';
    const TYPE_WEATHER = 'weather';
    const TYPE_ROAD_ISSUE = 'road_issue';
    const TYPE_OTHER = 'other';

    const TYPES = [
        self::TYPE_ACCIDENT => 'حادث',
        self::TYPE_VEHICLE_BREAKDOWN => 'عطل آلية',
        self::TYPE_MATERIAL_SPOILAGE => 'تلف مواد',
        self::TYPE_SPILLAGE => 'انسكاب',
        self::TYPE_REJECTION => 'رفض من العميل',
        self::TYPE_WEATHER => 'ظروف جوية',
        self::TYPE_ROAD_ISSUE => 'مشكلة طريق',
        self::TYPE_OTHER => 'أخرى',
    ];

    const TYPE_ICONS = [
        self::TYPE_ACCIDENT => 'fa-car-crash',
        self::TYPE_VEHICLE_BREAKDOWN => 'fa-tools',
        self::TYPE_MATERIAL_SPOILAGE => 'fa-box-open',
        self::TYPE_SPILLAGE => 'fa-tint-slash',
        self::TYPE_REJECTION => 'fa-user-times',
        self::TYPE_WEATHER => 'fa-cloud-rain',
        self::TYPE_ROAD_ISSUE => 'fa-road',
        self::TYPE_OTHER => 'fa-question-circle',
    ];

    // حالات الخسارة
    const STATUS_REPORTED = 'reported';
    const STATUS_INVESTIGATING = 'investigating';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    const STATUSES = [
        self::STATUS_REPORTED => 'تم الإبلاغ',
        self::STATUS_INVESTIGATING => 'قيد التحقيق',
        self::STATUS_RESOLVED => 'تم الحل',
        self::STATUS_CLOSED => 'مغلق',
    ];

    const STATUS_BADGES = [
        self::STATUS_REPORTED => 'danger',
        self::STATUS_INVESTIGATING => 'warning',
        self::STATUS_RESOLVED => 'success',
        self::STATUS_CLOSED => 'secondary',
    ];

    // =====================
    // العلاقات - Relationships
    // =====================

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function job()
    {
        return $this->belongsTo(WorkJob::class, 'job_id');
    }

    public function shipment()
    {
        return $this->belongsTo(WorkShipment::class, 'shipment_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Cars::class, 'vehicle_id');
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function investigatedBy()
    {
        return $this->belongsTo(User::class, 'investigated_by');
    }

    // =====================
    // Accessors
    // =====================

    public function getTypeLabelAttribute()
    {
        return self::TYPES[$this->loss_type] ?? $this->loss_type;
    }

    public function getTypeIconAttribute()
    {
        return self::TYPE_ICONS[$this->loss_type] ?? 'fa-exclamation-triangle';
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        return self::STATUS_BADGES[$this->status] ?? 'secondary';
    }

    public function getTotalCostAttribute()
    {
        return $this->actual_cost ?? $this->estimated_cost;
    }

    public function getHasLocationAttribute()
    {
        return $this->latitude && $this->longitude;
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

    public function scopeOfType($query, $type)
    {
        return $query->where('loss_type', $type);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_REPORTED, self::STATUS_INVESTIGATING]);
    }

    public function scopeResolved($query)
    {
        return $query->whereIn('status', [self::STATUS_RESOLVED, self::STATUS_CLOSED]);
    }

    public function scopeReportedBetween($query, $from, $to)
    {
        return $query->whereBetween('reported_at', [$from, $to]);
    }

    // =====================
    // Boot
    // =====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($loss) {
            if (!$loss->reported_at) {
                $loss->reported_at = now();
            }
        });
    }
}
