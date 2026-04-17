<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeType extends Model
{
    use HasFactory;
    protected $table = 'employee_types';

    protected $fillable = [
        'name','id'
    ];

    /**
     * كود الحساب في جدول users: سائق (driver) أو موظف عادي (emp) حسب اسم نوع الموظف.
     */
    public static function accountCodeForUser(int|string|null $employeeTypeId): string
    {
        $empType = static::find($employeeTypeId);
        $empTypeName = (string) ($empType?->name ?? '');
        $isDriverType = $empTypeName !== ''
            && (str_contains($empTypeName, 'سائق') || str_contains(strtolower($empTypeName), 'driver'));

        return $isDriverType ? 'driver' : 'emp';
    }
}
