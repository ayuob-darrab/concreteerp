<?php

namespace App\Http\Controllers;

use App\Models\Advance;
use App\Models\AdvancePayment;
use App\Models\AdvanceSetting;
use App\Models\Employee;
use App\Models\Notification;
use App\Services\AdvanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class AdvanceController extends Controller
{
    protected $advanceService;

    public function __construct(AdvanceService $advanceService)
    {
        $this->advanceService = $advanceService;
    }

    /**
     * عرض قائمة السلف
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $companyCode = session('company_code') ?? $user->company_code;
        $branchId = session('branch_id') ?? $user->branch_id;

        $query = Advance::with(['branch', 'requester', 'approver', 'payments']);

        // تصفية حسب الشركة فقط إذا كان company_code موجود
        if ($companyCode) {
            $query->forCompany($companyCode);
        }

        // تصفية حسب الفرع
        if ($request->branch_id) {
            $query->forBranch($request->branch_id);
        } elseif ($branchId && $user->usertype_id != 'CM') {
            // مدير الفرع يرى سلف فرعه فقط، مدير الشركة يرى الكل
            $query->forBranch($branchId);
        }

        // تصفية حسب الحالة
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // تصفية حسب نوع المستفيد
        if ($request->beneficiary_type) {
            $query->where('beneficiary_type', $request->beneficiary_type);
        }

        // البحث برقم السلفة
        if ($request->search) {
            $query->where('advance_number', 'like', "%{$request->search}%");
        }

        // البحث بالمستفيد
        if ($request->beneficiary) {
            $beneficiarySearch = $request->beneficiary;
            $query->where(function ($q) use ($beneficiarySearch) {
                // البحث في الموظفين
                $q->whereHas('employeeBeneficiary', function ($eq) use ($beneficiarySearch) {
                    $eq->where('fullname', 'like', "%{$beneficiarySearch}%");
                })
                    // البحث في الموردين
                    ->orWhereHas('supplierBeneficiary', function ($sq) use ($beneficiarySearch) {
                        $sq->where('supplier_name', 'like', "%{$beneficiarySearch}%");
                    })
                    // البحث في المقاولين
                    ->orWhereHas('contractorBeneficiary', function ($cq) use ($beneficiarySearch) {
                        $cq->where('contract_name', 'like', "%{$beneficiarySearch}%");
                    });
            });
        }

        $advances = $query->orderBy('created_at', 'desc')->paginate(20);

        // الإحصائيات
        $statistics = $this->advanceService->getStatistics($companyCode, $branchId);

        return view('advances.index', compact('advances', 'statistics'));
    }

    /**
     * عرض نموذج إنشاء سلفة
     */
    public function create()
    {
        $companyCode = session('company_code');
        $branchId = session('branch_id');
        $user = Auth::user();

        // جلب الموظفين
        if ($branchId) {
            $employees = Employee::where('branch_id', $branchId)
                ->where('isactive', 1)
                ->get();
        } else {
            $employees = Employee::where('isactive', 1)->get();
        }

        // جلب المقاولين
        if ($companyCode) {
            $contractors = \App\Models\Contractor::where('company_code', $companyCode)
                ->where('isactive', 1)
                ->get();
        } else {
            $contractors = \App\Models\Contractor::where('isactive', 1)->get();
        }

        // جلب الموردين
        if ($companyCode) {
            $suppliers = \App\Models\Supplier::where('company_code', $companyCode)->get();
        } else {
            $suppliers = \App\Models\Supplier::all();
        }

        $settings = AdvanceSetting::getSettings($companyCode, $branchId);

        return view('advances.create', compact('employees', 'contractors', 'suppliers', 'settings'));
    }

    /**
     * حفظ سلفة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'beneficiary_type' => 'required|in:employee,agent,supplier,contractor',
            'beneficiary_id' => 'required|integer',
            'amount' => 'required|numeric|min:1',
            'deduction_type' => 'required|in:percentage,fixed',
            'deduction_value' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $data = $request->all();

            // الحصول على company_code و branch_id من الـ session أو من المستخدم
            $user = Auth::user();
            $data['company_code'] = session('company_code') ?? $user->company_code ?? 'DEFAULT';
            $data['branch_id'] = session('branch_id') ?? $user->branch_id ?? 1;
            $data['branch_code'] = session('branch_code') ?? 'BR001';

            $advance = $this->advanceService->createAdvance($data);

            // إنشاء إشعار للموافقة على السلفة
            Notification::create([
                'company_code' => $data['company_code'],
                'branch_id' => $data['branch_id'],
                'title' => 'طلب سلفة جديد',
                'message' => 'طلب سلفة جديد من ' . $advance->beneficiary_name . ' بمبلغ ' . number_format($advance->amount) . ' د.ع',
                'type' => 'warning',
                'related_type' => 'advance',
                'related_id' => $advance->id,
                'sent_by' => $user->id,
            ]);

            return redirect()
                ->route('advances.create')
                ->with('success', 'تم إضافة السلفة في انتظار الموافقة');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل السلفة
     */
    public function show(Advance $advance)
    {
        $advance->load(['branch', 'requester', 'approver', 'payments.payer']);

        return view('advances.show', compact('advance'));
    }

    /**
     * عرض نموذج تعديل السلفة
     */
    public function edit(Advance $advance)
    {
        // لا يمكن تعديل السلف النشطة أو المكتملة
        if (in_array($advance->status, [Advance::STATUS_ACTIVE, Advance::STATUS_COMPLETED])) {
            return back()->with('error', 'لا يمكن تعديل سلفة نشطة أو مكتملة');
        }

        $branchId = session('branch_id');
        $employees = Employee::where('branch_id', $branchId)
            ->where('isactive', 1)
            ->get();

        return view('advances.edit', compact('advance', 'employees'));
    }

    /**
     * تحديث السلفة
     */
    public function update(Request $request, Advance $advance)
    {
        // لا يمكن تعديل السلف النشطة أو المكتملة
        if (in_array($advance->status, [Advance::STATUS_ACTIVE, Advance::STATUS_COMPLETED])) {
            return back()->with('error', 'لا يمكن تعديل سلفة نشطة أو مكتملة');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'deduction_type' => 'required|in:percentage,fixed',
            'deduction_value' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:500',
        ]);

        $advance->update([
            'amount' => $request->amount,
            'remaining_amount' => $request->amount,
            'deduction_type' => $request->deduction_type,
            'deduction_value' => $request->deduction_value,
            'reason' => $request->reason,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('advances.show', $advance)
            ->with('success', 'تم تحديث السلفة بنجاح');
    }

    /**
     * حذف السلفة
     */
    public function destroy(Advance $advance)
    {
        // لا يمكن حذف السلف النشطة
        if ($advance->status === Advance::STATUS_ACTIVE) {
            return back()->with('error', 'لا يمكن حذف سلفة نشطة');
        }

        $advance->delete();

        return redirect()
            ->route('advances.index')
            ->with('success', 'تم حذف السلفة بنجاح');
    }

    /**
     * الموافقة على السلفة
     */
    public function approve(Request $request, Advance $advance)
    {
        try {
            $this->advanceService->approveAdvance($advance, [
                'notes' => $request->notes,
            ]);

            // حذف إشعار السلفة
            Notification::where('related_type', 'advance')
                ->where('related_id', $advance->id)
                ->delete();

            // الرجوع إلى صفحة السلفة
            return redirect()->route('advances.show', $advance)
                ->with('success', 'تمت الموافقة على السلفة بنجاح');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * الموافقة على السلفة مع تعديل المبلغ
     */
    public function approveWithEdit(Request $request, Advance $advance)
    {
        $request->validate([
            'new_amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $originalAmount = $advance->amount;
            $newAmount = $request->new_amount;

            // تحديث المبلغ
            $advance->update([
                'amount' => $newAmount,
                'remaining_amount' => $newAmount,
                'notes' => ($advance->notes ? $advance->notes . "\n" : '') .
                    "[تعديل المبلغ]: من " . number_format($originalAmount) . " إلى " . number_format($newAmount) . " د.ع" .
                    ($request->notes ? " - السبب: " . $request->notes : ''),
                'updated_by' => Auth::id(),
            ]);

            // الموافقة على السلفة
            $this->advanceService->approveAdvance($advance, [
                'notes' => 'تم تعديل المبلغ من ' . number_format($originalAmount) . ' إلى ' . number_format($newAmount) . ' د.ع',
            ]);

            // حذف إشعار السلفة
            Notification::where('related_type', 'advance')
                ->where('related_id', $advance->id)
                ->delete();

            // الرجوع إلى صفحة السلفة
            return redirect()->route('advances.show', $advance)
                ->with('success', 'تمت الموافقة على السلفة مع تعديل المبلغ بنجاح');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * رفض السلفة
     */
    public function reject(Request $request, Advance $advance)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->advanceService->rejectAdvance($advance, $request->reason);

            // حذف إشعار السلفة
            Notification::where('related_type', 'advance')
                ->where('related_id', $advance->id)
                ->delete();

            return redirect()->route('advances.pending')->with('success', 'تم رفض السلفة');
        } catch (Exception $e) {
            return redirect()->route('advances.pending')->with('error', $e->getMessage());
        }
    }

    /**
     * إلغاء السلفة
     */
    public function cancel(Request $request, Advance $advance)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->advanceService->cancelAdvance($advance, $request->reason);

            return back()->with('success', 'تم إلغاء السلفة');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * عرض نموذج تسديد دفعة
     */
    public function showPaymentForm(Advance $advance)
    {
        if (!$advance->canAcceptPayment()) {
            return back()->with('error', 'لا يمكن تسديد دفعات على هذه السلفة');
        }

        return view('advances.payment', compact('advance'));
    }

    /**
     * تسديد دفعة
     */
    public function makePayment(Request $request, Advance $advance)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $payment = $this->advanceService->makePayment($advance, [
                'amount' => $request->amount,
                'payment_type' => AdvancePayment::TYPE_MANUAL,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            return redirect()
                ->route('advances.show', $advance)
                ->with('success', 'تم تسجيل الدفعة بنجاح. رقم الدفعة: ' . $payment->payment_number);
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * تبديل الاستقطاع التلقائي
     */
    public function toggleAutoDeduction(Advance $advance)
    {
        $this->advanceService->toggleAutoDeduction($advance);

        $status = $advance->fresh()->auto_deduction ? 'مفعل' : 'معطل';
        return back()->with('success', "تم تغيير حالة الاستقطاع التلقائي إلى: {$status}");
    }

    /**
     * طباعة السلفة
     */
    public function print(Advance $advance)
    {
        $advance->load(['branch', 'requester', 'approver', 'payments']);

        return view('advances.print', compact('advance'));
    }

    /**
     * طباعة إيصال الدفعة
     */
    public function printPayment(AdvancePayment $payment)
    {
        $payment->load(['advance', 'advance.branch', 'payer']);

        return view('advances.print-payment', compact('payment'));
    }

    /**
     * طباعة كشف حساب المستفيد
     */
    public function printStatement(Request $request)
    {
        $request->validate([
            'beneficiary_type' => 'required',
            'beneficiary_id' => 'required',
        ]);

        $advances = Advance::forBeneficiary($request->beneficiary_type, $request->beneficiary_id)
            ->with(['payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        $balance = $this->advanceService->getBalance($request->beneficiary_type, $request->beneficiary_id);

        return view('advances.statement', compact('advances', 'balance'));
    }

    /**
     * السلف المعلقة للموافقة
     */
    public function pending()
    {
        $user = Auth::user();
        $companyCode = session('company_code') ?? $user->company_code ?? null;
        $branchId = session('branch_id') ?? $user->branch_id ?? null;

        $query = Advance::where('status', Advance::STATUS_PENDING)
            ->with(['branch', 'requester']);

        // للمدير العام - عرض جميع السلف المعلقة
        // للمدير الفرع - عرض سلف فرعه فقط
        if ($branchId && $user->usertype_id != 'SA') {
            $query->where('branch_id', $branchId);
        }

        $advances = $query->orderBy('created_at', 'desc')->get();

        // جلب إشعارات السلف الجديدة
        $advanceNotifications = Notification::where('related_type', 'advance')
            ->where('is_read', false)
            ->when($branchId && $user->usertype_id != 'SA', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('advances.pending', compact('advances', 'advanceNotifications'));
    }

    /**
     * السلف الموافق عليها (للدفع)
     */
    public function approved()
    {
        $companyCode = session('company_code') ?? Auth::user()->company_code ?? 'DEFAULT';
        $branchId = session('branch_id') ?? Auth::user()->branch_id;

        $advances = Advance::where(function ($q) use ($companyCode) {
            if ($companyCode && $companyCode != 'DEFAULT') {
                $q->where('company_code', $companyCode);
            }
        })
            ->when($branchId, function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereIn('status', [Advance::STATUS_APPROVED, Advance::STATUS_ACTIVE])
            ->where('remaining_amount', '>', 0) // فقط السلف التي لم تُدفع بالكامل
            ->with(['branch', 'requester'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('advances.approved', compact('advances'));
    }

    /**
     * تقرير السلف
     */
    public function report(Request $request)
    {
        $companyCode = session('company_code');
        $branchId = $request->branch_id ?? session('branch_id');

        $query = Advance::forCompany($companyCode)
            ->with(['branch', 'payments']);

        if ($branchId) {
            $query->forBranch($branchId);
        }

        // تصفية حسب التاريخ
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // تصفية حسب نوع المستفيد
        if ($request->beneficiary_type) {
            $query->where('beneficiary_type', $request->beneficiary_type);
        }

        $advances = $query->orderBy('created_at', 'desc')->get();
        $statistics = $this->advanceService->getStatistics($companyCode, $branchId);

        return view('advances.report', compact('advances', 'statistics'));
    }

    /**
     * إعدادات السلف
     */
    public function settings()
    {
        $companyCode = session('company_code');
        $branchId = session('branch_id');

        $settings = AdvanceSetting::getSettings($companyCode, $branchId);

        return view('advances.settings', compact('settings'));
    }

    /**
     * حفظ إعدادات السلف
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'max_employee_advance' => 'nullable|numeric|min:0',
            'max_agent_advance' => 'nullable|numeric|min:0',
            'max_supplier_advance' => 'nullable|numeric|min:0',
            'max_contractor_advance' => 'nullable|numeric|min:0',
            'default_employee_deduction' => 'nullable|numeric|min:0|max:100',
            'default_agent_deduction' => 'nullable|numeric|min:0|max:100',
            'default_supplier_deduction' => 'nullable|numeric|min:0|max:100',
            'default_contractor_deduction' => 'nullable|numeric|min:0|max:100',
        ]);

        $companyCode = session('company_code');
        $branchId = session('branch_id');

        AdvanceSetting::updateOrCreate(
            [
                'company_code' => $companyCode,
                'branch_id' => $branchId,
            ],
            [
                'max_employee_advance' => $request->max_employee_advance ?? 0,
                'max_agent_advance' => $request->max_agent_advance ?? 0,
                'max_supplier_advance' => $request->max_supplier_advance ?? 0,
                'max_contractor_advance' => $request->max_contractor_advance ?? 0,
                'default_employee_deduction' => $request->default_employee_deduction ?? 10,
                'default_agent_deduction' => $request->default_agent_deduction ?? 10,
                'default_supplier_deduction' => $request->default_supplier_deduction ?? 10,
                'default_contractor_deduction' => $request->default_contractor_deduction ?? 10,
                'auto_deduction_enabled' => $request->has('auto_deduction_enabled'),
                'allow_multiple_advances' => $request->has('allow_multiple_advances'),
                'require_approval' => $request->has('require_approval'),
                'allow_overpayment' => $request->has('allow_overpayment'),
                'updated_by' => Auth::id(),
            ]
        );

        return back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }
}
