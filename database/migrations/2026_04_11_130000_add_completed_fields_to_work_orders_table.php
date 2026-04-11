<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('work_orders')) {
            return;
        }

        Schema::table('work_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('work_orders', 'completed_at')) {
                $table->dateTime('completed_at')->nullable();
            }
            if (!Schema::hasColumn('work_orders', 'completed_by')) {
                $table->unsignedBigInteger('completed_by')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('work_orders')) {
            return;
        }

        Schema::table('work_orders', function (Blueprint $table) {
            if (Schema::hasColumn('work_orders', 'completed_by')) {
                $table->dropColumn('completed_by');
            }
            if (Schema::hasColumn('work_orders', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
        });
    }
};
