<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول تعديلات الراتب
     */
    public function up(): void
    {
        Schema::create('salary_adjustments', function (Blueprint $table) {
            $table->id();

            // معرفات النظام
            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            // نوع التعديل
            $table->enum('adjustment_type', ['increase', 'decrease']);

            // المبالغ
            $table->decimal('old_salary', 15, 2);
            $table->decimal('new_salary', 15, 2);
            $table->decimal('difference', 15, 2);

            // التاريخ والسبب
            $table->date('effective_date');
            $table->text('reason');

            // التتبع
            $table->foreignId('approved_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            // الفهارس
            $table->index('employee_id');
            $table->index('effective_date');
            $table->index(['company_code', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_adjustments');
    }
};
