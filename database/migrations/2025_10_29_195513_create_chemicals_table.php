<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChemicalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chemicals', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 50)->nullable();
            $table->integer('branch_id', 20);
            $table->string('name', 100);
            $table->integer('total_price');
            $table->integer('unit');
            $table->decimal('unit_cost', 18, 2)->default(0);




            $table->text('description')->nullable();
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
        Schema::dropIfExists('chemicals');
    }
}
