<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إنشاء جدول إعدادات الإشعارات للمستخدمين
     */
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');

            // نوع الإشعار
            $table->string('notification_type', 50);

            // القنوات المفعلة
            $table->boolean('app_enabled')->default(true);
            $table->boolean('sms_enabled')->default(false);
            $table->boolean('whatsapp_enabled')->default(false);
            $table->boolean('email_enabled')->default(false);

            $table->timestamps();

            // فهرس فريد
            $table->unique(['user_id', 'notification_type'], 'unique_user_type');

            // العلاقات
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
