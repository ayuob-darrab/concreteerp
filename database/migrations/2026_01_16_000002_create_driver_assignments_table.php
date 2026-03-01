<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول سجل تعيينات السائقين على الآليات
     */
    public function up(): void
    {
        Schema::create('driver_assignments', function (Blueprint $table) {
            $table->id();

            $table->string('company_code', 10);
            $table->unsignedBigInteger('branch_id');

            // معلومات السيارة
            $table->unsignedBigInteger('car_id');

            // معلومات السائق
            $table->unsignedBigInteger('driver_id');
            $table->enum('assignment_type', ['primary', 'backup'])->default('primary');

            // فترة التعيين
            $table->date('start_date');
            $table->date('end_date')->nullable(); // NULL = مستمر

            // سبب الإنهاء
            $table->string('end_reason')->nullable();

            // من قام بالتعيين/الإنهاء
            $table->unsignedBigInteger('assigned_by');
            $table->unsignedBigInteger('ended_by')->nullable();

            $table->timestamps();

            // الفهارس
            $table->index('car_id');
            $table->index('driver_id');
            $table->index('end_date');
            $table->index(['company_code', 'branch_id']);

            // العلاقات
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_assignments');
    }
};
