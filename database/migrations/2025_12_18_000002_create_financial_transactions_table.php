<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 10);
            $table->unsignedBigInteger('branch_id')->nullable();

            // نوع المعاملة
            $table->enum('transaction_type', [
                'sale_invoice',      // فاتورة بيع
                'purchase_invoice',  // فاتورة شراء
                'payment_received',  // دفعة مستلمة
                'payment_made',      // دفعة مدفوعة
                'salary',            // راتب
                'commission',        // عمولة
                'expense',           // مصروف
                'loss',              // خسارة
                'refund',            // استرجاع
                'adjustment'         // تسوية
            ]);

            // الربط
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();

            // تفاصيل المعاملة
            $table->string('reference_number', 100)->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IQD');
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->decimal('amount_in_iqd', 15, 2)->nullable();

            // طريقة الدفع
            $table->enum('payment_method', ['cash', 'check', 'bank_transfer', 'credit'])->default('cash');
            $table->string('check_number', 100)->nullable();
            $table->date('check_date')->nullable();
            $table->string('bank_name')->nullable();

            // ملاحظات
            $table->text('notes')->nullable();

            // من أجرى العملية
            $table->unsignedBigInteger('performed_by');
            $table->timestamp('performed_at')->useCurrent();

            // الموافقة
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->boolean('is_approved')->default(false);

            // التواريخ
            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index('company_code');
            $table->index('branch_id');
            $table->index('account_id');
            $table->index('order_id');
            $table->index('transaction_type');
            $table->index('performed_at');
            $table->index('is_approved');
            $table->index('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_transactions');
    }
}
