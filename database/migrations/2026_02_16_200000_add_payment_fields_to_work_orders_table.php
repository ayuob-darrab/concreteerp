<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToWorkOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('work_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('work_orders', 'payment_status')) {
                $table->string('payment_status')->default('unpaid')
                    ->comment('حالة الدفع: unpaid, partial, paid');
            }
            if (!Schema::hasColumn('work_orders', 'paid_amount')) {
                $table->decimal('paid_amount', 12, 2)->default(0)
                    ->comment('المبلغ المدفوع');
            }
            if (!Schema::hasColumn('work_orders', 'payment_method')) {
                $table->string('payment_method')->nullable()
                    ->comment('طريقة الدفع: cash, bank_transfer, check, card');
            }
            if (!Schema::hasColumn('work_orders', 'payment_note')) {
                $table->text('payment_note')->nullable()
                    ->comment('ملاحظة الدفع');
            }
            if (!Schema::hasColumn('work_orders', 'paid_at')) {
                $table->dateTime('paid_at')->nullable()
                    ->comment('تاريخ الدفع');
            }
            if (!Schema::hasColumn('work_orders', 'paid_by')) {
                $table->unsignedBigInteger('paid_by')->nullable()
                    ->comment('من قام بتسجيل الدفع');
            }
        });

        if (Schema::hasColumn('work_orders', 'payment_status')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->index('payment_status');
            });
        }
    }

    public function down()
    {
        Schema::table('work_orders', function (Blueprint $table) {
            if (Schema::hasColumn('work_orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('work_orders', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
            if (Schema::hasColumn('work_orders', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            if (Schema::hasColumn('work_orders', 'payment_note')) {
                $table->dropColumn('payment_note');
            }
            if (Schema::hasColumn('work_orders', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
            if (Schema::hasColumn('work_orders', 'paid_by')) {
                $table->dropColumn('paid_by');
            }
        });
    }
}
