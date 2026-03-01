<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarMaintenance extends Model
{
    use HasFactory;

    protected $table = 'car_maintenances';

    protected $fillable = [
        'company_code',
        'branch_id',
        'car_id',
        'maintenance_type',
        'title',
        'description',
        'total_cost',
        'parts_cost',
        'labor_cost',
        'maintenance_date',
        'next_maintenance_date',
        'next_maintenance_km',
        'odometer_reading',
        'performed_by',
        'workshop_name',
        'workshop_phone',
        'invoice_number',
        'status',
        'notes',
        'attachment',
        'created_by',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'total_cost' => 'decimal:2',
        'parts_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
    ];

    // ==================== العلاقات ====================

    /**
     * السيارة
     */
    public function car()
    {
        return $this->belongsTo(Cars::class, 'car_id');
    }

    /**
     * الفرع
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * المنشئ
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==================== الثوابت ====================

    const TYPE_PERIODIC = 'periodic';
    const TYPE_EMERGENCY = 'emergency';
    const TYPE_REPAIR = 'repair';
    const TYPE_INSPECTION = 'inspection';
    const TYPE_OIL_CHANGE = 'oil_change';
    const TYPE_TIRES = 'tires';
    const TYPE_OTHER = 'other';

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * أنواع الصيانة مع التفاصيل
     */
    public static function getMaintenanceTypes()
    {
        return [
            self::TYPE_PERIODIC => ['name' => 'صيانة دورية', 'icon' => '🔧', 'color' => '#3B82F6'],
            self::TYPE_EMERGENCY => ['name' => 'صيانة طارئة', 'icon' => '🚨', 'color' => '#EF4444'],
            self::TYPE_REPAIR => ['name' => 'إصلاح', 'icon' => '🛠️', 'color' => '#F59E0B'],
            self::TYPE_INSPECTION => ['name' => 'فحص', 'icon' => '🔍', 'color' => '#10B981'],
            self::TYPE_OIL_CHANGE => ['name' => 'تغيير زيت', 'icon' => '🛢️', 'color' => '#6366F1'],
            self::TYPE_TIRES => ['name' => 'إطارات', 'icon' => '🛞', 'color' => '#8B5CF6'],
            self::TYPE_OTHER => ['name' => 'أخرى', 'icon' => '📋', 'color' => '#6B7280'],
        ];
    }

    /**
     * حالات الصيانة مع التفاصيل
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_SCHEDULED => ['name' => 'مجدولة', 'icon' => '📅', 'color' => '#3B82F6'],
            self::STATUS_IN_PROGRESS => ['name' => 'قيد التنفيذ', 'icon' => '⏳', 'color' => '#F59E0B'],
            self::STATUS_COMPLETED => ['name' => 'مكتملة', 'icon' => '✅', 'color' => '#10B981'],
            self::STATUS_CANCELLED => ['name' => 'ملغية', 'icon' => '❌', 'color' => '#EF4444'],
        ];
    }

    /**
     * اسم نوع الصيانة
     */
    public function getTypeNameAttribute()
    {
        $types = self::getMaintenanceTypes();
        return $types[$this->maintenance_type]['name'] ?? $this->maintenance_type;
    }

    /**
     * أيقونة نوع الصيانة
     */
    public function getTypeIconAttribute()
    {
        $types = self::getMaintenanceTypes();
        return $types[$this->maintenance_type]['icon'] ?? '🔧';
    }

    /**
     * لون نوع الصيانة
     */
    public function getTypeColorAttribute()
    {
        $types = self::getMaintenanceTypes();
        return $types[$this->maintenance_type]['color'] ?? '#6B7280';
    }

    /**
     * اسم الحالة
     */
    public function getStatusNameAttribute()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status]['name'] ?? $this->status;
    }

    /**
     * أيقونة الحالة
     */
    public function getStatusIconAttribute()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status]['icon'] ?? '📋';
    }

    /**
     * لون الحالة
     */
    public function getStatusColorAttribute()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status]['color'] ?? '#6B7280';
    }

    /**
     * التكلفة الإجمالية
     */
    public function getTotalCostAttribute()
    {
        return ($this->parts_cost ?? 0) + ($this->labor_cost ?? 0);
    }
}
