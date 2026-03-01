<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إنشاء جدول سجل الإرسال
     */
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();

            $table->uuid('notification_id');

            // القناة
            $table->enum('channel', ['app', 'sms', 'whatsapp', 'email']);

            // المستلم
            $table->string('recipient', 255); // رقم الهاتف أو البريد

            // الحالة
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed'])->default('pending');

            // التفاصيل
            $table->string('provider', 50)->nullable(); // twilio, whatsapp_api, etc.
            $table->text('provider_response')->nullable();
            $table->text('error_message')->nullable();

            // التواريخ
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // الفهارس
            $table->index('notification_id', 'idx_notification');
            $table->index('channel', 'idx_channel');
            $table->index('status', 'idx_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
