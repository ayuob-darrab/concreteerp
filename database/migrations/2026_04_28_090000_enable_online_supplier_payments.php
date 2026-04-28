<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('supplier_payments')) {
            return;
        }

        if (!Schema::hasColumn('supplier_payments', 'company_payment_card_id')) {
            Schema::table('supplier_payments', function (Blueprint $table) {
                $table->unsignedBigInteger('company_payment_card_id')->nullable()->after('payment_method');
                $table->foreign('company_payment_card_id')
                    ->references('id')
                    ->on('company_payment_cards')
                    ->onDelete('set null');
            });
        }

        if (Schema::getConnection()->getDriverName() === 'mysql' && Schema::hasColumn('supplier_payments', 'payment_method')) {
            DB::statement("ALTER TABLE `supplier_payments` MODIFY `payment_method` ENUM('cash','bank_transfer','check','online') NOT NULL DEFAULT 'cash'");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('supplier_payments')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'mysql' && Schema::hasColumn('supplier_payments', 'payment_method')) {
            DB::statement("ALTER TABLE `supplier_payments` MODIFY `payment_method` ENUM('cash','bank_transfer','check') NOT NULL DEFAULT 'cash'");
        }

        if (Schema::hasColumn('supplier_payments', 'company_payment_card_id')) {
            Schema::table('supplier_payments', function (Blueprint $table) {
                $table->dropForeign(['company_payment_card_id']);
                $table->dropColumn('company_payment_card_id');
            });
        }
    }
};
