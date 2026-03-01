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
        Schema::create('daily_cash_summaries', function (Blueprint $table) {
            $table->id();

            $table->string('company_code', 10);
            $table->foreignId('branch_id')->constrained('branches');
            $table->date('summary_date');

            // العملة
            $table->string('currency_code', 3)->default('IQD');

            // الأرصدة
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('total_receipts', 15, 2)->default(0); // إجمالي المقبوضات
            $table->decimal('total_payments', 15, 2)->default(0); // إجمالي المدفوعات
            $table->decimal('closing_balance', 15, 2)->default(0);

            // التفاصيل
            $table->integer('receipts_count')->default(0);
            $table->integer('payments_count')->default(0);

            // الحالة
            $table->enum('status', ['open', 'closed'])->default('open');

            // التوقيع
            $table->foreignId('opened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('opened_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('closed_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // فهرس فريد
            $table->unique(['company_code', 'branch_id', 'summary_date', 'currency_code'], 'unique_daily_summary');

            // الفهارس
            $table->index('summary_date');
            $table->index('status');
            $table->index('company_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_cash_summaries');
    }
};
