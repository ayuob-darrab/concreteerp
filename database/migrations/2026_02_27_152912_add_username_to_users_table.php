<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUsernameToUsersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable()->after('email');
                $table->index('username');
            });
        }

        // تحويل البيانات الحالية: استخراج الجزء قبل @ من email
        DB::statement("UPDATE users SET username = SUBSTRING_INDEX(email, '@', 1) WHERE email IS NOT NULL AND (username IS NULL OR username = '')");

        // التعامل مع التكرارات: إضافة رقم تسلسلي للمكرر
        $duplicates = DB::select("
            SELECT username, COUNT(*) as cnt
            FROM users
            WHERE username IS NOT NULL AND username != ''
            GROUP BY username
            HAVING cnt > 1
        ");

        foreach ($duplicates as $dup) {
            $users = DB::select("SELECT id FROM users WHERE username = ? ORDER BY id", [$dup->username]);
            for ($i = 1; $i < count($users); $i++) {
                DB::update("UPDATE users SET username = ? WHERE id = ?", [
                    $dup->username . ($i + 1),
                    $users[$i]->id
                ]);
            }
        }

        // إضافة unique constraint بعد التنظيف
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('username', 'users_username_unique');
            });
        } catch (\Exception $e) {
            // القيد موجود مسبقاً
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
}
