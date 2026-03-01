<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();

            // معلومات المرسل
            $table->string('sender_type');                   // عميل / فرع / موظف / نظام
            $table->unsignedBigInteger('sender_id')->nullable();

            // معلومات الطلب
            $table->string('classification')->nullable();    // نوع المادة (concrete_mix_id)
            $table->string('company_code');                  // كود الشركة
            $table->unsignedBigInteger('branch_id');         // معرف الفرع

            // الحالة الموحدة للطلب
            $table->enum('status', [
                'new',                  // طلب جديد
                'under_review',         // قيد المراجعة
                'waiting_customer',     // بانتظار موافقة العميل
                'approved',            // معتمد
                'rejected',            // مرفوض
                'scheduled',           // مجدول للتنفيذ
                'in_progress',         // قيد التنفيذ
                'completed',           // مكتمل
                'cancelled'            // ملغي
            ])->default('new');

            // تفاصيل الطلب الأساسية
            $table->string('request_type')->nullable();           // نوع الطلب: Concrete, Pump...
            $table->decimal('quantity', 10, 2);                   // الكمية المطلوبة
            $table->decimal('executed_quantity', 10, 2)->default(0); // الكمية المنفذة
            $table->string('location')->nullable();               // موقع الصب
            $table->dateTime('delivery_datetime')->nullable();    // وقت التسليم المطلوب
            $table->string('customer_name')->nullable();          // اسم العميل
            $table->string('customer_phone')->nullable();         // رقم العميل

            // السعر (فقط السعر الحالي)
            $table->decimal('initial_price', 10, 2)->nullable();  // السعر المبدئي (للعرض فقط)
            $table->decimal('final_price', 10, 2)->nullable();    // السعر النهائي المعتمد
            $table->boolean('price_approved')->default(false);    // هل السعر معتمد محاسبيًا

            // تواريخ رئيسية
            $table->dateTime('request_date');                     // تاريخ الطلب
            $table->unsignedBigInteger('created_by');             // منشئ الطلب

            // ملاحظات عامة
            $table->text('notes')->nullable();

            // تتبع التعديلات
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();  // للحذف الآمن

            // الفهارس
            $table->index('status');
            $table->index('company_code');
            $table->index('branch_id');
            $table->index('request_date');
            $table->index('delivery_datetime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_orders');
    }
}
