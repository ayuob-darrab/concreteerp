<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // بعد أول migrate للجداول الجديدة، شغّل مرة واحدة:
        // php artisan db:seed --class=PublicDisplayContentSeeder
    }
}
