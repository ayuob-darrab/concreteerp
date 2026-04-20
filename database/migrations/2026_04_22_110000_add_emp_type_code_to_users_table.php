<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        if (!Schema::hasColumn('users', 'emp_type_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('emp_type_code', 32)->nullable()->after('emp_type_id');
            });
        }

        if (Schema::hasTable('employee_types')) {
            DB::statement('
                UPDATE users u
                INNER JOIN employee_types t ON t.id = u.emp_type_id
                SET u.emp_type_code = t.code
                WHERE u.emp_type_code IS NULL AND t.code IS NOT NULL AND t.code <> \'\'
            ');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'emp_type_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('emp_type_code');
            });
        }
    }
};
