<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('companies')) {
            return;
        }

        $cityId = DB::table('cities')->orderBy('id')->value('id');
        if (! $cityId) {
            $cityId = DB::table('cities')->insertGetId([
                'name_ar' => 'بغداد',
                'name_en' => 'Baghdad',
            ]);
        }

        $dir = public_path('uploads/companies_logo');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $target = $dir . DIRECTORY_SEPARATOR . 'nine.png';
        if (! is_file($target)) {
            $candidates = [
                public_path('uploads/vJxy6/companies_logo/1776657809_SYVCdpDu9r7M3AGQ.png'),
                public_path('uploads/W4bFD/companies_logo/1776657939_VqpR0BnEGb81RU1K.png'),
            ];
            foreach ($candidates as $src) {
                if (is_file($src)) {
                    @File::copy($src, $target);
                    break;
                }
            }
        }

        $logo = 'assets/favicons/home.svg';
        if (is_file(public_path('uploads/companies_logo/nine.png'))) {
            $logo = 'uploads/companies_logo/nine.png';
        }

        $payload = [
            'name' => 'السوبر ادمن',
            'managername' => 'مدير النظام',
            'city_id' => (int) $cityId,
            'phone' => '7713863214',
            'email' => 'ninesoft@gmail.com',
            'is_active' => 1,
            'is_suspended' => 0,
            'userAdmin' => null,
            'address' => 'بغداد - شارع الصناعة',
            'note' => null,
            'creation_price' => 0.0,
            'logo' => $logo,
            'latitude' => '',
            'longitude' => '',
        ];

        if (Schema::hasColumn('companies', 'code_v2')) {
            $payload['code_v2'] = null;
        }
        if (Schema::hasColumn('companies', 'files_path')) {
            $payload['files_path'] = null;
        }

        $company = Company::withTrashed()->where('code', 'SA')->first();
        if ($company && $company->trashed()) {
            $company->restore();
        }

        Company::unguarded(function () use ($payload) {
            Company::updateOrCreate(
                ['code' => 'SA'],
                array_merge($payload, ['updated_at' => now(), 'deleted_at' => null])
            );
        });
    }

    public function down(): void
    {
        // لا نحذف الشركة تلقائياً
    }
};
