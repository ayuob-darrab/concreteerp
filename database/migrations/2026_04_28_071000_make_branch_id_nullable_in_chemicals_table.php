<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if (Schema::hasTable('chemicals') && Schema::hasColumn('chemicals', 'branch_id')) {
            DB::statement('ALTER TABLE `chemicals` MODIFY `branch_id` INT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if (Schema::hasTable('chemicals') && Schema::hasColumn('chemicals', 'branch_id')) {
            DB::statement('UPDATE `chemicals` SET `branch_id` = 0 WHERE `branch_id` IS NULL');
            DB::statement('ALTER TABLE `chemicals` MODIFY `branch_id` INT NOT NULL');
        }
    }
};
