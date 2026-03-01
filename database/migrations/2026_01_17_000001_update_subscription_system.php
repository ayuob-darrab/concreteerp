<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحديث نظام الاشتراكات - يعتمد على عدد المستخدمين
     */
    public function up()
    {
        // إضافة حقول جديدة لجدول الاشتراكات
        Schema::table('company_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('company_subscriptions', 'users_count')) {
                $table->integer('users_count')->default(1)->after('plan_type');
            }
            if (!Schema::hasColumn('company_subscriptions', 'price_per_user')) {
                $table->decimal('price_per_user', 12, 2)->nullable()->after('users_count');
            }
            if (!Schema::hasColumn('company_subscriptions', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0)->after('price_per_user');
            }
            if (!Schema::hasColumn('company_subscriptions', 'grace_days_used')) {
                $table->integer('grace_days_used')->default(0)->after('extension_days');
            }
            if (!Schema::hasColumn('company_subscriptions', 'grace_period_start')) {
                $table->date('grace_period_start')->nullable()->after('grace_days_used');
            }
            if (!Schema::hasColumn('company_subscriptions', 'is_in_grace_period')) {
                $table->boolean('is_in_grace_period')->default(false)->after('grace_period_start');
            }
            if (!Schema::hasColumn('company_subscriptions', 'days_to_deduct')) {
                $table->integer('days_to_deduct')->default(0)->after('is_in_grace_period');
            }
            if (!Schema::hasColumn('company_subscriptions', 'last_invoice_date')) {
                $table->date('last_invoice_date')->nullable()->after('days_to_deduct');
            }
            if (!Schema::hasColumn('company_subscriptions', 'years_count')) {
                $table->integer('years_count')->default(1)->after('duration_quantity');
            }
        });

        // إنشاء جدول إعدادات أسعار الاشتراكات
        if (!Schema::hasTable('subscription_pricing')) {
            Schema::create('subscription_pricing', function (Blueprint $table) {
                $table->id();
                $table->decimal('standard_price_monthly', 12, 2)->default(10000);
                $table->decimal('standard_price_yearly', 12, 2)->default(8000);
                $table->decimal('default_percentage_rate', 5, 2)->default(5);
                $table->decimal('default_fixed_order_fee', 12, 2)->default(1000);
                $table->integer('grace_period_days')->default(7);
                $table->integer('warning_days')->default(4);
                $table->integer('payment_due_days')->default(7);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // إنشاء جدول أسعار خاصة بالشركات
        if (!Schema::hasTable('company_subscription_prices')) {
            Schema::create('company_subscription_prices', function (Blueprint $table) {
                $table->id();
                $table->string('company_code');
                $table->decimal('price_per_user_monthly', 12, 2)->nullable();
                $table->decimal('price_per_user_yearly', 12, 2)->nullable();
                $table->decimal('custom_percentage_rate', 5, 2)->nullable();
                $table->decimal('custom_fixed_order_fee', 12, 2)->nullable();
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('company_code');
            });
        }

        // إنشاء جدول فواتير الاشتراكات
        if (!Schema::hasTable('subscription_invoices')) {
            Schema::create('subscription_invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->string('company_code');
                $table->unsignedBigInteger('subscription_id')->nullable();
                $table->enum('invoice_type', ['subscription', 'orders_percentage', 'renewal', 'additional_user'])->default('subscription');
                $table->date('period_start');
                $table->date('period_end');
                $table->integer('users_count')->default(1);
                $table->decimal('price_per_user', 12, 2)->default(0);
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->integer('orders_count')->nullable();
                $table->decimal('orders_total_value', 12, 2)->nullable();
                $table->decimal('percentage_rate', 5, 2)->nullable();
                $table->enum('payment_status', ['pending', 'paid', 'partial', 'overdue'])->default('pending');
                $table->decimal('paid_amount', 12, 2)->default(0);
                $table->date('due_date')->nullable();
                $table->date('paid_at')->nullable();
                $table->string('payment_method')->nullable();
                $table->string('payment_reference')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index('company_code');
            });
        }

        // إدخال الإعدادات الافتراضية
        if (DB::table('subscription_pricing')->count() == 0) {
            DB::table('subscription_pricing')->insert([
                'standard_price_monthly' => 10000,
                'standard_price_yearly' => 8000,
                'default_percentage_rate' => 5,
                'default_fixed_order_fee' => 1000,
                'grace_period_days' => 7,
                'warning_days' => 4,
                'payment_due_days' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('company_subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'users_count',
                'price_per_user',
                'total_amount',
                'grace_days_used',
                'grace_period_start',
                'is_in_grace_period',
                'days_to_deduct',
                'last_invoice_date',
                'years_count',
            ]);
        });

        Schema::dropIfExists('subscription_invoices');
        Schema::dropIfExists('company_subscription_prices');
        Schema::dropIfExists('subscription_pricing');
    }
};
