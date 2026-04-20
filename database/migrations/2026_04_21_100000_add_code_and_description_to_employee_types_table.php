<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_types', function (Blueprint $table) {
            $table->string('code', 32)->nullable()->unique()->after('name');
            $table->text('description')->nullable()->after('code');
        });

        $defaults = [
            [
                'code' => 'ENG',
                'name' => 'مهندس',
                'description' => 'مسؤول عن الجانب الفني مثل جودة الكونكريت والإنتاج والمواصفات.',
            ],
            [
                'code' => 'ACC',
                'name' => 'محاسب',
                'description' => 'يتابع الفواتير والمدفوعات والرواتب والحسابات المالية.',
            ],
            [
                'code' => 'DRV',
                'name' => 'سائق',
                'description' => 'مسؤول عن توصيل الكونكريت للمواقع بالميكسر.',
            ],
            [
                'code' => 'WHS',
                'name' => 'أمين مستودع',
                'description' => 'يتحكم بدخول وخروج المواد والمعدات من المخزن.',
            ],
            [
                'code' => 'GRD',
                'name' => 'حارس',
                'description' => 'مسؤول عن أمن المصنع ومراقبة الدخول والخروج.',
            ],
        ];

        foreach ($defaults as $row) {
            $existingByCode = DB::table('employee_types')->where('code', $row['code'])->first();
            if ($existingByCode) {
                DB::table('employee_types')->where('id', $existingByCode->id)->update([
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'updated_at' => now(),
                ]);
                continue;
            }

            $existingByName = DB::table('employee_types')->where('name', $row['name'])->whereNull('code')->first();
            if ($existingByName) {
                DB::table('employee_types')->where('id', $existingByName->id)->update([
                    'code' => $row['code'],
                    'description' => $row['description'],
                    'updated_at' => now(),
                ]);
                continue;
            }

            DB::table('employee_types')->insert([
                'name' => $row['name'],
                'code' => $row['code'],
                'description' => $row['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('employee_types', function (Blueprint $table) {
            $table->dropColumn(['code', 'description']);
        });
    }
};
