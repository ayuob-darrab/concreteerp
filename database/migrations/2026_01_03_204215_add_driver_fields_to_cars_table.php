<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDriverFieldsToCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cars', function (Blueprint $table) {
            // التحقق من عدم وجود الأعمدة قبل إضافتها
            if (!Schema::hasColumn('cars', 'driver_id')) {
                $table->unsignedBigInteger('driver_id')->nullable()->after('driver_name');
            }
            if (!Schema::hasColumn('cars', 'backup_driver_id')) {
                $table->unsignedBigInteger('backup_driver_id')->nullable()->after('driver_id');
            }

            // Foreign keys (اختياري - يمكن تفعيلها لاحقاً)
            // $table->foreign('driver_id')->references('id')->on('employees')->onDelete('set null');
            // $table->foreign('backup_driver_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['driver_id', 'backup_driver_id']);
        });
    }
}
