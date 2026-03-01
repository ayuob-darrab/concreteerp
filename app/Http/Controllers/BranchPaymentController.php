<?php

namespace App\Http\Controllers;

use App\Models\CustomerPayment;
use App\Models\CustomerPaymentRecord;
use App\Models\CompanyPaymentCard;
use App\Models\WorkOrder;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BranchPaymentController extends Controller
{
    /**
     * عرض قائمة الزبائن الذين عليهم مبالغ (للفرع)
     */
    public function index()
    {
        $companyCode = Auth::user()->company_code;
        $branchId = Auth::user()->branch_id;

        // طلبات الفرع التي لم يتم دفعها بالكامل
        $unpaidOrders = WorkOrder::with(['concreteMix', 'sender'])
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->whereIn('status_code', ['in_progress', 'completed'])
            ->where(function ($q) {
                $q->where('payment_status', '!=', 'paid')
                    ->orWhereNull('payment_status');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // تجميع حسب الزبون
        $customers = $unpaidOrders->groupBy('customer_phone')->map(function ($orders, $phone) {
            $firstOrder = $orders->first();
            $totalAmount = $orders->sum(function ($order) {
                return $order->price ?? 0;
            });
            $paidAmount = $orders->sum('paid_amount');
            $remainingAmount = $totalAmount - $paidAmount;

            return (object) [
                'customer_name' => $firstOrder->customer_name ?? $firstOrder->sender->fullname ?? 'غير محدد',
                'customer_phone' => $phone,
                'orders_count' => $orders->count(),
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'orders' => $orders,
            ];
        })->filter(function ($customer) {
            return $customer->remaining_amount > 0;
        })->values();

        // إحصائيات
        $stats = [
            'total_customers' => $customers->count(),
            'total_amount' => $customers->sum('total_amount'),
            'total_paid' => $customers->sum('paid_amount'),
            'total_remaining' => $customers->sum('remaining_amount'),
        ];

        return view('branch.payments.index', compact('customers', 'stats'));
    }

    /**
     * عرض صفحة الدفع لزبون معين
     */
    public function customerPayment($phone)
    {
        $companyCode = Auth::user()->company_code;
        $branchId = Auth::user()->branch_id;

        // طلبات الزبون غير المدفوعة
        $orders = WorkOrder::with(['concreteMix', 'sender'])
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->where('customer_phone', $phone)
            ->whereIn('status_code', ['in_progress', 'completed'])
            ->where(function ($q) {
                $q->where('payment_status', '!=', 'paid')
                    ->orWhereNull('payment_status');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('branch.payments.index')
                ->with('info', 'لا توجد طلبات غير مدفوعة لهذا الزبون');
        }

        $first = $orders->first();
        $customerName = $first->customer_name ?? $first->sender->fullname ?? 'غير محدد';

        // بطاقات الدفع المتاحة
        $paymentCards = CompanyPaymentCard::forCompany($companyCode)
            ->active()
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                    ->orWhereNull('branch_id');
            })
            ->get();

        // سجل المدفوعات السابقة
        $paymentHistory = CustomerPayment::with(['records', 'workOrder'])
            ->forCompany($companyCode)
            ->forBranch($branchId)
            ->whereHas('workOrder', function ($q) use ($phone) {
                $q->where('customer_phone', $phone);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $paymentMethods = CustomerPayment::$paymentMethods;

        // حساب الإجماليات 
        $grandTotal = $orders->sum(function ($order) {
            return $order->price ?? 0;
        });
        $grandPaid = $orders->sum('paid_amount');
        $grandRemaining = $grandTotal - $grandPaid;

        return view('branch.payments.customer', compact(
            'orders',
            'customerName',
            'phone',
            'paymentCards',
            'paymentHistory',
            'paymentMethods',
            'grandTotal',
            'grandPaid',
            'grandRemaining'
        ));
    }

    /**
     * تسجيل دفعة
     */
    public function storePayment(Request $request)
    {
        $request->validate([
            'customer_phone' => 'required|string',
            'total_debt' => 'required|numeric|min:0.01',
            'payment_type' => 'required|in:cash,deferred',
            'payment_method' => 'required_if:payment_type,cash|in:cash,bank_transfer,check,online',
            'amount' => 'required_if:payment_type,cash|numeric|min:0.01|regex:/^\d+(\.\d{1,2})?$/',
            'company_payment_card_id' => 'required_if:payment_method,online|nullable|exists:company_payment_cards,id',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $companyCode = Auth::user()->company_code;
        $branchId = Auth::user()->branch_id;

        // جلب الطلبات المستحقة للعميل
        $orders = WorkOrder::with('sender')
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->where('customer_phone', $request->customer_phone)
            ->whereIn('status_code', ['in_progress', 'completed'])
            ->where(function ($q) {
                $q->where('payment_status', '!=', 'paid')
                    ->orWhereNull('payment_status');
            })
            ->orderBy('created_at', 'asc')
            ->get();

        if ($orders->isEmpty()) {
            return back()->with('error', 'لا توجد طلبات مستحقة لهذا العميل');
        }

        // حساب إجمالي المديونية
        $totalDebt = $orders->sum(function ($order) {
            $total = $order->price ?? 0;
            return $total - ($order->paid_amount ?? 0);
        });

        if ($request->payment_type === 'cash' && $request->amount > $totalDebt) {
            return back()->with('error', 'المبلغ المدخل أكبر من إجمالي المديونية المستحقة');
        }

        try {
            DB::beginTransaction();

            if ($request->payment_type === 'cash') {
                $remainingAmount = $request->amount;
                $paymentsCreated = 0;

                // توزيع المبلغ على الطلبات بالترتيب
                foreach ($orders as $order) {
                    if ($remainingAmount <= 0) break;

                    $orderTotal = $order->price ?? 0;
                    $orderPaid = $order->paid_amount ?? 0;
                    $orderRemaining = $orderTotal - $orderPaid;

                    if ($orderRemaining <= 0) continue; // تخطي الطلبات المدفوعة

                    // حساب المبلغ للدفع في هذا الطلب
                    $paymentForThisOrder = min($remainingAmount, $orderRemaining);

                    // إنشاء سجل دفع أو تحديث الموجود
                    $customerPayment = CustomerPayment::firstOrCreate(
                        [
                            'work_order_id' => $order->id,
                            'company_code' => $companyCode,
                            'branch_id' => $branchId,
                        ],
                        [
                            'payment_number' => CustomerPayment::generatePaymentNumber($companyCode),
                            'customer_name' => $order->customer_name ?? $order->sender->fullname ?? 'غير محدد',
                            'customer_phone' => $order->customer_phone,
                            'payment_type' => 'cash',
                            'total_amount' => $orderTotal,
                            'paid_amount' => 0,
                            'remaining_amount' => $orderTotal,
                            'status' => 'unpaid',
                            'created_by' => Auth::id(),
                        ]
                    );

                    // تسجيل الدفعة
                    $customerPayment->recordPayment(
                        $paymentForThisOrder,
                        $request->payment_method,
                        $request->company_payment_card_id,
                        $request->reference_number,
                        $request->notes ?? "دفعة من إجمالي {$request->amount} د.ع - توزيع تلقائي"
                    );

                    $remainingAmount -= $paymentForThisOrder;
                    $paymentsCreated++;
                }

                $message = $paymentsCreated > 1
                    ? "تم توزيع الدفعة على {$paymentsCreated} طلبات بنجاح ✅"
                    : 'تم تسجيل الدفعة بنجاح ✅';
            } else {
                // دفع آجل - إنشاء سجلات آجلة لجميع الطلبات
                $deferredRecords = 0;

                foreach ($orders as $order) {
                    $orderTotal = $order->price ?? 0;
                    $orderPaid = $order->paid_amount ?? 0;

                    if (($orderTotal - $orderPaid) <= 0) continue;

                    $customerPayment = CustomerPayment::firstOrCreate(
                        [
                            'work_order_id' => $order->id,
                            'company_code' => $companyCode,
                            'branch_id' => $branchId,
                        ],
                        [
                            'payment_number' => CustomerPayment::generatePaymentNumber($companyCode),
                            'customer_name' => $order->customer_name ?? $order->sender->fullname ?? 'غير محدد',
                            'customer_phone' => $order->customer_phone,
                            'payment_type' => 'deferred',
                            'total_amount' => $orderTotal,
                            'paid_amount' => $orderPaid,
                            'remaining_amount' => $orderTotal - $orderPaid,
                            'status' => 'deferred',
                            'created_by' => Auth::id(),
                            'notes' => $request->notes,
                        ]
                    );

                    $deferredRecords++;
                }

                $message = "تم تسجيل {$deferredRecords} طلب كدفع آجل ✅";
            }

            DB::commit();

            return redirect()->route('branch.payments.customer', $request->customer_phone)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * تقرير المدفوعات (للمدفوعة بالكامل)
     */
    public function paymentsReport(Request $request)
    {
        $companyCode = Auth::user()->company_code;
        $branchId = Auth::user()->branch_id;

        $query = CustomerPayment::with(['workOrder.concreteMix', 'records', 'branch'])
            ->forCompany($companyCode);

        // إذا كان مدير فرع، يرى فقط فرعه
        if (Auth::user()->usertype_id === 'BM') {
            $query->forBranch($branchId);
        }

        // فلاتر
        if ($request->branch_id) {
            $query->forBranch($request->branch_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        // اختيار زبون محدد من القائمة
        if ($request->filled('customer_phone')) {
            $query->where('customer_phone', $request->customer_phone);
        }
        // بحث بالاسم أو الهاتف
        if ($request->filled('customer_search')) {
            $term = '%' . $request->customer_search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('customer_phone', 'like', $term)
                  ->orWhere('customer_name', 'like', $term);
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(25);

        $branches = Branch::where('company_code', $companyCode)->where('is_active', 1)->get();

        // قائمة الزبائن المميزين (لديهم مدفوعات) للاختيار من القائمة
        $customersQuery = CustomerPayment::forCompany($companyCode)
            ->selectRaw('customer_phone, MAX(customer_name) as customer_name')
            ->groupBy('customer_phone');
        if (Auth::user()->usertype_id === 'BM') {
            $customersQuery->forBranch($branchId);
        }
        if ($request->branch_id) {
            $customersQuery->forBranch($request->branch_id);
        }
        $customers = $customersQuery->orderBy('customer_name')->get();

        // تسمية الزبون المختار (لعرضها في حقل البحث عند العودة من النتائج)
        $selectedCustomerLabel = null;
        if ($request->filled('customer_phone')) {
            $sel = $customers->firstWhere('customer_phone', $request->customer_phone);
            $selectedCustomerLabel = $sel ? ($sel->customer_name ?: 'غير محدد') . ' — ' . $sel->customer_phone : $request->customer_phone;
        }

        // إحصائيات
        $statsQuery = CustomerPayment::forCompany($companyCode);
        if (Auth::user()->usertype_id === 'BM') {
            $statsQuery->forBranch($branchId);
        }

        $stats = [
            'total_payments' => $statsQuery->count(),
            'total_amount' => $statsQuery->sum('total_amount'),
            'total_paid' => $statsQuery->sum('paid_amount'),
            'total_remaining' => $statsQuery->sum('remaining_amount'),
            'paid_count' => (clone $statsQuery)->where('status', 'paid')->count(),
            'partial_count' => (clone $statsQuery)->where('status', 'partial')->count(),
            'unpaid_count' => (clone $statsQuery)->where('status', 'unpaid')->count(),
        ];

        return view('branch.payments.report', compact('payments', 'branches', 'stats', 'customers', 'selectedCustomerLabel'));
    }

    /**
     * طباعة فاتورة
     */
    public function printInvoice($id)
    {
        $payment = CustomerPayment::with(['workOrder.concreteMix', 'records.creator', 'records.paymentCard', 'company', 'branch', 'creator'])
            ->forCompany(Auth::user()->company_code)
            ->findOrFail($id);

        return view('branch.payments.invoice', compact('payment'));
    }

    /**
     * تقرير الفروع (لمدير الشركة)
     */
    public function branchesReport(Request $request)
    {
        $companyCode = Auth::user()->company_code;

        $branches = Branch::where('company_code', $companyCode)
            ->where('is_active', 1)
            ->get();

        $branchStats = [];
        foreach ($branches as $branch) {
            $ordersQuery = WorkOrder::where('company_code', $companyCode)
                ->where('branch_id', $branch->id);

            $paymentsQuery = CustomerPayment::where('company_code', $companyCode)
                ->where('branch_id', $branch->id);

            $branchStats[] = (object) [
                'branch' => $branch,
                'total_orders' => (clone $ordersQuery)->count(),
                'completed_orders' => (clone $ordersQuery)->where('status_code', 'completed')->count(),
                'in_progress_orders' => (clone $ordersQuery)->where('status_code', 'in_progress')->count(),
                'total_amount' => (clone $paymentsQuery)->sum('total_amount'),
                'paid_amount' => (clone $paymentsQuery)->sum('paid_amount'),
                'remaining_amount' => (clone $paymentsQuery)->sum('remaining_amount'),
                'paid_count' => (clone $paymentsQuery)->where('status', 'paid')->count(),
                'unpaid_count' => (clone $paymentsQuery)->whereIn('status', ['unpaid', 'partial'])->count(),
            ];
        }

        return view('branch.payments.branches-report', compact('branchStats', 'branches'));
    }

    /**
     * API - الحصول على تفاصيل طلب
     */
    public function getOrderDetails($id)
    {
        $order = WorkOrder::with(['concreteMix'])
            ->where('company_code', Auth::user()->company_code)
            ->findOrFail($id);

        $totalAmount = $order->price ?? 0;

        return response()->json([
            'id' => $order->id,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'concrete_mix' => $order->concreteMix->name ?? '',
            'quantity' => $order->quantity,
            'price' => $order->price ?? 0,
            'total_amount' => $totalAmount,
            'paid_amount' => $order->paid_amount ?? 0,
            'remaining_amount' => $totalAmount - ($order->paid_amount ?? 0),
            'payment_status' => $order->payment_status ?? 'unpaid',
        ]);
    }
}
