<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول أسعار الخلطات حسب الفئة السعرية لكل شركة
     */
    public function up()
    {
        Schema::create('concrete_mix_category_prices', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 20)->comment('كود الشركة');
            $table->unsignedBigInteger('concrete_mix_id')->comment('معرف الخلطة');
            $table->unsignedBigInteger('pricing_category_id')->comment('معرف الفئة السعرية');
            $table->decimal('price_per_meter', 15, 2)->default(0)->comment('سعر المتر المكعب');
            $table->decimal('cost_per_meter', 15, 2)->nullable()->comment('تكلفة المتر المكعب');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->boolean('is_active')->default(true)->comment('فعالة');
            $table->timestamps();

            // الفهارس
            $table->index('company_code');
            $table->index('concrete_mix_id');
            $table->index('pricing_category_id');

            // منع التكرار - كل شركة لها سعر واحد لكل خلطة في كل فئة
            $table->unique(['company_code', 'concrete_mix_id', 'pricing_category_id'], 'unique_mix_category_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('concrete_mix_category_prices');
    }
};
