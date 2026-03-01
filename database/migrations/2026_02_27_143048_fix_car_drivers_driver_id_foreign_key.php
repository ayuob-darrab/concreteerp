<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixCarDriversDriverIdForeignKey extends Migration
{
    /**
     * Run the migrations.
     * إصلاح القيد الأجنبي لـ driver_id ليشير إلى employees بدلاً من users
     *
     * @return void
     */
    public function up()
    {
        // إسقاط القيد الأجنبي إن وُجد (قد يكون مُسقَطاً مسبقاً)
        $driverFkExists = DB::selectOne("
            SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'car_drivers'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'car_drivers_driver_id_foreign'
        ");
        if ($driverFkExists) {
            Schema::table('car_drivers', function (Blueprint $table) {
                $table->dropForeign('car_drivers_driver_id_foreign');
            });
        }

        // تحويل driver_id من user_id إلى employee_id إن وُجدت سجلات قديمة
        DB::statement('
            UPDATE car_drivers cd
            INNER JOIN employees e ON e.user_id = cd.driver_id
            SET cd.driver_id = e.id
        ');

        // حذف أي سجلات لا يزال driver_id فيها لا ينتمي لـ employees (بيانات غير صالحة)
        DB::statement('
            DELETE cd FROM car_drivers cd
            LEFT JOIN employees e ON e.id = cd.driver_id
            WHERE e.id IS NULL
        ');

        Schema::table('car_drivers', function (Blueprint $table) {
            $table->foreign('driver_id')->references('id')->on('employees')->onDelete('cascade');
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
            $table->dropForeign(['driver_id']);
        });

        Schema::table('car_drivers', function (Blueprint $table) {
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
