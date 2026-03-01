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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();

            // الكود والاسم
            $table->string('code', 3)->unique(); // IQD, USD
            $table->string('name_ar', 50);
            $table->string('name_en', 50);
            $table->string('symbol', 10); // د.ع، $

            // سعر الصرف مقابل العملة الافتراضية
            $table->decimal('exchange_rate', 15, 6)->default(1);

            // الإعدادات
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('decimal_places')->default(0);

            // التتبع
            $table->timestamp('rate_updated_at')->nullable();
            $table->timestamps();

            // الفهارس
            $table->index('code');
            $table->index('is_default');
        });

        // البيانات الأولية
        DB::table('currencies')->insert([
            [
                'code' => 'IQD',
                'name_ar' => 'دينار عراقي',
                'name_en' => 'Iraqi Dinar',
                'symbol' => 'د.ع',
                'is_default' => true,
                'decimal_places' => 0,
                'exchange_rate' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'USD',
                'name_ar' => 'دولار أمريكي',
                'name_en' => 'US Dollar',
                'symbol' => '$',
                'is_default' => false,
                'decimal_places' => 2,
                'exchange_rate' => 1500, // سعر الصرف التقريبي
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
