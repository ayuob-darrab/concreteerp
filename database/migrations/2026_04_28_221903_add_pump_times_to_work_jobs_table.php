<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_jobs', function (Blueprint $table) {
            $table->timestamp('pump_departure_time')->nullable()->after('default_pump_driver_id');
            $table->timestamp('pump_arrival_time')->nullable()->after('pump_departure_time');
            $table->timestamp('pump_work_start_time')->nullable()->after('pump_arrival_time');
            $table->timestamp('pump_return_time')->nullable()->after('pump_work_start_time');
            $table->string('pump_status', 20)->default('pending')->after('pump_return_time');
        });
    }

    public function down(): void
    {
        Schema::table('work_jobs', function (Blueprint $table) {
            $table->dropColumn([
                'pump_departure_time',
                'pump_arrival_time',
                'pump_work_start_time',
                'pump_return_time',
                'pump_status',
            ]);
        });
    }
};
