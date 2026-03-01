<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول البدلات
     */
    public function up(): void
    {
        Schema::create('employee_allowances', function (Blueprint $table) {
            $table->id();

            // معرفات النظام
            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            // نوع البدل
            $table->enum('allowance_type', ['transportation', 'housing', 'meals', 'phone', 'other']);
            $table->string('custom_name', 100)->nullable(); // إذا كان النوع other

            // المبلغ
            $table->decimal('amount', 15, 2);

            // التكرار
            $table->boolean('is_recurring')->default(true); // شهري متكرر أو لمرة واحدة

            // الفترة (للبدلات المؤقتة)
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // الحالة
            $table->boolean('is_active')->default(true);

            // الملاحظات
            $table->text('notes')->nullable();

            // التتبع
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index('employee_id');
            $table->index('is_active');
            $table->index(['company_code', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_allowances');
    }
};
