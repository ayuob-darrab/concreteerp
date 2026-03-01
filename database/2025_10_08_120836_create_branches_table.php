<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('city_id'); // Reference to cities
            $table->string('company_code'); // Active flag
            $table->string('longitude',50);
            $table->string('latitude',50);
            
            $table->string('branch_name'); // Department name
            $table->string('branch_admin')->nullable(); // Admin name
            $table->string('phone')->nullable(); // Phone number
            $table->string('email')->nullable(); // Email
            $table->string('address')->nullable(); // Address
            $table->boolean('is_active')->default(true); // Active flag
            $table->dateTime('created_date')->useCurrent(); // Custom create date
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
        Schema::dropIfExists('branches');
    }
}
