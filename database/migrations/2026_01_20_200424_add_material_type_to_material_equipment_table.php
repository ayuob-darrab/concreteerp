<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaterialTypeToMaterialEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('material_equipment', function (Blueprint $table) {
            if (!Schema::hasColumn('material_equipment', 'material_type')) {
                $table->string('material_type', 50)->nullable()->after('code');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('material_equipment', function (Blueprint $table) {
            $table->dropColumn('material_type');
        });
    }
}
