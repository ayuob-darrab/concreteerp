<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * إصلاح قيود المفاتيح الأجنبية لجدول customer_payment_records إن كانت مفقودة أو فشلت سابقاً.
     */
    public function up()
    {
        if (!Schema::hasTable('customer_payment_records')) {
            return;
        }

        $count = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', 'customer_payment_records')
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->count();

        if ($count >= 4) {
            return; // القيود موجودة مسبقاً
        }

        Schema::disableForeignKeyConstraints();

        Schema::table('customer_payment_records', function (Blueprint $table) {
            $table->foreign('customer_payment_id', 'customer_payment_records_customer_payment_id_foreign')
                ->references('id')->on('customer_payments')->onDelete('cascade');
            $table->foreign('branch_id', 'customer_payment_records_branch_id_foreign')
                ->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('company_payment_card_id', 'customer_payment_records_company_payment_card_id_foreign')
                ->references('id')->on('company_payment_cards')->onDelete('set null');
            $table->foreign('created_by', 'customer_payment_records_created_by_foreign')
                ->references('id')->on('users')->onDelete('set null');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        if (!Schema::hasTable('customer_payment_records')) {
            return;
        }

        Schema::table('customer_payment_records', function (Blueprint $table) {
            $table->dropForeign('customer_payment_records_customer_payment_id_foreign');
            $table->dropForeign('customer_payment_records_branch_id_foreign');
            $table->dropForeign('customer_payment_records_company_payment_card_id_foreign');
            $table->dropForeign('customer_payment_records_created_by_foreign');
        });
    }
};
