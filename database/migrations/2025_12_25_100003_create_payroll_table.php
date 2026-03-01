<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول كشف الرواتب
     */
    public function up(): void
    {
        Schema::create('payroll', function (Blueprint $table) {
            $table->id();

            // معرفات النظام
            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('restrict');

            // الفترة
            $table->tinyInteger('payroll_month'); // 1-12
            $table->year('payroll_year');

            // الراتب الأساسي
            $table->decimal('basic_salary', 15, 2);

            // الإضافات
            $table->decimal('allowances_total', 15, 2)->default(0);
            $table->json('allowances_details')->nullable();
            $table->decimal('bonuses_total', 15, 2)->default(0);
            $table->json('bonuses_details')->nullable();
            $table->decimal('overtime_amount', 15, 2)->default(0);
            $table->decimal('overtime_hours', 5, 2)->default(0);

            // الخصومات
            $table->decimal('deductions_total', 15, 2)->default(0);
            $table->json('deductions_details')->nullable();
            $table->decimal('advances_deducted', 15, 2)->default(0);
            $table->json('advances_details')->nullable();
            $table->decimal('absence_deduction', 15, 2)->default(0);
            $table->integer('absence_days')->default(0);

            // التأمينات والضرائب
            $table->decimal('insurance_deduction', 15, 2)->default(0);
            $table->decimal('tax_deduction', 15, 2)->default(0);

            // الصافي
            $table->decimal('gross_salary', 15, 2); // الإجمالي قبل الخصومات
            $table->decimal('net_salary', 15, 2); // الصافي

            // حالة الدفع
            $table->enum('status', ['draft', 'approved', 'paid', 'cancelled'])->default('draft');

            // معلومات الدفع
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check'])->nullable();
            $table->string('payment_reference', 100)->nullable();
            $table->datetime('paid_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->onDelete('set null');

            // الموافقة
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('approved_at')->nullable();

            // التتبع
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            // فهرس فريد لمنع التكرار
            $table->unique(['employee_id', 'payroll_month', 'payroll_year'], 'unique_employee_month');

            // الفهارس
            $table->index(['payroll_year', 'payroll_month']);
            $table->index('status');
            $table->index(['company_code', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll');
    }
};
