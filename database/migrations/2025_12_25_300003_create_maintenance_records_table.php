<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول سجلات الصيانة
     */
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();

            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('vehicle_id')->constrained('cars')->onDelete('cascade');

            // نوع الصيانة
            $table->enum('maintenance_type', [
                'scheduled',     // دورية مجدولة
                'preventive',    // وقائية
                'corrective',    // تصحيحية (إصلاح)
                'emergency'      // طارئة
            ]);

            // الوصف
            $table->text('description');

            // قراءات
            $table->integer('odometer_before')->nullable();
            $table->integer('odometer_after')->nullable();
            $table->integer('working_hours_before')->nullable();
            $table->integer('working_hours_after')->nullable();

            // التكلفة
            $table->decimal('labor_cost', 15, 2)->default(0);
            $table->decimal('parts_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);

            // قطع الغيار
            $table->json('parts_used')->nullable(); // [{ name, quantity, unit_price }]

            // التواريخ
            $table->dateTime('started_at');
            $table->dateTime('completed_at')->nullable();

            // من قام بالصيانة
            $table->string('performed_by', 100)->nullable();
            $table->boolean('external_workshop')->default(false);
            $table->string('workshop_name', 100)->nullable();

            // المرفقات
            $table->json('attachments')->nullable();

            // ملاحظات
            $table->text('notes')->nullable();
            $table->text('next_maintenance_notes')->nullable();

            // التتبع
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();

            // فهارس
            $table->index('vehicle_id', 'idx_vehicle');
            $table->index('maintenance_type', 'idx_type');
            $table->index(['started_at', 'completed_at'], 'idx_dates');
            $table->index('company_code', 'idx_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
