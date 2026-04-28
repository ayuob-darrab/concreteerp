<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add performed_by column to car_maintenances table
     */
    public function up(): void
    {
        if (!Schema::hasColumn('car_maintenances', 'performed_by')) {
            Schema::table('car_maintenances', function (Blueprint $table) {
                $table->string('performed_by')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('car_maintenances', 'performed_by')) {
            Schema::table('car_maintenances', function (Blueprint $table) {
                $table->dropColumn('performed_by');
            });
        }
    }
};
