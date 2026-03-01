<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 10);
            $table->unsignedBigInteger('branch_id')->nullable();

            // نوع الحساب والمالك
            $table->enum('account_type', ['contractor', 'supplier', 'direct_client', 'employee']);
            $table->unsignedBigInteger('account_holder_id')->nullable();
            $table->string('account_holder_name');

            // الأرصدة
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->enum('opening_balance_type', ['debit', 'credit'])->default('debit');
            $table->decimal('current_balance', 15, 2)->default(0);

            // العملة
            $table->string('currency', 3)->default('IQD');

            // الحالة
            $table->boolean('is_active')->default(true);

            // ملاحظات
            $table->text('notes')->nullable();

            // التواريخ
            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index('company_code');
            $table->index('branch_id');
            $table->index('account_type');
            $table->index('is_active');
            $table->index('current_balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_accounts');
    }
}
