<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحديث جدول الفئات السعرية للخرسانة
     * السوبر أدمن يضيف الفئات العامة وكل شركة تحدد أسعارها
     */
    public function up()
    {
        Schema::table('pricing_categories', function (Blueprint $table) {
            $table->string('name', 100)->after('id')->comment('اسم الفئة السعرية');
            $table->text('description')->nullable()->after('name')->comment('وصف الفئة');
            $table->integer('sort_order')->default(0)->after('description')->comment('ترتيب العرض');
            $table->boolean('is_active')->default(true)->after('sort_order')->comment('حالة الفئة');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('pricing_categories', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'sort_order', 'is_active']);
        });
    }
};
