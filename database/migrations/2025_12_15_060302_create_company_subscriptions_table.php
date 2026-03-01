<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanySubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('company_code'); // كود الشركة
            $table->enum('plan_type', ['monthly', 'yearly', 'percentage', 'trial', 'hybrid']);
            $table->decimal('base_fee', 15, 2)->default(0);         // شهري/سنوي/هجين
            $table->decimal('percentage_rate', 5, 2)->nullable();   // نسبة من الطلبات أو جزء الهجين
            $table->integer('orders_limit')->nullable();            // للخطط ذات الحد (تجريبي/هجين)
            $table->integer('orders_used')->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->enum('status', ['active', 'expired', 'suspended'])->default('active');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_subscriptions');
    }
}
