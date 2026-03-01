<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * نقل بيانات الشفتات من النظام القديم (shift_id في employees) إلى الجدول الجديد (employee_shifts)
     */
    public function up()
    {
        // جلب جميع الشفتات الموجودة
        $validShiftIds = DB::table('shift_times')->pluck('id')->toArray();

        // جلب جميع الموظفين الذين لديهم shift_id
        $employees = DB::table('employees')
            ->whereNotNull('shift_id')
            ->get();

        foreach ($employees as $employee) {
            // التحقق من أن الشفت موجود
            if (!in_array($employee->shift_id, $validShiftIds)) {
                continue; // تخطي السجلات غير الصالحة
            }

            // التحقق من عدم وجود السجل مسبقاً
            $exists = DB::table('employee_shifts')
                ->where('employee_id', $employee->id)
                ->where('shift_id', $employee->shift_id)
                ->where('is_active', true)
                ->exists();

            if (!$exists) {
                DB::table('employee_shifts')->insert([
                    'company_code' => $employee->company_code,
                    'employee_id' => $employee->id,
                    'shift_id' => $employee->shift_id,
                    'is_active' => true,
                    'is_primary' => true,
                    'assigned_date' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // حذف جميع السجلات المنقولة
        DB::table('employee_shifts')->truncate();
    }
};
