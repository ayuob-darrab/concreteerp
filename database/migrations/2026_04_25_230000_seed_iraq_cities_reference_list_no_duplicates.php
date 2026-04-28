<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة محافظات عراقية بقائمة مرجعية (name_ar) دون تكرار.
     * يحاول إدراج المعرّف المرفق فقط إذا كان id غير مستخدم.
     */
    public function up(): void
    {
        if (! Schema::hasTable('cities')) {
            return;
        }

        $rows = [
            ['id' => 1, 'name_en' => 'Baghdad', 'name_ar' => 'بغداد'],
            ['id' => 2, 'name_en' => 'Basra', 'name_ar' => 'البصرة'],
            ['id' => 3, 'name_en' => 'Mosul', 'name_ar' => 'الموصل'],
            ['id' => 5, 'name_en' => 'Najaf', 'name_ar' => 'النجف'],
            ['id' => 6, 'name_en' => 'Karbala', 'name_ar' => 'كربلاء'],
            ['id' => 7, 'name_en' => 'Sulaymaniyah', 'name_ar' => 'السليمانية'],
            ['id' => 8, 'name_en' => 'Kirkuk', 'name_ar' => 'كركوك'],
            ['id' => 9, 'name_en' => 'Dhi Qar', 'name_ar' => 'ذي قار'],
            ['id' => 10, 'name_en' => 'Anbar', 'name_ar' => 'الأنبار'],
            ['id' => 11, 'name_en' => 'Erbil', 'name_ar' => 'أربيل'],
            ['id' => 12, 'name_en' => 'Babylon', 'name_ar' => 'بابل'],
            ['id' => 13, 'name_en' => 'Diyala', 'name_ar' => 'ديالى'],
            ['id' => 14, 'name_en' => 'Wasit', 'name_ar' => 'واسط'],
            ['id' => 15, 'name_en' => 'Maysan', 'name_ar' => 'ميسان'],
            ['id' => 16, 'name_en' => 'Muthanna', 'name_ar' => 'المثنى'],
            ['id' => 17, 'name_en' => 'Qadisiyyah', 'name_ar' => 'القادسية'],
            ['id' => 18, 'name_en' => 'Dohuk', 'name_ar' => 'دهوك'],
            ['id' => 19, 'name_en' => 'Saladin', 'name_ar' => 'صلاح الدين'],
            ['id' => 20, 'name_en' => 'Halabja', 'name_ar' => 'حلبجة'],
            ['id' => 22, 'name_en' => 'Nineveh', 'name_ar' => 'نينوى'],
        ];

        foreach ($rows as $row) {
            if (DB::table('cities')->where('name_ar', $row['name_ar'])->exists()) {
                continue;
            }

            $payload = [
                'name_en' => $row['name_en'],
                'name_ar' => $row['name_ar'],
            ];

            if (! DB::table('cities')->where('id', $row['id'])->exists()) {
                $payload['id'] = $row['id'];
            }

            DB::table('cities')->insert($payload);
        }
    }

    public function down(): void
    {
        // عدم حذف البيانات قد تكون مرتبطة بمفاتيح أجنبية
    }
};
