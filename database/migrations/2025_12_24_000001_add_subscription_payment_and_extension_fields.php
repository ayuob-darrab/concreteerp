<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscriptionPaymentAndExtensionFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // تحديث جدول الاشتراكات
        Schema::table('company_subscriptions', function (Blueprint $table) {
            // حقول الدفع
            $table->enum('payment_status', ['pending', 'paid', 'partial'])->default('pending')->after('status');
            $table->decimal('paid_amount', 15, 2)->default(0)->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('paid_amount');
            $table->string('payment_method')->nullable()->after('paid_at'); // cash, bank_transfer, check, online
            $table->string('payment_reference')->nullable()->after('payment_method'); // رقم المرجع/الإيصال

            // حقول التمديد
            $table->integer('extension_days')->default(0)->after('payment_reference'); // أيام التمديد الممنوحة
            $table->boolean('extension_deducted')->default(false)->after('extension_days'); // هل تم خصم التمديد

            // حقول مدة الاشتراك
            $table->integer('duration_quantity')->default(1)->after('extension_deducted'); // عدد الأشهر/السنوات
            $table->integer('total_days')->nullable()->after('duration_quantity'); // إجمالي الأيام الأصلية

            // منع التكرار
            $table->unique(['company_code', 'start_date', 'end_date'], 'unique_subscription_period');
        });

        // تحديث جدول تاريخ الاشتراكات
        Schema::table('subscription_history', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'paid', 'partial'])->default('pending')->after('status');
            $table->decimal('paid_amount', 15, 2)->default(0)->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('paid_amount');
            $table->string('payment_method')->nullable()->after('paid_at');
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->integer('extension_days')->default(0)->after('payment_reference');
            $table->boolean('extension_deducted')->default(false)->after('extension_days');
            $table->integer('duration_quantity')->default(1)->after('extension_deducted');
            $table->integer('total_days')->nullable()->after('duration_quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_subscriptions', function (Blueprint $table) {
            $table->dropUnique('unique_subscription_period');
            $table->dropColumn([
                'payment_status',
                'paid_amount',
                'paid_at',
                'payment_method',
                'payment_reference',
                'extension_days',
                'extension_deducted',
                'duration_quantity',
                'total_days'
            ]);
        });

        Schema::table('subscription_history', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'paid_amount',
                'paid_at',
                'payment_method',
                'payment_reference',
                'extension_days',
                'extension_deducted',
                'duration_quantity',
                'total_days'
            ]);
        });
    }
}
