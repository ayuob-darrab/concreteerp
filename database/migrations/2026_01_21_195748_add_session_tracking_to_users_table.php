<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionTrackingToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // معرف الجلسة الحالية
            $table->string('current_session_id')->nullable()->after('remember_token');
            // آخر نشاط للمستخدم
            $table->timestamp('last_activity_at')->nullable()->after('current_session_id');
            // مدة انتهاء الجلسة بالدقائق (يمكن تخصيصها لكل مستخدم)
            $table->integer('session_timeout_minutes')->default(120)->after('last_activity_at');
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
            $table->dropColumn(['current_session_id', 'last_activity_at', 'session_timeout_minutes']);
        });
    }
}
