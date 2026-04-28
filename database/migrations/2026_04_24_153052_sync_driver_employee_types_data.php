<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure the legacy driver type is renamed.
        DB::table('employee_types')
            ->where('code', 'DRV')
            ->update([
                'name' => 'سائق خباطة',
                'description' => 'مسؤول عن قيادة الخباطة وتوصيل الكونكريت للمواقع.',
                'updated_at' => now(),
            ]);

        $pumpDriver = DB::table('employee_types')
            ->where('code', 'PMP_DRV')
            ->first();

        if (! $pumpDriver) {
            DB::table('employee_types')->insert([
                'name' => 'سائق بم',
                'code' => 'PMP_DRV',
                'description' => 'مسؤول عن قيادة البَم وتشغيله في موقع الصب.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('employee_types')
            ->where('code', 'DRV')
            ->update([
                'name' => 'سائق',
                'updated_at' => now(),
            ]);

        DB::table('employee_types')
            ->where('code', 'PMP_DRV')
            ->delete();
    }
};
