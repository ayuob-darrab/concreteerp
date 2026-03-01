<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    /**
     * تقرير الطلبات الشامل لمدير الشركة
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // التحقق من صلاحيات مدير الشركة
        if (!in_array($user->usertype_id, ['SA', 'CM'])) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        $companyCode = $user->company_code;
        
        // الفترة الزمنية
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();
        
        // جلب الفروع
        $branches = Branch::where('company_code', $companyCode)
            ->where('is_active', true)
            ->get();
        
        // بناء الاستعلام
        $query = WorkOrder::with(['branch', 'concreteMix'])
            ->where('company_code', $companyCode)
            ->whereBetween('created_at', [$fromDate, $toDate]);
        
        // فلترة حسب الفرع
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        
        // فلترة حسب الحالة
        if ($request->status) {
            $query->where('status_code', $request->status);
        }
        
        // جلب الطلبات
        $orders = $query->orderBy('created_at', 'desc')->get();
        
        // حساب الإحصائيات
        $stats = [
            'total_orders' => $orders->count(),
            'total_quantity' => $orders->sum('quantity'),
            'total_amount' => $orders->sum(function($order) {
                return $order->final_price ?? $order->initial_price ?? 0;
            }),
            'completed' => $orders->where('status_code', 'completed')->count(),
            'in_progress' => $orders->where('status_code', 'in_progress')->count(),
            'pending' => $orders->whereIn('status_code', ['new', 'under_review', 'approved'])->count(),
            'cancelled' => $orders->where('status_code', 'cancelled')->count(),
        ];
        
        // الحالات المتاحة
        $statuses = [
            'new' => 'جديد',
            'under_review' => 'قيد المراجعة',
            'approved' => 'معتمد',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];

        return view('reports.financial.index', compact(
            'orders',
            'branches',
            'stats',
            'statuses',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * طباعة التقرير
     */
    public function print(Request $request)
    {
        $user = auth()->user();
        $companyCode = $user->company_code;
        
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();
        
        $query = WorkOrder::with(['branch', 'concreteMix'])
            ->where('company_code', $companyCode)
            ->whereBetween('created_at', [$fromDate, $toDate]);
        
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        
        if ($request->status) {
            $query->where('status_code', $request->status);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->get();
        
        $stats = [
            'total_orders' => $orders->count(),
            'total_quantity' => $orders->sum('quantity'),
            'total_amount' => $orders->sum(function($order) {
                return $order->final_price ?? $order->initial_price ?? 0;
            }),
        ];
        
        $branchName = $request->branch_id 
            ? Branch::find($request->branch_id)->branch_name ?? 'جميع الفروع'
            : 'جميع الفروع';

        return view('reports.financial.print', compact(
            'orders',
            'stats',
            'fromDate',
            'toDate',
            'branchName'
        ));
    }

    /**
     * تصدير التقرير
     */
    public function export(Request $request)
    {
        return back()->with('info', 'ميزة التصدير قيد التطوير');
    }
}
