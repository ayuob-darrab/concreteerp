<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الملف
            $table->string('size')->nullable(); // حجم الملف
            $table->integer('companies_count')->default(0); // عدد الشركات
            $table->integer('users_count')->default(0); // عدد المستخدمين
            $table->integer('tables_count')->default(0); // عدد الجداول
            $table->text('notes')->nullable(); // ملاحظات
            $table->unsignedBigInteger('created_by'); // من قام بالنسخ
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('backups');
    }
};
