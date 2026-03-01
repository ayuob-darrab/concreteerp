<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إنشاء جدول الشيكات
     */
    public function up(): void
    {
        if (Schema::hasTable('checks')) {
            return;
        }

        Schema::create('checks', function (Blueprint $table) {
            $table->id();

            // معلومات الشيك
            $table->string('check_number', 50);
            $table->date('check_date');
            $table->date('due_date');

            // البنك
            $table->string('bank_name', 255);
            $table->string('bank_branch', 255)->nullable();
            $table->string('bank_account', 100)->nullable();

            // المبلغ
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IQD');

            // الأطراف
            $table->string('drawer_name', 255);
            $table->enum('drawer_type', ['contractor', 'customer', 'other']);
            $table->unsignedBigInteger('drawer_id')->nullable();
            $table->string('beneficiary_name', 255)->nullable();

            // الحالة
            $table->enum('status', [
                'pending',           // معلق
                'deposited',         // مودع في البنك
                'collected',         // محصّل
                'returned',          // مرتجع
                'bounced',           // مرفوض (بدون رصيد)
                'cancelled',         // ملغي
                'replaced',          // مستبدل
            ])->default('pending');

            // تفاصيل الحالة
            $table->date('deposit_date')->nullable();
            $table->date('collection_date')->nullable();
            $table->date('return_date')->nullable();
            $table->text('return_reason')->nullable();
            $table->integer('bounce_count')->default(0);

            // الربط
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('replacement_check_id')->nullable();

            // الشركة والفرع
            $table->string('company_code', 20)->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();

            // المرفقات
            $table->string('image_front', 500)->nullable();
            $table->string('image_back', 500)->nullable();

            // ملاحظات
            $table->text('notes')->nullable();

            // التتبع
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index('check_number', 'idx_checks_number');
            $table->index('due_date', 'idx_checks_due');
            $table->index('status', 'idx_checks_status');
            $table->index(['drawer_type', 'drawer_id'], 'idx_checks_drawer');
            $table->index('company_code', 'idx_checks_company');
            $table->index('branch_id', 'idx_checks_branch');
            $table->index(['company_code', 'due_date'], 'idx_checks_company_due');
            $table->index(['company_code', 'status'], 'idx_checks_company_status');
        });

        // جدول سجل حالات الشيك
        if (!Schema::hasTable('check_status_logs')) {
            Schema::create('check_status_logs', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('check_id');
                $table->string('from_status', 50)->nullable();
                $table->string('to_status', 50);
                $table->text('reason')->nullable();
                $table->unsignedBigInteger('performed_by')->nullable();
                $table->timestamp('performed_at')->useCurrent();

                $table->foreign('check_id')->references('id')->on('checks')->onDelete('cascade');
                $table->index('check_id', 'idx_check_logs_check');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_status_logs');
        Schema::dropIfExists('checks');
    }
};
