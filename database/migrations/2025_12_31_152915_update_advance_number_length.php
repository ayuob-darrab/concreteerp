<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateAdvanceNumberLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // استخدام Raw SQL بدلاً من ->change() لتجنب الحاجة لـ doctrine/dbal
        DB::statement('ALTER TABLE `advances` MODIFY `advance_number` VARCHAR(50)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `advances` MODIFY `advance_number` VARCHAR(20)');
    }
}
