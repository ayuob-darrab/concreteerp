<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول الخط الزمني للطلبات
     */
    public function up(): void
    {
        Schema::create('order_timeline', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained('work_orders')->onDelete('cascade');

            // نوع الحدث
            $table->enum('event_type', [
                'created',              // إنشاء الطلب
                'updated',              // تحديث
                'status_changed',       // تغيير الحالة
                'branch_reviewed',      // مراجعة الفرع
                'offer_sent',           // إرسال عرض
                'offer_accepted',       // قبول العرض
                'offer_rejected',       // رفض العرض
                'counter_offer',        // عرض مضاد
                'final_approval',       // الموافقة النهائية
                'assigned',             // تعيين سائق/سيارة
                'dispatched',           // إرسال للتنفيذ
                'completed',            // اكتمال
                'cancelled',            // إلغاء
                'note_added',           // إضافة ملاحظة
                'attachment_added'      // إضافة مرفق
            ]);

            // تفاصيل الحدث
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // الموقع (للأحداث الميدانية)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // من قام بالإجراء
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name', 100)->nullable();
            $table->enum('created_by_type', ['system', 'employee', 'contractor', 'agent', 'customer'])->default('system');

            $table->timestamps();

            // الفهارس
            $table->index('order_id');
            $table->index('event_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_timeline');
    }
};
