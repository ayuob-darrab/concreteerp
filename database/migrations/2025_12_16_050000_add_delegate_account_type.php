<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddDelegateAccountType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // إضافة نوع حساب جديد للمندوبين
        DB::table('accounts_type')->insert([
            'typename' => 'حساب مندوب',
            'code' => 'delegate'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('accounts_type')->where('code', 'delegate')->delete();
    }
}
