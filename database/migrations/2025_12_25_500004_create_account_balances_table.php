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
        Schema::create('account_balances', function (Blueprint $table) {
            $table->id();

            $table->string('company_code', 10);
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();

            // الحساب
            $table->enum('account_type', ['contractor', 'supplier', 'employee', 'customer', 'cash_register', 'bank']);
            $table->unsignedBigInteger('account_id');

            // العملة
            $table->string('currency_code', 3)->default('IQD');

            // الأرصدة
            $table->decimal('opening_balance', 15, 2)->default(0); // الرصيد الافتتاحي
            $table->decimal('total_debits', 15, 2)->default(0);    // إجمالي المدين
            $table->decimal('total_credits', 15, 2)->default(0);   // إجمالي الدائن
            $table->decimal('current_balance', 15, 2)->default(0); // الرصيد الحالي

            // نوع الرصيد
            $table->enum('balance_type', ['debit', 'credit'])->nullable(); // له علينا أو لنا عليه

            // آخر تحديث
            $table->unsignedBigInteger('last_transaction_id')->nullable();
            $table->dateTime('last_transaction_at')->nullable();

            $table->timestamps();

            // فهرس فريد
            $table->unique(['company_code', 'branch_id', 'account_type', 'account_id', 'currency_code'], 'unique_account_balance');

            // الفهارس
            $table->index(['account_type', 'account_id']);
            $table->index('balance_type');
            $table->index('company_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_balances');
    }
};
