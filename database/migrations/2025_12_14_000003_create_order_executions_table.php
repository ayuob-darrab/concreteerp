<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderExecutionsTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * التنفيذ الجزئي: يسجل كل عملية تنفيذ (صبة) بكميتها
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_executions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');

            // معلومات التنفيذ
            $table->decimal('quantity', 10, 2);             // الكمية المنفذة في هذه المرة
            $table->unsignedBigInteger('car_id')->nullable(); // السيارة المستخدمة
            $table->unsignedBigInteger('driver_id')->nullable(); // السائق

            // التوقيت
            $table->dateTime('execution_date');              // تاريخ التنفيذ
            $table->dateTime('departure_time')->nullable();  // وقت المغادرة
            $table->dateTime('arrival_time')->nullable();    // وقت الوصول
            $table->dateTime('pour_start_time')->nullable(); // بداية الصب
            $table->dateTime('pour_end_time')->nullable();   // نهاية الصب
            $table->dateTime('return_time')->nullable();     // وقت العودة

            // معلومات الجودة
            $table->decimal('temperature', 5, 2)->nullable(); // درجة حرارة الخلطة
            $table->decimal('slump', 5, 2)->nullable();      // الهبوط (slump test)
            $table->string('quality_status')->nullable();     // حالة الجودة

            // التكاليف والأسعار
            $table->decimal('unit_price', 10, 2)->nullable(); // سعر الوحدة وقت التنفيذ
            $table->decimal('total_price', 10, 2)->nullable(); // الإجمالي لهذا التنفيذ

            // خصم المخزون
            $table->boolean('inventory_deducted')->default(false); // هل تم الخصم من المخزن
            $table->dateTime('inventory_deducted_at')->nullable();
            $table->unsignedBigInteger('inventory_deducted_by')->nullable();

            // الموقع
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // الحالة
            $table->enum('status', [
                'scheduled',     // مجدول
                'loading',       // قيد التحميل
                'in_transit',    // في الطريق
                'pouring',       // قيد الصب
                'completed',     // مكتمل
                'returned',      // تم الإرجاع (لم يتم الصب)
                'cancelled'      // ملغي
            ])->default('scheduled');

            // الملاحظات
            $table->text('notes')->nullable();

            // من أنشأ وعدل
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            // الفهارس
            $table->index('work_order_id');
            $table->index('execution_date');
            $table->index('status');
            $table->index('car_id');

            // العلاقات
            $table->foreign('work_order_id')
                ->references('id')
                ->on('work_orders')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_executions');
    }
}
