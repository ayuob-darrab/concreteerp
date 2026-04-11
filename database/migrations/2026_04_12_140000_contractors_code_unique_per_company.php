<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * كان code فريداً عالمياً في الجدول بينما التوليد CON-00001 لكل شركة على حدة.
 * الفهرس الفريد المركب (company_code, code) يسمح لكل شركة بسلسلة CON-00001 مستقلة.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('contractors')) {
            return;
        }

        $db = Schema::getConnection()->getDatabaseName();

        $oldUnique = DB::selectOne(
            'SELECT 1 AS ok FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$db, 'contractors', 'contractors_code_unique']
        );

        if ($oldUnique) {
            Schema::table('contractors', function (Blueprint $table) {
                $table->dropUnique('contractors_code_unique');
            });
        }

        $composite = DB::selectOne(
            'SELECT 1 AS ok FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$db, 'contractors', 'contractors_company_code_code_unique']
        );

        if (! $composite && Schema::hasColumn('contractors', 'code') && Schema::hasColumn('contractors', 'company_code')) {
            Schema::table('contractors', function (Blueprint $table) {
                $table->unique(['company_code', 'code'], 'contractors_company_code_code_unique');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('contractors')) {
            return;
        }

        $db = Schema::getConnection()->getDatabaseName();

        $composite = DB::selectOne(
            'SELECT 1 AS ok FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$db, 'contractors', 'contractors_company_code_code_unique']
        );

        if ($composite) {
            Schema::table('contractors', function (Blueprint $table) {
                $table->dropUnique('contractors_company_code_code_unique');
            });
        }

        $oldUnique = DB::selectOne(
            'SELECT 1 AS ok FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$db, 'contractors', 'contractors_code_unique']
        );

        if (! $oldUnique && Schema::hasColumn('contractors', 'code')) {
            Schema::table('contractors', function (Blueprint $table) {
                $table->unique('code', 'contractors_code_unique');
            });
        }
    }
};
