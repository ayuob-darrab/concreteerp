<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * الشحنات - Work Shipments
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_shipments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_id')->constrained('work_jobs')->onDelete('cascade');
            $table->integer('shipment_number'); // رقم الشحنة ضمن أمر العمل

            // الكمية
            $table->decimal('planned_quantity', 10, 2); // الكمية المخطط لها
            $table->decimal('actual_quantity', 10, 2)->nullable(); // الكمية الفعلية المنفذة

            // الآليات
            $table->foreignId('mixer_id')->nullable()->constrained('cars')->onDelete('set null'); // الخلاطة
            $table->foreignId('truck_id')->nullable()->constrained('cars')->onDelete('set null'); // اللوري
            $table->foreignId('pump_id')->nullable()->constrained('cars')->onDelete('set null');  // المضخة

            // السائقين
            $table->foreignId('mixer_driver_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('truck_driver_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('pump_driver_id')->nullable()->constrained('employees')->onDelete('set null');

            // الأوقات
            $table->dateTime('departure_time')->nullable();   // وقت الانطلاق
            $table->dateTime('arrival_time')->nullable();     // وقت الوصول
            $table->dateTime('work_start_time')->nullable();  // وقت بدء الصب
            $table->dateTime('work_end_time')->nullable();    // وقت انتهاء الصب
            $table->dateTime('return_time')->nullable();      // وقت العودة للمقر

            // الحالة
            $table->enum('status', [
                'planned',      // مخطط
                'preparing',    // جاري التحضير
                'departed',     // انطلق
                'arrived',      // وصل
                'working',      // يعمل
                'completed',    // أكمل
                'returned',     // عاد
                'cancelled'     // ملغي
            ])->default('planned');

            // الملاحظات
            $table->text('notes')->nullable();
            $table->text('driver_notes')->nullable();

            // التتبع
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            // فهرس فريد
            $table->unique(['job_id', 'shipment_number'], 'unique_job_shipment');

            // الفهارس
            $table->index('status', 'idx_shipments_status');
            $table->index('mixer_id', 'idx_shipments_mixer');
            $table->index('departure_time', 'idx_shipments_departure');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_shipments');
    }
};
