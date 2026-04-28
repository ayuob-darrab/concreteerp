<?php

use App\Models\CarsType;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $rows = [
            ['code' => 'CT-MIXER', 'name' => 'خباطة'],
            ['code' => 'CT-PUMP', 'name' => 'بم'],
        ];

        foreach ($rows as $row) {
            CarsType::updateOrCreate(
                [
                    'company_code' => 'SA',
                    'code' => $row['code'],
                ],
                [
                    'name' => $row['name'],
                    'note' => '',
                ]
            );
        }
    }

    public function down(): void
    {
        CarsType::query()
            ->where('company_code', 'SA')
            ->whereIn('code', ['CT-MIXER', 'CT-PUMP'])
            ->delete();
    }
};
