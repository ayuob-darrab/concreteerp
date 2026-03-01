<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحديث جدول أوامر العمل لدعم نظام الطلبات والتفاوض
     */
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            // رقم الطلب الفريد
            if (!Schema::hasColumn('work_orders', 'order_number')) {
                $table->string('order_number', 20)->unique()->nullable()->after('id');
            }

            // مصدر الطلب
            if (!Schema::hasColumn('work_orders', 'order_source')) {
                $table->enum('order_source', ['contractor', 'agent', 'direct'])->default('direct')->after('sender_type');
            }

            // حقول العرض الأولي من الفرع
            if (!Schema::hasColumn('work_orders', 'branch_approved')) {
                $table->enum('branch_approved', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            }
            if (!Schema::hasColumn('work_orders', 'branch_price')) {
                $table->decimal('branch_price', 15, 2)->nullable()->after('approved_price');
            }
            if (!Schema::hasColumn('work_orders', 'branch_discount')) {
                $table->decimal('branch_discount', 10, 2)->nullable()->after('branch_price');
            }
            if (!Schema::hasColumn('work_orders', 'branch_suggested_date')) {
                $table->date('branch_suggested_date')->nullable()->after('branch_discount');
            }
            if (!Schema::hasColumn('work_orders', 'branch_suggested_time')) {
                $table->time('branch_suggested_time')->nullable()->after('branch_suggested_date');
            }
            if (!Schema::hasColumn('work_orders', 'branch_notes')) {
                $table->text('branch_notes')->nullable()->after('branch_suggested_time');
            }
            if (!Schema::hasColumn('work_orders', 'branch_reviewed_by')) {
                $table->foreignId('branch_reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('work_orders', 'branch_reviewed_at')) {
                $table->datetime('branch_reviewed_at')->nullable();
            }

            // حقول رد صاحب الطلب
            if (!Schema::hasColumn('work_orders', 'requester_response')) {
                $table->enum('requester_response', ['pending', 'accepted', 'rejected', 'edit_requested'])->default('pending');
            }
            if (!Schema::hasColumn('work_orders', 'requester_notes')) {
                $table->text('requester_notes')->nullable();
            }
            if (!Schema::hasColumn('work_orders', 'requester_responded_by')) {
                $table->foreignId('requester_responded_by')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('work_orders', 'requester_responded_at')) {
                $table->datetime('requester_responded_at')->nullable();
            }

            // حقول الموافقة النهائية
            if (!Schema::hasColumn('work_orders', 'final_approved')) {
                $table->boolean('final_approved')->default(false);
            }
            if (!Schema::hasColumn('work_orders', 'final_approved_by')) {
                $table->foreignId('final_approved_by')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('work_orders', 'final_approved_at')) {
                $table->datetime('final_approved_at')->nullable();
            }
            if (!Schema::hasColumn('work_orders', 'final_notes')) {
                $table->text('final_notes')->nullable();
            }

            // حقول إضافية
            if (!Schema::hasColumn('work_orders', 'concrete_type_notes')) {
                $table->text('concrete_type_notes')->nullable();
            }
            if (!Schema::hasColumn('work_orders', 'map_location')) {
                $table->string('map_location', 255)->nullable();
            }
            if (!Schema::hasColumn('work_orders', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('work_orders', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable();
            }

            // فهارس
            $table->index('order_source');
            $table->index('branch_approved');
            $table->index('requester_response');
            $table->index('final_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $columns = [
                'order_number',
                'order_source',
                'branch_approved',
                'branch_price',
                'branch_discount',
                'branch_suggested_date',
                'branch_suggested_time',
                'branch_notes',
                'branch_reviewed_by',
                'branch_reviewed_at',
                'requester_response',
                'requester_notes',
                'requester_responded_by',
                'requester_responded_at',
                'final_approved',
                'final_approved_by',
                'final_approved_at',
                'final_notes',
                'concrete_type_notes',
                'map_location',
                'latitude',
                'longitude'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('work_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
