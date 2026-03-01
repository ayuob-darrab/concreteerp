<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // الاسم
            $table->decimal('capacity', 8, 2);            // السعة
            $table->string('company_code')->nullable();  // كود الشركة (يمكن أن يكون فارغ)
            $table->text('note')->nullable();    // الملاحظات
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
        Schema::dropIfExists('material_equipment');
    }
}
