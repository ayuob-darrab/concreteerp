<?php

namespace App\Http\Controllers;

use App\Models\PaymentCard;
use App\Models\PaymentCardTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PaymentCardController extends Controller
{
    /**
     * عرض قائمة بطاقات الدفع الإلكتروني
     */
    public function index()
    {
        $cards = PaymentCard::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();

        // إحصائيات
        $stats = [
            'total_cards' => $cards->count(),
            'active_cards' => $cards->where('is_active', true)->count(),
            'total_balance' => $cards->sum('current_balance'),
            'total_deposits' => PaymentCardTransaction::where('type', 'deposit')->sum('amount'),
            'total_withdrawals' => PaymentCardTransaction::where('type', 'withdrawal')->sum('amount'),
        ];

        return view('payment-cards.index', compact('cards', 'stats'));
    }

    /**
     * عرض نموذج إنشاء بطاقة جديدة
     */
    public function create()
    {
        $cardTypes = PaymentCard::$cardTypes;
        return view('payment-cards.create', compact('cardTypes'));
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
            'card_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('payment_cards')->where(function ($query) use ($request) {
                    return $query->where('card_type', $request->card_type)
                        ->whereNull('deleted_at');
                }),
            ],
            'opening_balance' => 'required|numeric|min:0',
            'expiry_date' => 'required|date',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ], [
            'card_number.unique' => 'رقم البطاقة/الحساب مسجل مسبقاً لنفس نوع البطاقة',
            'card_type.required' => 'نوع البطاقة مطلوب',
            'card_name.required' => 'اسم البطاقة مطلوب',
            'holder_name.required' => 'اسم صاحب البطاقة مطلوب',
            'card_number.required' => 'رقم البطاقة/الحساب مطلوب',
            'opening_balance.required' => 'الرصيد الافتتاحي مطلوب',
            'expiry_date.required' => 'تاريخ انتهاء الصلاحية مطلوب',
        ]);

        DB::beginTransaction();
        try {
            $openingBalance = $validated['opening_balance'] ?? 0;

            $card = PaymentCard::create([
                'card_type' => $validated['card_type'],
                'card_name' => $validated['card_name'],
                'holder_name' => $validated['holder_name'],
                'card_number' => $validated['card_number'],
                'opening_balance' => $openingBalance,
                'current_balance' => $openingBalance,
                'expiry_date' => $validated['expiry_date'] ?? null,
                'is_active' => $request->has('is_active') ? true : true,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // تسجيل معاملة الرصيد الافتتاحي إذا كان أكبر من صفر
            if ($openingBalance > 0) {
                PaymentCardTransaction::create([
                    'payment_card_id' => $card->id,
                    'transaction_number' => PaymentCardTransaction::generateTransactionNumber(),
                    'type' => 'deposit',
                    'amount' => $openingBalance,
                    'balance_before' => 0,
                    'balance_after' => $openingBalance,
                    'reference_type' => 'manual',
                    'description' => 'رصيد افتتاحي',
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()->route('payment-cards.index')
                ->with('success', 'تم إضافة البطاقة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض تفاصيل بطاقة
     */
    public function show($id)
    {
        $card = PaymentCard::with(['transactions' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }, 'transactions.creator', 'transactions.company'])->findOrFail($id);

        // إحصائيات البطاقة
        $stats = [
            'total_deposits' => $card->transactions->where('type', 'deposit')->sum('amount'),
            'total_withdrawals' => $card->transactions->where('type', 'withdrawal')->sum('amount'),
            'transactions_count' => $card->transactions->count(),
        ];

        return view('payment-cards.show', compact('card', 'stats'));
    }

    /**
     * عرض نموذج تعديل بطاقة
     */
    public function edit($id)
    {
        $card = PaymentCard::findOrFail($id);
        $cardTypes = PaymentCard::$cardTypes;
        return view('payment-cards.edit', compact('card', 'cardTypes'));
    }

    /**
     * تحديث بطاقة
     */
    public function update(Request $request, $id)
    {
        $card = PaymentCard::findOrFail($id);

        $validated = $request->validate([
            'card_type' => 'required|string|max:50',
            'card_name' => 'required|string|max:100',
            'holder_name' => 'required|string|max:100',
            'card_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('payment_cards')->where(function ($query) use ($request) {
                    return $query->where('card_type', $request->card_type)
                        ->whereNull('deleted_at');
                })->ignore($id),
            ],
            'expiry_date' => 'required|date',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ], [
            'card_number.unique' => 'رقم البطاقة/الحساب مسجل مسبقاً لنفس نوع البطاقة',
            'card_type.required' => 'نوع البطاقة مطلوب',
            'card_name.required' => 'اسم البطاقة مطلوب',
            'holder_name.required' => 'اسم صاحب البطاقة مطلوب',
            'card_number.required' => 'رقم البطاقة/الحساب مطلوب',
            'expiry_date.required' => 'تاريخ انتهاء الصلاحية مطلوب',
        ]);

        $card->update([
            'card_type' => $validated['card_type'],
            'card_name' => $validated['card_name'],
            'holder_name' => $validated['holder_name'],
            'card_number' => $validated['card_number'],
            'expiry_date' => $validated['expiry_date'] ?? null,
            'is_active' => $request->has('is_active'),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('payment-cards.index')
            ->with('success', 'تم تحديث البطاقة بنجاح');
    }

    /**
     * حذف بطاقة
     */
    public function destroy($id)
    {
        $card = PaymentCard::findOrFail($id);

        // التحقق من عدم وجود أي معاملات مرتبطة بالبطاقة
        $hasTransactions = $card->transactions()->exists();

        if ($hasTransactions) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف البطاقة لوجود معاملات مرتبطة بها');
        }

        $card->delete();

        return redirect()->route('payment-cards.index')
            ->with('success', 'تم حذف البطاقة بنجاح');
    }

    /**
     * تبديل حالة البطاقة (تفعيل/تعطيل)
     */
    public function toggleStatus($id)
    {
        $card = PaymentCard::findOrFail($id);

        $card->is_active = !$card->is_active;
        $card->save();

        $status = $card->is_active ? 'تفعيل' : 'تعطيل';
        return redirect()->route('payment-cards.index')
            ->with('success', "تم {$status} البطاقة بنجاح");
    }

    /**
     * إضافة إيداع للبطاقة
     */
    public function deposit(Request $request, $id)
    {
        $card = PaymentCard::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $card->deposit(
                $validated['amount'],
                $validated['description'] ?? 'إيداع يدوي',
                'manual'
            );

            return redirect()->back()
                ->with('success', 'تم إضافة الإيداع بنجاح. الرصيد الحالي: ' . number_format($card->current_balance, 0) . ' دينار');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * سحب من البطاقة
     */
    public function withdraw(Request $request, $id)
    {
        $card = PaymentCard::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $card->current_balance,
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $card->withdraw(
                $validated['amount'],
                $validated['description'] ?? 'سحب يدوي',
                'manual'
            );

            return redirect()->back()
                ->with('success', 'تم السحب بنجاح. الرصيد الحالي: ' . number_format($card->current_balance, 0) . ' دينار');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * الحصول على البطاقات النشطة (API)
     */
    public function getActiveCards()
    {
        $cards = PaymentCard::active()
            ->select('id', 'card_name', 'card_type', 'holder_name', 'card_number_masked', 'current_balance')
            ->get();

        return response()->json($cards);
    }

    /**
     * الحصول على تفاصيل بطاقة (API)
     */
    public function getCardDetails($id)
    {
        $card = PaymentCard::select('id', 'card_name', 'card_type', 'holder_name', 'card_number_masked', 'current_balance')
            ->findOrFail($id);

        return response()->json($card);
    }

    /**
     * تقرير المعاملات
     */
    public function transactionsReport(Request $request)
    {
        $query = PaymentCardTransaction::with(['paymentCard', 'creator', 'company']);

        // فلتر حسب البطاقة
        if ($request->card_id) {
            $query->where('payment_card_id', $request->card_id);
        }

        // فلتر حسب النوع
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // فلتر حسب التاريخ
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // فلتر حسب الشركة
        if ($request->company_code) {
            $query->where('company_code', $request->company_code);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);
        $cards = PaymentCard::all();

        // إحصائيات
        $statsQuery = PaymentCardTransaction::query();
        if ($request->card_id) {
            $statsQuery->where('payment_card_id', $request->card_id);
        }
        if ($request->date_from) {
            $statsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $statsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $stats = [
            'total_deposits' => (clone $statsQuery)->where('type', 'deposit')->sum('amount'),
            'total_withdrawals' => (clone $statsQuery)->where('type', 'withdrawal')->sum('amount'),
            'transactions_count' => (clone $statsQuery)->count(),
        ];

        return view('payment-cards.transactions', compact('transactions', 'cards', 'stats'));
    }
}
