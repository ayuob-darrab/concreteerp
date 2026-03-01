<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->string('group')->default('general'); // general, security, email, etc.
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // إدراج الإعدادات الافتراضية
        DB::table('settings')->insert([
            // الإعدادات العامة
            ['key' => 'app_name', 'value' => 'ConcreteERP', 'type' => 'string', 'group' => 'general', 'description' => 'اسم النظام', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'support_email', 'value' => '', 'type' => 'string', 'group' => 'general', 'description' => 'البريد الإلكتروني للدعم', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'timezone', 'value' => 'Asia/Baghdad', 'type' => 'string', 'group' => 'general', 'description' => 'المنطقة الزمنية', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'currency', 'value' => 'دينار عراقي', 'type' => 'string', 'group' => 'general', 'description' => 'العملة الافتراضية', 'created_at' => now(), 'updated_at' => now()],

            // إعدادات الأمان
            ['key' => 'force_https', 'value' => '0', 'type' => 'boolean', 'group' => 'security', 'description' => 'فرض HTTPS', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'enable_2fa', 'value' => '0', 'type' => 'boolean', 'group' => 'security', 'description' => 'تفعيل التحقق بخطوتين', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'session_lifetime', 'value' => '120', 'type' => 'integer', 'group' => 'security', 'description' => 'مدة الجلسة بالدقائق', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
