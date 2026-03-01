<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->id();

            // المعرفات
            $table->string('voucher_number', 30)->unique(); // PV-BR001-202512-0001
            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches');

            // المعاملة المالية المرتبطة
            $table->foreignId('transaction_id')->nullable()->constrained('financial_transactions')->nullOnDelete();

            // المستفيد
            $table->enum('payee_type', ['supplier', 'contractor', 'employee', 'other']);
            $table->unsignedBigInteger('payee_id')->nullable();
            $table->string('payee_name');

            // المبلغ
            $table->decimal('amount', 15, 2);
            $table->string('currency_code', 3)->default('IQD');
            $table->decimal('exchange_rate', 15, 6)->default(1);
            $table->decimal('amount_in_default', 15, 2)->nullable();
            $table->string('amount_in_words', 500)->nullable();

            // طريقة الدفع
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'card', 'other']);

            // معلومات الدفع
            $table->string('reference_number', 100)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('check_number', 50)->nullable();
            $table->date('check_date')->nullable();

            // السبب/الوصف
            $table->text('description');

            // المرتبط به
            $table->string('related_type', 50)->nullable(); // salary, advance, supplier_invoice
            $table->unsignedBigInteger('related_id')->nullable();

            // الموافقات
            $table->boolean('requires_approval')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();

            // الحالة
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'paid', 'cancelled'])->default('draft');
            $table->text('cancelled_reason')->nullable();

            // التتبع
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('paid_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index('voucher_number');
            $table->index(['payee_type', 'payee_id']);
            $table->index('paid_at');
            $table->index('status');
            $table->index('company_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_vouchers');
    }
};
