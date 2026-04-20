<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeType extends Model
{
    use HasFactory;

    protected $table = 'employee_types';

    /** رموز الأنواع المعيارية (ثابتة للاستخدام في الكود والتقارير) */
    public const CODE_ENGINEER = 'ENG';

    public const CODE_ACCOUNTANT = 'ACC';

    public const CODE_DRIVER = 'DRV';

    public const CODE_WAREHOUSE = 'WHS';

    public const CODE_GUARD = 'GRD';

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'employee_types_id');
    }

    /**
     * تحديد account_code للمستخدم حسب سجل نوع الموظف.
     */
    public static function accountCodeForEmployeeType(?self $type): string
    {
        if (!$type) {
            return 'emp';
        }

        if (static::isDriverType($type)) {
            return 'driver';
        }

        return 'emp';
    }

    /**
     * @deprecated استخدم accountCodeForEmployeeType بعد جلب النوع من الرمز أو المعرف.
     */
    public static function accountCodeForUser(int|string $employeeTypeId): string
    {
        return static::accountCodeForEmployeeType(static::query()->find($employeeTypeId));
    }

    public static function isDriverType(?self $type): bool
    {
        if (!$type) {
            return false;
        }

        if ($type->code === self::CODE_DRIVER) {
            return true;
        }

        $typeName = (string) $type->name;

        return str_contains($typeName, 'سائق') || str_contains(strtolower($typeName), 'driver');
    }
}
