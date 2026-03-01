<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // جدول بطاقات/حسابات الدفع الإلكتروني
        Schema::create('payment_cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_type', 50); // mastercard, visa, zaincash, etc
            $table->string('card_name', 100); // اسم البطاقة/الحساب (مثل: ماستر كارد رئيسي)
            $table->string('holder_name', 100); // اسم صاحب البطاقة
            $table->string('card_number', 50); // رقم البطاقة أو الحساب
            $table->string('card_number_masked', 50)->nullable(); // الرقم المخفي للعرض (****1234)
            $table->decimal('opening_balance', 15, 2)->default(0); // الرصيد الافتتاحي
            $table->decimal('current_balance', 15, 2)->default(0); // الرصيد الحالي
            $table->date('expiry_date')->nullable(); // تاريخ انتهاء الصلاحية
            $table->boolean('is_active')->default(true); // حالة البطاقة
            $table->text('notes')->nullable(); // ملاحظات
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // جدول معاملات البطاقات (الإيداعات والسحوبات)
        Schema::create('payment_card_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_card_id')->constrained('payment_cards')->cascadeOnDelete();
            $table->string('transaction_number', 50)->unique(); // رقم المعاملة
            $table->enum('type', ['deposit', 'withdrawal']); // نوع المعاملة: إيداع أو سحب
            $table->decimal('amount', 15, 2); // المبلغ
            $table->decimal('balance_before', 15, 2); // الرصيد قبل
            $table->decimal('balance_after', 15, 2); // الرصيد بعد
            $table->string('reference_type', 50)->nullable(); // نوع المرجع (subscription, manual, etc)
            $table->unsignedBigInteger('reference_id')->nullable(); // معرف المرجع
            $table->string('company_code', 20)->nullable(); // كود الشركة (في حالة اشتراكات)
            $table->string('description', 255)->nullable(); // وصف المعاملة
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['payment_card_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_card_transactions');
        Schema::dropIfExists('payment_cards');
    }
}
