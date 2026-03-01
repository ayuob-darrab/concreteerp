<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إنشاء جدول الإيصالات
     */
    public function up(): void
    {
        if (Schema::hasTable('receipts')) {
            return;
        }

        Schema::create('receipts', function (Blueprint $table) {
            $table->id();

            // رقم الإيصال
            $table->string('receipt_number', 50)->unique();

            // نوع الإيصال
            $table->enum('receipt_type', ['payment_in', 'payment_out'])->default('payment_in');

            // الربط
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();

            // الشركة والفرع
            $table->string('company_code', 20)->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();

            // بيانات الطرف
            $table->string('party_name', 255);

            // المبلغ
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IQD');
            $table->string('amount_in_words', 500)->nullable();

            // طريقة الدفع
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'card', 'other'])->default('cash');

            // التاريخ
            $table->date('receipt_date');

            // ملاحظات
            $table->text('notes')->nullable();

            // التتبع
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('is_printed')->default(false);
            $table->timestamp('printed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index('receipt_date', 'idx_receipts_date');
            $table->index('company_code', 'idx_receipts_company');
            $table->index('branch_id', 'idx_receipts_branch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
