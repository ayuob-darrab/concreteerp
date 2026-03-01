<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConcreteMixChemicalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concrete_mix_chemicals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('concrete_mix_id');
            $table->unsignedBigInteger('chemical_id');
            $table->string('quantity', 50)->nullable();
            $table->integer('mix_type_id', 20)->nullable();

            $table->foreign('concrete_mix_id')
                ->references('id')->on('concrete_mixes')
                ->onDelete('cascade');

            $table->foreign('chemical_id')
                ->references('id')->on('chemicals')
                ->onDelete('cascade');

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
        Schema::dropIfExists('concrete_mix_chemicals');
    }
}
