<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationMapUrlToWorkOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('work_orders', 'location_map_url')) {
                $table->text('location_map_url')->nullable()->after('location');
            }
            if (!Schema::hasColumn('work_orders', 'location_lat')) {
                $table->decimal('location_lat', 10, 7)->nullable()->after('location_map_url');
            }
            if (!Schema::hasColumn('work_orders', 'location_lng')) {
                $table->decimal('location_lng', 10, 7)->nullable()->after('location_lat');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(['location_map_url', 'location_lat', 'location_lng']);
        });
    }
}
