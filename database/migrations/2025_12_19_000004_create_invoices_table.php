<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إنشاء جدول الفواتير
     */
    public function up(): void
    {
        if (Schema::hasTable('invoices')) {
            return;
        }

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // رقم الفاتورة
            $table->string('invoice_number', 50)->unique();

            // نوع الفاتورة
            $table->enum('invoice_type', ['sale', 'credit_note', 'debit_note'])->default('sale');

            // الربط
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('work_order_id')->nullable();

            // الشركة والفرع
            $table->string('company_code', 20)->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();

            // بيانات الطرف الآخر
            $table->string('party_name', 255);
            $table->string('party_phone', 20)->nullable();
            $table->text('party_address')->nullable();
            $table->string('party_tax_number', 50)->nullable();

            // التواريخ
            $table->date('invoice_date');
            $table->date('due_date');

            // المبالغ
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2);

            // العملة
            $table->string('currency', 3)->default('IQD');
            $table->decimal('exchange_rate', 15, 4)->default(1);

            // الحالة
            $table->enum('status', [
                'draft',           // مسودة
                'issued',          // صادرة
                'partially_paid',  // مدفوعة جزئياً
                'paid',            // مدفوعة بالكامل
                'overdue',         // متأخرة
                'cancelled',       // ملغاة
            ])->default('draft');

            // ملاحظات
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();

            // التتبع
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index('invoice_date', 'idx_invoices_date');
            $table->index('due_date', 'idx_invoices_due');
            $table->index('status', 'idx_invoices_status');
            $table->index('company_code', 'idx_invoices_company');
            $table->index('branch_id', 'idx_invoices_branch');
            $table->index(['company_code', 'invoice_date'], 'idx_invoices_company_date');
        });

        // جدول بنود الفاتورة
        if (!Schema::hasTable('invoice_items')) {
            Schema::create('invoice_items', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('invoice_id');

                // البند
                $table->string('item_type', 50)->default('concrete');
                $table->unsignedBigInteger('item_id')->nullable();
                $table->text('description');

                // الكمية والسعر
                $table->decimal('quantity', 15, 3);
                $table->string('unit', 20)->default('م³');
                $table->decimal('unit_price', 15, 2);
                $table->decimal('discount_percentage', 5, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_percentage', 5, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('line_total', 15, 2);

                $table->timestamps();

                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
                $table->index('invoice_id', 'idx_invoice_items_invoice');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
