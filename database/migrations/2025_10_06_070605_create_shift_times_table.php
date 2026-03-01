<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_times', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // اسم الشفت (صباحي / مسائي)
            $table->string('company_code'); // اسم الشفت (صباحي / مسائي)
            $table->string('notes')->nullable(); // اسم الشفت (صباحي / مسائي)
            $table->time('start_time')->nullable(); // وقت بداية الشفت
            $table->time('end_time')->nullable();   // وقت نهاية الشفت
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_times');
    }
}
