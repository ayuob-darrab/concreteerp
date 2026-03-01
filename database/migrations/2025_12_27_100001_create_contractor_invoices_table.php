<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contractor_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('contractor_id')->constrained('contractors');
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders');

            $table->string('invoice_number', 30)->unique();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->text('description');
            $table->json('items'); // بنود الفاتورة

            // المبالغ
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);

            // الحالة
            $table->enum('status', ['draft', 'issued', 'partial', 'paid', 'cancelled', 'overdue'])->default('draft');
            $table->text('notes')->nullable();

            // الإلغاء
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users');

            // التواريخ
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_code', 'status']);
            $table->index(['contractor_id', 'status']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_invoices');
    }
};
