<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * فهارس أداء حسب خطة-تطوير (02-database-indexes).
     * يُتحقق من وجود الجدول/العمود قبل الإضافة لتفادي الأخطاء.
     */
    public function up(): void
    {
        $this->indexIfMissing('work_orders', ['company_code', 'status'], 'idx_wo_company_status');
        $this->indexIfMissing('work_orders', ['company_code', 'branch_id'], 'idx_wo_company_branch');
        $this->indexIfMissing('work_orders', ['branch_id', 'status'], 'idx_wo_branch_status');
        $this->indexIfMissing('work_orders', ['status', 'created_at'], 'idx_wo_status_date');
        // payment_status و delivery_datetime لها فهارس مفردة مسبقاً في migrations أخرى

        if ($this->tableHasColumns('employees', ['company_code', 'branch_id'])) {
            $this->indexIfMissing('employees', ['company_code', 'branch_id'], 'idx_emp_company_branch');
        }
        if ($this->tableHasColumns('employees', ['company_code', 'isactive'])) {
            $this->indexIfMissing('employees', ['company_code', 'isactive'], 'idx_emp_company_active');
        }
        $this->indexIfMissing('employees', ['employee_types_id'], 'idx_emp_type');
        $this->indexIfMissing('employees', ['shift_id'], 'idx_emp_shift');

        if ($this->tableHasColumn('branches', 'company_code')) {
            $this->indexIfMissing('branches', ['company_code'], 'idx_branch_company');
        }
        if ($this->tableHasColumns('branches', ['company_code', 'isactive'])) {
            $this->indexIfMissing('branches', ['company_code', 'isactive'], 'idx_branch_company_active');
        }

        if ($this->tableHasColumns('inventories', ['company_code', 'branch_id'])) {
            $this->indexIfMissing('inventories', ['company_code', 'branch_id'], 'idx_inv_company_branch');
        }
        if ($this->tableHasColumn('inventories', 'code')) {
            $this->indexIfMissing('inventories', ['code'], 'idx_inv_code');
        }

        if ($this->tableHasColumns('inventory_histories', ['inventory_id', 'created_at'])) {
            $this->indexIfMissing('inventory_histories', ['inventory_id', 'created_at'], 'idx_invh_inv_date');
        }
        if ($this->tableHasColumn('inventory_histories', 'company_code')) {
            $this->indexIfMissing('inventory_histories', ['company_code'], 'idx_invh_company');
        }

        if ($this->tableHasColumns('suppliers', ['company_code', 'branch_id'])) {
            $this->indexIfMissing('suppliers', ['company_code', 'branch_id'], 'idx_sup_company_branch');
        }

        if ($this->tableHasColumns('supplier_payments', ['supplier_id', 'created_at'])) {
            $this->indexIfMissing('supplier_payments', ['supplier_id', 'created_at'], 'idx_suppay_supplier_date');
        }
        if ($this->tableHasColumn('supplier_payments', 'company_code')) {
            $this->indexIfMissing('supplier_payments', ['company_code'], 'idx_suppay_company');
        }

        if ($this->tableHasColumns('contractors', ['company_code', 'branch_id'])) {
            $this->indexIfMissing('contractors', ['company_code', 'branch_id'], 'idx_cont_company_branch');
        }
        if ($this->tableHasColumns('contractors', ['company_code', 'isactive'])) {
            $this->indexIfMissing('contractors', ['company_code', 'isactive'], 'idx_cont_company_active');
        }

        if ($this->tableHasColumns('invoices', ['company_code', 'status'])) {
            $this->indexIfMissing('invoices', ['company_code', 'status'], 'idx_inv_invoice_company_status');
        }
        if ($this->tableHasColumns('invoices', ['contractor_id', 'created_at'])) {
            $this->indexIfMissing('invoices', ['contractor_id', 'created_at'], 'idx_inv_contractor_date');
        }

        if ($this->tableHasColumns('checks', ['company_code', 'status'])) {
            $this->indexIfMissing('checks', ['company_code', 'status'], 'idx_chk_company_status');
        }
        if ($this->tableHasColumn('checks', 'due_date')) {
            $this->indexIfMissing('checks', ['due_date'], 'idx_chk_due');
        }

        if ($this->tableHasColumns('receipts', ['company_code', 'created_at'])) {
            $this->indexIfMissing('receipts', ['company_code', 'created_at'], 'idx_rec_company_date');
        }

        if ($this->tableHasColumns('financial_transactions', ['company_code', 'transaction_type'])) {
            $this->indexIfMissing('financial_transactions', ['company_code', 'transaction_type'], 'idx_ft_company_txn_type');
        }
        if ($this->tableHasColumns('financial_transactions', ['company_code', 'created_at'])) {
            $this->indexIfMissing('financial_transactions', ['company_code', 'created_at'], 'idx_ft_company_created');
        }
        if ($this->tableHasColumn('financial_transactions', 'status')) {
            $this->indexIfMissing('financial_transactions', ['status'], 'idx_ft_status');
        }

        if ($this->tableHasColumns('cars', ['company_code', 'branch_id'])) {
            $this->indexIfMissing('cars', ['company_code', 'branch_id'], 'idx_car_company_branch');
        }
        if ($this->tableHasColumns('cars', ['company_code', 'status'])) {
            $this->indexIfMissing('cars', ['company_code', 'status'], 'idx_car_company_status');
        }

        if ($this->tableHasColumn('work_jobs', 'work_order_id')) {
            $this->indexIfMissing('work_jobs', ['work_order_id'], 'idx_wj_order');
        }
        if ($this->tableHasColumns('work_jobs', ['car_id', 'status'])) {
            $this->indexIfMissing('work_jobs', ['car_id', 'status'], 'idx_wj_car_status');
        }

        if ($this->tableHasColumn('work_shipments', 'work_job_id')) {
            $this->indexIfMissing('work_shipments', ['work_job_id'], 'idx_ws_job');
        }
        if ($this->tableHasColumn('work_shipments', 'driver_id')) {
            $this->indexIfMissing('work_shipments', ['driver_id'], 'idx_ws_driver');
        }

        if ($this->tableHasColumns('attendances', ['employee_id', 'attendance_date'])) {
            $this->indexIfMissing('attendances', ['employee_id', 'attendance_date'], 'idx_att_emp_date');
        }
        if ($this->tableHasColumns('attendances', ['company_code', 'attendance_date'])) {
            $this->indexIfMissing('attendances', ['company_code', 'attendance_date'], 'idx_att_company_date');
        }

        if ($this->tableHasColumns('advances', ['company_code', 'status'])) {
            $this->indexIfMissing('advances', ['company_code', 'status'], 'idx_adv_company_status');
        }
        if ($this->tableHasColumn('advances', 'employee_id')) {
            $this->indexIfMissing('advances', ['employee_id'], 'idx_adv_employee');
        }

        if (Schema::hasTable('notifications')) {
            if ($this->tableHasColumns('notifications', ['notifiable_id', 'read_at'])) {
                $this->indexIfMissing('notifications', ['notifiable_id', 'read_at'], 'idx_notif_user_read');
            }
            if ($this->tableHasColumn('notifications', 'company_code')) {
                $this->indexIfMissing('notifications', ['company_code'], 'idx_notif_company');
            }
        }

        if ($this->tableHasColumn('users', 'company_code')) {
            $this->indexIfMissing('users', ['company_code'], 'idx_user_company');
        }
        if ($this->tableHasColumns('users', ['company_code', 'usertype_id'])) {
            $this->indexIfMissing('users', ['company_code', 'usertype_id'], 'idx_user_company_type');
        }

        // payroll: يوجد فهرس فريد (employee_id, payroll_month, payroll_year) — نضيف فقط فهرس شركة+حالة إن لزم
        if ($this->tableHasColumns('payroll', ['company_code', 'status'])) {
            $this->indexIfMissing('payroll', ['company_code', 'status'], 'idx_pay_company_status');
        }
    }

    public function down(): void
    {
        $map = [
            'work_orders' => ['idx_wo_company_status', 'idx_wo_company_branch', 'idx_wo_branch_status', 'idx_wo_status_date'],
            'employees' => ['idx_emp_company_branch', 'idx_emp_company_active', 'idx_emp_type', 'idx_emp_shift'],
            'branches' => ['idx_branch_company', 'idx_branch_company_active'],
            'inventories' => ['idx_inv_company_branch', 'idx_inv_code'],
            'inventory_histories' => ['idx_invh_inv_date', 'idx_invh_company'],
            'suppliers' => ['idx_sup_company_branch'],
            'supplier_payments' => ['idx_suppay_supplier_date', 'idx_suppay_company'],
            'contractors' => ['idx_cont_company_branch', 'idx_cont_company_active'],
            'invoices' => ['idx_inv_invoice_company_status', 'idx_inv_contractor_date'],
            'checks' => ['idx_chk_company_status', 'idx_chk_due'],
            'receipts' => ['idx_rec_company_date'],
            'financial_transactions' => ['idx_ft_company_txn_type', 'idx_ft_company_created', 'idx_ft_status'],
            'cars' => ['idx_car_company_branch', 'idx_car_company_status'],
            'work_jobs' => ['idx_wj_order', 'idx_wj_car_status'],
            'work_shipments' => ['idx_ws_job', 'idx_ws_driver'],
            'attendances' => ['idx_att_emp_date', 'idx_att_company_date'],
            'advances' => ['idx_adv_company_status', 'idx_adv_employee'],
            'notifications' => ['idx_notif_user_read', 'idx_notif_company'],
            'users' => ['idx_user_company', 'idx_user_company_type'],
            'payroll' => ['idx_pay_company_status'],
        ];

        foreach ($map as $table => $indexes) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            Schema::table($table, function (Blueprint $blueprint) use ($indexes, $table) {
                foreach ($indexes as $index) {
                    if ($this->indexExists($table, $index)) {
                        $blueprint->dropIndex($index);
                    }
                }
            });
        }
    }

    private function tableHasColumn(string $table, string $column): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, $column);
    }

    private function tableHasColumns(string $table, array $columns): bool
    {
        if (! Schema::hasTable($table)) {
            return false;
        }
        foreach ($columns as $col) {
            if (! Schema::hasColumn($table, $col)) {
                return false;
            }
        }

        return true;
    }

    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $rows = DB::select('SHOW INDEX FROM `'.$table.'` WHERE Key_name = ?', [$indexName]);

            return count($rows) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function indexIfMissing(string $table, array $columns, string $indexName): void
    {
        if (! $this->tableHasColumns($table, $columns)) {
            return;
        }
        if ($this->indexExists($table, $indexName)) {
            return;
        }
        Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName) {
            $blueprint->index($columns, $indexName);
        });
    }
};
