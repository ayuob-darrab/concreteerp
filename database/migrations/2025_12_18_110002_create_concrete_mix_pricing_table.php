<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConcreteMixPricingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concrete_mix_pricing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('concrete_mix_id');
            $table->unsignedBigInteger('pricing_tier_id');
            $table->decimal('sale_price', 15, 2); // السعر المخصص لهذه الفئة
            $table->decimal('cost_price', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['concrete_mix_id', 'pricing_tier_id']);
            $table->index('concrete_mix_id');
            $table->index('pricing_tier_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('concrete_mix_pricing');
    }
}
