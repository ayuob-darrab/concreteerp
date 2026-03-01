<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * توليد تقرير الشركات (سوبر أدمن)
     */
    public static function companiesReport(array $filters = []): array
    {
        $query = DB::table('companies')
            ->select([
                'companies.*',
                DB::raw('(SELECT COUNT(*) FROM users WHERE users.company_code = companies.company_code) as users_count'),
                DB::raw('(SELECT COUNT(*) FROM branches WHERE branches.company_code = companies.company_code) as branches_count'),
            ]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('company_code', 'like', "%{$filters['search']}%");
            });
        }

        $companies = $query->orderBy('created_at', 'desc')->get();

        $summary = [
            'total' => $companies->count(),
            'active' => $companies->where('status', 'active')->count(),
            'inactive' => $companies->where('status', '!=', 'active')->count(),
        ];

        return [
            'data' => $companies,
            'summary' => $summary,
            'columns' => [
                'company_code' => 'كود الشركة',
                'name' => 'اسم الشركة',
                'branches_count' => 'عدد الفروع',
                'users_count' => 'عدد المستخدمين',
                'status' => 'الحالة',
                'created_at' => 'تاريخ التسجيل',
            ],
        ];
    }

    /**
     * تقرير الطلبات
     */
    public static function ordersReport(string $companyCode, ?int $branchId = null, ?string $fromDate = null, ?string $toDate = null, array $filters = []): array
    {
        $query = DB::table('orders')
            ->where('company_code', $companyCode);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $summary = [
            'total_orders' => $orders->count(),
            'total_quantity' => $orders->sum('quantity'),
            'total_value' => $orders->sum('total_price'),
            'by_status' => $orders->groupBy('status')->map->count(),
        ];

        return [
            'data' => $orders,
            'summary' => $summary,
            'columns' => [
                'order_number' => 'رقم الطلب',
                'created_at' => 'التاريخ',
                'customer_name' => 'العميل',
                'quantity' => 'الكمية',
                'total_price' => 'الإجمالي',
                'status' => 'الحالة',
            ],
        ];
    }

    /**
     * تقرير المخزون
     */
    public static function inventoryReport(string $companyCode, ?int $branchId = null, array $filters = []): array
    {
        $query = DB::table('materials')
            ->where('company_code', $companyCode);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if (!empty($filters['low_stock'])) {
            $query->whereColumn('current_quantity', '<=', 'min_quantity');
        }

        $materials = $query->orderBy('name')->get();

        $summary = [
            'total_items' => $materials->count(),
            'low_stock' => $materials->where('current_quantity', '<=', DB::raw('min_quantity'))->count(),
            'total_value' => $materials->sum(function ($m) {
                return $m->current_quantity * $m->unit_price;
            }),
        ];

        return [
            'data' => $materials,
            'summary' => $summary,
            'columns' => [
                'name' => 'اسم المادة',
                'current_quantity' => 'الكمية الحالية',
                'min_quantity' => 'الحد الأدنى',
                'unit' => 'الوحدة',
                'unit_price' => 'سعر الوحدة',
                'total_value' => 'القيمة الإجمالية',
            ],
        ];
    }

    /**
     * تقرير الموظفين
     */
    public static function employeesReport(string $companyCode, ?int $branchId = null, array $filters = []): array
    {
        $query = DB::table('employees')
            ->where('company_code', $companyCode);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }

        $employees = $query->orderBy('name')->get();

        $summary = [
            'total' => $employees->count(),
            'active' => $employees->where('status', 'active')->count(),
            'total_salaries' => $employees->sum('basic_salary'),
        ];

        return [
            'data' => $employees,
            'summary' => $summary,
            'columns' => [
                'employee_number' => 'رقم الموظف',
                'name' => 'الاسم',
                'department' => 'القسم',
                'job_title' => 'المسمى الوظيفي',
                'basic_salary' => 'الراتب الأساسي',
                'status' => 'الحالة',
            ],
        ];
    }

    /**
     * تقرير السلف
     */
    public static function advancesReport(string $companyCode, ?int $branchId = null, ?string $fromDate = null, ?string $toDate = null, array $filters = []): array
    {
        $query = DB::table('advances')
            ->leftJoin('employees', 'advances.employee_id', '=', 'employees.id')
            ->where('advances.company_code', $companyCode)
            ->select('advances.*', 'employees.name as employee_name');

        if ($branchId) {
            $query->where('advances.branch_id', $branchId);
        }

        if ($fromDate) {
            $query->whereDate('advances.created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('advances.created_at', '<=', $toDate);
        }

        if (!empty($filters['status'])) {
            $query->where('advances.status', $filters['status']);
        }

        $advances = $query->orderBy('advances.created_at', 'desc')->get();

        $summary = [
            'total_advances' => $advances->count(),
            'total_amount' => $advances->sum('amount'),
            'total_remaining' => $advances->sum('remaining_amount'),
            'active' => $advances->where('status', 'active')->count(),
        ];

        return [
            'data' => $advances,
            'summary' => $summary,
            'columns' => [
                'advance_number' => 'رقم السلفة',
                'created_at' => 'التاريخ',
                'employee_name' => 'الموظف',
                'amount' => 'المبلغ',
                'remaining_amount' => 'المتبقي',
                'status' => 'الحالة',
            ],
        ];
    }

    /**
     * تقرير الآليات
     */
    public static function vehiclesReport(string $companyCode, ?int $branchId = null, array $filters = []): array
    {
        $query = DB::table('cars')
            ->where('company_code', $companyCode);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $vehicles = $query->orderBy('name')->get();

        $summary = [
            'total' => $vehicles->count(),
            'active' => $vehicles->where('status', 'active')->count(),
            'in_maintenance' => $vehicles->where('status', 'maintenance')->count(),
        ];

        return [
            'data' => $vehicles,
            'summary' => $summary,
            'columns' => [
                'plate_number' => 'رقم اللوحة',
                'name' => 'اسم الآلية',
                'type' => 'النوع',
                'model' => 'الموديل',
                'status' => 'الحالة',
            ],
        ];
    }

    /**
     * تقرير الخسائر
     */
    public static function lossesReport(string $companyCode, ?int $branchId = null, ?string $fromDate = null, ?string $toDate = null, array $filters = []): array
    {
        $query = DB::table('losses')
            ->where('company_code', $companyCode);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        if (!empty($filters['loss_type'])) {
            $query->where('loss_type', $filters['loss_type']);
        }

        $losses = $query->orderBy('created_at', 'desc')->get();

        $summary = [
            'total_losses' => $losses->count(),
            'total_amount' => $losses->sum('loss_amount'),
            'by_type' => $losses->groupBy('loss_type')->map->sum('loss_amount'),
        ];

        return [
            'data' => $losses,
            'summary' => $summary,
            'columns' => [
                'loss_number' => 'رقم الخسارة',
                'loss_date' => 'التاريخ',
                'loss_type' => 'النوع',
                'description' => 'الوصف',
                'loss_amount' => 'المبلغ',
                'status' => 'الحالة',
            ],
        ];
    }

    /**
     * تقرير الصندوق اليومي
     */
    public static function dailyCashReport(string $companyCode, int $branchId, ?string $fromDate = null, ?string $toDate = null): array
    {
        $query = DB::table('daily_cash_summaries')
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId);

        if ($fromDate) {
            $query->whereDate('summary_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('summary_date', '<=', $toDate);
        }

        $summaries = $query->orderBy('summary_date', 'desc')->get();

        $summary = [
            'total_days' => $summaries->count(),
            'total_receipts' => $summaries->sum('total_receipts'),
            'total_payments' => $summaries->sum('total_payments'),
            'net' => $summaries->sum('total_receipts') - $summaries->sum('total_payments'),
        ];

        return [
            'data' => $summaries,
            'summary' => $summary,
            'columns' => [
                'summary_date' => 'التاريخ',
                'opening_balance' => 'الرصيد الافتتاحي',
                'total_receipts' => 'المقبوضات',
                'total_payments' => 'المدفوعات',
                'closing_balance' => 'الرصيد الختامي',
                'status' => 'الحالة',
            ],
        ];
    }
}
