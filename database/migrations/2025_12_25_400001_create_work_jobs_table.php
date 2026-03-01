<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * أوامر العمل - Work Jobs
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_jobs', function (Blueprint $table) {
            $table->id();

            // معرفات
            $table->string('job_number', 20)->unique(); // JOB-BR001-202512-0001
            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');

            // الطلب الأصلي
            $table->foreignId('order_id')->constrained('work_orders')->onDelete('restrict');

            // معلومات العميل
            $table->enum('customer_type', ['contractor', 'agent_customer', 'direct_customer']);
            $table->unsignedBigInteger('customer_id')->nullable(); // contractor_id أو NULL للمباشر
            $table->string('customer_name', 255)->nullable();
            $table->string('customer_phone', 20)->nullable();

            // تفاصيل العمل
            $table->foreignId('concrete_type_id')->constrained('concrete_mixes')->onDelete('restrict');
            $table->decimal('total_quantity', 10, 2); // الكمية المطلوبة
            $table->decimal('executed_quantity', 10, 2)->default(0); // الكمية المنفذة
            $table->decimal('completion_percentage', 5, 2)->default(0); // نسبة الإنجاز

            // الأسعار
            $table->decimal('unit_price', 15, 2); // سعر المتر
            $table->decimal('total_price', 15, 2); // السعر الإجمالي
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('final_price', 15, 2); // بعد الخصم

            // الموقع
            $table->text('location_address');
            $table->string('location_map_url', 500)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // المواعيد
            $table->date('scheduled_date');
            $table->time('scheduled_time')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();

            // الحالة
            $table->enum('status', [
                'pending',              // بانتظار التنفيذ
                'materials_reserved',   // تم حجز المواد
                'in_progress',          // قيد التنفيذ
                'partially_completed',  // منجز جزئياً
                'completed',            // مكتمل
                'cancelled',            // ملغي
                'on_hold'              // معلق
            ])->default('pending');

            // الموظف المسؤول
            $table->foreignId('supervisor_id')->nullable()->constrained('employees')->onDelete('set null');

            // ملاحظات
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();

            // إحصائيات
            $table->integer('total_shipments')->default(0);
            $table->decimal('total_working_hours', 5, 2)->default(0);

            // التتبع
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index(['company_code', 'branch_id'], 'idx_work_jobs_company_branch');
            $table->index('order_id', 'idx_work_jobs_order');
            $table->index('status', 'idx_work_jobs_status');
            $table->index('scheduled_date', 'idx_work_jobs_scheduled');
            $table->index(['customer_type', 'customer_id'], 'idx_work_jobs_customer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_jobs');
    }
};
