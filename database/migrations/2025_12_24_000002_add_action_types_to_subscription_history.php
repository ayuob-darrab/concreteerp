<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddActionTypesToSubscriptionHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // تحديث ENUM لإضافة القيم الجديدة
        DB::statement("ALTER TABLE subscription_history MODIFY COLUMN action_type ENUM('created', 'renewed', 'terminated', 'expired', 'suspended', 'extended', 'payment') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE subscription_history MODIFY COLUMN action_type ENUM('created', 'renewed', 'terminated', 'expired', 'suspended') NOT NULL");
    }
}
