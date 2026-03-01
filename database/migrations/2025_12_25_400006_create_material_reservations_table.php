<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * حجز المواد - Material Reservations
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_id')->constrained('work_jobs')->onDelete('cascade');

            // المادة
            $table->foreignId('material_id')->constrained('materials')->onDelete('restrict');
            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('restrict');

            // الكمية
            $table->decimal('quantity_reserved', 15, 4);
            $table->decimal('quantity_used', 15, 4)->default(0);

            // الحالة
            $table->enum('status', ['reserved', 'partially_used', 'fully_used', 'released'])->default('reserved');

            // التتبع
            $table->timestamp('reserved_at')->useCurrent();
            $table->foreignId('reserved_by')->constrained('users')->onDelete('restrict');

            // الفهارس
            $table->index('job_id', 'idx_reservations_job');
            $table->index('material_id', 'idx_reservations_material');
            $table->index('inventory_id', 'idx_reservations_inventory');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_reservations');
    }
};
