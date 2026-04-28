<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars_types', function (Blueprint $table) {
            if (!Schema::hasColumn('cars_types', 'hose_length')) {
                $table->decimal('hose_length', 8, 2)
                    ->nullable()
                    ->after('capacity')
                    ->comment('طول خرطوم البَم (متر)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cars_types', function (Blueprint $table) {
            if (Schema::hasColumn('cars_types', 'hose_length')) {
                $table->dropColumn('hose_length');
            }
        });
    }
};
