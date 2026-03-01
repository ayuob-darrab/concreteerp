<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('company_code', 10);
            $table->unsignedBigInteger('branch_id')->nullable();

            // نوع الدفعة
            $table->enum('payment_type', ['full', 'partial', 'advance'])->default('full');

            // اتجاه الدفعة
            $table->enum('direction', ['in', 'out'])->default('in'); // in = مقبوضات، out = مدفوعات

            // المبلغ والعملة
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IQD');

            // التفاصيل
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'check', 'bank_transfer', 'credit'])->default('cash');
            $table->string('receipt_number', 100)->nullable();
            $table->string('check_number', 100)->nullable();
            $table->date('check_date')->nullable();
            $table->string('bank_name')->nullable();

            // ملاحظات
            $table->text('notes')->nullable();

            // من يتعلق به
            $table->unsignedBigInteger('received_by')->nullable();  // من استقبل (للمقبوضات)
            $table->unsignedBigInteger('paid_to')->nullable();       // لمن دُفع (للمدفوعات)
            $table->unsignedBigInteger('recorded_by');               // من سجل الدفعة
            $table->timestamp('recorded_at')->useCurrent();

            // التواريخ
            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index('company_code');
            $table->index('account_id');
            $table->index('order_id');
            $table->index('payment_date');
            $table->index('payment_type');
            $table->index('direction');
            $table->index('receipt_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
