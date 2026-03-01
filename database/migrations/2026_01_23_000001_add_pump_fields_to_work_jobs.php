<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة حقول البَم الافتراضي لأوامر العمل
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_jobs', function (Blueprint $table) {
            // البَم المخصص للعمل
            $table->foreignId('default_pump_id')
                ->nullable()
                ->after('supervisor_id')
                ->constrained('cars')
                ->onDelete('set null')
                ->comment('البَم المخصص لهذا العمل');

            // سائق البَم
            $table->foreignId('default_pump_driver_id')
                ->nullable()
                ->after('default_pump_id')
                ->constrained('employees')
                ->onDelete('set null')
                ->comment('سائق البَم المخصص');

            // تاريخ تخصيص البَم
            $table->timestamp('pump_assigned_at')->nullable()->after('default_pump_driver_id');

            // ملاحظات البَم
            $table->text('pump_notes')->nullable()->after('pump_assigned_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_jobs', function (Blueprint $table) {
            $table->dropForeign(['default_pump_id']);
            $table->dropForeign(['default_pump_driver_id']);
            $table->dropColumn([
                'default_pump_id',
                'default_pump_driver_id',
                'pump_assigned_at',
                'pump_notes'
            ]);
        });
    }
};
