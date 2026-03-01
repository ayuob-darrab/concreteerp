<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * سجل المواقع - Location Logs
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')->constrained('work_shipments')->onDelete('cascade');
            $table->foreignId('vehicle_id')->nullable()->constrained('cars')->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained('employees')->onDelete('set null');

            // الموقع
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            // السرعة والاتجاه (اختياري)
            $table->decimal('speed', 5, 2)->nullable(); // كم/ساعة
            $table->decimal('heading', 5, 2)->nullable(); // الاتجاه بالدرجات

            // دقة الموقع
            $table->decimal('accuracy', 10, 2)->nullable(); // بالمتر

            $table->timestamp('recorded_at')->useCurrent();

            // الفهارس
            $table->index('shipment_id', 'idx_location_shipment');
            $table->index('vehicle_id', 'idx_location_vehicle');
            $table->index('recorded_at', 'idx_location_recorded');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_logs');
    }
};
