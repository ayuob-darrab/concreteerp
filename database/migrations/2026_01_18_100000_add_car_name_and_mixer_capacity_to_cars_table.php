<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCarNameAndMixerCapacityToCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('car_name')->nullable()->after('car_type_id'); // اسم السيارة
            $table->decimal('mixer_capacity', 8, 2)->nullable()->after('car_model'); // سعة الخباطة (بالمتر المكعب)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['car_name', 'mixer_capacity']);
        });
    }
}
