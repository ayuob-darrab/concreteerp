<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddAdditionalUserActionTypeToSubscriptionHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE subscription_history MODIFY COLUMN action_type ENUM('created', 'renewed', 'terminated', 'expired', 'suspended', 'extended', 'payment', 'additional_user') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE subscription_history MODIFY COLUMN action_type ENUM('created', 'renewed', 'terminated', 'expired', 'suspended', 'extended', 'payment') NOT NULL");
    }
}
