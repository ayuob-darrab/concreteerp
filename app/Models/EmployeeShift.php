<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeShift extends Model
{
    use HasFactory;

    protected $table = 'employee_shifts';

    protected $fillable = [
        'company_code',
        'employee_id',
        'shift_id',
        'is_active',
        'is_primary',
        'assigned_date',
        'end_date',
        'end_reason',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
        'assigned_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * العلاقة مع الموظف
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * العلاقة مع الشفت
     */
    public function shift()
    {
        return $this->belongsTo(ShiftTime::class, 'shift_id');
    }

    /**
     * Scope للشفتات النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope للشفت الرئيسي
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
