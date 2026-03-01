<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول تسجيل الحضور والانصراف للموظفين
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('user_id'); // المستخدم الذي سجل الحضور
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->date('attendance_date');

            // بيانات الحضور
            $table->time('check_in_time')->nullable(); // وقت تسجيل الدخول الفعلي
            $table->time('shift_start_time')->nullable(); // وقت بداية الشفت المفترض
            $table->integer('late_minutes')->default(0); // مدة التأخير بالدقائق
            $table->integer('early_minutes')->default(0); // مدة الحضور المبكر بالدقائق

            // بيانات الانصراف
            $table->time('check_out_time')->nullable(); // وقت تسجيل الخروج الفعلي
            $table->time('shift_end_time')->nullable(); // وقت نهاية الشفت المفترض
            $table->integer('early_leave_minutes')->default(0); // مدة الخروج المبكر بالدقائق
            $table->integer('overtime_minutes')->default(0); // مدة العمل الإضافي بالدقائق

            // إجمالي وقت العمل
            $table->integer('total_work_minutes')->default(0); // إجمالي وقت العمل بالدقائق

            // الحالة
            $table->enum('status', [
                'present',      // حاضر
                'late',         // متأخر
                'absent',       // غائب
                'on_leave',     // في إجازة
                'on_mission',   // مأمورية
                'sick_leave',   // إجازة مرضية
                'half_day'      // نصف يوم
            ])->default('present');

            // بيانات الموقع (اختياري)
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->decimal('check_out_latitude', 10, 8)->nullable();
            $table->decimal('check_out_longitude', 11, 8)->nullable();

            // ملاحظات
            $table->text('notes')->nullable();
            $table->text('manager_notes')->nullable();

            // التعديل من قبل المدير
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamp('modified_at')->nullable();
            $table->string('modification_reason')->nullable();

            // IP Address للأمان
            $table->string('check_in_ip')->nullable();
            $table->string('check_out_ip')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index(['company_code', 'attendance_date']);
            $table->index(['employee_id', 'attendance_date']);
            $table->index(['branch_id', 'attendance_date']);
            $table->index('status');

            // منع تسجيل الحضور مرتين لنفس اليوم
            $table->unique(['employee_id', 'attendance_date'], 'unique_employee_daily_attendance');

            // Foreign Keys
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('shift_times')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
