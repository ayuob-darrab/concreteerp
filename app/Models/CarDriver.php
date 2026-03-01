<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * CarDriver Model - نموذج سائقي السيارات
 * 
 * يمثل العلاقة بين السيارات والسائقين حسب الشفتات
 * يدعم تعيين سائق رئيسي واحتياطي لكل شفت لكل سيارة
 */
class CarDriver extends Model
{
    use HasFactory;

    protected $table = 'car_drivers';

    // أنواع السائقين
    const TYPE_PRIMARY = 'primary';
    const TYPE_BACKUP = 'backup';

    protected $fillable = [
        'company_code',
        'car_id',
        'driver_id',
        'shift_id',
        'driver_type',
        'is_active',
        'assigned_date',
        'end_date',
        'end_reason',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'assigned_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * علاقة بالسيارة
     */
    public function car()
    {
        return $this->belongsTo(Cars::class, 'car_id');
    }

    /**
     * علاقة بالسائق (الموظف)
     */
    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    /**
     * علاقة بالشفت
     */
    public function shift()
    {
        return $this->belongsTo(ShiftTime::class, 'shift_id');
    }

    /**
     * هل السائق رئيسي؟
     */
    public function isPrimary()
    {
        return $this->driver_type === self::TYPE_PRIMARY;
    }

    /**
     * هل السائق احتياطي؟
     */
    public function isBackup()
    {
        return $this->driver_type === self::TYPE_BACKUP;
    }

    /**
     * Scope للسائقين النشطين فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope للسائقين الرئيسيين
     */
    public function scopePrimary($query)
    {
        return $query->where('driver_type', self::TYPE_PRIMARY);
    }

    /**
     * Scope للسائقين الاحتياطيين
     */
    public function scopeBackup($query)
    {
        return $query->where('driver_type', self::TYPE_BACKUP);
    }

    /**
     * Scope حسب الشفت
     */
    public function scopeByShift($query, $shiftId)
    {
        return $query->where('shift_id', $shiftId);
    }

    /**
     * إنهاء تكليف السائق
     */
    public function endAssignment($reason = null)
    {
        $this->update([
            'is_active' => false,
            'end_date' => now(),
            'end_reason' => $reason,
        ]);
    }

    /**
     * الحصول على اسم نوع السائق بالعربية
     */
    public function getDriverTypeNameAttribute()
    {
        return $this->driver_type === self::TYPE_PRIMARY ? 'رئيسي' : 'احتياطي';
    }
}
