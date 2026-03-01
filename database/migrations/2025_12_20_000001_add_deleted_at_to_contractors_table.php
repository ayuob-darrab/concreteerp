<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة عمود deleted_at لجدول المقاولين
     */
    public function up(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            if (!Schema::hasColumn('contractors', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
