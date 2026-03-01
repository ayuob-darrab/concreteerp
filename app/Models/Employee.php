<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;


    protected $table = 'employees';

    protected $fillable = [
        'id',
        'user_id',
        'company_code',
        'branch_id',
        'fullname',
        'employee_types_id',
        'shift_id',
        'isactive',
        'createdate',
        'file',
        'personImage',
        'phone',
        'salary',
        'email',
    ];

    // العلاقات
    public function Companyname()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }
    public function Branchesname()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'employee_types_id');
    }

    public function shift()
    {
        return $this->belongsTo(ShiftTime::class, 'shift_id');
    }

    /**
     * العلاقة مع جدول employee_shifts (الشفتات المتعددة)
     */
    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class, 'employee_id');
    }

    /**
     * الشفتات النشطة للموظف
     */
    public function activeShifts()
    {
        return $this->hasMany(EmployeeShift::class, 'employee_id')->where('is_active', true);
    }

    /**
     * الشفت الرئيسي للموظف
     */
    public function primaryShift()
    {
        return $this->hasOne(EmployeeShift::class, 'employee_id')
            ->where('is_active', true)
            ->where('is_primary', true);
    }

    /**
     * جلب جميع الشفتات النشطة للموظف
     */
    public function getActiveShiftIdsAttribute()
    {
        return $this->activeShifts->pluck('shift_id')->toArray();
    }

    /**
     * جلب أسماء الشفتات النشطة
     */
    public function getShiftNamesAttribute()
    {
        $shifts = $this->activeShifts()->with('shift')->get();
        if ($shifts->isEmpty()) {
            // fallback للنظام القديم
            return $this->shift ? $this->shift->name : 'غير محدد';
        }
        return $shifts->map(function ($es) {
            $name = $es->shift ? $es->shift->name : 'غير محدد';
            return $es->is_primary ? "⭐ {$name}" : $name;
        })->implode(' ، ');
    }

    /**
     * التحقق من أن الموظف يعمل في شفت معين
     */
    public function worksInShift($shiftId)
    {
        // التحقق من الجدول الجديد أولاً
        $hasNewShift = $this->activeShifts()->where('shift_id', $shiftId)->exists();
        if ($hasNewShift) {
            return true;
        }
        // fallback للنظام القديم
        return $this->shift_id == $shiftId;
    }

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * هل الموظف سائق؟
     */
    public function getIsDriverAttribute()
    {
        // التحقق من نوع الموظف - سائق (يتضمن أي نوع يحتوي على كلمة سائق)
        if (!$this->employeeType) {
            return false;
        }
        $typeName = $this->employeeType->name;
        return str_contains($typeName, 'سائق') || str_contains(strtolower($typeName), 'driver');
    }

    /**
     * Scope للسائقين فقط
     */
    public function scopeDrivers($query)
    {
        return $query->whereHas('employeeType', function ($q) {
            $q->where('name', 'like', '%سائق%')
                ->orWhere('name', 'like', '%driver%');
        });
    }
}
