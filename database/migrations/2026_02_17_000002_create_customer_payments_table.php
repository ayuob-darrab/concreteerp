<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // مدفوعات الزبائن (أصحاب الطلبات المباشرة وغير المباشرة)
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number', 30)->unique();
            $table->string('company_code', 15);
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('work_order_id');

            // معلومات الزبون
            $table->string('customer_name', 255);
            $table->string('customer_phone', 20)->nullable();

            // تفاصيل الدفع
            $table->enum('payment_type', ['cash', 'deferred'])->default('cash');
            $table->string('payment_method', 50)->nullable(); // نقدي، تحويل بنكي، شيك، دفع إلكتروني
            $table->decimal('total_amount', 15, 2); // المبلغ الكلي للطلب
            $table->decimal('paid_amount', 15, 2)->default(0); // المبلغ المدفوع
            $table->decimal('remaining_amount', 15, 2)->default(0); // المبلغ المتبقي

            // بطاقة الدفع (إذا كان الدفع إلكتروني)
            $table->unsignedBigInteger('company_payment_card_id')->nullable();

            // معلومات إضافية
            $table->string('reference_number', 100)->nullable();
            $table->string('receipt_number', 100)->nullable();
            $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('work_order_id')->references('id')->on('work_orders')->onDelete('cascade');
            $table->foreign('company_payment_card_id')->references('id')->on('company_payment_cards')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->index('company_code');
            $table->index('branch_id');
            $table->index('work_order_id');
            $table->index('status');
            $table->index('customer_phone');
        });

        // سجل الدفعات (لتتبع الدفعات الجزئية)
        Schema::create('customer_payment_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_number', 30)->unique();
            $table->unsignedBigInteger('customer_payment_id');
            $table->string('company_code', 15);
            $table->unsignedBigInteger('branch_id');

            // تفاصيل الدفعة
            $table->string('payment_method', 50); // نقدي، تحويل بنكي، شيك، دفع إلكتروني
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2); // الرصيد المتبقي قبل الدفعة
            $table->decimal('balance_after', 15, 2); // الرصيد المتبقي بعد الدفعة

            // بطاقة الدفع
            $table->unsignedBigInteger('company_payment_card_id')->nullable();

            // معلومات إضافية
            $table->string('reference_number', 100)->nullable();
            $table->string('receipt_number', 100)->nullable();
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('customer_payment_id');
            $table->index('company_code');
            $table->index('branch_id');
        });

        // إضافة قيود المفاتيح الأجنبية لـ customer_payment_records بعد إنشاء الجدول (لتجنب فشل ADD CONSTRAINT)
        Schema::disableForeignKeyConstraints();
        Schema::table('customer_payment_records', function (Blueprint $table) {
            $table->foreign('customer_payment_id')->references('id')->on('customer_payments')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('company_payment_card_id')->references('id')->on('company_payment_cards')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::dropIfExists('customer_payment_records');
        Schema::dropIfExists('customer_payments');
    }
};
