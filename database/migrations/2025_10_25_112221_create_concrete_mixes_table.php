<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConcreteMixesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concrete_mixes', function (Blueprint $table) {
            $table->id();
            $table->string('classification'); // C20, C30, ...
            $table->string('company_code');
            $table->decimal('salePrice', 18, 2)->default(0);
            $table->decimal('costPrice', 18, 2)->default(0);
            $table->integer('branch_id',20)->nullable();
            $table->float('cement');          // عدد أكياس الأسمنت
            $table->float('sand');            // الرمل بالمتر المكعب
            $table->float('gravel');          // الحصى بالمتر المكعب
            $table->float('water');           // الماء باللتر

            $table->string('cement_code')->nullable();
            $table->string('sand_code')->nullable();
            $table->string('gravel_code')->nullable();
            $table->string('water_code')->nullable();
            
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('concrete_mixes');
    }
}
