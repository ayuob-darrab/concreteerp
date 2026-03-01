<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('branch_id'); // department_id
            $table->unsignedBigInteger('car_type_id');  // car type (CarsType)
            $table->string('car_number');     // car number
    
            $table->string('car_model');      // car model
            $table->boolean('is_active')->default(true); // is active
            $table->string('driver_name');    // driver name
            $table->date('add_date');         // add date
            $table->text('note')->nullable(); // note (اختياري)
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
        Schema::dropIfExists('cars');
    }
}
