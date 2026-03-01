<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingTiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 10);
            $table->string('name'); // مثل: فئة A، فئة B، عادي، VIP
            $table->string('description')->nullable();
            $table->decimal('price_modifier', 5, 2)->default(0); // نسبة تعديل السعر (+10%, -5%)
            $table->decimal('fixed_modifier', 15, 2)->default(0); // تعديل ثابت (+1000, -500)
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_code');
            $table->index(['company_code', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pricing_tiers');
    }
}
