<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarMaintenancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('car_id');
            
            // نوع الصيانة
            $table->enum('maintenance_type', ['periodic', 'emergency', 'repair', 'inspection', 'oil_change', 'tires', 'other'])
                ->default('periodic')
                ->comment('نوع الصيانة: دورية، طارئة، إصلاح، فحص، تغيير زيت، إطارات، أخرى');
            
            // تفاصيل الصيانة
            $table->string('title'); // عنوان الصيانة
            $table->text('description')->nullable(); // وصف تفصيلي
            $table->decimal('cost', 12, 2)->default(0); // التكلفة
            $table->decimal('parts_cost', 12, 2)->default(0)->nullable(); // تكلفة القطع
            $table->decimal('labor_cost', 12, 2)->default(0)->nullable(); // تكلفة العمالة
            
            // التواريخ
            $table->date('maintenance_date'); // تاريخ الصيانة
            $table->date('next_maintenance_date')->nullable(); // تاريخ الصيانة القادمة
            
            // معلومات إضافية
            $table->integer('odometer_reading')->nullable(); // قراءة العداد (كم)
            $table->string('performed_by')->nullable(); // من قام بالصيانة (ورشة/فني)
            $table->string('workshop_name')->nullable(); // اسم الورشة
            $table->string('invoice_number')->nullable(); // رقم الفاتورة
            
            // الحالة
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])
                ->default('completed')
                ->comment('الحالة: مجدولة، قيد التنفيذ، مكتملة، ملغية');
            
            // ملاحظات ومرفقات
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable(); // مرفق (فاتورة/صورة)
            
            // التتبع
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            // الفهارس
            $table->index(['company_code', 'branch_id']);
            $table->index(['car_id', 'maintenance_date']);
            $table->index('status');
            
            // العلاقات
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_maintenances');
    }
}
