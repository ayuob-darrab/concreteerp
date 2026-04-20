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

        if (Schema::hasColumn('users', 'emp_type_id') && Schema::hasTable('employee_types')) {
            DB::statement('
                UPDATE users u
                INNER JOIN employee_types t ON t.id = u.emp_type_id
                SET u.emp_type_code = t.code
                WHERE (u.emp_type_code IS NULL OR u.emp_type_code = \'\')
                  AND t.code IS NOT NULL AND t.code <> \'\'
            ');
        }

        if (Schema::hasColumn('users', 'emp_type_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('emp_type_id');
            });
        }

        if (Schema::hasColumn('users', 'emp_type_code')) {
            $hasIndex = collect(Schema::getIndexes('users'))
                ->contains(fn (array $idx) => ($idx['columns'] ?? []) === ['emp_type_code']);
            if (!$hasIndex) {
                Schema::table('users', function (Blueprint $table) {
                    $table->index('emp_type_code');
                });
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        if (Schema::hasColumn('users', 'emp_type_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['emp_type_code']);
            });
        }

        if (!Schema::hasColumn('users', 'emp_type_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('emp_type_id')->nullable()->after('branch_id');
            });
        }

        if (Schema::hasTable('employee_types') && Schema::hasColumn('users', 'emp_type_id')) {
            DB::statement('
                UPDATE users u
                INNER JOIN employee_types t ON t.code = u.emp_type_code
                SET u.emp_type_id = t.id
                WHERE u.emp_type_code IS NOT NULL AND u.emp_type_code <> \'\'
            ');
        }
    }
};
