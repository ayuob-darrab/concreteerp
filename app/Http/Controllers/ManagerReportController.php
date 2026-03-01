<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\CarMaintenance;
use App\Models\Cars;
use App\Models\Branch;
use App\Models\ConcreteMix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ManagerReportController extends Controller
{
    /**
     * لوحة التقارير المالية الرئيسية لمدير الشركة
     */
    public function index()
    {
        $user = Auth::user();
        $companyCode = $user->company_code;
        
        // إحصائيات سريعة
        $quickStats = $this->getQuickStats($companyCode);
        
        // الفروع
        $branches = Branch::where('company_code', $companyCode)
            ->where('is_active', true)
            ->get();
        
        return view('manager-reports.index', compact('quickStats', 'branches'));
    }
    
    /**
     * التقرير المالي للطلبات - مفصل واحترافي
     */
    public function ordersFinancialReport(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;
        
        // الفلاتر
        $branchId = $request->branch_id;
        $fromDate = $request->from_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? Carbon::now()->format('Y-m-d');
        $status = $request->status;
        $mixType = $request->mix_type;
        
        // بناء الاستعلام
        $query = WorkOrder::where('company_code', $companyCode)
            ->with(['branch', 'concreteMix', 'creator', 'executions'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($mixType) {
            $query->where('classification', $mixType);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->get();
        
        // حساب الإحصائيات المالية
        $financialStats = $this->calculateOrdersFinancialStats($orders);
        
        // إحصائيات حسب الفرع
        $branchStats = $this->calculateOrdersByBranch($orders);
        
        // إحصائيات حسب نوع الخلطة
        $mixStats = $this->calculateOrdersByMix($orders);
        
        // إحصائيات حسب الحالة
        $statusStats = $this->calculateOrdersByStatus($orders);
        
        // بيانات الرسم البياني اليومي
        $dailyChartData = $this->getDailyOrdersChartData($orders);
        
        // الفروع والخلطات للفلاتر
        $branches = Branch::where('company_code', $companyCode)->where('is_active', true)->get();
        $mixes = ConcreteMix::where('company_code', $companyCode)->where('is_active', true)->get();
        
        return view('manager-reports.orders-financial', compact(
            'orders',
            'financialStats',
            'branchStats',
            'mixStats',
            'statusStats',
            'dailyChartData',
            'branches',
            'mixes',
            'fromDate',
            'toDate',
            'branchId',
            'status',
            'mixType'
        ));
    }
    
    /**
     * التقرير المالي للصيانة - مفصل واحترافي
     */
    public function maintenanceFinancialReport(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;
        
        // الفلاتر
        $branchId = $request->branch_id;
        $fromDate = $request->from_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? Carbon::now()->format('Y-m-d');
        $maintenanceType = $request->maintenance_type;
        $carId = $request->car_id;
        
        // بناء الاستعلام
        $query = CarMaintenance::where('company_code', $companyCode)
            ->with(['car.carType', 'branch', 'creator'])
            ->whereBetween('maintenance_date', [$fromDate, $toDate]);
        
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        if ($maintenanceType) {
            $query->where('maintenance_type', $maintenanceType);
        }
        
        if ($carId) {
            $query->where('car_id', $carId);
        }
        
        $maintenances = $query->orderBy('maintenance_date', 'desc')->get();
        
        // حساب الإحصائيات المالية
        $financialStats = $this->calculateMaintenanceFinancialStats($maintenances);
        
        // إحصائيات حسب الفرع
        $branchStats = $this->calculateMaintenanceByBranch($maintenances);
        
        // إحصائيات حسب نوع الصيانة
        $typeStats = $this->calculateMaintenanceByType($maintenances);
        
        // إحصائيات حسب السيارة
        $carStats = $this->calculateMaintenanceByCar($maintenances);
        
        // بيانات الرسم البياني الشهري
        $monthlyChartData = $this->getMonthlyMaintenanceChartData($companyCode, $branchId);
        
        // الفروع والسيارات للفلاتر
        $branches = Branch::where('company_code', $companyCode)->where('is_active', true)->get();
        
        $carsQuery = Cars::where('company_code', $companyCode);
        if ($branchId) {
            $carsQuery->where('branch_id', $branchId);
        }
        $cars = $carsQuery->get();
        
        $maintenanceTypes = CarMaintenance::getMaintenanceTypes();
        
        return view('manager-reports.maintenance-financial', compact(
            'maintenances',
            'financialStats',
            'branchStats',
            'typeStats',
            'carStats',
            'monthlyChartData',
            'branches',
            'cars',
            'maintenanceTypes',
            'fromDate',
            'toDate',
            'branchId',
            'maintenanceType',
            'carId'
        ));
    }
    
    /**
     * طباعة تقرير الطلبات المالي
     */
    public function printOrdersReport(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;
        
        $branchId = $request->branch_id;
        $fromDate = $request->from_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? Carbon::now()->format('Y-m-d');
        
        $query = WorkOrder::where('company_code', $companyCode)
            ->with(['branch', 'concreteMix', 'executions'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->get();
        $financialStats = $this->calculateOrdersFinancialStats($orders);
        $branchStats = $this->calculateOrdersByBranch($orders);
        
        $branch = $branchId ? Branch::find($branchId) : null;
        $company = $user->CompanyName;
        
        return view('manager-reports.prints.orders-report', compact(
            'orders',
            'financialStats',
            'branchStats',
            'branch',
            'company',
            'fromDate',
            'toDate'
        ));
    }
    
    /**
     * طباعة تقرير الصيانة المالي
     */
    public function printMaintenanceReport(Request $request)
    {
        $user = Auth::user();
        $companyCode = $user->company_code;
        
        $branchId = $request->branch_id;
        $fromDate = $request->from_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? Carbon::now()->format('Y-m-d');
        
        $query = CarMaintenance::where('company_code', $companyCode)
            ->with(['car.carType', 'branch'])
            ->whereBetween('maintenance_date', [$fromDate, $toDate]);
        
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        $maintenances = $query->orderBy('maintenance_date', 'desc')->get();
        $financialStats = $this->calculateMaintenanceFinancialStats($maintenances);
        $typeStats = $this->calculateMaintenanceByType($maintenances);
        $carStats = $this->calculateMaintenanceByCar($maintenances);
        
        $branch = $branchId ? Branch::find($branchId) : null;
        $company = $user->CompanyName;
        $maintenanceTypes = CarMaintenance::getMaintenanceTypes();
        
        return view('manager-reports.prints.maintenance-report', compact(
            'maintenances',
            'financialStats',
            'typeStats',
            'carStats',
            'branch',
            'company',
            'maintenanceTypes',
            'fromDate',
            'toDate'
        ));
    }
    
    // ==================== Helper Methods ====================
    
    /**
     * إحصائيات سريعة للوحة الرئيسية
     */
    private function getQuickStats($companyCode)
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        // طلبات الشهر الحالي
        $currentMonthOrders = WorkOrder::where('company_code', $companyCode)
            ->where('created_at', '>=', $currentMonth)
            ->get();
        
        // صيانة الشهر الحالي
        $currentMonthMaintenance = CarMaintenance::where('company_code', $companyCode)
            ->where('maintenance_date', '>=', $currentMonth)
            ->where('status', 'completed')
            ->get();
        
        return [
            'orders' => [
                'count' => $currentMonthOrders->count(),
                'total_value' => $currentMonthOrders->sum('final_price') ?: $currentMonthOrders->sum('initial_price'),
                'total_quantity' => $currentMonthOrders->sum('quantity'),
                'completed' => $currentMonthOrders->where('status', 'completed')->count(),
            ],
            'maintenance' => [
                'count' => $currentMonthMaintenance->count(),
                'total_cost' => $currentMonthMaintenance->sum('total_cost'),
                'parts_cost' => $currentMonthMaintenance->sum('parts_cost'),
                'labor_cost' => $currentMonthMaintenance->sum('labor_cost'),
            ],
        ];
    }
    
    /**
     * حساب الإحصائيات المالية للطلبات
     */
    private function calculateOrdersFinancialStats($orders)
    {
        $completed = $orders->whereIn('status', ['completed', 'delivered']);
        $pending = $orders->whereIn('status', ['pending', 'approved', 'in_progress']);
        $cancelled = $orders->where('status', 'cancelled');
        
        return [
            'total_orders' => $orders->count(),
            'completed_orders' => $completed->count(),
            'pending_orders' => $pending->count(),
            'cancelled_orders' => $cancelled->count(),
            
            'total_quantity' => $orders->sum('quantity'),
            'executed_quantity' => $orders->sum('executed_quantity'),
            
            'total_value' => $orders->sum(function($order) {
                return $order->final_price ?: $order->initial_price ?: 0;
            }),
            'completed_value' => $completed->sum(function($order) {
                return $order->final_price ?: $order->initial_price ?: 0;
            }),
            'pending_value' => $pending->sum(function($order) {
                return $order->final_price ?: $order->initial_price ?: 0;
            }),
            
            'average_order_value' => $orders->count() > 0 
                ? $orders->sum(function($order) { return $order->final_price ?: $order->initial_price ?: 0; }) / $orders->count() 
                : 0,
            
            'completion_rate' => $orders->count() > 0 
                ? round(($completed->count() / $orders->count()) * 100, 1) 
                : 0,
        ];
    }
    
    /**
     * إحصائيات الطلبات حسب الفرع
     */
    private function calculateOrdersByBranch($orders)
    {
        return $orders->groupBy('branch_id')->map(function($branchOrders) {
            $branch = $branchOrders->first()->branch;
            return [
                'branch_name' => $branch->branch_name ?? 'غير محدد',
                'count' => $branchOrders->count(),
                'quantity' => $branchOrders->sum('quantity'),
                'value' => $branchOrders->sum(function($order) {
                    return $order->final_price ?: $order->initial_price ?: 0;
                }),
                'completed' => $branchOrders->whereIn('status', ['completed', 'delivered'])->count(),
            ];
        })->sortByDesc('value');
    }
    
    /**
     * إحصائيات الطلبات حسب نوع الخلطة
     */
    private function calculateOrdersByMix($orders)
    {
        return $orders->groupBy('classification')->map(function($mixOrders) {
            $mix = $mixOrders->first()->concreteMix;
            return [
                'mix_name' => $mix->name ?? 'غير محدد',
                'count' => $mixOrders->count(),
                'quantity' => $mixOrders->sum('quantity'),
                'value' => $mixOrders->sum(function($order) {
                    return $order->final_price ?: $order->initial_price ?: 0;
                }),
            ];
        })->sortByDesc('quantity');
    }
    
    /**
     * إحصائيات الطلبات حسب الحالة
     */
    private function calculateOrdersByStatus($orders)
    {
        $statuses = [
            'pending' => ['name' => 'قيد الانتظار', 'color' => '#FCD34D', 'icon' => '⏳'],
            'approved' => ['name' => 'معتمد', 'color' => '#60A5FA', 'icon' => '✓'],
            'in_progress' => ['name' => 'جاري التنفيذ', 'color' => '#818CF8', 'icon' => '🔄'],
            'completed' => ['name' => 'مكتمل', 'color' => '#34D399', 'icon' => '✅'],
            'delivered' => ['name' => 'تم التسليم', 'color' => '#10B981', 'icon' => '📦'],
            'cancelled' => ['name' => 'ملغي', 'color' => '#F87171', 'icon' => '❌'],
        ];
        
        return $orders->groupBy('status')->map(function($statusOrders, $status) use ($statuses) {
            $statusInfo = $statuses[$status] ?? ['name' => $status, 'color' => '#6B7280', 'icon' => '📋'];
            return [
                'name' => $statusInfo['name'],
                'color' => $statusInfo['color'],
                'icon' => $statusInfo['icon'],
                'count' => $statusOrders->count(),
                'value' => $statusOrders->sum(function($order) {
                    return $order->final_price ?: $order->initial_price ?: 0;
                }),
            ];
        });
    }
    
    /**
     * بيانات الرسم البياني اليومي للطلبات
     */
    private function getDailyOrdersChartData($orders)
    {
        $daily = $orders->groupBy(function($order) {
            return Carbon::parse($order->created_at)->format('Y-m-d');
        })->map(function($dayOrders, $date) {
            return [
                'date' => $date,
                'count' => $dayOrders->count(),
                'value' => $dayOrders->sum(function($order) {
                    return $order->final_price ?: $order->initial_price ?: 0;
                }),
                'quantity' => $dayOrders->sum('quantity'),
            ];
        })->sortKeys();
        
        return [
            'labels' => $daily->keys()->map(function($date) {
                return Carbon::parse($date)->format('m/d');
            })->values(),
            'counts' => $daily->pluck('count')->values(),
            'values' => $daily->pluck('value')->values(),
            'quantities' => $daily->pluck('quantity')->values(),
        ];
    }
    
    /**
     * حساب الإحصائيات المالية للصيانة
     */
    private function calculateMaintenanceFinancialStats($maintenances)
    {
        $completed = $maintenances->where('status', 'completed');
        $inProgress = $maintenances->where('status', 'in_progress');
        $scheduled = $maintenances->where('status', 'scheduled');
        
        return [
            'total_maintenances' => $maintenances->count(),
            'completed_maintenances' => $completed->count(),
            'in_progress_maintenances' => $inProgress->count(),
            'scheduled_maintenances' => $scheduled->count(),
            
            'total_cost' => $completed->sum('total_cost'),
            'parts_cost' => $completed->sum('parts_cost'),
            'labor_cost' => $completed->sum('labor_cost'),
            
            'average_cost' => $completed->count() > 0 
                ? round($completed->sum('total_cost') / $completed->count(), 2) 
                : 0,
            
            'max_cost' => $completed->max('total_cost') ?? 0,
            'min_cost' => $completed->min('total_cost') ?? 0,
            
            'unique_cars' => $maintenances->pluck('car_id')->unique()->count(),
        ];
    }
    
    /**
     * إحصائيات الصيانة حسب الفرع
     */
    private function calculateMaintenanceByBranch($maintenances)
    {
        return $maintenances->groupBy('branch_id')->map(function($branchMaintenances) {
            $branch = $branchMaintenances->first()->branch;
            $completed = $branchMaintenances->where('status', 'completed');
            return [
                'branch_name' => $branch->branch_name ?? 'غير محدد',
                'count' => $branchMaintenances->count(),
                'completed' => $completed->count(),
                'total_cost' => $completed->sum('total_cost'),
                'parts_cost' => $completed->sum('parts_cost'),
                'labor_cost' => $completed->sum('labor_cost'),
                'unique_cars' => $branchMaintenances->pluck('car_id')->unique()->count(),
            ];
        })->sortByDesc('total_cost');
    }
    
    /**
     * إحصائيات الصيانة حسب النوع
     */
    private function calculateMaintenanceByType($maintenances)
    {
        $types = CarMaintenance::getMaintenanceTypes();
        
        return $maintenances->groupBy('maintenance_type')->map(function($typeMaintenances, $type) use ($types) {
            $typeInfo = $types[$type] ?? ['name' => $type, 'icon' => '🔧', 'color' => '#6B7280'];
            $completed = $typeMaintenances->where('status', 'completed');
            return [
                'name' => $typeInfo['name'],
                'icon' => $typeInfo['icon'],
                'color' => $typeInfo['color'],
                'count' => $typeMaintenances->count(),
                'total_cost' => $completed->sum('total_cost'),
            ];
        })->sortByDesc('total_cost');
    }
    
    /**
     * إحصائيات الصيانة حسب السيارة
     */
    private function calculateMaintenanceByCar($maintenances)
    {
        return $maintenances->groupBy('car_id')->map(function($carMaintenances) {
            $car = $carMaintenances->first()->car;
            $completed = $carMaintenances->where('status', 'completed');
            return [
                'car_name' => $car->car_name ?? $car->car_number ?? 'غير محدد',
                'car_type' => $car->carType->name ?? '-',
                'plate_number' => $car->car_number ?? '-',
                'count' => $carMaintenances->count(),
                'total_cost' => $completed->sum('total_cost'),
                'last_maintenance' => $carMaintenances->max('maintenance_date'),
            ];
        })->sortByDesc('total_cost')->take(10);
    }
    
    /**
     * بيانات الرسم البياني الشهري للصيانة
     */
    private function getMonthlyMaintenanceChartData($companyCode, $branchId = null)
    {
        $query = CarMaintenance::where('company_code', $companyCode)
            ->where('status', 'completed')
            ->where('maintenance_date', '>=', Carbon::now()->subMonths(6)->startOfMonth());
        
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        $monthlyData = $query->get()
            ->groupBy(function($maintenance) {
                return Carbon::parse($maintenance->maintenance_date)->format('Y-m');
            })
            ->map(function($monthMaintenances, $month) {
                return [
                    'month' => $month,
                    'count' => $monthMaintenances->count(),
                    'total_cost' => $monthMaintenances->sum('total_cost'),
                    'parts_cost' => $monthMaintenances->sum('parts_cost'),
                    'labor_cost' => $monthMaintenances->sum('labor_cost'),
                ];
            })->sortKeys();
        
        return [
            'labels' => $monthlyData->keys()->map(function($month) {
                return Carbon::parse($month . '-01')->translatedFormat('M Y');
            })->values(),
            'counts' => $monthlyData->pluck('count')->values(),
            'total_costs' => $monthlyData->pluck('total_cost')->values(),
            'parts_costs' => $monthlyData->pluck('parts_cost')->values(),
            'labor_costs' => $monthlyData->pluck('labor_cost')->values(),
        ];
    }
}
