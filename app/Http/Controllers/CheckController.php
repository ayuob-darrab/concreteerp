<?php

namespace App\Http\Controllers;

use App\Models\Check;
use App\Services\CheckService;
use App\Http\Requests\StoreCheckRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CheckController extends Controller
{
    protected CheckService $checkService;

    public function __construct(CheckService $checkService)
    {
        $this->checkService = $checkService;
        $this->middleware('auth');
    }

    /**
     * عرض قائمة الشيكات
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'search',
            'type',
            'status',
            'branch_id',
            'due_from',
            'due_to',
            'due_within_days',
            'overdue',
            'sort_by',
            'sort_direction'
        ]);

        $checks = $this->checkService->getChecks($filters, 15);
        $statistics = $this->checkService->getStatistics(auth()->user()->company_code);

        return view('checks.index', compact('checks', 'statistics', 'filters'));
    }

    /**
     * عرض نموذج إنشاء شيك
     */
    public function create(): View
    {
        return view('checks.create');
    }

    /**
     * حفظ شيك جديد
     */
    public function store(StoreCheckRequest $request): RedirectResponse
    {
        try {
            $check = $this->checkService->createCheck($request->validated());

            return redirect()
                ->route('checks.show', $check)
                ->with('success', 'تم إنشاء الشيك بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الشيك: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل شيك
     */
    public function show(Check $check): View
    {
        $check->load([
            'account.contractor',
            'invoice',
            'statusLogs.creator',
            'creator',
        ]);

        return view('checks.show', compact('check'));
    }

    /**
     * إيداع شيك
     */
    public function deposit(Check $check): RedirectResponse
    {
        try {
            $this->checkService->depositCheck($check);

            return back()->with('success', 'تم إيداع الشيك بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * تحصيل شيك
     */
    public function collect(Check $check): RedirectResponse
    {
        try {
            $this->checkService->collectCheck($check);

            return back()->with('success', 'تم تحصيل الشيك بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * رفض شيك
     */
    public function reject(Request $request, Check $check): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->checkService->rejectCheck($check, $request->reason);

            return back()->with('warning', 'تم رفض الشيك');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * إرجاع شيك
     */
    public function returnCheck(Request $request, Check $check): RedirectResponse
    {
        try {
            $this->checkService->returnCheck($check, $request->reason);

            return back()->with('info', 'تم إرجاع الشيك');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * إلغاء شيك
     */
    public function cancel(Request $request, Check $check): RedirectResponse
    {
        try {
            $this->checkService->cancelCheck($check, $request->reason);

            return back()->with('warning', 'تم إلغاء الشيك');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * تظهير شيك
     */
    public function endorse(Request $request, Check $check): RedirectResponse
    {
        $request->validate([
            'new_holder' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $this->checkService->endorseCheck($check, $request->new_holder, $request->notes);

            return back()->with('success', 'تم تظهير الشيك بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * عرض الشيكات المستحقة اليوم
     */
    public function dueToday(): View
    {
        $checks = $this->checkService->getDueToday(auth()->user()->company_code);

        return view('checks.due-today', compact('checks'));
    }

    /**
     * عرض الشيكات المستحقة هذا الأسبوع
     */
    public function dueThisWeek(): View
    {
        $checks = $this->checkService->getDueWithinDays(auth()->user()->company_code, 7);

        return view('checks.due-this-week', compact('checks'));
    }

    /**
     * عرض الشيكات المتأخرة
     */
    public function overdue(): View
    {
        $checks = $this->checkService->getOverdue(auth()->user()->company_code);

        return view('checks.overdue', compact('checks'));
    }

    /**
     * تقرير الشيكات المستحقة
     */
    public function dueReport(Request $request): View
    {
        $days = $request->get('days', 30);
        $checks = $this->checkService->getDueWithinDays(auth()->user()->company_code, $days);
        $statistics = $this->checkService->getStatistics(auth()->user()->company_code);

        return view('checks.due-report', compact('checks', 'statistics', 'days'));
    }

    /**
     * تصدير الشيكات المستحقة
     */
    public function exportDue(Request $request)
    {
        $days = $request->get('days', 30);
        $checks = $this->checkService->exportDueChecks(auth()->user()->company_code, $days);

        // TODO: تنفيذ التصدير لـ Excel
        return back()->with('info', 'سيتم تنفيذ التصدير قريباً');
    }

    /**
     * لوحة تحكم الشيكات
     */
    public function dashboard(): View
    {
        $companyCode = auth()->user()->company_code;

        $data = [
            'statistics' => $this->checkService->getStatistics($companyCode),
            'dueToday' => $this->checkService->getDueToday($companyCode),
            'dueThisWeek' => $this->checkService->getDueWithinDays($companyCode, 7),
            'overdue' => $this->checkService->getOverdue($companyCode),
        ];

        return view('checks.dashboard', $data);
    }
}
