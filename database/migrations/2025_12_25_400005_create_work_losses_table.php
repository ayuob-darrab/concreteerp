<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * الخسائر - Work Losses
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_losses', function (Blueprint $table) {
            $table->id();

            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');

            // المرتبط به
            $table->foreignId('job_id')->nullable()->constrained('work_jobs')->onDelete('set null');
            $table->foreignId('shipment_id')->nullable()->constrained('work_shipments')->onDelete('set null');
            $table->foreignId('vehicle_id')->nullable()->constrained('cars')->onDelete('set null');

            // نوع الخسارة
            $table->enum('loss_type', [
                'accident',             // حادث
                'vehicle_breakdown',    // عطل آلية
                'material_spoilage',    // تلف مواد
                'spillage',             // انسكاب
                'rejection',            // رفض من العميل
                'weather',              // ظروف جوية
                'road_issue',           // مشكلة طريق
                'other'                 // أخرى
            ]);

            // الكميات
            $table->decimal('quantity_lost', 10, 2)->nullable(); // كمية الكونكريت المفقودة

            // التكلفة
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->nullable();

            // التفاصيل
            $table->text('description');
            $table->text('location_description')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // المرفقات
            $table->json('attachments')->nullable(); // صور، تقارير

            // التحقيق
            $table->text('investigation_notes')->nullable();
            $table->foreignId('investigated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('investigated_at')->nullable();

            // القرار
            $table->text('resolution')->nullable();
            $table->date('resolution_date')->nullable();

            // الحالة
            $table->enum('status', ['reported', 'investigating', 'resolved', 'closed'])->default('reported');

            // التتبع
            $table->foreignId('reported_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('reported_at')->useCurrent();
            $table->timestamps();

            // الفهارس
            $table->index(['company_code', 'branch_id'], 'idx_losses_company_branch');
            $table->index('job_id', 'idx_losses_job');
            $table->index('shipment_id', 'idx_losses_shipment');
            $table->index('loss_type', 'idx_losses_type');
            $table->index('status', 'idx_losses_status');
            $table->index('reported_at', 'idx_losses_reported');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_losses');
    }
};
