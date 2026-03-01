<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * إنشاء حساب سائق تجريبي
     */
    public function run(): void
    {
        DB::transaction(function () {
            // التحقق من وجود الحساب مسبقاً
            $existingUser = User::where('email', 'd1@amean.info')->first();

            if ($existingUser) {
                $this->command->info('حساب السائق موجود مسبقاً: d1@amean.info');
                return;
            }

            // الحصول على أول فرع وشركة
            $branch = DB::table('branches')->first();
            $company = DB::table('companies')->first();

            if (!$branch || !$company) {
                $this->command->error('يجب وجود فرع وشركة على الأقل!');
                return;
            }

            // إنشاء مستخدم السائق
            $user = User::create([
                'fullname' => 'سائق تجريبي',
                'email' => 'd1@amean.info',
                'password' => Hash::make(Str::random(16)), // كلمة مرور عشوائية - يجب تغييرها
                'usertype_id' => 'DR', // Driver
                'account_code' => 'emp', // Employee type
                'company_code' => $company->code,
                'branch_id' => $branch->id,
                'is_active' => true,
            ]);

            $this->command->info("تم إنشاء مستخدم السائق: {$user->email}");

            // البحث عن موظف سائق موجود وربطه
            $driverEmployee = Employee::whereHas('employeeType', function ($q) {
                $q->where('name', 'like', '%سائق%')
                    ->orWhere('name', 'like', '%driver%');
            })->whereNull('user_id')->first();

            if ($driverEmployee) {
                $driverEmployee->update(['user_id' => $user->id]);
                $this->command->info("تم ربط المستخدم بالموظف: {$driverEmployee->fullname}");
            } else {
                // إنشاء موظف سائق جديد
                $driverType = DB::table('employee_types')
                    ->where('name', 'like', '%سائق%')
                    ->orWhere('name', 'like', '%driver%')
                    ->first();

                $shift = DB::table('shift_times')->first();

                if ($driverType && $shift) {
                    $employee = Employee::create([
                        'user_id' => $user->id,
                        'company_code' => $company->code,
                        'branch_id' => $branch->id,
                        'fullname' => 'سائق تجريبي',
                        'employee_types_id' => $driverType->id,
                        'shift_id' => $shift->id,
                        'isactive' => 1,
                        'phone' => '0500000001',
                        'email' => 'd1@amean.info',
                    ]);
                    $this->command->info("تم إنشاء موظف سائق جديد: {$employee->fullname}");
                } else {
                    $this->command->warn('لم يتم العثور على نوع موظف سائق. قم بربط المستخدم يدوياً.');
                }
            }

            $this->command->info('');
            $this->command->info('=================================');
            $this->command->info('بيانات تسجيل دخول السائق:');
            $this->command->info('البريد: d1@amean.info');
            $this->command->info('كلمة المرور: zzzzzzzz');
            $this->command->info('=================================');
        });
    }
}
