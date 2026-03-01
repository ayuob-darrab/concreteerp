<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول ربط الموظفين بالشفتات - يدعم عمل الموظف في أكثر من شفت
     */
    public function up()
    {
        Schema::create('employee_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 50);
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('shift_id');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false)->comment('هل هذا الشفت الرئيسي للموظف');
            $table->date('assigned_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('end_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // العلاقات
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('shift_times')->onDelete('cascade');

            // فهرس مركب لمنع التكرار
            $table->unique(['employee_id', 'shift_id', 'is_active'], 'unique_employee_shift_active');

            // فهارس للأداء
            $table->index(['company_code', 'is_active']);
            $table->index(['shift_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('employee_shifts');
    }
};
