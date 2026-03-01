<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول إعدادات السلف
     */
    public function up(): void
    {
        Schema::create('advance_settings', function (Blueprint $table) {
            $table->id();

            $table->string('company_code', 10);
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');

            // الحدود القصوى
            $table->decimal('max_advance_employee', 15, 2)->default(0); // 0 = بدون حد
            $table->decimal('max_advance_agent', 15, 2)->default(0);
            $table->decimal('max_advance_supplier', 15, 2)->default(0);
            $table->decimal('max_advance_contractor', 15, 2)->default(0);

            // النسب الافتراضية للاستقطاع
            $table->decimal('default_deduction_employee', 5, 2)->default(10.00);
            $table->decimal('default_deduction_agent', 5, 2)->default(15.00);
            $table->decimal('default_deduction_supplier', 5, 2)->default(20.00);
            $table->decimal('default_deduction_contractor', 5, 2)->default(0.00);

            // التفعيل
            $table->boolean('auto_deduction_enabled')->default(true);
            $table->boolean('allow_overpayment')->default(true); // السماح بالدفع أكثر من المستحق

            $table->timestamps();

            // فهرس فريد
            $table->unique(['company_code', 'branch_id'], 'unique_company_branch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_settings');
    }
};
