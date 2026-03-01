<?php

namespace App\Http\Controllers;

use App\Services\Reports\ReportService;
use App\Services\Reports\BaseReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * لوحة التقارير الرئيسية
     */
    public function index()
    {
        $user = auth()->user();
        $reportCategories = $this->getReportCategories($user);

        return view('reports.index', compact('reportCategories'));
    }

    /**
     * تصنيفات التقارير حسب صلاحيات المستخدم
     */
    private function getReportCategories($user): array
    {
        $categories = [];

        // تقارير الفرع (متاحة للجميع)
        $categories['branch'] = [
            'title' => 'تقارير الفرع',
            'icon' => 'building',
            'reports' => [
                ['name' => 'orders', 'title' => 'تقرير الطلبات', 'route' => 'reports.orders'],
                ['name' => 'inventory', 'title' => 'تقرير المخزون', 'route' => 'reports.inventory'],
                ['name' => 'employees', 'title' => 'تقرير الموظفين', 'route' => 'reports.employees'],
                ['name' => 'advances', 'title' => 'تقرير السلف', 'route' => 'reports.advances'],
                ['name' => 'vehicles', 'title' => 'تقرير الآليات', 'route' => 'reports.vehicles'],
                ['name' => 'losses', 'title' => 'تقرير الخسائر', 'route' => 'reports.losses'],
                ['name' => 'daily-cash', 'title' => 'تقرير الصندوق', 'route' => 'reports.daily-cash'],
            ],
        ];

        // تقارير الشركة (لمدير الشركة)
        if ($user->role === 'company_admin' || $user->role === 'super_admin') {
            $categories['company'] = [
                'title' => 'تقارير الشركة',
                'icon' => 'briefcase',
                'reports' => [
                    ['name' => 'branches-summary', 'title' => 'ملخص الفروع', 'route' => 'reports.branches-summary'],
                    ['name' => 'company-employees', 'title' => 'موظفو الشركة', 'route' => 'reports.company-employees'],
                    ['name' => 'sales', 'title' => 'تقرير المبيعات', 'route' => 'reports.sales'],
                    ['name' => 'profit-loss', 'title' => 'الأرباح والخسائر', 'route' => 'reports.profit-loss'],
                ],
            ];
        }

        // تقارير السوبر أدمن
        if ($user->role === 'super_admin') {
            $categories['admin'] = [
                'title' => 'تقارير المنصة',
                'icon' => 'cogs',
                'reports' => [
                    ['name' => 'companies', 'title' => 'تقرير الشركات', 'route' => 'reports.companies'],
                    ['name' => 'subscriptions', 'title' => 'تقرير الاشتراكات', 'route' => 'reports.subscriptions'],
                    ['name' => 'all-orders', 'title' => 'جميع الطلبات', 'route' => 'reports.all-orders'],
                    ['name' => 'activity', 'title' => 'النشاط العام', 'route' => 'reports.activity'],
                ],
            ];
        }

        return $categories;
    }

    /**
     * تقرير الطلبات
     */
    public function orders(Request $request)
    {
        $user = auth()->user();
        $report = ReportService::ordersReport(
            $user->company_code,
            $request->branch_id ?? $user->branch_id,
            $request->from_date,
            $request->to_date,
            $request->only(['status'])
        );

        return view('reports.branch.orders', [
            'report' => $report,
            'presets' => BaseReport::getPresets(),
            'filters' => $request->all(),
        ]);
    }

    /**
     * تقرير المخزون
     */
    public function inventory(Request $request)
    {
        $user = auth()->user();
        $report = ReportService::inventoryReport(
            $user->company_code,
            $request->branch_id ?? $user->branch_id,
            $request->only(['low_stock'])
        );

        return view('reports.branch.inventory', [
            'report' => $report,
            'filters' => $request->all(),
        ]);
    }

    /**
     * تقرير الموظفين
     */
    public function employees(Request $request)
    {
        $user = auth()->user();
        $report = ReportService::employeesReport(
            $user->company_code,
            $request->branch_id ?? $user->branch_id,
            $request->only(['status', 'department'])
        );

        return view('reports.branch.employees', [
            'report' => $report,
            'filters' => $request->all(),
        ]);
    }

    /**
     * تقرير السلف
     */
    public function advances(Request $request)
    {
        $user = auth()->user();
        $report = ReportService::advancesReport(
            $user->company_code,
            $request->branch_id ?? $user->branch_id,
            $request->from_date,
            $request->to_date,
            $request->only(['status'])
        );

        return view('reports.branch.advances', [
            'report' => $report,
            'presets' => BaseReport::getPresets(),
            'filters' => $request->all(),
        ]);
    }

    /**
     * تقرير الآليات
     */
    public function vehicles(Request $request)
    {
        $user = auth()->user();
        $report = ReportService::vehiclesReport(
            $user->company_code,
            $request->branch_id ?? $user->branch_id,
            $request->only(['status'])
        );

        return view('reports.branch.vehicles', [
            'report' => $report,
            'filters' => $request->all(),
        ]);
    }

    /**
     * تقرير الخسائر
     */
    public function losses(Request $request)
    {
        $user = auth()->user();
        $report = ReportService::lossesReport(
            $user->company_code,
            $request->branch_id ?? $user->branch_id,
            $request->from_date,
            $request->to_date,
            $request->only(['loss_type'])
        );

        return view('reports.branch.losses', [
            'report' => $report,
            'presets' => BaseReport::getPresets(),
            'filters' => $request->all(),
        ]);
    }

    /**
     * تقرير الصندوق اليومي
     */
    public function dailyCash(Request $request)
    {
        $user = auth()->user();
        $branchId = $request->branch_id ?? $user->branch_id;

        if (!$branchId) {
            return back()->with('error', 'يجب تحديد الفرع');
        }

        $report = ReportService::dailyCashReport(
            $user->company_code,
            $branchId,
            $request->from_date,
            $request->to_date
        );

        return view('reports.branch.daily-cash', [
            'report' => $report,
            'presets' => BaseReport::getPresets(),
            'filters' => $request->all(),
        ]);
    }

    /**
     * تقرير الشركات (سوبر أدمن)
     */
    public function companies(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Company::class);

        $report = ReportService::companiesReport($request->only(['status', 'search']));

        return view('reports.admin.companies', [
            'report' => $report,
            'filters' => $request->all(),
        ]);
    }

    /**
     * طباعة التقرير
     */
    public function print(Request $request, string $type)
    {
        $user = auth()->user();

        $reportMethod = $type . 'Report';
        if (!method_exists(ReportService::class, $reportMethod)) {
            abort(404);
        }

        $report = ReportService::$reportMethod(
            $user->company_code,
            $request->branch_id ?? $user->branch_id,
            $request->from_date,
            $request->to_date,
            $request->except(['branch_id', 'from_date', 'to_date'])
        );

        return view('reports.prints.print-template', [
            'report' => $report,
            'title' => $this->getReportTitle($type),
            'dateFrom' => $request->from_date,
            'dateTo' => $request->to_date,
        ]);
    }

    /**
     * الحصول على عنوان التقرير
     */
    private function getReportTitle(string $type): string
    {
        $titles = [
            'orders' => 'تقرير الطلبات',
            'inventory' => 'تقرير المخزون',
            'employees' => 'تقرير الموظفين',
            'advances' => 'تقرير السلف',
            'vehicles' => 'تقرير الآليات',
            'losses' => 'تقرير الخسائر',
            'dailyCash' => 'تقرير الصندوق اليومي',
            'companies' => 'تقرير الشركات',
        ];

        return $titles[$type] ?? 'تقرير';
    }
}
