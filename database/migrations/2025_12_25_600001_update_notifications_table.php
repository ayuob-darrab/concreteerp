<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحديث جدول الإشعارات
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // إضافة حقول جديدة
            $table->string('company_code', 10)->nullable()->after('id');
            $table->unsignedBigInteger('branch_id')->nullable()->after('company_code');
            $table->string('notification_type', 50)->default('general')->after('type');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('notification_type');
            $table->json('channels')->nullable()->comment('["app","sms","whatsapp"]')->after('priority');
            $table->json('sent_channels')->nullable()->comment('القنوات التي تم الإرسال عبرها فعلاً')->after('channels');
            $table->string('action_url', 500)->nullable()->after('data');
            $table->string('action_label', 100)->nullable()->after('action_url');
            $table->string('icon', 50)->nullable()->after('action_label');
            $table->timestamp('expires_at')->nullable()->after('read_at');

            // فهارس
            $table->index(['company_code', 'branch_id'], 'idx_company_branch');
            $table->index('notification_type', 'idx_type');
            $table->index('priority', 'idx_priority');
            $table->index('read_at', 'idx_read');
        });
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
