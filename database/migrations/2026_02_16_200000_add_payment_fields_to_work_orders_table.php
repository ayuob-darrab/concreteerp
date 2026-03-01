<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToWorkOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('price')
                ->comment('حالة الدفع: unpaid, partial, paid');
            $table->decimal('paid_amount', 12, 2)->default(0)->after('payment_status')
                ->comment('المبلغ المدفوع');
            $table->string('payment_method')->nullable()->after('paid_amount')
                ->comment('طريقة الدفع: cash, bank_transfer, check, card');
            $table->text('payment_note')->nullable()->after('payment_method')
                ->comment('ملاحظة الدفع');
            $table->dateTime('paid_at')->nullable()->after('payment_note')
                ->comment('تاريخ الدفع');
            $table->unsignedBigInteger('paid_by')->nullable()->after('paid_at')
                ->comment('من قام بتسجيل الدفع');

            $table->index('payment_status');
        });
    }

    public function down()
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropColumn([
                'payment_status',
                'paid_amount',
                'payment_method',
                'payment_note',
                'paid_at',
                'paid_by',
            ]);
        });
    }
}
