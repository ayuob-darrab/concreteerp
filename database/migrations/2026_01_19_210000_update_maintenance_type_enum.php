<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update maintenance_type ENUM to include all needed values
     */
    public function up(): void
    {
        // Update ENUM to include all maintenance types
        DB::statement("ALTER TABLE car_maintenances MODIFY COLUMN maintenance_type ENUM('periodic', 'emergency', 'repair', 'inspection', 'oil_change', 'tires', 'tire_change', 'brake', 'engine', 'electrical', 'body', 'other') DEFAULT 'periodic'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE car_maintenances MODIFY COLUMN maintenance_type ENUM('periodic','repair','tire_change','oil_change','brake','engine','electrical','body','other') DEFAULT 'periodic'");
    }
};
