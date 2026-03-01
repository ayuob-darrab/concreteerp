<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\DB;

class ChartService
{
    /**
     * بيانات رسم بياني للطلبات
     */
    public static function ordersChart(string $companyCode, ?int $branchId = null, ?string $fromDate = null, ?string $toDate = null): array
    {
        $query = DB::table('orders')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total_price) as total'))
            ->where('company_code', $companyCode)
            ->groupBy('date')
            ->orderBy('date');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $data = $query->get();

        return [
            'labels' => $data->pluck('date')->toArray(),
            'datasets' => [
                [
                    'label' => 'عدد الطلبات',
                    'data' => $data->pluck('count')->toArray(),
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'tension' => 0.1,
                ],
                [
                    'label' => 'إجمالي المبيعات',
                    'data' => $data->pluck('total')->toArray(),
                    'borderColor' => 'rgb(255, 99, 132)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'tension' => 0.1,
                    'yAxisID' => 'y1',
                ],
            ],
        ];
    }

    /**
     * بيانات رسم بياني للحالات
     */
    public static function statusPieChart(string $companyCode, ?int $branchId = null): array
    {
        $query = DB::table('orders')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->where('company_code', $companyCode)
            ->groupBy('status');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $data = $query->get();

        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'approved' => 'معتمد',
            'in_progress' => 'جاري التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];

        $colors = [
            'pending' => 'rgb(255, 205, 86)',
            'approved' => 'rgb(54, 162, 235)',
            'in_progress' => 'rgb(75, 192, 192)',
            'completed' => 'rgb(46, 204, 113)',
            'cancelled' => 'rgb(255, 99, 132)',
        ];

        return [
            'labels' => $data->map(fn($d) => $statusLabels[$d->status] ?? $d->status)->toArray(),
            'datasets' => [
                [
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => $data->map(fn($d) => $colors[$d->status] ?? 'rgb(201, 203, 207)')->toArray(),
                ],
            ],
        ];
    }

    /**
     * بيانات رسم بياني للإيرادات الشهرية
     */
    public static function monthlyRevenueChart(string $companyCode, ?int $branchId = null, ?int $year = null): array
    {
        $year = $year ?? date('Y');

        $query = DB::table('orders')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_price) as total'))
            ->where('company_code', $companyCode)
            ->whereYear('created_at', $year)
            ->whereIn('status', ['completed', 'approved', 'in_progress'])
            ->groupBy('month')
            ->orderBy('month');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $data = $query->get()->keyBy('month');

        $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        $values = [];

        for ($i = 1; $i <= 12; $i++) {
            $values[] = $data->get($i)?->total ?? 0;
        }

        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => "الإيرادات {$year}",
                    'data' => $values,
                    'backgroundColor' => 'rgba(46, 204, 113, 0.5)',
                    'borderColor' => 'rgb(46, 204, 113)',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    /**
     * بيانات رسم بياني للمخزون
     */
    public static function inventoryChart(string $companyCode, ?int $branchId = null): array
    {
        $query = DB::table('materials')
            ->select('name', 'current_quantity', 'min_quantity')
            ->where('company_code', $companyCode)
            ->orderBy('current_quantity', 'desc')
            ->limit(10);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $data = $query->get();

        return [
            'labels' => $data->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label' => 'الكمية الحالية',
                    'data' => $data->pluck('current_quantity')->toArray(),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'الحد الأدنى',
                    'data' => $data->pluck('min_quantity')->toArray(),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    /**
     * بيانات رسم بياني للموظفين حسب القسم
     */
    public static function employeesByDepartmentChart(string $companyCode, ?int $branchId = null): array
    {
        $query = DB::table('employees')
            ->select('department', DB::raw('COUNT(*) as count'))
            ->where('company_code', $companyCode)
            ->where('status', 'active')
            ->groupBy('department');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $data = $query->get();

        $colors = [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)',
        ];

        return [
            'labels' => $data->pluck('department')->toArray(),
            'datasets' => [
                [
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $data->count()),
                ],
            ],
        ];
    }

    /**
     * بيانات رسم بياني للسلف
     */
    public static function advancesChart(string $companyCode, ?int $branchId = null, ?string $fromDate = null, ?string $toDate = null): array
    {
        $query = DB::table('advances')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->where('company_code', $companyCode)
            ->groupBy('date')
            ->orderBy('date');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $data = $query->get();

        return [
            'labels' => $data->pluck('date')->toArray(),
            'datasets' => [
                [
                    'label' => 'إجمالي السلف',
                    'data' => $data->pluck('total')->toArray(),
                    'borderColor' => 'rgb(153, 102, 255)',
                    'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                    'fill' => true,
                    'tension' => 0.1,
                ],
            ],
        ];
    }

    /**
     * بيانات رسم بياني للصندوق
     */
    public static function cashFlowChart(string $companyCode, int $branchId, ?string $fromDate = null, ?string $toDate = null): array
    {
        $query = DB::table('daily_cash_summaries')
            ->select('summary_date', 'total_receipts', 'total_payments', 'closing_balance')
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->orderBy('summary_date');

        if ($fromDate) {
            $query->whereDate('summary_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('summary_date', '<=', $toDate);
        }

        $data = $query->get();

        return [
            'labels' => $data->pluck('summary_date')->toArray(),
            'datasets' => [
                [
                    'label' => 'المقبوضات',
                    'data' => $data->pluck('total_receipts')->toArray(),
                    'borderColor' => 'rgb(46, 204, 113)',
                    'backgroundColor' => 'rgba(46, 204, 113, 0.2)',
                    'tension' => 0.1,
                ],
                [
                    'label' => 'المدفوعات',
                    'data' => $data->pluck('total_payments')->toArray(),
                    'borderColor' => 'rgb(255, 99, 132)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'tension' => 0.1,
                ],
                [
                    'label' => 'الرصيد الختامي',
                    'data' => $data->pluck('closing_balance')->toArray(),
                    'borderColor' => 'rgb(54, 162, 235)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'tension' => 0.1,
                ],
            ],
        ];
    }

    /**
     * بيانات مقارنة الفروع
     */
    public static function branchesComparisonChart(string $companyCode): array
    {
        $branches = DB::table('branches')
            ->where('company_code', $companyCode)
            ->where('status', 'active')
            ->get();

        $branchesData = [];

        foreach ($branches as $branch) {
            $ordersSum = DB::table('orders')
                ->where('company_code', $companyCode)
                ->where('branch_id', $branch->id)
                ->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))
                ->sum('total_price');

            $branchesData[] = [
                'name' => $branch->name,
                'value' => $ordersSum ?? 0,
            ];
        }

        usort($branchesData, fn($a, $b) => $b['value'] <=> $a['value']);

        return [
            'labels' => array_column($branchesData, 'name'),
            'datasets' => [
                [
                    'label' => 'مبيعات الشهر الحالي',
                    'data' => array_column($branchesData, 'value'),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.7)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }
}
