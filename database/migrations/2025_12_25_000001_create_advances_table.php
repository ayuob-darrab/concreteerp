<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول السلف الرئيسي
     */
    public function up(): void
    {
        Schema::create('advances', function (Blueprint $table) {
            $table->id();

            // معرفات النظام
            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->string('advance_number', 20)->unique(); // رقم السلفة الفريد

            // المستفيد
            $table->enum('beneficiary_type', ['employee', 'agent', 'supplier', 'contractor']);
            $table->unsignedBigInteger('beneficiary_id');

            // المبالغ
            $table->decimal('amount', 15, 2); // المبلغ الأصلي
            $table->decimal('remaining_amount', 15, 2); // المبلغ المتبقي

            // الاستقطاع
            $table->enum('deduction_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('deduction_value', 10, 2)->default(0); // نسبة أو مبلغ ثابت
            $table->boolean('auto_deduction')->default(true); // تفعيل الاستقطاع التلقائي

            // الحالة
            $table->enum('status', ['pending', 'approved', 'active', 'completed', 'cancelled'])->default('pending');

            // الموافقات
            $table->foreignId('requested_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('approved_at')->nullable();
            $table->text('approval_notes')->nullable();

            // السبب والملاحظات
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();

            // التواريخ
            $table->datetime('requested_at')->useCurrent();
            $table->datetime('completed_at')->nullable();

            // التتبع
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index(['company_code', 'branch_id']);
            $table->index(['beneficiary_type', 'beneficiary_id']);
            $table->index('status');
            $table->index('requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advances');
    }
};
