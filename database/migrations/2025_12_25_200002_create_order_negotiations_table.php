<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول التفاوض على الطلبات
     */
    public function up(): void
    {
        Schema::create('order_negotiations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained('work_orders')->onDelete('cascade');

            // مرحلة التفاوض
            $table->enum('stage', [
                'initial_request',      // الطلب الأولي
                'branch_offer',         // عرض الفرع
                'requester_accept',     // قبول صاحب الطلب
                'requester_reject',     // رفض صاحب الطلب
                'requester_counter',    // عرض مضاد من صاحب الطلب
                'branch_counter',       // عرض مضاد من الفرع
                'final_agreement',      // الاتفاق النهائي
                'cancelled'             // إلغاء
            ]);

            // التفاصيل
            $table->decimal('price_offered', 15, 2)->nullable();
            $table->decimal('discount_offered', 10, 2)->nullable();
            $table->date('suggested_date')->nullable();
            $table->time('suggested_time')->nullable();
            $table->text('notes')->nullable();

            // من قام بالإجراء
            $table->foreignId('action_by')->constrained('users')->onDelete('restrict');
            $table->enum('action_by_type', ['branch_employee', 'contractor', 'agent', 'customer']);

            $table->timestamps();

            // الفهارس
            $table->index('order_id');
            $table->index('stage');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_negotiations');
    }
};
