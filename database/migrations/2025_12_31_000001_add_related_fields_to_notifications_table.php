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
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'related_type')) {
                $table->string('related_type', 50)->nullable()->after('type'); // نوع المرتبط (advance, order, etc.)
            }
            if (!Schema::hasColumn('notifications', 'related_id')) {
                $table->unsignedBigInteger('related_id')->nullable()->after('related_type'); // معرف المرتبط
            }
            if (!Schema::hasColumn('notifications', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('company_code'); // الفرع
            }

            // Index for better performance
            $table->index(['related_type', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['related_type', 'related_id']);
            $table->dropColumn(['related_type', 'related_id', 'branch_id']);
        });
    }
};
