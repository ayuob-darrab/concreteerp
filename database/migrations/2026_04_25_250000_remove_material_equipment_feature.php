<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inventory_histories')) {
            Schema::table('inventory_histories', function (Blueprint $table) {
                if (! Schema::hasColumn('inventory_histories', 'unit_capacity')) {
                    $table->decimal('unit_capacity', 10, 2)->nullable()->after('supplier_id');
                }
                if (! Schema::hasColumn('inventory_histories', 'unit_code')) {
                    $table->string('unit_code', 20)->nullable()->after('unit_capacity');
                }
            });

            if (Schema::hasTable('material_equipment') && Schema::hasColumn('inventory_histories', 'MaterialEquipment_id')) {
                $rows = DB::table('inventory_histories as h')
                    ->leftJoin('material_equipment as m', 'm.id', '=', 'h.MaterialEquipment_id')
                    ->select('h.id', 'h.unit_capacity', 'h.unit_code', 'm.capacity', 'm.code')
                    ->get();

                foreach ($rows as $row) {
                    DB::table('inventory_histories')
                        ->where('id', $row->id)
                        ->update([
                            'unit_capacity' => $row->unit_capacity ?? $row->capacity,
                            'unit_code' => $row->unit_code ?? $row->code,
                        ]);
                }
            }

            Schema::table('inventory_histories', function (Blueprint $table) {
                if (Schema::hasColumn('inventory_histories', 'MaterialEquipment_id')) {
                    $table->dropColumn('MaterialEquipment_id');
                }
            });
        }

        if (Schema::hasTable('material_equipment')) {
            Schema::drop('material_equipment');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('material_equipment')) {
            Schema::create('material_equipment', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->decimal('capacity', 8, 2);
                $table->string('company_code')->nullable();
                $table->text('note')->nullable();
                $table->string('code', 50)->nullable();
                $table->string('material_type', 50)->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('inventory_histories')) {
            Schema::table('inventory_histories', function (Blueprint $table) {
                if (! Schema::hasColumn('inventory_histories', 'MaterialEquipment_id')) {
                    $table->integer('MaterialEquipment_id')->nullable()->after('supplier_id');
                }
            });
        }
    }
};

