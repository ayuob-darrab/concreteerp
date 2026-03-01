<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إنشاء جدول الإشعارات
     */
    public function up(): void
    {
        // إنشاء جدول الإشعارات إذا لم يكن موجوداً
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['notifiable_type', 'notifiable_id']);
            });
        }

        // جدول إعدادات الإشعارات للمستخدمين
        if (!Schema::hasTable('notification_settings')) {
            Schema::create('notification_settings', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('user_id');

                // إشعارات الطلبات
                $table->boolean('notify_new_order')->default(true);
                $table->boolean('notify_order_approved')->default(true);
                $table->boolean('notify_order_rejected')->default(true);
                $table->boolean('notify_order_completed')->default(true);

                // إشعارات مالية
                $table->boolean('notify_payment_received')->default(true);
                $table->boolean('notify_invoice_issued')->default(true);
                $table->boolean('notify_check_due')->default(true);
                $table->boolean('notify_check_bounced')->default(true);

                // القنوات
                $table->boolean('channel_database')->default(true);
                $table->boolean('channel_email')->default(true);
                $table->boolean('channel_sms')->default(false);
                $table->boolean('channel_whatsapp')->default(false);

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
        // لا نحذف جدول notifications لأنه قد يكون موجوداً مسبقاً
    }
};
