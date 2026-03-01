<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverAssignment extends Model
{
    use HasFactory;

    protected $table = 'driver_assignments';

    // أنواع التعيين
    const TYPE_PRIMARY = 'primary';
    const TYPE_BACKUP = 'backup';

    const ASSIGNMENT_TYPES = [
        self::TYPE_PRIMARY => 'سائق رئيسي',
        self::TYPE_BACKUP => 'سائق احتياطي',
    ];

    protected $fillable = [
        'company_code',
        'branch_id',
        'car_id',
        'driver_id',
        'assignment_type',
        'start_date',
        'end_date',
        'end_reason',
        'assigned_by',
        'ended_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // ==========================================
    // العلاقات
    // ==========================================

    public function car()
    {
        return $this->belongsTo(Cars::class, 'car_id');
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function ender()
    {
        return $this->belongsTo(User::class, 'ended_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * التعيينات النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->whereNull('end_date');
    }

    /**
     * التعيينات المنتهية
     */
    public function scopeEnded($query)
    {
        return $query->whereNotNull('end_date');
    }

    /**
     * التعيينات الرئيسية
     */
    public function scopePrimary($query)
    {
        return $query->where('assignment_type', self::TYPE_PRIMARY);
    }

    /**
     * التعيينات الاحتياطية
     */
    public function scopeBackup($query)
    {
        return $query->where('assignment_type', self::TYPE_BACKUP);
    }

    /**
     * تعيينات سيارة معينة
     */
    public function scopeForCar($query, $carId)
    {
        return $query->where('car_id', $carId);
    }

    /**
     * تعيينات سائق معين
     */
    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    // ==========================================
    // الدوال المساعدة
    // ==========================================

    /**
     * هل التعيين نشط؟
     */
    public function isActive()
    {
        return is_null($this->end_date);
    }

    /**
     * إنهاء التعيين
     */
    public function endAssignment($userId, $reason = null)
    {
        $this->update([
            'end_date' => now(),
            'end_reason' => $reason,
            'ended_by' => $userId,
        ]);
    }

    /**
     * الحصول على نوع التعيين بالعربية
     */
    public function getTypeNameAttribute()
    {
        return self::ASSIGNMENT_TYPES[$this->assignment_type] ?? $this->assignment_type;
    }
}
