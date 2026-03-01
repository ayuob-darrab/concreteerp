<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة نوع مستخدم جديد للموظفين
     */
    public function up(): void
    {
        // إضافة نوع موظف EMP إلى جدول usertype
        DB::table('usertype')->insertOrIgnore([
            'code' => 'EMP',
            'name' => 'موظف',
            'role' => 'employee',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('usertype')->where('code', 'EMP')->delete();
    }
};
