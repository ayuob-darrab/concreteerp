<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodeToCarsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cars_types', function (Blueprint $table) {
            $table->string('code', 10)->nullable()->after('id')->comment('كود فريد لنوع السيارة');
            $table->unique(['company_code', 'code'], 'unique_company_car_type_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cars_types', function (Blueprint $table) {
            $table->dropUnique('unique_company_car_type_code');
            $table->dropColumn('code');
        });
    }
}
