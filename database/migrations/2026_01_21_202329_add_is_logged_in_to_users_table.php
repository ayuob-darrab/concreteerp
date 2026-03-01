<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsLoggedInToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // هل المستخدم مسجل دخول حالياً
            $table->boolean('is_logged_in')->default(false)->after('is_active');
            // بصمة الجهاز (Device Fingerprint) - بديل عن MAC Address
            $table->string('device_fingerprint')->nullable()->after('is_logged_in');
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
            $table->dropColumn(['is_logged_in', 'device_fingerprint']);
        });
    }
}
