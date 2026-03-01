<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveCarDriversUniqueConstraint extends Migration
{
    /**
     * Run the migrations.
     * 
     * إزالة الـ unique constraint الذي يسبب مشاكل
     * سنعتمد على الكود للتحقق من عدم التكرار
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_drivers', function (Blueprint $table) {
            // حذف الـ unique constraint
            $table->dropUnique('unique_driver_assignment');
        });

        // إضافة index عادي للأداء فقط (بدون unique)
        Schema::table('car_drivers', function (Blueprint $table) {
            $table->index(['car_id', 'shift_id', 'driver_type', 'is_active'], 'idx_car_shift_type_active');
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
            $table->dropIndex('idx_car_shift_type_active');
        });

        Schema::table('car_drivers', function (Blueprint $table) {
            $table->unique(['car_id', 'shift_id', 'driver_type', 'driver_id', 'is_active'], 'unique_driver_assignment');
        });
    }
}
