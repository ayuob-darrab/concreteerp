<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_history', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('subscription_id')->nullable(); // ربط مع الاشتراك الأصلي
            $table->enum('plan_type', ['monthly', 'yearly', 'percentage', 'trial', 'hybrid']);
            $table->decimal('base_fee', 10, 2)->default(0);
            $table->decimal('percentage_rate', 5, 2)->default(0);
            $table->integer('orders_limit')->nullable();
            $table->integer('orders_used')->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('actual_start_date')->nullable(); // تاريخ البداية الفعلي
            $table->date('actual_end_date')->nullable(); // تاريخ الانتهاء الفعلي
            $table->boolean('auto_renew')->default(false);
            $table->enum('status', ['active', 'expired', 'suspended', 'cancelled', 'completed'])->default('active');
            $table->text('notes')->nullable();
            $table->enum('action_type', ['created', 'renewed', 'terminated', 'expired', 'suspended']); // نوع الإجراء
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('company_code');
            $table->index('subscription_id');
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_history');
    }
}
