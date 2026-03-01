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
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();

            // المعرفات
            $table->string('receipt_number', 30)->unique(); // RCP-BR001-202512-0001
            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches');

            // المعاملة المالية المرتبطة
            $table->foreignId('transaction_id')->nullable()->constrained('financial_transactions')->nullOnDelete();

            // الدافع
            $table->enum('payer_type', ['contractor', 'customer', 'other']);
            $table->unsignedBigInteger('payer_id')->nullable();
            $table->string('payer_name');
            $table->string('payer_phone', 20)->nullable();

            // المبلغ
            $table->decimal('amount', 15, 2);
            $table->string('currency_code', 3)->default('IQD');
            $table->decimal('exchange_rate', 15, 6)->default(1);
            $table->decimal('amount_in_default', 15, 2)->nullable(); // المبلغ بالعملة الافتراضية
            $table->string('amount_in_words', 500)->nullable(); // المبلغ كتابة

            // طريقة الدفع
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'card', 'other']);

            // معلومات الدفع
            $table->string('reference_number', 100)->nullable(); // رقم الحوالة أو الشيك
            $table->string('bank_name', 100)->nullable();
            $table->string('check_number', 50)->nullable();
            $table->date('check_date')->nullable();

            // السبب/الوصف
            $table->text('description');

            // المرتبط به
            $table->string('related_type', 50)->nullable(); // work_job, advance, etc.
            $table->unsignedBigInteger('related_id')->nullable();

            // الحالة
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'bounced'])->default('confirmed');
            $table->text('cancelled_reason')->nullable();

            // التتبع
            $table->foreignId('received_by')->constrained('users');
            $table->dateTime('received_at');
            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index('receipt_number');
            $table->index(['payer_type', 'payer_id']);
            $table->index('received_at');
            $table->index('status');
            $table->index('company_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};
