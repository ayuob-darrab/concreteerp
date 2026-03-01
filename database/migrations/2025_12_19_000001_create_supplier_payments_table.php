<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique(); // رقم الإيصال
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('company_code');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->decimal('amount', 18, 2); // مبلغ الدفعة
            $table->decimal('balance_before', 18, 2); // الرصيد قبل الدفع
            $table->decimal('balance_after', 18, 2); // الرصيد بعد الدفع
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check'])->default('cash'); // طريقة الدفع
            $table->string('reference_number')->nullable(); // رقم المرجع (للشيك أو التحويل)
            $table->text('notes')->nullable(); // ملاحظات
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // المستخدم الذي أنشأ الدفعة
            $table->timestamps();

            $table->index(['supplier_id', 'company_code']);
            $table->index('payment_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_payments');
    }
}
