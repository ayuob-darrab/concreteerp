<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        if (!Schema::hasColumn('users', 'shift_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('shift_id')->nullable()->after('branch_id');
                $table->index('shift_id', 'users_shift_id_index');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'shift_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_shift_id_index');
            $table->dropColumn('shift_id');
        });
    }
};

