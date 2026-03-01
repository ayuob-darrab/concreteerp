<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول ربط السائقين بالآليات
     */
    public function up(): void
    {
        Schema::create('vehicle_drivers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')->constrained('cars')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('employees')->onDelete('cascade');

            // نوع التعيين
            $table->enum('assignment_type', ['primary', 'backup'])->default('primary');

            // فترة التعيين
            $table->date('start_date');
            $table->date('end_date')->nullable(); // NULL = مستمر

            // الحالة
            $table->boolean('is_active')->default(true);

            // من قام بالتعيين
            $table->foreignId('assigned_by')->constrained('users')->onDelete('restrict');

            $table->timestamps();

            // فهارس
            $table->index('vehicle_id', 'idx_vehicle');
            $table->index('driver_id', 'idx_driver');
            $table->index('is_active', 'idx_active');

            // منع تكرار السائق الأساسي للآلية في نفس الفترة
            $table->unique(['vehicle_id', 'driver_id', 'assignment_type', 'start_date'], 'unique_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_drivers');
    }
};
