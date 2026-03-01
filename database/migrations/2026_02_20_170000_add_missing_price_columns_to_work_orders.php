<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('work_orders')) {
            return;
        }
        if (!Schema::hasColumn('work_orders', 'initial_price')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->decimal('initial_price', 15, 2)->nullable()->after('price');
            });
        }
        if (!Schema::hasColumn('work_orders', 'price_approved')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $col = Schema::hasColumn('work_orders', 'initial_price') ? 'initial_price' : 'price';
                $table->boolean('price_approved')->default(false)->after($col);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('work_orders', 'initial_price')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->dropColumn('initial_price');
            });
        }
        if (Schema::hasColumn('work_orders', 'price_approved')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->dropColumn('price_approved');
            });
        }
    }
};
