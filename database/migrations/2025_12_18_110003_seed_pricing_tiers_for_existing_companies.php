<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedPricingTiersForExistingCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $companies = DB::table('companies')->where('code', '!=', 'SA')->get();

        foreach ($companies as $company) {
            $exists = DB::table('pricing_tiers')->where('company_code', $company->code)->exists();

            if (!$exists) {
                DB::table('pricing_tiers')->insert([
                    [
                        'company_code' => $company->code,
                        'name' => 'عادي',
                        'description' => 'السعر الأساسي للعملاء العاديين',
                        'price_modifier' => 0,
                        'fixed_modifier' => 0,
                        'is_default' => true,
                        'is_active' => true,
                        'sort_order' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'company_code' => $company->code,
                        'name' => 'مميز',
                        'description' => 'سعر مخفض للعملاء المميزين',
                        'price_modifier' => -5,
                        'fixed_modifier' => 0,
                        'is_default' => false,
                        'is_active' => true,
                        'sort_order' => 2,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'company_code' => $company->code,
                        'name' => 'VIP',
                        'description' => 'سعر خاص لعملاء VIP',
                        'price_modifier' => -10,
                        'fixed_modifier' => 0,
                        'is_default' => false,
                        'is_active' => true,
                        'sort_order' => 3,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'company_code' => $company->code,
                        'name' => 'مقاول',
                        'description' => 'سعر المقاولين',
                        'price_modifier' => -15,
                        'fixed_modifier' => 0,
                        'is_default' => false,
                        'is_active' => true,
                        'sort_order' => 4,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // لا نحذف البيانات في التراجع
    }
}
