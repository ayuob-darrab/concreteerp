<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول دفعات السلف
     */
    public function up(): void
    {
        Schema::create('advance_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('advance_id')->constrained('advances')->onDelete('restrict');
            $table->string('payment_number', 20)->unique();

            // نوع الدفع
            $table->enum('payment_type', ['manual', 'salary_deduction', 'invoice_deduction', 'commission_deduction']);

            // المبلغ
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);

            // المرجع (في حالة الاستقطاع التلقائي)
            $table->string('reference_type', 50)->nullable(); // payroll, supplier_invoice, commission
            $table->unsignedBigInteger('reference_id')->nullable();

            // طريقة الدفع (في حالة الدفع اليدوي)
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check'])->nullable();

            // الملاحظات
            $table->text('notes')->nullable();

            // التتبع
            $table->datetime('paid_at');
            $table->foreignId('paid_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            // الفهارس
            $table->index('advance_id');
            $table->index('payment_type');
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_payments');
    }
};
