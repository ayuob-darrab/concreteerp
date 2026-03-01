<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول الخصومات
     */
    public function up(): void
    {
        Schema::create('employee_deductions', function (Blueprint $table) {
            $table->id();

            // معرفات النظام
            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            // نوع الخصم
            $table->enum('deduction_type', ['absence', 'late', 'early_leave', 'violation', 'damage', 'other']);
            $table->string('custom_name', 100)->nullable();

            // المبلغ
            $table->decimal('amount', 15, 2);

            // التاريخ والسبب
            $table->date('deduction_date');
            $table->text('reason');

            // حالة الخصم من الراتب
            $table->boolean('is_deducted')->default(false);
            $table->foreignId('deducted_in_payroll_id')->nullable()->constrained('payroll')->onDelete('set null');

            // التتبع
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            // الفهارس
            $table->index('employee_id');
            $table->index('deduction_date');
            $table->index('is_deducted');
            $table->index(['company_code', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_deductions');
    }
};
