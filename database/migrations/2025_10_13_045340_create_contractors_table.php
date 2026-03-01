<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->string('contract_name'); // اسم الشركة
            $table->string('contract_adminstarter'); // مدير الشركة
            $table->int('user_id')->nullable();; // مدير الشركة
            $table->string('phone1')->nullable(); // رقم الهاتف الأول
            $table->string('phone2')->nullable(); // رقم الهاتف الثاني
            $table->decimal('opening_balance', 15, 2)->default(0); // الرصيد الافتتاحي
            $table->boolean('isactive')->default(true); // حالة التفعيل
            $table->string('company_code',20); 
            $table->integer('branch_id'); 
            $table->string('address')->nullable(); // العنوان
            $table->date('createdate')->nullable(); // تاريخ الإنشاء
            $table->unsignedBigInteger('branch_id')->nullable(); // رقم الفرع
            $table->text('note')->nullable(); // ملاحظات
            $table->string('logo'); // اسم الشركة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contractors');
    }
}
