<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarDriversTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * جدول سائقي السيارات - يدعم عدة شفتات وسائقين لكل سيارة
     * كل سيارة يمكن أن يكون لها سائق رئيسي واحتياطي لكل شفت
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_drivers', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('car_id')->comment('معرف السيارة');
            $table->unsignedBigInteger('driver_id')->comment('معرف السائق (الموظف)');
            $table->unsignedBigInteger('shift_id')->comment('معرف الشفت');
            $table->enum('driver_type', ['primary', 'backup'])->default('primary')->comment('نوع السائق: رئيسي أو احتياطي');
            $table->boolean('is_active')->default(true)->comment('هل التكليف نشط؟');
            $table->date('assigned_date')->nullable()->comment('تاريخ التكليف');
            $table->date('end_date')->nullable()->comment('تاريخ انتهاء التكليف');
            $table->text('end_reason')->nullable()->comment('سبب إنهاء التكليف');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->timestamps();

            // الفهارس
            $table->index('company_code');
            $table->index('car_id');
            $table->index('driver_id');
            $table->index('shift_id');
            $table->index('driver_type');
            $table->index('is_active');

            // ضمان عدم تكرار نفس السائق بنفس النوع لنفس السيارة في نفس الشفت
            $table->unique(['car_id', 'shift_id', 'driver_type', 'is_active'], 'unique_active_driver_per_shift');

            // العلاقات
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('shift_times')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_drivers');
    }
}
