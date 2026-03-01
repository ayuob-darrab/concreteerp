<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول المكافآت
     */
    public function up(): void
    {
        Schema::create('employee_bonuses', function (Blueprint $table) {
            $table->id();

            // معرفات النظام
            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            // نوع المكافأة
            $table->enum('bonus_type', ['performance', 'attendance', 'overtime', 'eid', 'annual', 'project', 'other']);
            $table->string('custom_name', 100)->nullable();

            // المبلغ
            $table->decimal('amount', 15, 2);

            // التاريخ والسبب
            $table->date('bonus_date');
            $table->text('reason')->nullable();

            // حالة الصرف
            $table->boolean('is_paid')->default(false);
            $table->foreignId('paid_in_payroll_id')->nullable()->constrained('payroll')->onDelete('set null');

            // التتبع
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            // الفهارس
            $table->index('employee_id');
            $table->index('bonus_date');
            $table->index('is_paid');
            $table->index(['company_code', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_bonuses');
    }
};
