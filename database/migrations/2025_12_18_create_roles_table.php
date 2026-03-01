<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // رمز الدور مثل SA, CM, BM
            $table->string('name', 100); // اسم الدور بالعربي
            $table->text('description')->nullable(); // وصف الدور
            $table->boolean('is_system')->default(false); // هل هو دور نظام (لا يمكن حذفه)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // إدخال الأدوار الافتراضية
        DB::table('roles')->insert([
            ['code' => 'SA', 'name' => 'سوبر أدمن', 'description' => 'صلاحيات كاملة على النظام', 'is_system' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CM', 'name' => 'مدير شركة', 'description' => 'إدارة شركة كاملة', 'is_system' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'BM', 'name' => 'مدير فرع', 'description' => 'إدارة فرع واحد', 'is_system' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'cont', 'name' => 'مقاول', 'description' => 'حساب مقاول', 'is_system' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'delegate', 'name' => 'مندوب', 'description' => 'حساب مندوب', 'is_system' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
