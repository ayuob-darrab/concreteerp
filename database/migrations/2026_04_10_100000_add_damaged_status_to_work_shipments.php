<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة حالة «تالفة» لشحنات أمر العمل (MySQL ENUM).
     */
    public function up(): void
    {
        if (!Schema::hasTable('work_shipments')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            return;
        }

        DB::statement("ALTER TABLE work_shipments MODIFY COLUMN status ENUM(
            'planned','preparing','departed','arrived','working','completed','returned','cancelled','damaged'
        ) NOT NULL DEFAULT 'planned'");
    }

    /**
     * @return void
     */
    public function down(): void
    {
        if (!Schema::hasTable('work_shipments')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            return;
        }

        DB::table('work_shipments')->where('status', 'damaged')->update(['status' => 'cancelled']);

        DB::statement("ALTER TABLE work_shipments MODIFY COLUMN status ENUM(
            'planned','preparing','departed','arrived','working','completed','returned','cancelled'
        ) NOT NULL DEFAULT 'planned'");
    }
};
