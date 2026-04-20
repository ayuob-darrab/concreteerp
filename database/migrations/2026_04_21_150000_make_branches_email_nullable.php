<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * بعض التثبيتات القديمة قد يكون فيها عمود email للفروع NOT NULL.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if (Schema::hasTable('branches') && Schema::hasColumn('branches', 'email')) {
            DB::statement('ALTER TABLE `branches` MODIFY `email` VARCHAR(255) NULL');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if (Schema::hasTable('branches') && Schema::hasColumn('branches', 'email')) {
            DB::statement('ALTER TABLE `branches` MODIFY `email` VARCHAR(255) NOT NULL');
        }
    }
};
