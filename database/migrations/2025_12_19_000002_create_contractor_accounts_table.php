<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إنشاء جدول حسابات المقاولين المالية
     */
    public function up(): void
    {
        if (Schema::hasTable('contractor_accounts')) {
            return;
        }

        Schema::create('contractor_accounts', function (Blueprint $table) {
            $table->id();

            // الربط
            $table->unsignedBigInteger('contractor_id')->unique();
            $table->string('company_code', 20)->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();

            // رقم الحساب
            $table->string('account_number', 50)->unique()->nullable();

            // الأرصدة
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->enum('opening_balance_type', ['debit', 'credit'])->default('debit');
            $table->decimal('current_balance', 15, 2)->default(0);

            // العملة
            $table->string('currency', 3)->default('IQD');

            // إحصائيات
            $table->decimal('total_invoiced', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);

            // التواريخ
            $table->timestamp('last_invoice_date')->nullable();
            $table->timestamp('last_payment_date')->nullable();
            $table->timestamp('last_transaction_date')->nullable();

            // الحالة
            $table->boolean('is_frozen')->default(false);
            $table->text('freeze_reason')->nullable();

            $table->timestamps();

            // المفاتيح الخارجية
            $table->foreign('contractor_id')->references('id')->on('contractors')->onDelete('cascade');

            // الفهارس
            $table->index('company_code', 'idx_contractor_accounts_company');
            $table->index('branch_id', 'idx_contractor_accounts_branch');
            $table->index('current_balance', 'idx_contractor_accounts_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_accounts');
    }
};
