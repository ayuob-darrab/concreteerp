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
        Schema::create('contractor_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('contractor_id')->constrained('contractors');
            $table->foreignId('invoice_id')->nullable()->constrained('contractor_invoices');

            // نوع السند
            $table->enum('type', ['receipt', 'payment']); // سند قبض أو سند صرف

            // بيانات السند
            $table->string('receipt_number', 30)->unique();
            $table->date('receipt_date');
            $table->decimal('amount', 12, 2);

            // طريقة الدفع
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check']);
            $table->string('bank_name', 100)->nullable();
            $table->string('transfer_reference', 50)->nullable();
            $table->string('check_number', 50)->nullable();
            $table->string('check_bank', 100)->nullable();
            $table->date('check_date')->nullable();

            $table->text('description')->nullable();

            // الحالة
            $table->enum('status', ['pending', 'approved', 'cancelled'])->default('pending');

            // الاعتماد
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');

            // الإلغاء
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users');

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_code', 'type', 'status']);
            $table->index(['contractor_id', 'type']);
            $table->index('receipt_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_receipts');
    }
};
