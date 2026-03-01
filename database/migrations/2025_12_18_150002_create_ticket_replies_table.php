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
        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id'); // معرف التذكرة
            $table->enum('user_type', ['customer', 'support']); // نوع المرسل
            $table->unsignedBigInteger('user_id')->nullable(); // معرف المستخدم
            $table->string('user_name', 100)->nullable(); // اسم المرسل

            $table->text('message'); // نص الرد
            $table->json('attachments')->nullable(); // مرفقات الرد
            $table->boolean('is_internal')->default(false); // ملاحظة داخلية

            $table->timestamps();

            // الفهارس
            $table->index('ticket_id');
            $table->index('created_at');

            // العلاقات
            $table->foreign('ticket_id')->references('id')->on('support_tickets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_replies');
    }
};
