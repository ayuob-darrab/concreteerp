<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {

            // ``, ` `, `userAdmin`, ``, ``
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('managername');
            $table->integer('city_id');
            $table->string('phone');
            $table->string('email');
            $table->string('address');
            $table->string('userAdmin')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->string('note');

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
        Schema::dropIfExists('companies');
    }
}
