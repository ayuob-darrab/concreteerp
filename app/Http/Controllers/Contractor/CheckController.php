<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\Contractor;
use App\Models\ContractorCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckController extends Controller
{
    /**
     * قائمة الشيكات
     */
    public function index(Request $request)
    {
        $query = ContractorCheck::with(['contractor', 'branch', 'createdBy'])
            ->where('company_code', Auth::user()->company_code);

        // تصفية حسب المقاول
        if ($request->filled('contractor_id')) {
            $query->where('contractor_id', $request->contractor_id);
        }

        // تصفية حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // تصفية حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // تصفية حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('due_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('due_date', '<=', $request->date_to);
        }

        $checks = $query->orderBy('due_date', 'asc')->paginate(20);

        $contractors = Contractor::where('company_code', Auth::user()->company_code)
            ->where('status', 'active')
            ->get();

        return view('contractors.checks.index', compact('checks', 'contractors'));
    }

    /**
     * نموذج إضافة شيك
     */
    public function create()
    {
        $contractors = Contractor::where('company_code', Auth::user()->company_code)
            ->where('status', 'active')
            ->get();

        return view('contractors.checks.create', compact('contractors'));
    }

    /**
     * حفظ شيك جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'contractor_id' => 'required|exists:contractors,id',
            'branch_id' => 'required|exists:branches,id',
            'type' => 'required|in:received,issued',
            'check_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'drawer_name' => 'required_if:type,received|string|max:100',
            'payee_name' => 'required_if:type,issued|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $check = ContractorCheck::create([
            'company_code' => Auth::user()->company_code,
            'branch_id' => $request->branch_id,
            'contractor_id' => $request->contractor_id,
            'type' => $request->type,
            'check_number' => $request->check_number,
            'bank_name' => $request->bank_name,
            'bank_account' => $request->bank_account,
            'amount' => $request->amount,
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date,
            'drawer_name' => $request->drawer_name,
            'payee_name' => $request->payee_name,
            'notes' => $request->notes,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('contractor-checks.show', $check)
            ->with('success', 'تم إضافة الشيك بنجاح');
    }

    /**
     * عرض تفاصيل الشيك
     */
    public function show(ContractorCheck $check)
    {
        $this->authorize('view', $check);

        $check->load(['contractor', 'branch', 'createdBy', 'statusHistory']);

        return view('contractors.checks.show', compact('check'));
    }

    /**
     * نموذج تعديل الشيك
     */
    public function edit(ContractorCheck $check)
    {
        $this->authorize('update', $check);

        if ($check->status !== 'pending') {
            return back()->with('error', 'لا يمكن تعديل شيك تم معالجته');
        }

        $contractors = Contractor::where('company_code', Auth::user()->company_code)
            ->where('status', 'active')
            ->get();

        return view('contractors.checks.edit', compact('check', 'contractors'));
    }

    /**
     * تحديث الشيك
     */
    public function update(Request $request, ContractorCheck $check)
    {
        $this->authorize('update', $check);

        if ($check->status !== 'pending') {
            return back()->with('error', 'لا يمكن تعديل شيك تم معالجته');
        }

        $request->validate([
            'check_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'notes' => 'nullable|string|max:500',
        ]);

        $check->update($request->only([
            'check_number',
            'bank_name',
            'bank_account',
            'amount',
            'issue_date',
            'due_date',
            'drawer_name',
            'payee_name',
            'notes',
        ]));

        return redirect()->route('contractor-checks.show', $check)
            ->with('success', 'تم تحديث الشيك بنجاح');
    }

    /**
     * حذف الشيك
     */
    public function destroy(ContractorCheck $check)
    {
        $this->authorize('delete', $check);

        if ($check->status !== 'pending') {
            return back()->with('error', 'لا يمكن حذف شيك تم معالجته');
        }

        $check->delete();

        return redirect()->route('contractor-checks.index')
            ->with('success', 'تم حذف الشيك بنجاح');
    }

    /**
     * إيداع الشيك في البنك
     */
    public function deposit(ContractorCheck $check)
    {
        $this->authorize('update', $check);

        if ($check->status !== 'pending') {
            return back()->with('error', 'لا يمكن إيداع شيك في هذه الحالة');
        }

        $check->update([
            'status' => 'deposited',
            'deposited_at' => now(),
            'deposited_by' => Auth::id(),
        ]);

        $this->logStatusChange($check, 'deposited', 'تم إيداع الشيك في البنك');

        return back()->with('success', 'تم إيداع الشيك بنجاح');
    }

    /**
     * تحصيل الشيك
     */
    public function collect(Request $request, ContractorCheck $check)
    {
        $this->authorize('update', $check);

        if (!in_array($check->status, ['pending', 'deposited'])) {
            return back()->with('error', 'لا يمكن تحصيل شيك في هذه الحالة');
        }

        $request->validate([
            'collected_at' => 'required|date',
            'collected_amount' => 'required|numeric|min:0.01|max:' . $check->amount,
        ]);

        $check->update([
            'status' => 'collected',
            'collected_at' => $request->collected_at,
            'collected_amount' => $request->collected_amount,
            'collected_by' => Auth::id(),
        ]);

        // تحديث رصيد المقاول
        if ($check->type === 'received') {
            $check->contractor->decrement('current_balance', $request->collected_amount);
        } else {
            $check->contractor->increment('current_balance', $request->collected_amount);
        }

        $this->logStatusChange($check, 'collected', 'تم تحصيل الشيك');

        return back()->with('success', 'تم تحصيل الشيك بنجاح');
    }

    /**
     * رفض الشيك (مرتجع من البنك)
     */
    public function reject(Request $request, ContractorCheck $check)
    {
        $this->authorize('update', $check);

        if (!in_array($check->status, ['deposited', 'pending'])) {
            return back()->with('error', 'لا يمكن رفض شيك في هذه الحالة');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $check->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
        ]);

        $this->logStatusChange($check, 'rejected', $request->rejection_reason);

        return back()->with('success', 'تم تسجيل رفض الشيك');
    }

    /**
     * إرجاع الشيك للمقاول
     */
    public function return(Request $request, ContractorCheck $check)
    {
        $this->authorize('update', $check);

        if (!in_array($check->status, ['pending', 'rejected'])) {
            return back()->with('error', 'لا يمكن إرجاع شيك في هذه الحالة');
        }

        $request->validate([
            'return_reason' => 'required|string|max:500',
        ]);

        $check->update([
            'status' => 'returned',
            'return_reason' => $request->return_reason,
            'returned_at' => now(),
            'returned_by' => Auth::id(),
        ]);

        $this->logStatusChange($check, 'returned', $request->return_reason);

        return back()->with('success', 'تم إرجاع الشيك');
    }

    /**
     * إلغاء الشيك
     */
    public function cancel(Request $request, ContractorCheck $check)
    {
        $this->authorize('update', $check);

        if ($check->status === 'collected') {
            return back()->with('error', 'لا يمكن إلغاء شيك محصل');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $check->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id(),
        ]);

        $this->logStatusChange($check, 'cancelled', $request->cancellation_reason);

        return back()->with('success', 'تم إلغاء الشيك');
    }

    /**
     * تظهير الشيك (نقل ملكية)
     */
    public function endorse(Request $request, ContractorCheck $check)
    {
        $this->authorize('update', $check);

        if ($check->status !== 'pending' || $check->type !== 'received') {
            return back()->with('error', 'لا يمكن تظهير شيك في هذه الحالة');
        }

        $request->validate([
            'endorsed_to' => 'required|string|max:100',
            'endorsement_date' => 'required|date',
            'endorsement_notes' => 'nullable|string|max:500',
        ]);

        $check->update([
            'status' => 'endorsed',
            'endorsed_to' => $request->endorsed_to,
            'endorsed_at' => $request->endorsement_date,
            'endorsement_notes' => $request->endorsement_notes,
            'endorsed_by' => Auth::id(),
        ]);

        $this->logStatusChange($check, 'endorsed', 'تم تظهير الشيك إلى: ' . $request->endorsed_to);

        return back()->with('success', 'تم تظهير الشيك بنجاح');
    }

    /**
     * الشيكات المستحقة اليوم
     */
    public function dueToday()
    {
        $checks = ContractorCheck::with(['contractor', 'branch'])
            ->where('company_code', Auth::user()->company_code)
            ->whereIn('status', ['pending', 'deposited'])
            ->whereDate('due_date', today())
            ->orderBy('amount', 'desc')
            ->get();

        return view('contractors.checks.due-today', compact('checks'));
    }

    /**
     * الشيكات المستحقة هذا الأسبوع
     */
    public function dueThisWeek()
    {
        $checks = ContractorCheck::with(['contractor', 'branch'])
            ->where('company_code', Auth::user()->company_code)
            ->whereIn('status', ['pending', 'deposited'])
            ->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->orderBy('due_date')
            ->get();

        return view('contractors.checks.due-week', compact('checks'));
    }

    /**
     * الشيكات المتأخرة
     */
    public function overdueList()
    {
        $checks = ContractorCheck::with(['contractor', 'branch'])
            ->where('company_code', Auth::user()->company_code)
            ->whereIn('status', ['pending', 'deposited'])
            ->where('due_date', '<', today())
            ->orderBy('due_date')
            ->paginate(20);

        return view('contractors.checks.overdue', compact('checks'));
    }

    /**
     * لوحة معلومات الشيكات
     */
    public function dashboard()
    {
        $companyCode = Auth::user()->company_code;

        $statistics = [
            'pending_received' => ContractorCheck::where('company_code', $companyCode)
                ->where('type', 'received')
                ->where('status', 'pending')
                ->sum('amount'),
            'pending_issued' => ContractorCheck::where('company_code', $companyCode)
                ->where('type', 'issued')
                ->where('status', 'pending')
                ->sum('amount'),
            'due_today' => ContractorCheck::where('company_code', $companyCode)
                ->whereIn('status', ['pending', 'deposited'])
                ->whereDate('due_date', today())
                ->sum('amount'),
            'due_this_week' => ContractorCheck::where('company_code', $companyCode)
                ->whereIn('status', ['pending', 'deposited'])
                ->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum('amount'),
            'overdue' => ContractorCheck::where('company_code', $companyCode)
                ->whereIn('status', ['pending', 'deposited'])
                ->where('due_date', '<', today())
                ->sum('amount'),
            'total_collected' => ContractorCheck::where('company_code', $companyCode)
                ->where('status', 'collected')
                ->whereMonth('collected_at', now()->month)
                ->sum('collected_amount'),
            'rejected_count' => ContractorCheck::where('company_code', $companyCode)
                ->where('status', 'rejected')
                ->whereMonth('rejected_at', now()->month)
                ->count(),
        ];

        // الشيكات القادمة
        $upcomingChecks = ContractorCheck::with('contractor')
            ->where('company_code', $companyCode)
            ->whereIn('status', ['pending', 'deposited'])
            ->where('due_date', '>=', today())
            ->orderBy('due_date')
            ->take(10)
            ->get();

        return view('contractors.checks.dashboard', compact('statistics', 'upcomingChecks'));
    }

    /**
     * تسجيل تغيير الحالة
     */
    private function logStatusChange(ContractorCheck $check, string $status, string $notes = null): void
    {
        // يمكن إضافة جدول لتتبع تاريخ حالات الشيك
        DB::table('contractor_check_status_history')->insert([
            'check_id' => $check->id,
            'status' => $status,
            'notes' => $notes,
            'changed_by' => Auth::id(),
            'created_at' => now(),
        ]);
    }
}
