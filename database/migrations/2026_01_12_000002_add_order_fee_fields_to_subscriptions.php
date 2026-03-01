<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderFeeFieldsToSubscriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('company_subscriptions', 'order_fee_type')) {
                $table->enum('order_fee_type', ['percentage', 'fixed'])->default('percentage')->after('percentage_rate');
            }
            if (!Schema::hasColumn('company_subscriptions', 'fixed_order_fee')) {
                $table->decimal('fixed_order_fee', 15, 2)->nullable()->after('order_fee_type');
            }
        });

        Schema::table('subscription_history', function (Blueprint $table) {
            if (!Schema::hasColumn('subscription_history', 'order_fee_type')) {
                $table->enum('order_fee_type', ['percentage', 'fixed'])->default('percentage')->after('percentage_rate');
            }
            if (!Schema::hasColumn('subscription_history', 'fixed_order_fee')) {
                $table->decimal('fixed_order_fee', 15, 2)->nullable()->after('order_fee_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['order_fee_type', 'fixed_order_fee']);
        });

        Schema::table('subscription_history', function (Blueprint $table) {
            $table->dropColumn(['order_fee_type', 'fixed_order_fee']);
        });
    }
}
