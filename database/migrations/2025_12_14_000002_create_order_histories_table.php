<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * السجل التاريخي الكامل: يسجل كل تغيير يحدث على الطلب
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');

            // نوع التغيير
            $table->enum('action_type', [
                'created',              // إنشاء الطلب
                'status_changed',       // تغيير الحالة
                'price_changed',        // تغيير السعر
                'quantity_modified',    // تعديل الكمية
                'scheduled',            // جدولة التسليم
                'execution_added',      // إضافة تنفيذ جزئي
                'inventory_deducted',   // خصم من المخزن
                'note_added',           // إضافة ملاحظة
                'approval_requested',   // طلب موافقة
                'approval_given',       // موافقة
                'approval_rejected',    // رفض
                'cancelled',            // إلغاء
                'other'                 // أخرى
            ]);

            // التفاصيل
            $table->string('field_name')->nullable();       // اسم الحقل المتغير
            $table->text('old_value')->nullable();          // القيمة القديمة
            $table->text('new_value')->nullable();          // القيمة الجديدة

            // من قام بالتغيير
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type')->nullable();        // نوع المستخدم (admin, employee, system)

            // الوصف والملاحظات
            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            // معلومات إضافية (JSON)
            $table->json('metadata')->nullable();

            // التاريخ
            $table->timestamp('created_at');

            // الفهارس
            $table->index('work_order_id');
            $table->index('action_type');
            $table->index('user_id');
            $table->index('created_at');

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
        Schema::dropIfExists('order_histories');
    }
}
