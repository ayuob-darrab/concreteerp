<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحديث جدول cars لإضافة حقول التشغيل والصيانة
     */
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // الحالة التشغيلية
            $table->enum('operational_status', [
                'available',      // متاحة
                'reserved',       // محجوزة لطلب
                'in_maintenance', // في الصيانة
                'out_of_service', // خارج الخدمة
                'scrapped'        // مشطوبة
            ])->default('available')->after('isactive');

            // الصيانة
            $table->date('last_maintenance_date')->nullable()->after('operational_status');
            $table->date('next_maintenance_date')->nullable()->after('last_maintenance_date');
            $table->integer('maintenance_interval_days')->default(30)->after('next_maintenance_date');

            // استهلاك الوقود
            $table->decimal('fuel_consumption', 5, 2)->nullable()->comment('لتر/ساعة أو لتر/كم')->after('maintenance_interval_days');
            $table->enum('fuel_consumption_unit', ['per_hour', 'per_km'])->default('per_hour')->after('fuel_consumption');

            // العدادات
            $table->integer('odometer_reading')->default(0)->comment('قراءة العداد')->after('fuel_consumption_unit');
            $table->integer('working_hours')->default(0)->comment('ساعات العمل')->after('odometer_reading');

            // سبب الحالة
            $table->text('status_reason')->nullable()->comment('سبب الحالة')->after('working_hours');
            $table->dateTime('status_changed_at')->nullable()->after('status_reason');
            $table->unsignedBigInteger('status_changed_by')->nullable()->after('status_changed_at');

            // Soft delete
            $table->softDeletes();

            // فهارس
            $table->index('operational_status', 'idx_operational_status');
            $table->index('next_maintenance_date', 'idx_next_maintenance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropIndex('idx_operational_status');
            $table->dropIndex('idx_next_maintenance');

            $table->dropColumn([
                'operational_status',
                'last_maintenance_date',
                'next_maintenance_date',
                'maintenance_interval_days',
                'fuel_consumption',
                'fuel_consumption_unit',
                'odometer_reading',
                'working_hours',
                'status_reason',
                'status_changed_at',
                'status_changed_by',
                'deleted_at'
            ]);
        });
    }
};
