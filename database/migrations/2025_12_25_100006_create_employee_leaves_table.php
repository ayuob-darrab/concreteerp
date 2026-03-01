<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول الإجازات
     */
    public function up(): void
    {
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();

            // معرفات النظام
            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            // نوع الإجازة
            $table->enum('leave_type', ['annual', 'sick', 'emergency', 'unpaid', 'maternity', 'paternity', 'study', 'other']);

            // التواريخ
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_count');

            // السبب والمرفقات
            $table->text('reason')->nullable();
            $table->string('attachment', 255)->nullable(); // للتقارير الطبية

            // الموافقة
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // التتبع
            $table->foreignId('requested_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            // الفهارس
            $table->index('employee_id');
            $table->index(['start_date', 'end_date']);
            $table->index('status');
            $table->index(['company_code', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leaves');
    }
};
