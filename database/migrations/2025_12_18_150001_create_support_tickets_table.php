<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 20)->unique(); // رقم التذكرة TKT-2025-0001
            $table->string('company_code', 50)->index(); // كود الشركة المرسلة
            $table->unsignedBigInteger('user_id')->nullable(); // المستخدم الذي فتح التذكرة

            // معلومات التذكرة
            $table->string('subject'); // عنوان/موضوع التذكرة
            $table->text('description'); // وصف المشكلة
            $table->enum('category', ['technical', 'billing', 'feature_request', 'bug', 'general'])->default('general');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'pending_response', 'resolved', 'closed'])->default('open');

            // معلومات الرد
            $table->string('assigned_to', 50)->nullable(); // الموظف المسؤول
            $table->timestamp('first_response_at')->nullable(); // أول رد من الدعم
            $table->timestamp('resolved_at')->nullable(); // تاريخ الحل
            $table->timestamp('closed_at')->nullable(); // تاريخ الإغلاق

            // تقييم الخدمة
            $table->tinyInteger('rating')->nullable(); // تقييم من 1-5
            $table->text('feedback')->nullable(); // ملاحظات العميل

            // المرفقات والبيانات الإضافية
            $table->json('attachments')->nullable(); // أسماء الملفات المرفقة

            $table->timestamps();

            // الفهارس
            $table->index('status');
            $table->index('priority');
            $table->index('category');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
