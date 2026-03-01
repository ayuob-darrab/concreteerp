<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscriptionDeactivationToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // هل المستخدم معطل بسبب تقليل عدد الاشتراك؟
            // إذا كان true، لا يمكن تفعيله إلا بزيادة العدد من السوبر أدمن
            $table->boolean('deactivated_by_subscription')->default(false)->after('is_active');
            // تاريخ التعطيل بسبب الاشتراك
            $table->timestamp('subscription_deactivated_at')->nullable()->after('deactivated_by_subscription');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['deactivated_by_subscription', 'subscription_deactivated_at']);
        });
    }
}
