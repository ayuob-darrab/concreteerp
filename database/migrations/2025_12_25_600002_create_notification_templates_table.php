<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إنشاء جدول قوالب الإشعارات
     */
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();

            // نوع القالب
            $table->string('type', 50)->unique();

            // المحتوى بالعربية
            $table->string('title_ar', 255);
            $table->text('body_ar');

            // المحتوى بالإنجليزية
            $table->string('title_en', 255)->nullable();
            $table->text('body_en')->nullable();

            // المتغيرات المتاحة
            $table->json('variables'); // ["order_number", "customer_name", "amount"]

            // الإعدادات الافتراضية
            $table->json('default_channels')->default('["app"]'); // ["app", "sms", "whatsapp"]
            $table->enum('default_priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            // الأيقونة والرابط
            $table->string('default_icon', 50)->nullable();
            $table->string('action_route', 100)->nullable(); // مسار Laravel لتوليد الرابط

            // الحالة
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        // إدخال البيانات الأولية
        $this->seedTemplates();
    }

    /**
     * إدخال قوالب الإشعارات الافتراضية
     */
    private function seedTemplates(): void
    {
        $templates = [
            // إشعارات الطلبات
            [
                'type' => 'new_order',
                'title_ar' => 'طلب جديد',
                'body_ar' => 'تم استلام طلب جديد رقم {order_number} من {customer_name}',
                'variables' => json_encode(['order_number', 'customer_name']),
                'default_channels' => json_encode(['app']),
                'default_priority' => 'high',
                'default_icon' => 'bell',
                'action_route' => 'orders.show',
            ],
            [
                'type' => 'order_offer_sent',
                'title_ar' => 'عرض سعر جديد',
                'body_ar' => 'تم إرسال عرض سعر لطلبك رقم {order_number} بمبلغ {price}',
                'variables' => json_encode(['order_number', 'price']),
                'default_channels' => json_encode(['app', 'whatsapp']),
                'default_priority' => 'normal',
                'default_icon' => 'tag',
                'action_route' => 'orders.show',
            ],
            [
                'type' => 'order_accepted',
                'title_ar' => 'تم قبول العرض',
                'body_ar' => 'تم قبول العرض على الطلب رقم {order_number}',
                'variables' => json_encode(['order_number']),
                'default_channels' => json_encode(['app']),
                'default_priority' => 'high',
                'default_icon' => 'check-circle',
                'action_route' => 'orders.show',
            ],
            [
                'type' => 'order_rejected',
                'title_ar' => 'تم رفض العرض',
                'body_ar' => 'تم رفض العرض على الطلب رقم {order_number}. السبب: {reason}',
                'variables' => json_encode(['order_number', 'reason']),
                'default_channels' => json_encode(['app']),
                'default_priority' => 'normal',
                'default_icon' => 'times-circle',
                'action_route' => 'orders.show',
            ],
            [
                'type' => 'order_final_approved',
                'title_ar' => 'موافقة نهائية',
                'body_ar' => 'تمت الموافقة النهائية على طلبك رقم {order_number}. موعد التنفيذ: {execution_date}',
                'variables' => json_encode(['order_number', 'execution_date']),
                'default_channels' => json_encode(['app', 'whatsapp']),
                'default_priority' => 'high',
                'default_icon' => 'check-double',
                'action_route' => 'orders.show',
            ],
            // إشعارات أوامر العمل
            [
                'type' => 'work_started',
                'title_ar' => 'بدء العمل',
                'body_ar' => 'تم بدء العمل على طلبك رقم {order_number}',
                'variables' => json_encode(['order_number']),
                'default_channels' => json_encode(['app', 'whatsapp']),
                'default_priority' => 'high',
                'default_icon' => 'play',
                'action_route' => 'work-jobs.show',
            ],
            [
                'type' => 'work_completed',
                'title_ar' => 'اكتمال العمل',
                'body_ar' => 'تم إكمال العمل على طلبك رقم {order_number}. الكمية المنجزة: {total_quantity}',
                'variables' => json_encode(['order_number', 'total_quantity']),
                'default_channels' => json_encode(['app', 'whatsapp']),
                'default_priority' => 'high',
                'default_icon' => 'check',
                'action_route' => 'work-jobs.show',
            ],
            [
                'type' => 'shipment_departed',
                'title_ar' => 'انطلاق الشحنة',
                'body_ar' => 'انطلقت الشحنة رقم {shipment_number} لطلبك رقم {order_number}',
                'variables' => json_encode(['order_number', 'shipment_number']),
                'default_channels' => json_encode(['app']),
                'default_priority' => 'normal',
                'default_icon' => 'truck',
                'action_route' => 'shipments.show',
            ],
            // إشعارات مالية
            [
                'type' => 'payment_received',
                'title_ar' => 'استلام دفعة',
                'body_ar' => 'تم استلام مبلغ {amount} من {payer_name}',
                'variables' => json_encode(['amount', 'payer_name']),
                'default_channels' => json_encode(['app']),
                'default_priority' => 'normal',
                'default_icon' => 'money-bill',
                'action_route' => 'receipts.show',
            ],
            [
                'type' => 'payment_due',
                'title_ar' => 'مستحقات',
                'body_ar' => 'يوجد مبلغ مستحق بقيمة {amount} على طلب رقم {order_number}',
                'variables' => json_encode(['amount', 'order_number']),
                'default_channels' => json_encode(['app']),
                'default_priority' => 'normal',
                'default_icon' => 'exclamation-triangle',
                'action_route' => null,
            ],
            // إشعارات السلف
            [
                'type' => 'advance_approved',
                'title_ar' => 'موافقة سلفة',
                'body_ar' => 'تمت الموافقة على سلفتك بمبلغ {amount}',
                'variables' => json_encode(['amount']),
                'default_channels' => json_encode(['app']),
                'default_priority' => 'normal',
                'default_icon' => 'hand-holding-usd',
                'action_route' => 'advances.show',
            ],
            [
                'type' => 'advance_deducted',
                'title_ar' => 'استقطاع سلفة',
                'body_ar' => 'تم استقطاع {amount} من سلفتك. المتبقي: {remaining}',
                'variables' => json_encode(['amount', 'remaining']),
                'default_channels' => json_encode(['app']),
                'default_priority' => 'low',
                'default_icon' => 'minus-circle',
                'action_route' => 'advances.show',
            ],
            // إشعارات الاشتراك
            [
                'type' => 'subscription_expiring',
                'title_ar' => 'انتهاء الاشتراك',
                'body_ar' => 'ينتهي اشتراكك خلال {days} أيام',
                'variables' => json_encode(['days']),
                'default_channels' => json_encode(['app']),
                'default_priority' => 'high',
                'default_icon' => 'clock',
                'action_route' => null,
            ],
            [
                'type' => 'subscription_expired',
                'title_ar' => 'انتهى الاشتراك',
                'body_ar' => 'انتهى اشتراكك، يرجى التجديد للاستمرار في استخدام النظام',
                'variables' => json_encode([]),
                'default_channels' => json_encode(['app']),
                'default_priority' => 'urgent',
                'default_icon' => 'exclamation-circle',
                'action_route' => null,
            ],
            // إشعارات الصيانة
            [
                'type' => 'maintenance_due',
                'title_ar' => 'موعد صيانة',
                'body_ar' => 'موعد صيانة الآلية {vehicle_name} خلال {days} أيام',
                'variables' => json_encode(['vehicle_name', 'days']),
                'default_channels' => json_encode(['app']),
                'default_priority' => 'normal',
                'default_icon' => 'tools',
                'action_route' => 'cars.show',
            ],
            [
                'type' => 'maintenance_overdue',
                'title_ar' => 'صيانة متأخرة',
                'body_ar' => 'تأخرت صيانة الآلية {vehicle_name}',
                'variables' => json_encode(['vehicle_name']),
                'default_channels' => json_encode(['app']),
                'default_priority' => 'high',
                'default_icon' => 'exclamation-triangle',
                'action_route' => 'cars.show',
            ],
        ];

        foreach ($templates as $template) {
            $template['created_at'] = now();
            $template['updated_at'] = now();
            DB::table('notification_templates')->insert($template);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
