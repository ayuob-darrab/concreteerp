<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول حجز الآليات
     */
    public function up(): void
    {
        Schema::create('vehicle_reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')->constrained('cars')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('work_orders')->onDelete('set null');
            $table->unsignedBigInteger('job_id')->nullable(); // للربط المستقبلي مع work_jobs

            // فترة الحجز
            $table->dateTime('reserved_from');
            $table->dateTime('reserved_to');

            // السائق المكلف
            $table->foreignId('driver_id')->nullable()->constrained('employees')->onDelete('set null');

            // الحالة
            $table->enum('status', ['pending', 'confirmed', 'in_use', 'completed', 'cancelled'])->default('pending');

            // ملاحظات
            $table->text('purpose')->nullable();
            $table->text('notes')->nullable();

            // التتبع
            $table->foreignId('reserved_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();

            // فهارس
            $table->index('vehicle_id', 'idx_vehicle');
            $table->index(['reserved_from', 'reserved_to'], 'idx_dates');
            $table->index('status', 'idx_status');
            $table->index('order_id', 'idx_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_reservations');
    }
};
