<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * Attendance Model - نموذج الحضور والانصراف
 * 
 * يدير تسجيل حضور وانصراف الموظفين ويحسب التأخير والعمل الإضافي
 */
class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attendances';

    // حالات الحضور
    const STATUS_PRESENT = 'present';       // حاضر
    const STATUS_LATE = 'late';             // متأخر
    const STATUS_ABSENT = 'absent';         // غائب
    const STATUS_ON_LEAVE = 'on_leave';     // في إجازة
    const STATUS_ON_MISSION = 'on_mission'; // مأمورية
    const STATUS_SICK_LEAVE = 'sick_leave'; // إجازة مرضية
    const STATUS_HALF_DAY = 'half_day';     // نصف يوم

    protected $fillable = [
        'company_code',
        'branch_id',
        'employee_id',
        'user_id',
        'shift_id',
        'attendance_date',
        'check_in_time',
        'shift_start_time',
        'late_minutes',
        'early_minutes',
        'check_out_time',
        'shift_end_time',
        'early_leave_minutes',
        'overtime_minutes',
        'total_work_minutes',
        'status',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'notes',
        'manager_notes',
        'modified_by',
        'modified_at',
        'modification_reason',
        'check_in_ip',
        'check_out_ip',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime:H:i:s',
        'check_out_time' => 'datetime:H:i:s',
        'shift_start_time' => 'datetime:H:i:s',
        'shift_end_time' => 'datetime:H:i:s',
        'modified_at' => 'datetime',
        'late_minutes' => 'integer',
        'early_minutes' => 'integer',
        'early_leave_minutes' => 'integer',
        'overtime_minutes' => 'integer',
        'total_work_minutes' => 'integer',
        'check_in_latitude' => 'decimal:8',
        'check_in_longitude' => 'decimal:8',
        'check_out_latitude' => 'decimal:8',
        'check_out_longitude' => 'decimal:8',
    ];

    /**
     * علاقة بالموظف
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * علاقة بالمستخدم الذي سجل الحضور
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة بالشفت
     */
    public function shift()
    {
        return $this->belongsTo(ShiftTime::class, 'shift_id');
    }

    /**
     * علاقة بالفرع
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * علاقة بمن قام بالتعديل
     */
    public function modifier()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    /**
     * الحصول على أسماء الحالات
     */
    public static function getStatusLabels()
    {
        return [
            self::STATUS_PRESENT => 'حاضر',
            self::STATUS_LATE => 'متأخر',
            self::STATUS_ABSENT => 'غائب',
            self::STATUS_ON_LEAVE => 'في إجازة',
            self::STATUS_ON_MISSION => 'مأمورية',
            self::STATUS_SICK_LEAVE => 'إجازة مرضية',
            self::STATUS_HALF_DAY => 'نصف يوم',
        ];
    }

    /**
     * الحصول على اسم الحالة
     */
    public function getStatusLabelAttribute()
    {
        return self::getStatusLabels()[$this->status] ?? $this->status;
    }

    /**
     * الحصول على لون الحالة للعرض
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_PRESENT => 'success',
            self::STATUS_LATE => 'warning',
            self::STATUS_ABSENT => 'danger',
            self::STATUS_ON_LEAVE => 'info',
            self::STATUS_ON_MISSION => 'primary',
            self::STATUS_SICK_LEAVE => 'secondary',
            self::STATUS_HALF_DAY => 'warning',
            default => 'secondary',
        };
    }

    /**
     * تحويل الدقائق إلى صيغة الساعات والدقائق
     */
    public static function minutesToHumanReadable($minutes)
    {
        if ($minutes <= 0) return '0 دقيقة';

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        $result = [];
        if ($hours > 0) $result[] = $hours . ' ساعة';
        if ($mins > 0) $result[] = $mins . ' دقيقة';

        return implode(' و ', $result);
    }

    /**
     * الحصول على مدة التأخير بصيغة قابلة للقراءة
     */
    public function getLateHumanAttribute()
    {
        return self::minutesToHumanReadable($this->late_minutes);
    }

    /**
     * الحصول على مدة العمل الإضافي بصيغة قابلة للقراءة
     */
    public function getOvertimeHumanAttribute()
    {
        return self::minutesToHumanReadable($this->overtime_minutes);
    }

    /**
     * الحصول على إجمالي وقت العمل بصيغة قابلة للقراءة
     */
    public function getTotalWorkHumanAttribute()
    {
        return self::minutesToHumanReadable($this->total_work_minutes);
    }

    /**
     * حساب التأخير بناءً على وقت الحضور ووقت بدء الشفت
     */
    public static function calculateLateMinutes($checkInTime, $shiftStartTime)
    {
        $checkIn = Carbon::parse($checkInTime);
        $shiftStart = Carbon::parse($shiftStartTime);

        if ($checkIn->gt($shiftStart)) {
            return $checkIn->diffInMinutes($shiftStart);
        }

        return 0;
    }

    /**
     * حساب الحضور المبكر
     */
    public static function calculateEarlyMinutes($checkInTime, $shiftStartTime)
    {
        $checkIn = Carbon::parse($checkInTime);
        $shiftStart = Carbon::parse($shiftStartTime);

        if ($checkIn->lt($shiftStart)) {
            return $shiftStart->diffInMinutes($checkIn);
        }

        return 0;
    }

    /**
     * حساب الخروج المبكر
     */
    public static function calculateEarlyLeaveMinutes($checkOutTime, $shiftEndTime)
    {
        $checkOut = Carbon::parse($checkOutTime);
        $shiftEnd = Carbon::parse($shiftEndTime);

        if ($checkOut->lt($shiftEnd)) {
            return $shiftEnd->diffInMinutes($checkOut);
        }

        return 0;
    }

    /**
     * حساب العمل الإضافي
     */
    public static function calculateOvertimeMinutes($checkOutTime, $shiftEndTime)
    {
        $checkOut = Carbon::parse($checkOutTime);
        $shiftEnd = Carbon::parse($shiftEndTime);

        if ($checkOut->gt($shiftEnd)) {
            return $checkOut->diffInMinutes($shiftEnd);
        }

        return 0;
    }

    /**
     * حساب إجمالي وقت العمل
     */
    public static function calculateTotalWorkMinutes($checkInTime, $checkOutTime)
    {
        if (!$checkInTime || !$checkOutTime) return 0;

        $checkIn = Carbon::parse($checkInTime);
        $checkOut = Carbon::parse($checkOutTime);

        return $checkOut->diffInMinutes($checkIn);
    }

    /**
     * تحديد حالة الحضور تلقائياً
     */
    public static function determineStatus($lateMinutes, $isLeave = false, $isMission = false, $isSickLeave = false)
    {
        if ($isSickLeave) return self::STATUS_SICK_LEAVE;
        if ($isLeave) return self::STATUS_ON_LEAVE;
        if ($isMission) return self::STATUS_ON_MISSION;
        if ($lateMinutes > 0) return self::STATUS_LATE;

        return self::STATUS_PRESENT;
    }

    // ========================= Scopes =========================

    /**
     * Scope للشركة
     */
    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    /**
     * Scope للفرع
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope لموظف معين
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope لتاريخ معين
     */
    public function scopeOnDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }

    /**
     * Scope لنطاق تاريخي
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }

    /**
     * Scope لحالة معينة
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope للمتأخرين فقط
     */
    public function scopeLateOnly($query)
    {
        return $query->where('status', self::STATUS_LATE);
    }

    /**
     * Scope للحاضرين فقط
     */
    public function scopePresentOnly($query)
    {
        return $query->whereIn('status', [self::STATUS_PRESENT, self::STATUS_LATE]);
    }

    /**
     * Scope للغائبين فقط
     */
    public function scopeAbsentOnly($query)
    {
        return $query->where('status', self::STATUS_ABSENT);
    }

    /**
     * Scope لمن لم يسجلوا الانصراف بعد
     */
    public function scopeNotCheckedOut($query)
    {
        return $query->whereNull('check_out_time');
    }

    /**
     * Scope لحضور اليوم
     */
    public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', Carbon::today());
    }

    /**
     * Scope لهذا الأسبوع
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('attendance_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope لهذا الشهر
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('attendance_date', Carbon::now()->month)
            ->whereYear('attendance_date', Carbon::now()->year);
    }

    /**
     * التحقق من وجود تسجيل حضور لموظف في يوم معين
     */
    public static function hasCheckedInToday($employeeId, $date = null)
    {
        $date = $date ?? Carbon::today();

        return self::where('employee_id', $employeeId)
            ->whereDate('attendance_date', $date)
            ->exists();
    }

    /**
     * الحصول على آخر تسجيل حضور لموظف
     */
    public static function getLastAttendance($employeeId)
    {
        return self::where('employee_id', $employeeId)
            ->orderBy('attendance_date', 'desc')
            ->orderBy('check_in_time', 'desc')
            ->first();
    }

    /**
     * إحصائيات حضور الموظف لفترة معينة
     */
    public static function getEmployeeStats($employeeId, $startDate, $endDate)
    {
        $attendances = self::where('employee_id', $employeeId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        return [
            'total_days' => $attendances->count(),
            'present_days' => $attendances->whereIn('status', [self::STATUS_PRESENT, self::STATUS_LATE])->count(),
            'late_days' => $attendances->where('status', self::STATUS_LATE)->count(),
            'absent_days' => $attendances->where('status', self::STATUS_ABSENT)->count(),
            'leave_days' => $attendances->whereIn('status', [self::STATUS_ON_LEAVE, self::STATUS_SICK_LEAVE])->count(),
            'total_late_minutes' => $attendances->sum('late_minutes'),
            'total_overtime_minutes' => $attendances->sum('overtime_minutes'),
            'total_work_minutes' => $attendances->sum('total_work_minutes'),
        ];
    }
}
