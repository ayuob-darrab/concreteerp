<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderStagesTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * جدول المراحل: يسجل كل مرحلة يمر بها الطلب
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');

            // معلومات المرحلة
            $table->enum('stage', [
                'new',
                'under_review',
                'waiting_customer',
                'approved',
                'rejected',
                'scheduled',
                'in_progress',
                'completed',
                'cancelled'
            ]);

            // من قام بالتغيير
            $table->unsignedBigInteger('user_id')->nullable();

            // ملاحظات المرحلة
            $table->text('notes')->nullable();

            // بيانات إضافية (JSON) - مثلاً: أسباب الرفض، تفاصيل الجدولة
            $table->json('metadata')->nullable();

            // التاريخ والوقت
            $table->timestamp('created_at');

            // الفهارس
            $table->index('work_order_id');
            $table->index('stage');
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
        Schema::dropIfExists('order_stages');
    }
}
