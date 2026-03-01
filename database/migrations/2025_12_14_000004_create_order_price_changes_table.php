<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPriceChangesTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * سجل تغييرات الأسعار: كل تغيير في السعر يُسجّل مع سببه
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_price_changes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');

            // الأسعار
            $table->decimal('old_price', 10, 2)->nullable(); // السعر القديم
            $table->decimal('new_price', 10, 2);             // السعر الجديد
            $table->decimal('change_amount', 10, 2);         // مقدار التغيير
            $table->decimal('change_percentage', 5, 2);      // نسبة التغيير

            // نوع التغيير
            $table->enum('change_type', [
                'initial',          // السعر الأولي
                'customer_request', // بناءً على طلب العميل
                'quantity_change',  // تغيير في الكمية
                'market_change',    // تغيير أسعار السوق
                'discount',         // خصم
                'surcharge',        // رسوم إضافية
                'correction',       // تصحيح خطأ
                'management',       // قرار إداري
                'final_approval'    // الموافقة النهائية
            ]);

            // السبب والتفاصيل
            $table->text('reason')->nullable();              // سبب التغيير
            $table->text('notes')->nullable();

            // من قام بالتغيير
            $table->unsignedBigInteger('changed_by');
            $table->string('changed_by_role')->nullable();   // صلاحية من غيّر

            // الموافقة
            $table->boolean('requires_approval')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->text('approval_notes')->nullable();

            // التأثير المحاسبي
            $table->boolean('accounting_impact')->default(false); // هل له تأثير محاسبي
            $table->boolean('accounting_processed')->default(false);
            $table->dateTime('accounting_processed_at')->nullable();

            $table->timestamp('created_at');

            // الفهارس
            $table->index('work_order_id');
            $table->index('change_type');
            $table->index('changed_by');
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
        Schema::dropIfExists('order_price_changes');
    }
}
