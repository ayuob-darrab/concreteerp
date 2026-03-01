<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة أيام الاشتراك التجريبي القابلة للتحديد في الإعدادات
     */
    public function up()
    {
        Schema::table('subscription_pricing', function (Blueprint $table) {
            if (!Schema::hasColumn('subscription_pricing', 'trial_days')) {
                $table->unsignedInteger('trial_days')->default(7)->after('payment_due_days');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('subscription_pricing', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_pricing', 'trial_days')) {
                $table->dropColumn('trial_days');
            }
        });
    }
};
