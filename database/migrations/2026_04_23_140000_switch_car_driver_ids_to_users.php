<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // تحويل بيانات car_drivers.driver_id من employee_id إلى user_id حيثما أمكن
        DB::statement('
            UPDATE car_drivers cd
            INNER JOIN employees e ON e.id = cd.driver_id
            SET cd.driver_id = e.user_id
            WHERE e.user_id IS NOT NULL
        ');

        // تنظيف أي قيود قديمة ثم ربط driver_id بجدول users
        $fk = DB::selectOne("
            SELECT CONSTRAINT_NAME as name
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'car_drivers'
              AND CONSTRAINT_TYPE = 'FOREIGN KEY'
              AND CONSTRAINT_NAME = 'car_drivers_driver_id_foreign'
        ");

        if ($fk) {
            Schema::table('car_drivers', function (Blueprint $table) {
                $table->dropForeign('car_drivers_driver_id_foreign');
            });
        }

        // حذف السجلات التي لا تملك user صالح بعد التحويل
        DB::statement('
            DELETE cd FROM car_drivers cd
            LEFT JOIN users u ON u.id = cd.driver_id
            WHERE u.id IS NULL
        ');

        Schema::table('car_drivers', function (Blueprint $table) {
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
        });

        // تحويل بيانات cars.driver_id و cars.backup_driver_id إلى user_id للتوافق
        DB::statement('
            UPDATE cars c
            INNER JOIN employees e ON e.id = c.driver_id
            SET c.driver_id = e.user_id
            WHERE c.driver_id IS NOT NULL AND e.user_id IS NOT NULL
        ');

        DB::statement('
            UPDATE cars c
            INNER JOIN employees e ON e.id = c.backup_driver_id
            SET c.backup_driver_id = e.user_id
            WHERE c.backup_driver_id IS NOT NULL AND e.user_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        $fk = DB::selectOne("
            SELECT CONSTRAINT_NAME as name
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'car_drivers'
              AND CONSTRAINT_TYPE = 'FOREIGN KEY'
              AND CONSTRAINT_NAME = 'car_drivers_driver_id_foreign'
        ");

        if ($fk) {
            Schema::table('car_drivers', function (Blueprint $table) {
                $table->dropForeign('car_drivers_driver_id_foreign');
            });
        }

        DB::statement('
            UPDATE car_drivers cd
            INNER JOIN employees e ON e.user_id = cd.driver_id
            SET cd.driver_id = e.id
        ');

        // حذف السجلات التي لا تملك employee صالح بعد الرجوع
        DB::statement('
            DELETE cd FROM car_drivers cd
            LEFT JOIN employees e ON e.id = cd.driver_id
            WHERE e.id IS NULL
        ');

        Schema::table('car_drivers', function (Blueprint $table) {
            $table->foreign('driver_id')->references('id')->on('employees')->onDelete('cascade');
        });

        DB::statement('
            UPDATE cars c
            INNER JOIN employees e ON e.user_id = c.driver_id
            SET c.driver_id = e.id
            WHERE c.driver_id IS NOT NULL
        ');

        DB::statement('
            UPDATE cars c
            INNER JOIN employees e ON e.user_id = c.backup_driver_id
            SET c.backup_driver_id = e.id
            WHERE c.backup_driver_id IS NOT NULL
        ');
    }
};

