<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class IraqiGovernoratesSeeder extends Seeder
{
    /**
     * المحافظات العراقية (19 محافظة)
     * يضيف فقط المحافظات التي لا يوجد اسمها بالعربي مسبقاً
     */
    public function run()
    {
        $governorates = [
            ['name_ar' => 'بغداد', 'name_en' => 'Baghdad'],
            ['name_ar' => 'البصرة', 'name_en' => 'Basra'],
            ['name_ar' => 'نينوى', 'name_en' => 'Nineveh'],
            ['name_ar' => 'أربيل', 'name_en' => 'Erbil'],
            ['name_ar' => 'النجف', 'name_en' => 'Najaf'],
            ['name_ar' => 'كربلاء', 'name_en' => 'Karbala'],
            ['name_ar' => 'بابل', 'name_en' => 'Babylon'],
            ['name_ar' => 'ديالى', 'name_en' => 'Diyala'],
            ['name_ar' => 'واسط', 'name_en' => 'Wasit'],
            ['name_ar' => 'ميسان', 'name_en' => 'Maysan'],
            ['name_ar' => 'ذي قار', 'name_en' => 'Dhi Qar'],
            ['name_ar' => 'المثنى', 'name_en' => 'Muthanna'],
            ['name_ar' => 'القادسية', 'name_en' => 'Qadisiyyah'],
            ['name_ar' => 'الأنبار', 'name_en' => 'Anbar'],
            ['name_ar' => 'كركوك', 'name_en' => 'Kirkuk'],
            ['name_ar' => 'السليمانية', 'name_en' => 'Sulaymaniyah'],
            ['name_ar' => 'دهوك', 'name_en' => 'Dohuk'],
            ['name_ar' => 'صلاح الدين', 'name_en' => 'Saladin'],
            ['name_ar' => 'حلبجة', 'name_en' => 'Halabja'],
        ];

        foreach ($governorates as $gov) {
            City::firstOrCreate(
                ['name_ar' => $gov['name_ar']],
                ['name_en' => $gov['name_en'] ?? $gov['name_ar']]
            );
        }
    }
}
