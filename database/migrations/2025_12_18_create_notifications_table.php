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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 50)->index(); // كود الشركة المستلمة
            $table->string('title'); // عنوان الإشعار
            $table->text('message'); // نص الإشعار
            $table->enum('type', ['info', 'warning', 'success', 'danger'])->default('info'); // نوع الإشعار
            $table->boolean('is_read')->default(false)->index(); // حالة القراءة
            $table->string('sent_by', 50)->default('SA'); // المرسل (السوبر أدمن)
            $table->timestamp('read_at')->nullable(); // وقت القراءة
            $table->timestamps();

            // Indexes for better performance
            $table->index(['company_code', 'is_read']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
