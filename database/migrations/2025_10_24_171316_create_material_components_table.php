<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_components', function (Blueprint $table) {
            $table->id();
            // 🔹 ربط مع الشركة
            $table->string('company_code')->nullable(); // لربط المكونات بالشركة
            // 🔹 الأعمدة الأساسية
            $table->string('material_name'); // اسم المادة (مثل رمل، حصى، إسمنت)
            $table->string('material_type')->nullable(); // نوع المادة (خشن، ناعم، إلخ)
            $table->decimal('unit_price', 10, 2)->nullable(); // سعر الوحدة

            $table->text('notes')->nullable(); // ملاحظات إضافية



            $table->timestamps(); // created_at و updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_components');
    }
}
