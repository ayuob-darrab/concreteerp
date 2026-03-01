<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 10);
            $table->unsignedBigInteger('branch_id');

            // نوع الحركة
            $table->enum('transaction_type', ['cash_in', 'cash_out']);

            // المبلغ
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IQD');

            // الربط
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->unsignedBigInteger('financial_transaction_id')->nullable();

            // الأرصدة
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2)->default(0);

            // الوصف والملاحظات
            $table->string('description')->nullable();
            $table->text('notes')->nullable();

            // من تعامل
            $table->unsignedBigInteger('handled_by');
            $table->timestamp('handled_at')->useCurrent();

            // التواريخ
            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index('company_code');
            $table->index('branch_id');
            $table->index('handled_at');
            $table->index('transaction_type');
            $table->index(['company_code', 'branch_id', 'handled_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_registers');
    }
}
