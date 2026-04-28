<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحديث جدول الإشعارات
     */
    public function up(): void
    {
        if (!Schema::hasColumn('notifications', 'company_code')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('company_code', 10)->nullable()->after('id');
            });
        }
        if (!Schema::hasColumn('notifications', 'branch_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('company_code');
            });
        }
        if (!Schema::hasColumn('notifications', 'notification_type')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('notification_type', 50)->default('general')->after('type');
            });
        }
        if (!Schema::hasColumn('notifications', 'priority')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('notification_type');
            });
        }
        if (!Schema::hasColumn('notifications', 'channels')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->json('channels')->nullable()->comment('["app","sms","whatsapp"]')->after('priority');
            });
        }
        if (!Schema::hasColumn('notifications', 'sent_channels')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->json('sent_channels')->nullable()->comment('القنوات التي تم الإرسال عبرها فعلاً')->after('channels');
            });
        }
        if (!Schema::hasColumn('notifications', 'action_url')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('action_url', 500)->nullable()->after('message');
            });
        }
        if (!Schema::hasColumn('notifications', 'action_label')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('action_label', 100)->nullable()->after('action_url');
            });
        }
        if (!Schema::hasColumn('notifications', 'icon')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('icon', 50)->nullable()->after('action_label');
            });
        }
        if (!Schema::hasColumn('notifications', 'expires_at')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->timestamp('expires_at')->nullable()->after('read_at');
            });
        }

        $indexes = collect(DB::select("SHOW INDEX FROM notifications"))->pluck('Key_name')->all();
        if (!in_array('idx_company_branch', $indexes, true) && Schema::hasColumn('notifications', 'company_code') && Schema::hasColumn('notifications', 'branch_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['company_code', 'branch_id'], 'idx_company_branch');
            });
        }
        if (!in_array('idx_type', $indexes, true) && Schema::hasColumn('notifications', 'notification_type')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index('notification_type', 'idx_type');
            });
        }
        if (!in_array('idx_priority', $indexes, true) && Schema::hasColumn('notifications', 'priority')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index('priority', 'idx_priority');
            });
        }
        if (!in_array('idx_read', $indexes, true) && Schema::hasColumn('notifications', 'read_at')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index('read_at', 'idx_read');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_company_branch');
            $table->dropIndex('idx_type');
            $table->dropIndex('idx_priority');
            $table->dropIndex('idx_read');

            $table->dropColumn([
                'company_code',
                'branch_id',
                'notification_type',
                'priority',
                'channels',
                'sent_channels',
                'action_url',
                'action_label',
                'icon',
                'expires_at'
            ]);
        });
    }
};
