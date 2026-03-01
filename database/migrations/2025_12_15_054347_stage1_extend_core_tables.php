<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Stage1ExtendCoreTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ===== الشركات: إضافة كود جديد ومسار ملفات مخصص =====
        Schema::table('companies', function (Blueprint $table) {
            // كود فريد بطول 10 أحرف (لا يغيّر الكود الحالي)
            $table->string('code_v2', 10)->nullable()->unique()->after('code');
            // مسار مجلد ملفات الشركة
            $table->string('files_path', 255)->nullable()->after('logo');
        });

        // ===== الفروع: إضافة كود جديد ومسار ملفات =====
        Schema::table('branches', function (Blueprint $table) {
            $table->string('branch_code_v2', 10)->nullable()->unique()->after('company_code');
            $table->string('files_path', 255)->nullable()->after('address');
        });

        // ===== أوامر العمل/الطلبات: دعم مراحل الموافقة والتنفيذ =====
        Schema::table('work_orders', function (Blueprint $table) {
            // موافقة الفرع/الموظف
            $table->decimal('approved_price', 15, 2)->nullable()->after('price');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_price');
            $table->dateTime('approved_at')->nullable()->after('approved_by');
            $table->text('approved_note')->nullable()->after('approved_at');

            // موافقة العميل (للمقاولين)
            $table->enum('client_approved', ['accepted', 'rejected', 'edit_requested', 'pending'])
                ->default('pending')
                ->after('approved_note');
            $table->dateTime('client_approved_at')->nullable()->after('client_approved');

            // الموافقة النهائية/التنفيذ
            $table->decimal('final_price', 15, 2)->nullable()->after('client_approved_at');
            $table->date('execution_date')->nullable()->after('final_price');
            $table->time('execution_time')->nullable()->after('execution_date');

            // الإلغاء
            $table->text('cancellation_reason')->nullable()->after('execution_time');
        });

        // ===== المواد: تحضير للمتوسط المرجح والحجز =====
        Schema::table('materials', function (Blueprint $table) {
            $table->decimal('reserved_quantity', 15, 3)->default(0)->after('price');
            $table->decimal('unit_cost', 15, 2)->default(0)->after('reserved_quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['reserved_quantity', 'unit_cost']);
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn([
                'approved_price',
                'approved_by',
                'approved_at',
                'approved_note',
                'client_approved',
                'client_approved_at',
                'final_price',
                'execution_date',
                'execution_time',
                'cancellation_reason',
            ]);
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['branch_code_v2', 'files_path']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['code_v2', 'files_path']);
        });
    }
}
