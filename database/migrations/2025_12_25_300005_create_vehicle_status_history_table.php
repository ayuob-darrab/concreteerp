<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول سجل تغيير حالات الآليات
     */
    public function up(): void
    {
        Schema::create('vehicle_status_history', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')->constrained('cars')->onDelete('cascade');

            // الحالة
            $table->enum('old_status', ['available', 'reserved', 'in_maintenance', 'out_of_service', 'scrapped'])->nullable();
            $table->enum('new_status', ['available', 'reserved', 'in_maintenance', 'out_of_service', 'scrapped']);

            // السبب
            $table->text('reason')->nullable();

            // مرتبط بـ
            $table->string('related_type', 50)->nullable(); // maintenance, reservation, etc.
            $table->unsignedBigInteger('related_id')->nullable();

            // من قام
            $table->foreignId('changed_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('changed_at')->useCurrent();

            // فهارس
            $table->index('vehicle_id', 'idx_vehicle');
            $table->index('changed_at', 'idx_changed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_status_history');
    }
};
