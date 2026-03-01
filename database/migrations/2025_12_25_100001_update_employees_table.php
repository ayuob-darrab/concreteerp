<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحديث جدول الموظفين - إضافة حقول جديدة
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // المعلومات الشخصية
            if (!Schema::hasColumn('employees', 'national_id')) {
                $table->string('national_id', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('employees', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('national_id');
            }

            // معلومات العقد
            if (!Schema::hasColumn('employees', 'contract_type')) {
                $table->enum('contract_type', ['permanent', 'contract', 'daily', 'hourly'])
                    ->default('permanent')->after('hire_date');
            }
            if (!Schema::hasColumn('employees', 'contract_start_date')) {
                $table->date('contract_start_date')->nullable()->after('contract_type');
            }
            if (!Schema::hasColumn('employees', 'contract_end_date')) {
                $table->date('contract_end_date')->nullable()->after('contract_start_date');
            }

            // المعلومات البنكية
            if (!Schema::hasColumn('employees', 'bank_name')) {
                $table->string('bank_name', 100)->nullable()->after('contract_end_date');
            }
            if (!Schema::hasColumn('employees', 'bank_account')) {
                $table->string('bank_account', 50)->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('employees', 'iban')) {
                $table->string('iban', 50)->nullable()->after('bank_account');
            }

            // معلومات الطوارئ
            if (!Schema::hasColumn('employees', 'emergency_contact_name')) {
                $table->string('emergency_contact_name', 100)->nullable()->after('iban');
            }
            if (!Schema::hasColumn('employees', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
            }
            if (!Schema::hasColumn('employees', 'address')) {
                $table->text('address')->nullable()->after('emergency_contact_phone');
            }

            // أرصدة الإجازات
            if (!Schema::hasColumn('employees', 'annual_leave_balance')) {
                $table->integer('annual_leave_balance')->default(0)->after('address');
            }
            if (!Schema::hasColumn('employees', 'sick_leave_balance')) {
                $table->integer('sick_leave_balance')->default(0)->after('annual_leave_balance');
            }

            // Soft Delete
            if (!Schema::hasColumn('employees', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $columns = [
                'national_id',
                'hire_date',
                'contract_type',
                'contract_start_date',
                'contract_end_date',
                'bank_name',
                'bank_account',
                'iban',
                'emergency_contact_name',
                'emergency_contact_phone',
                'address',
                'annual_leave_balance',
                'sick_leave_balance',
                'deleted_at'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
