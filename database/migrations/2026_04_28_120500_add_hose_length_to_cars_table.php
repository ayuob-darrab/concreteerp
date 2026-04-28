<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cars')) {
            return;
        }

        if (!Schema::hasColumn('cars', 'hose_length')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->decimal('hose_length', 8, 2)->nullable()->after('mixer_capacity');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('cars')) {
            return;
        }

        if (Schema::hasColumn('cars', 'hose_length')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->dropColumn('hose_length');
            });
        }
    }
};

