<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRequestStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_status', function (Blueprint $table) {
            $table->id();
            $table->string('name');   // اسم الحالة
            $table->string('code');   // رمز مختصر مثل NEW - IN_PROGRESS
            $table->timestamps();
        });

        // إضافة الحالات داخل نفس المايغريشن
        DB::table('request_status')->insert([
            [
                'name' => 'طلب جديد',
                'code' => 'NEW',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'قيد العمل',
                'code' => 'IN_PROGRESS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'تم الإنجاز',
                'code' => 'COMPLETED',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'تم الرفض',
                'code' => 'REJECTED',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_status');
    }
}
