<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * أحداث الشحنة - Shipment Events
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')->constrained('work_shipments')->onDelete('cascade');

            // نوع الحدث
            $table->enum('event_type', [
                'created',          // إنشاء
                'prepared',         // تم التحضير
                'departed',         // انطلاق
                'arrived',          // وصول
                'work_started',     // بدء العمل
                'work_ended',       // انتهاء العمل
                'returned',         // عودة
                'cancelled',        // إلغاء
                'issue_reported',   // تقرير مشكلة
                'location_updated'  // تحديث الموقع
            ]);

            // التفاصيل
            $table->text('description')->nullable();

            // الموقع
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // بيانات إضافية
            $table->json('metadata')->nullable();

            // من قام
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('recorded_at')->useCurrent();

            // الفهارس
            $table->index('shipment_id', 'idx_events_shipment');
            $table->index('event_type', 'idx_events_type');
            $table->index('recorded_at', 'idx_events_recorded');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipment_events');
    }
};
