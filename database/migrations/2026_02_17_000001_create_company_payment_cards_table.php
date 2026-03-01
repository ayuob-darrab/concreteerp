<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // بطاقات الدفع الخاصة بالشركات
        Schema::create('company_payment_cards', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 15);
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('card_type', 50); // mastercard, visa, zaincash, etc.
            $table->string('card_name', 100);
            $table->string('holder_name', 100);
            $table->string('card_number', 50);
            $table->string('card_number_masked', 50);
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->index('company_code');
            $table->index('branch_id');
            $table->index('is_active');
        });

        // معاملات بطاقات الدفع الخاصة بالشركات
        Schema::create('company_payment_card_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_payment_card_id');
            $table->string('transaction_number', 30)->unique();
            $table->enum('type', ['deposit', 'withdrawal']);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('reference_type', 50)->nullable(); // order_payment, manual, adjustment
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('company_code', 15);
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('company_payment_card_id', 'cpct_card_id_fk')->references('id')->on('company_payment_cards')->onDelete('cascade');
            $table->foreign('branch_id', 'cpct_branch_id_fk')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('created_by', 'cpct_created_by_fk')->references('id')->on('users')->onDelete('set null');

            $table->index('company_payment_card_id');
            $table->index('company_code');
            $table->index('branch_id');
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_payment_card_transactions');
        Schema::dropIfExists('company_payment_cards');
    }
};
