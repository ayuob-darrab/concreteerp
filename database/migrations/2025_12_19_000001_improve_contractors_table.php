<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحسين جدول المقاولين
     */
    public function up(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            // إضافة الأعمدة الجديدة إذا لم تكن موجودة

            // كود فريد للمقاول
            if (!Schema::hasColumn('contractors', 'code')) {
                $table->string('code', 20)->unique()->nullable()->after('id');
            }

            // الاسم بالإنجليزية
            if (!Schema::hasColumn('contractors', 'contract_name_en')) {
                $table->string('contract_name_en', 255)->nullable()->after('contract_name');
            }

            // رقم رخصة المقاولة
            if (!Schema::hasColumn('contractors', 'license_number')) {
                $table->string('license_number', 100)->nullable()->after('contract_adminstarter');
            }

            // الرقم الضريبي
            if (!Schema::hasColumn('contractors', 'tax_number')) {
                $table->string('tax_number', 100)->nullable()->after('license_number');
            }

            // رقم الواتساب
            if (!Schema::hasColumn('contractors', 'whatsapp')) {
                $table->string('whatsapp', 20)->nullable()->after('phone2');
            }

            // البريد الإلكتروني
            if (!Schema::hasColumn('contractors', 'email')) {
                $table->string('email', 255)->nullable()->after('whatsapp');
            }

            // الموقع الإلكتروني
            if (!Schema::hasColumn('contractors', 'website')) {
                $table->string('website', 255)->nullable()->after('email');
            }

            // المحافظة
            if (!Schema::hasColumn('contractors', 'city_id')) {
                $table->unsignedInteger('city_id')->nullable()->after('address');
            }

            // الموقع الجغرافي
            if (!Schema::hasColumn('contractors', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('city_id');
            }
            if (!Schema::hasColumn('contractors', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }

            // نوع الرصيد الافتتاحي
            if (!Schema::hasColumn('contractors', 'opening_balance_type')) {
                $table->enum('opening_balance_type', ['debit', 'credit'])->default('debit')->after('opening_balance');
            }

            // الحد الائتماني
            if (!Schema::hasColumn('contractors', 'credit_limit')) {
                $table->decimal('credit_limit', 15, 2)->default(0)->after('opening_balance_type');
            }

            // مدة السداد بالأيام
            if (!Schema::hasColumn('contractors', 'payment_terms')) {
                $table->integer('payment_terms')->default(30)->after('credit_limit');
            }

            // نسبة الخصم
            if (!Schema::hasColumn('contractors', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0)->after('payment_terms');
            }

            // فئة السعر
            if (!Schema::hasColumn('contractors', 'price_category_id')) {
                $table->unsignedInteger('price_category_id')->nullable()->after('discount_percentage');
            }

            // العملة المفضلة
            if (!Schema::hasColumn('contractors', 'currency')) {
                $table->string('currency', 3)->default('IQD')->after('price_category_id');
            }

            // نوع المقاول
            if (!Schema::hasColumn('contractors', 'contractor_type')) {
                $table->enum('contractor_type', ['individual', 'company', 'government'])->default('company')->after('currency');
            }

            // تصنيف المقاول
            if (!Schema::hasColumn('contractors', 'classification')) {
                $table->enum('classification', ['A', 'B', 'C', 'D', 'VIP'])->default('C')->after('contractor_type');
            }

            // التقييم
            if (!Schema::hasColumn('contractors', 'rating')) {
                $table->decimal('rating', 2, 1)->default(0)->after('classification');
            }

            // محظور
            if (!Schema::hasColumn('contractors', 'is_blocked')) {
                $table->boolean('is_blocked')->default(false)->after('isactive');
            }

            // سبب الحظر
            if (!Schema::hasColumn('contractors', 'block_reason')) {
                $table->text('block_reason')->nullable()->after('is_blocked');
            }

            // تاريخ الحظر
            if (!Schema::hasColumn('contractors', 'blocked_at')) {
                $table->timestamp('blocked_at')->nullable()->after('block_reason');
            }

            // من قام بالحظر
            if (!Schema::hasColumn('contractors', 'blocked_by')) {
                $table->unsignedInteger('blocked_by')->nullable()->after('blocked_at');
            }

            // ملف العقد
            if (!Schema::hasColumn('contractors', 'contract_file')) {
                $table->string('contract_file', 255)->nullable()->after('logo');
            }

            // مستندات إضافية
            if (!Schema::hasColumn('contractors', 'documents')) {
                $table->json('documents')->nullable()->after('contract_file');
            }

            // ملاحظات داخلية
            if (!Schema::hasColumn('contractors', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('note');
            }

            // تاريخ آخر طلب
            if (!Schema::hasColumn('contractors', 'last_order_date')) {
                $table->date('last_order_date')->nullable()->after('createdate');
            }

            // إجمالي الطلبات
            if (!Schema::hasColumn('contractors', 'total_orders')) {
                $table->integer('total_orders')->default(0)->after('last_order_date');
            }

            // إجمالي الكمية
            if (!Schema::hasColumn('contractors', 'total_quantity')) {
                $table->decimal('total_quantity', 15, 2)->default(0)->after('total_orders');
            }

            // إجمالي المبلغ
            if (!Schema::hasColumn('contractors', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('total_quantity');
            }

            // من أنشأ
            if (!Schema::hasColumn('contractors', 'created_by')) {
                $table->unsignedInteger('created_by')->nullable()->after('total_amount');
            }

            // من عدّل
            if (!Schema::hasColumn('contractors', 'updated_by')) {
                $table->unsignedInteger('updated_by')->nullable()->after('created_by');
            }

            // الحذف الناعم
            if (!Schema::hasColumn('contractors', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // إضافة الفهارس
        Schema::table('contractors', function (Blueprint $table) {
            // التحقق من عدم وجود الفهارس قبل إضافتها
            $indexes = collect(DB::select("SHOW INDEX FROM contractors"))->pluck('Key_name')->unique()->toArray();

            if (!in_array('idx_contractors_code', $indexes)) {
                $table->index('code', 'idx_contractors_code');
            }
            if (!in_array('idx_contractors_classification', $indexes)) {
                $table->index('classification', 'idx_contractors_classification');
            }
            if (!in_array('idx_contractors_is_blocked', $indexes)) {
                $table->index('is_blocked', 'idx_contractors_is_blocked');
            }
            if (!in_array('idx_contractors_last_order', $indexes)) {
                $table->index('last_order_date', 'idx_contractors_last_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            $columns = [
                'code',
                'contract_name_en',
                'license_number',
                'tax_number',
                'whatsapp',
                'email',
                'website',
                'city_id',
                'latitude',
                'longitude',
                'opening_balance_type',
                'credit_limit',
                'payment_terms',
                'discount_percentage',
                'price_category_id',
                'currency',
                'contractor_type',
                'classification',
                'rating',
                'is_blocked',
                'block_reason',
                'blocked_at',
                'blocked_by',
                'contract_file',
                'documents',
                'internal_notes',
                'last_order_date',
                'total_orders',
                'total_quantity',
                'total_amount',
                'created_by',
                'updated_by',
                'deleted_at'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('contractors', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
