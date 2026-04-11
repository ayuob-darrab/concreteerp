<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * حالة «تسليم مع تلف» للشحنات ذات التلف الجزئي (ليست «مكتمل» بالمعنى الكامل).
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
            'planned','preparing','departed','arrived','working','completed','completed_with_loss','returned','cancelled','damaged'
        ) NOT NULL DEFAULT 'planned'");

        // شحنات كانت «مكتمل» ولها سجل تلف جزئي → حالة أوضح
        if (Schema::hasTable('work_losses')) {
            DB::statement("
                UPDATE work_shipments ws
                INNER JOIN work_losses wl ON wl.shipment_id = ws.id
                SET ws.status = 'completed_with_loss'
                WHERE ws.status = 'completed'
                  AND wl.quantity_lost IS NOT NULL
                  AND wl.quantity_lost > 0
                  AND wl.quantity_lost < ws.planned_quantity
            ");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('work_shipments')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            return;
        }

        DB::table('work_shipments')->where('status', 'completed_with_loss')->update(['status' => 'completed']);

        DB::statement("ALTER TABLE work_shipments MODIFY COLUMN status ENUM(
            'planned','preparing','departed','arrived','working','completed','returned','cancelled','damaged'
        ) NOT NULL DEFAULT 'planned'");
    }
};
