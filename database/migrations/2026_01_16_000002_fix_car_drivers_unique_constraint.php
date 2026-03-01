<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixCarDriversUniqueConstraint extends Migration
{
    /**
     * Run the migrations.
     * 
     * إصلاح الـ unique constraint بحيث يمنع التكرار فقط للسجلات النشطة
     * ويسمح بوجود عدة سجلات غير نشطة (تاريخية)
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_drivers', function (Blueprint $table) {
            // حذف الـ unique constraint القديم
            $table->dropUnique('unique_active_driver_per_shift');
        });

        Schema::table('car_drivers', function (Blueprint $table) {
            // إنشاء unique constraint جديد بدون is_active
            // هذا يضمن أنه لا يمكن أن يكون هناك أكثر من سائق نشط بنفس النوع لنفس السيارة في نفس الشفت
            // لكن يسمح بوجود سجلات تاريخية متعددة
            $table->unique(['car_id', 'shift_id', 'driver_type', 'driver_id', 'is_active'], 'unique_driver_assignment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_drivers', function (Blueprint $table) {
            $table->dropUnique('unique_driver_assignment');
        });

        Schema::table('car_drivers', function (Blueprint $table) {
            $table->unique(['car_id', 'shift_id', 'driver_type', 'is_active'], 'unique_active_driver_per_shift');
        });
    }
}
