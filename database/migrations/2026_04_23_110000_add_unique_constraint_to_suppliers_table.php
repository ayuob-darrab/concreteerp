<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('suppliers')) {
            return;
        }

        // فهرس فريد مركب: اسم المورد + الفرع (لكل شركة)
        // هذا يمنع تكرار نفس المورد في نفس الفرع
        Schema::table('suppliers', function (Blueprint $table) {
            $table->unique(
                ['company_code', 'branch_id', 'supplier_name'],
                'suppliers_company_branch_name_unique'
            );
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('suppliers')) {
            return;
        }

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropUnique('suppliers_company_branch_name_unique');
        });
    }
};
