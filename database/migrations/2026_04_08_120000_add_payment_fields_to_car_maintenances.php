<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * طرق الدفع وبطاقة الفرع (دفع إلكتروني) لسجلات صيانة السيارات.
     */
    public function up(): void
    {
        Schema::table('car_maintenances', function (Blueprint $table) {
            $table->string('payment_method', 32)->nullable()->after('invoice_number');
            $table->unsignedBigInteger('company_payment_card_id')->nullable()->after('payment_method');
            $table->string('payment_reference', 120)->nullable()->after('company_payment_card_id');
        });

        Schema::table('car_maintenances', function (Blueprint $table) {
            $table->foreign('company_payment_card_id')
                ->references('id')
                ->on('company_payment_cards')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_maintenances', function (Blueprint $table) {
            $table->dropForeign(['company_payment_card_id']);
        });

        Schema::table('car_maintenances', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'company_payment_card_id', 'payment_reference']);
        });
    }
};
