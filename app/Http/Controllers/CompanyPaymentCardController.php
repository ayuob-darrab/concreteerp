<?php

namespace App\Http\Controllers;

use App\Models\CompanyPaymentCard;
use App\Models\CompanyPaymentCardTransaction;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyPaymentCardController extends Controller
{
    /**
     * عرض قائمة بطاقات الدفع الخاصة بالشركة
     */
    public function index()
    {
        $companyCode = Auth::user()->company_code;
        $cards = CompanyPaymentCard::with(['creator', 'branch'])
            ->forCompany($companyCode)
            ->orderBy('created_at', 'desc')
            ->get();

        $branches = Branch::where('company_code', $companyCode)->where('is_active', 1)->get();

        $stats = [
            'total_cards' => $cards->count(),
            'active_cards' => $cards->where('is_active', true)->count(),
            'total_balance' => $cards->sum('current_balance'),
            'total_deposits' => CompanyPaymentCardTransaction::forCompany($companyCode)->deposits()->sum('amount'),
            'total_withdrawals' => CompanyPaymentCardTransaction::forCompany($companyCode)->withdrawals()->sum('amount'),
        ];

        return view('company-payment-cards.index', compact('cards', 'stats', 'branches'));
    }

    /**
     * عرض نموذج إنشاء بطاقة جديدة
     */
    public function create()
    {
        $companyCode = Auth::user()->company_code;
        $cardTypes = CompanyPaymentCard::$cardTypes;
        $branches = Branch::where('company_code', $companyCode)->where('is_active', 1)->get();

        return view('company-payment-cards.create', compact('cardTypes', 'branches'));
    }

    /**
     * حفظ بطاقة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'card_type' => 'required|string|max:50',
            'card_name' => 'required|string|max:100',
            'holder_name' => 'required|string|max:100',
            'card_number' => 'required|string|max:50',
            'opening_balance' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $card = CompanyPaymentCard::create([
                'company_code' => Auth::user()->company_code,
                'branch_id' => $request->branch_id,
                'card_type' => $request->card_type,
                'card_name' => $request->card_name,
                'holder_name' => $request->holder_name,
                'card_number' => $request->card_number,
                'opening_balance' => $request->opening_balance ?? 0,
                'current_balance' => $request->opening_balance ?? 0,
                'expiry_date' => $request->expiry_date,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            // إنشاء معاملة الرصيد الافتتاحي
            if ($card->opening_balance > 0) {
                CompanyPaymentCardTransaction::create([
                    'company_payment_card_id' => $card->id,
                    'transaction_number' => CompanyPaymentCardTransaction::generateTransactionNumber(),
                    'type' => 'deposit',
                    'amount' => $card->opening_balance,
                    'balance_before' => 0,
                    'balance_after' => $card->opening_balance,
                    'reference_type' => 'manual',
                    'company_code' => Auth::user()->company_code,
                    'branch_id' => $request->branch_id,
                    'description' => 'رصيد افتتاحي',
                    'created_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()->route('company-payment-cards.index')
                ->with('success', 'تم إضافة بطاقة الدفع بنجاح ✅');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل بطاقة
     */
    public function show($id)
    {
        $card = CompanyPaymentCard::with(['creator', 'branch', 'transactions.creator'])
            ->forCompany(Auth::user()->company_code)
            ->findOrFail($id);

        $transactions = $card->transactions()
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_deposits' => $card->transactions()->deposits()->sum('amount'),
            'total_withdrawals' => $card->transactions()->withdrawals()->sum('amount'),
            'transactions_count' => $card->transactions()->count(),
        ];

        return view('company-payment-cards.show', compact('card', 'transactions', 'stats'));
    }

    /**
     * عرض نموذج تعديل بطاقة
     */
    public function edit($id)
    {
        $card = CompanyPaymentCard::forCompany(Auth::user()->company_code)->findOrFail($id);
        $cardTypes = CompanyPaymentCard::$cardTypes;
        $branches = Branch::where('company_code', Auth::user()->company_code)->where('is_active', 1)->get();

        return view('company-payment-cards.edit', compact('card', 'cardTypes', 'branches'));
    }

    /**
     * تحديث بطاقة
     */
    public function update(Request $request, $id)
    {
        $card = CompanyPaymentCard::forCompany(Auth::user()->company_code)->findOrFail($id);

        $validated = $request->validate([
            'card_type' => 'required|string|max:50',
            'card_name' => 'required|string|max:100',
            'holder_name' => 'required|string|max:100',
            'card_number' => 'required|string|max:50',
            'expiry_date' => 'nullable|date',
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $card->update([
            'card_type' => $request->card_type,
            'card_name' => $request->card_name,
            'holder_name' => $request->holder_name,
            'card_number' => $request->card_number,
            'expiry_date' => $request->expiry_date,
            'branch_id' => $request->branch_id,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'notes' => $request->notes,
        ]);

        return redirect()->route('company-payment-cards.index')
            ->with('success', 'تم تحديث بطاقة الدفع بنجاح ✅');
    }

    /**
     * حذف بطاقة
     */
    public function destroy($id)
    {
        $card = CompanyPaymentCard::forCompany(Auth::user()->company_code)->findOrFail($id);

        if ($card->transactions()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف البطاقة لوجود معاملات مرتبطة بها');
        }

        $card->delete();

        return redirect()->route('company-payment-cards.index')
            ->with('success', 'تم حذف بطاقة الدفع بنجاح ✅');
    }

    /**
     * تبديل حالة البطاقة
     */
    public function toggleStatus($id)
    {
        $card = CompanyPaymentCard::forCompany(Auth::user()->company_code)->findOrFail($id);
        $card->is_active = !$card->is_active;
        $card->save();

        $status = $card->is_active ? 'تفعيل' : 'تعطيل';
        return back()->with('success', "تم {$status} البطاقة بنجاح ✅");
    }

    /**
     * إيداع مبلغ
     */
    public function deposit(Request $request, $id)
    {
        $card = CompanyPaymentCard::forCompany(Auth::user()->company_code)->findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string',
        ]);

        try {
            $card->deposit($request->amount, $request->description ?? 'إيداع يدوي', 'manual');
            return back()->with('success', 'تم إيداع المبلغ بنجاح ✅');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * سحب مبلغ
     */
    public function withdraw(Request $request, $id)
    {
        $card = CompanyPaymentCard::forCompany(Auth::user()->company_code)->findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $card->current_balance,
            'description' => 'nullable|string',
        ]);

        try {
            $card->withdraw($request->amount, $request->description ?? 'سحب يدوي', 'manual');
            return back()->with('success', 'تم سحب المبلغ بنجاح ✅');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * API - الحصول على البطاقات النشطة
     */
    public function getActiveCards()
    {
        $cards = CompanyPaymentCard::forCompany(Auth::user()->company_code)
            ->active()
            ->select('id', 'card_name', 'card_number_masked', 'current_balance', 'card_type', 'branch_id')
            ->get();

        return response()->json($cards);
    }

    /**
     * تقرير المعاملات
     */
    public function transactionsReport(Request $request)
    {
        $companyCode = Auth::user()->company_code;
        $branches = Branch::where('company_code', $companyCode)->where('is_active', 1)->get();
        $cards = CompanyPaymentCard::forCompany($companyCode)->get();

        $query = CompanyPaymentCardTransaction::with(['paymentCard', 'creator', 'branch'])
            ->forCompany($companyCode);

        if ($request->card_id) {
            $query->where('company_payment_card_id', $request->card_id);
        }
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(25);

        return view('company-payment-cards.transactions', compact('transactions', 'branches', 'cards'));
    }
}
