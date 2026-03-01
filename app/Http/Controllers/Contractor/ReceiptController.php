<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\Contractor;
use App\Models\ContractorReceipt;
use App\Models\ContractorInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    /**
     * قائمة السندات
     */
    public function index(Request $request)
    {
        $query = ContractorReceipt::with(['contractor', 'branch', 'createdBy'])
            ->where('company_code', Auth::user()->company_code);

        // تصفية حسب المقاول
        if ($request->filled('contractor_id')) {
            $query->where('contractor_id', $request->contractor_id);
        }

        // تصفية حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // تصفية حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // تصفية حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('receipt_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('receipt_date', '<=', $request->date_to);
        }

        $receipts = $query->orderBy('created_at', 'desc')->paginate(20);

        $contractors = Contractor::where('company_code', Auth::user()->company_code)
            ->where('status', 'active')
            ->get();

        return view('contractors.receipts.index', compact('receipts', 'contractors'));
    }

    /**
     * نموذج إنشاء سند قبض
     */
    public function createReceiptForm()
    {
        $contractors = Contractor::where('company_code', Auth::user()->company_code)
            ->where('status', 'active')
            ->get();

        // الفواتير غير المسددة
        $unpaidInvoices = ContractorInvoice::where('company_code', Auth::user()->company_code)
            ->where('status', 'issued')
            ->whereColumn('paid_amount', '<', 'total')
            ->with('contractor')
            ->get();

        return view('contractors.receipts.create-receipt', compact('contractors', 'unpaidInvoices'));
    }

    /**
     * حفظ سند قبض
     */
    public function createReceipt(Request $request)
    {
        $request->validate([
            'contractor_id' => 'required|exists:contractors,id',
            'branch_id' => 'required|exists:branches,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,check',
            'receipt_date' => 'required|date',
            'description' => 'nullable|string|max:500',
            'invoice_id' => 'nullable|exists:contractor_invoices,id',
            // بيانات التحويل البنكي
            'bank_name' => 'required_if:payment_method,bank_transfer|string|max:100',
            'transfer_reference' => 'required_if:payment_method,bank_transfer|string|max:50',
            // بيانات الشيك
            'check_number' => 'required_if:payment_method,check|string|max:50',
            'check_bank' => 'required_if:payment_method,check|string|max:100',
            'check_date' => 'required_if:payment_method,check|date',
        ]);

        DB::beginTransaction();

        try {
            $receipt = ContractorReceipt::create([
                'company_code' => Auth::user()->company_code,
                'branch_id' => $request->branch_id,
                'contractor_id' => $request->contractor_id,
                'type' => 'receipt', // سند قبض
                'receipt_number' => $this->generateReceiptNumber('REC'),
                'receipt_date' => $request->receipt_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'bank_name' => $request->bank_name,
                'transfer_reference' => $request->transfer_reference,
                'check_number' => $request->check_number,
                'check_bank' => $request->check_bank,
                'check_date' => $request->check_date,
                'description' => $request->description,
                'invoice_id' => $request->invoice_id,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('contractor-receipts.show', $receipt)
                ->with('success', 'تم إنشاء سند القبض بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * نموذج إنشاء سند صرف
     */
    public function createPaymentForm()
    {
        $contractors = Contractor::where('company_code', Auth::user()->company_code)
            ->where('status', 'active')
            ->get();

        return view('contractors.receipts.create-payment', compact('contractors'));
    }

    /**
     * حفظ سند صرف
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'contractor_id' => 'required|exists:contractors,id',
            'branch_id' => 'required|exists:branches,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,check',
            'receipt_date' => 'required|date',
            'description' => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $receipt = ContractorReceipt::create([
                'company_code' => Auth::user()->company_code,
                'branch_id' => $request->branch_id,
                'contractor_id' => $request->contractor_id,
                'type' => 'payment', // سند صرف
                'receipt_number' => $this->generateReceiptNumber('PAY'),
                'receipt_date' => $request->receipt_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'bank_name' => $request->bank_name,
                'transfer_reference' => $request->transfer_reference,
                'check_number' => $request->check_number,
                'check_bank' => $request->check_bank,
                'check_date' => $request->check_date,
                'description' => $request->description,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('contractor-receipts.show', $receipt)
                ->with('success', 'تم إنشاء سند الصرف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل السند
     */
    public function show(ContractorReceipt $receipt)
    {
        $this->authorize('view', $receipt);

        $receipt->load(['contractor', 'branch', 'createdBy', 'approvedBy', 'invoice']);

        return view('contractors.receipts.show', compact('receipt'));
    }

    /**
     * اعتماد السند
     */
    public function approve(ContractorReceipt $receipt)
    {
        $this->authorize('update', $receipt);

        if ($receipt->status !== 'pending') {
            return back()->with('error', 'السند معتمد بالفعل أو ملغي');
        }

        DB::beginTransaction();

        try {
            $receipt->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]);

            // تحديث رصيد المقاول
            if ($receipt->type === 'receipt') {
                // سند قبض - تخفيض رصيد المقاول
                $receipt->contractor->decrement('current_balance', $receipt->amount);
            } else {
                // سند صرف - زيادة رصيد المقاول
                $receipt->contractor->increment('current_balance', $receipt->amount);
            }

            // تحديث مبلغ السداد في الفاتورة إن وجدت
            if ($receipt->invoice_id && $receipt->type === 'receipt') {
                $invoice = $receipt->invoice;
                $invoice->increment('paid_amount', $receipt->amount);

                // تحديث حالة الفاتورة
                if ($invoice->paid_amount >= $invoice->total) {
                    $invoice->update(['status' => 'paid', 'paid_at' => now()]);
                } elseif ($invoice->paid_amount > 0) {
                    $invoice->update(['status' => 'partial']);
                }
            }

            DB::commit();

            return back()->with('success', 'تم اعتماد السند بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * إلغاء السند
     */
    public function cancel(Request $request, ContractorReceipt $receipt)
    {
        $this->authorize('update', $receipt);

        if ($receipt->status === 'cancelled') {
            return back()->with('error', 'السند ملغي بالفعل');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // إعادة الرصيد إذا كان معتمداً
            if ($receipt->status === 'approved') {
                if ($receipt->type === 'receipt') {
                    $receipt->contractor->increment('current_balance', $receipt->amount);
                } else {
                    $receipt->contractor->decrement('current_balance', $receipt->amount);
                }

                // إعادة مبلغ السداد من الفاتورة
                if ($receipt->invoice_id && $receipt->type === 'receipt') {
                    $invoice = $receipt->invoice;
                    $invoice->decrement('paid_amount', $receipt->amount);

                    // تحديث حالة الفاتورة
                    if ($invoice->paid_amount <= 0) {
                        $invoice->update(['status' => 'issued', 'paid_at' => null]);
                    } elseif ($invoice->paid_amount < $invoice->total) {
                        $invoice->update(['status' => 'partial', 'paid_at' => null]);
                    }
                }
            }

            $receipt->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_at' => now(),
                'cancelled_by' => Auth::id(),
            ]);

            DB::commit();

            return back()->with('success', 'تم إلغاء السند بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * طباعة السند
     */
    public function print(ContractorReceipt $receipt)
    {
        $this->authorize('view', $receipt);

        $receipt->load(['contractor', 'branch', 'createdBy', 'approvedBy']);

        return view('contractors.receipts.print', compact('receipt'));
    }

    /**
     * تسوية فاتورة
     */
    public function settleInvoice(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:contractor_invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,check',
        ]);

        $invoice = ContractorInvoice::findOrFail($request->invoice_id);

        if ($invoice->company_code !== Auth::user()->company_code) {
            abort(403);
        }

        $remainingAmount = $invoice->total - $invoice->paid_amount;

        if ($request->amount > $remainingAmount) {
            return back()->with('error', 'المبلغ أكبر من المتبقي على الفاتورة');
        }

        DB::beginTransaction();

        try {
            $receipt = ContractorReceipt::create([
                'company_code' => Auth::user()->company_code,
                'branch_id' => $invoice->branch_id,
                'contractor_id' => $invoice->contractor_id,
                'type' => 'receipt',
                'receipt_number' => $this->generateReceiptNumber('REC'),
                'receipt_date' => now(),
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'bank_name' => $request->bank_name,
                'transfer_reference' => $request->transfer_reference,
                'check_number' => $request->check_number,
                'check_bank' => $request->check_bank,
                'check_date' => $request->check_date,
                'description' => 'تسوية فاتورة رقم ' . $invoice->invoice_number,
                'invoice_id' => $invoice->id,
                'status' => 'approved', // اعتماد تلقائي
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),
            ]);

            // تحديث رصيد المقاول
            $invoice->contractor->decrement('current_balance', $request->amount);

            // تحديث الفاتورة
            $invoice->increment('paid_amount', $request->amount);

            if ($invoice->paid_amount >= $invoice->total) {
                $invoice->update(['status' => 'paid', 'paid_at' => now()]);
            } else {
                $invoice->update(['status' => 'partial']);
            }

            DB::commit();

            return redirect()->route('contractor-invoices.show', $invoice)
                ->with('success', 'تم تسوية الفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * توليد رقم سند
     */
    private function generateReceiptNumber(string $prefix): string
    {
        $year = date('Y');
        $lastReceipt = ContractorReceipt::where('company_code', Auth::user()->company_code)
            ->where('receipt_number', 'like', $prefix . '-' . $year . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastReceipt ? (intval(substr($lastReceipt->receipt_number, -5)) + 1) : 1;

        return $prefix . '-' . $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }
}
