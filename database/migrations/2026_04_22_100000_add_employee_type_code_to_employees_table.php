<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        if (!Schema::hasColumn('employees', 'employee_type_code')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('employee_type_code', 32)->nullable()->after('employee_types_id');
            });
        }

        if (Schema::hasTable('employee_types')) {
            DB::statement('
                UPDATE employees e
                INNER JOIN employee_types t ON t.id = e.employee_types_id
                SET e.employee_type_code = t.code
                WHERE e.employee_type_code IS NULL AND t.code IS NOT NULL AND t.code <> \'\'
            ');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'employee_type_code')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('employee_type_code');
            });
        }
    }
};
