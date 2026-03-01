<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id'); // معرف القسم
            $table->string('fullname', 255); // الاسم الكامل
            $table->unsignedBigInteger('employee_types_id'); // نوع الموظف
            $table->unsignedBigInteger('shift_id'); // شفت العمل
            $table->boolean('isactive')->default(1); // حالة التفعيل
            $table->date('createdate')->nullable(); // تاريخ الإنشاء
            $table->string('file')->nullable(); // ملف أو مستند الموظف
            $table->string('personImage')->nullable(); // ملف أو مستند الموظف
            $table->string('phone', 20); // رقم الهاتف
            $table->decimal('salary', 10, 2)->nullable(); // الراتب
            $table->string('email', 255)->nullable(); // البريد الإلكتروني
            $table->timestamps();

            // العلاقات
            // $table->foreign('employee_types_id')->references('id')->on('employee_types')->onDelete('cascade');
            // $table->foreign('shift_id')->references('id')->on('shift_times')->onDelete('cascade');
;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
