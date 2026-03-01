<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة عمود status لجدول المعاملات المالية إن لم يكن موجوداً
     */
    public function up(): void
    {
        if (!Schema::hasTable('financial_transactions')) {
            return;
        }

        if (Schema::hasColumn('financial_transactions', 'status')) {
            return;
        }

        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->string('status', 20)->default('approved')->after('notes');
        });

        // إذا وُجد عمود is_approved ننسخ قيمته إلى status
        if (Schema::hasColumn('financial_transactions', 'is_approved')) {
            DB::table('financial_transactions')->where('is_approved', true)->update(['status' => 'approved']);
            DB::table('financial_transactions')->where('is_approved', false)->update(['status' => 'pending']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('financial_transactions') && Schema::hasColumn('financial_transactions', 'status')) {
            Schema::table('financial_transactions', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
