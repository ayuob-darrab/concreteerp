<?php

namespace App\Services;

use App\Models\CarsType;

class StandardCarTypeService
{
    /**
     * نسخ أنواع السيارات الافتراضية (قالب SA) لشركة جديدة.
     * يحافظ على نفس رموز الأنواع (CT-MIXER، CT-PUMP) لكل company_code.
     */
    public static function seedDefaultCarTypesForCompany(string $companyCode): int
    {
        if ($companyCode === '') {
            return 0;
        }

        $source = CarsType::query()
            ->where('company_code', 'SA')
            ->orderBy('id')
            ->get();

        if ($source->isEmpty()) {
            $source = self::defaultTemplateRows();
        }

        $created = 0;
        foreach ($source as $row) {
            $code = (string) ($row->code ?? '');
            if ($code === '') {
                continue;
            }

            $exists = CarsType::query()
                ->where('company_code', $companyCode)
                ->where('code', $code)
                ->exists();

            if ($exists) {
                continue;
            }

            CarsType::create([
                'code' => $code,
                'name' => $row->name,
                'company_code' => $companyCode,
                'note' => (string) ($row->note ?? ''),
                'capacity' => $row->capacity,
                'hose_length' => $row->hose_length,
            ]);
            $created++;
        }

        return $created;
    }

    /**
     * @return \Illuminate\Support\Collection<int, object>
     */
    protected static function defaultTemplateRows()
    {
        return collect([
            (object) [
                'code' => 'CT-MIXER',
                'name' => 'خباطة',
                'note' => '',
                'capacity' => null,
                'hose_length' => null,
            ],
            (object) [
                'code' => 'CT-PUMP',
                'name' => 'بم',
                'note' => '',
                'capacity' => null,
                'hose_length' => null,
            ],
        ]);
    }
}
