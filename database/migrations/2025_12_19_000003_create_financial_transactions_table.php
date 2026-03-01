<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إنشاء جدول المعاملات المالية
     */
    public function up(): void
    {
        if (Schema::hasTable('financial_transactions')) {
            return;
        }

        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();

            // رقم المعاملة
            $table->string('transaction_number', 50)->unique();

            // الربط
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('work_order_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();

            // نوع المعاملة
            $table->enum('transaction_type', [
                'opening_balance',    // رصيد افتتاحي
                'invoice',            // فاتورة
                'payment_in',         // دفعة واردة
                'payment_out',        // دفعة صادرة
                'discount',           // خصم
                'adjustment',         // تسوية
                'refund',             // استرداد
                'check_received',     // شيك مستلم
                'check_reversal',     // إلغاء شيك
                'transfer_in',        // تحويل وارد
                'transfer_out',       // تحويل صادر
            ]);

            // المبلغ
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IQD');
            $table->decimal('exchange_rate', 15, 4)->default(1);
            $table->decimal('amount_in_base', 15, 2)->nullable();

            // تأثير الرصيد
            $table->enum('balance_effect', ['debit', 'credit']);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);

            // طريقة الدفع
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'card', 'other'])->nullable();

            // بيانات الشيك
            $table->string('check_number', 50)->nullable();
            $table->date('check_date')->nullable();
            $table->string('check_bank', 100)->nullable();
            $table->enum('check_status', ['pending', 'deposited', 'collected', 'bounced', 'cancelled'])->nullable();

            // بيانات التحويل البنكي
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account', 100)->nullable();
            $table->string('transfer_reference', 100)->nullable();

            // المرجع
            $table->string('reference_type', 50)->nullable();
            $table->string('reference_number', 100)->nullable();

            // الوصف
            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            // التاريخ
            $table->date('transaction_date');

            // الشركة والفرع
            $table->string('company_code', 20)->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();

            // التتبع
            $table->unsignedBigInteger('performed_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            // الفهارس
            $table->index('account_id', 'idx_fin_trx_account');
            $table->index('transaction_type', 'idx_fin_trx_type');
            $table->index('transaction_date', 'idx_fin_trx_date');
            $table->index('company_code', 'idx_fin_trx_company');
            $table->index('branch_id', 'idx_fin_trx_branch');
            $table->index(['company_code', 'transaction_date'], 'idx_fin_trx_company_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
