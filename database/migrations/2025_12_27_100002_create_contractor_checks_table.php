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
        Schema::create('contractor_checks', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('contractor_id')->constrained('contractors');

            // نوع الشيك
            $table->enum('type', ['received', 'issued']); // مستلم أو صادر

            // بيانات الشيك
            $table->string('check_number', 50);
            $table->string('bank_name', 100);
            $table->string('bank_account', 50)->nullable();
            $table->decimal('amount', 12, 2);
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('drawer_name', 100)->nullable(); // اسم المحرر
            $table->string('payee_name', 100)->nullable(); // اسم المستفيد

            // الحالة
            $table->enum('status', ['pending', 'deposited', 'collected', 'rejected', 'returned', 'endorsed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();

            // الإيداع
            $table->timestamp('deposited_at')->nullable();
            $table->foreignId('deposited_by')->nullable()->constrained('users');

            // التحصيل
            $table->timestamp('collected_at')->nullable();
            $table->decimal('collected_amount', 12, 2)->nullable();
            $table->foreignId('collected_by')->nullable()->constrained('users');

            // الرفض
            $table->text('rejection_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();

            // الإرجاع
            $table->text('return_reason')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->foreignId('returned_by')->nullable()->constrained('users');

            // التظهير
            $table->string('endorsed_to', 100)->nullable();
            $table->timestamp('endorsed_at')->nullable();
            $table->text('endorsement_notes')->nullable();
            $table->foreignId('endorsed_by')->nullable()->constrained('users');

            // الإلغاء
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users');

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_code', 'type', 'status']);
            $table->index(['contractor_id', 'status']);
            $table->index(['due_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_checks');
    }
};
