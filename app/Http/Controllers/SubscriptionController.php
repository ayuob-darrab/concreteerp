<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\CompanySubscriptionPrice;
use App\Models\SubscriptionPricing;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionHistory;
use App\Models\Notification;
use App\Models\PaymentCardTransaction;
use App\Models\CompanyPaymentCardTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * قائمة الشركات مع الاشتراكات الحالية
     */
    public function companies()
    {
        // استثناء الشركة المالكة للنظام (SA)
        $companies = Company::where('code', '!=', 'SA')->get();
        $subscriptions = CompanySubscription::get()->keyBy('company_code');

        // جلب إحصائيات التمديدات لكل شركة
        $extensionStats = SubscriptionHistory::where('action_type', 'extended')
            ->selectRaw('company_code, COUNT(*) as extension_count, SUM(extension_days) as total_extension_days')
            ->groupBy('company_code')
            ->get()
            ->keyBy('company_code');

        // جلب إعدادات الأسعار
        $pricingSettings = SubscriptionPricing::getSettings();

        return view('subscriptions.companies', compact('companies', 'subscriptions', 'extensionStats', 'pricingSettings'));
    }

    /**
     * عرض تفاصيل الشركة: بياناتها العامة، الاشتراكات، والحركات (صفحة عرض فقط)
     * فلتر مستقل لكل قسم: سجل الاشتراكات، فواتير الاشتراك، حركات بطاقات النظام، حركات بطاقات الشركة
     */
    public function companyDetails($companyCode)
    {
        $company = Company::with(['city', 'subscription'])->where('code', $companyCode)->firstOrFail();
        $subscription = $company->subscription;

        $allowedLimits = ['25', '50', '100', 'all'];

        $getLimit = function ($param, $default = '50') use ($allowedLimits) {
            $v = request($param, $default);
            return in_array($v, $allowedLimits) ? $v : $default;
        };

        $limitHistory = $getLimit('limit_history');
        $limitInvoices = $getLimit('limit_invoices');
        $limitPayment = $getLimit('limit_payment');
        $limitCompanyCards = $getLimit('limit_company_cards');

        $toValue = function ($limit) {
            return $limit === 'all' ? null : (int) $limit;
        };

        $subscriptionHistory = SubscriptionHistory::where('company_code', $companyCode)
            ->with('creator')
            ->orderBy('created_at', 'desc');
        if ($toValue($limitHistory) !== null) {
            $subscriptionHistory->limit($toValue($limitHistory));
        }
        $subscriptionHistory = $subscriptionHistory->get();

        $subscriptionInvoices = SubscriptionInvoice::where('company_code', $companyCode)
            ->with('subscription')
            ->orderBy('created_at', 'desc');
        if ($toValue($limitInvoices) !== null) {
            $subscriptionInvoices->limit($toValue($limitInvoices));
        }
        $subscriptionInvoices = $subscriptionInvoices->get();

        $paymentCardTransactions = PaymentCardTransaction::forCompany($companyCode)
            ->with(['paymentCard', 'creator'])
            ->orderBy('created_at', 'desc');
        if ($toValue($limitPayment) !== null) {
            $paymentCardTransactions->limit($toValue($limitPayment));
        }
        $paymentCardTransactions = $paymentCardTransactions->get();

        $companyCardTransactions = CompanyPaymentCardTransaction::where('company_code', $companyCode)
            ->with(['paymentCard', 'creator', 'branch'])
            ->orderBy('created_at', 'desc');
        if ($toValue($limitCompanyCards) !== null) {
            $companyCardTransactions->limit($toValue($limitCompanyCards));
        }
        $companyCardTransactions = $companyCardTransactions->get();

        $planLabels = [
            'monthly' => 'شهري',
            'yearly' => 'سنوي',
            'percentage' => 'نسبة من الطلبات',
            'trial' => 'تجريبي',
            'hybrid' => 'هجين',
        ];

        $limitLabel = function ($l) {
            return $l === 'all' ? 'الكل' : $l;
        };

        return view('subscriptions.company-details', compact(
            'company',
            'subscription',
            'subscriptionHistory',
            'subscriptionInvoices',
            'paymentCardTransactions',
            'companyCardTransactions',
            'planLabels',
            'limitHistory',
            'limitInvoices',
            'limitPayment',
            'limitCompanyCards',
            'limitLabel'
        ));
    }

    /**
     * صفحة تحرير اشتراك شركة
     */
    public function edit($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $subscription = CompanySubscription::where('company_code', $companyCode)->first();

        // منع التعديل على الاشتراك النشط (ما عدا: منتهٍ في فترة السماح، أو خطة نسبة/هجين يُسمح بتجديدها)
        $isExpiredInGrace = $subscription && $subscription->isExpired() && $subscription->isInGracePeriod();
        $isPercentageOrHybrid = $subscription && in_array($subscription->plan_type, ['percentage', 'hybrid']);
        if ($subscription && $subscription->status === 'active' && !$isExpiredInGrace && !$isPercentageOrHybrid) {
            return redirect()->route('subscriptions.companies')
                ->with('error', '⚠️ لا يمكن تعديل الاشتراك النشط. يجب إنهاء الاشتراك أولاً أو انتظار انتهائه.');
        }

        // جلب إعدادات الأسعار العامة
        $pricingSettings = SubscriptionPricing::getSettings();

        // جلب السعر الخاص بالشركة (إن وجد)
        $companyPricing = CompanySubscriptionPrice::getCompanyPricing($companyCode);

        // جلب آخر اشتراك لعرض السعر السابق
        $lastSubscription = SubscriptionHistory::where('company_code', $companyCode)
            ->orderBy('created_at', 'desc')
            ->first();

        // جلب بطاقات الدفع النشطة
        $paymentCards = \App\Models\PaymentCard::where('is_active', true)
            ->orderBy('card_name')
            ->get();

        return view('subscriptions.edit', compact('company', 'subscription', 'pricingSettings', 'companyPricing', 'lastSubscription', 'paymentCards'));
    }

    /**
     * عرض الخطط فقط (واجهة الخطط)
     */
    public function plans()
    {
        return view('subscriptions.plans');
    }

    /**
     * حفظ/تحديث اشتراك شركة
     */
    public function subscribe(Request $request, $companyCode)
    {
        // التحقق من عدم وجود اشتراك نشط قبل السماح بالتعديل (نسبة من الطلبات والهجين يُسمح بتجديدها دائماً)
        $existing = CompanySubscription::where('company_code', $companyCode)->first();

        $isExpiredInGrace = $existing && $existing->isExpired() && $existing->isInGracePeriod();
        $isPercentageOrHybrid = $existing && in_array($existing->plan_type, ['percentage', 'hybrid']);
        if ($existing && $existing->status === 'active' && !$isExpiredInGrace && !$isPercentageOrHybrid) {
            return redirect()->back()
                ->with('error', '⚠️ لا يمكن تعديل الاشتراك النشط. يجب إنهاء الاشتراك الحالي أولاً.');
        }

        // تطبيع مبلغ ثابت على كل طلب (إزالة الفواصل)
        if ($request->has('fixed_order_fee') && is_string($request->fixed_order_fee)) {
            $request->merge(['fixed_order_fee' => str_replace(',', '', $request->fixed_order_fee)]);
        }

        $data = $request->validate([
            'plan_type' => 'required|in:monthly,yearly,percentage,trial,hybrid',
            'users_count' => 'required|integer|min:1',
            'price_per_user' => 'nullable|numeric|min:0',
            'years_count' => 'nullable|integer|min:1|max:10',
            'base_fee' => 'nullable|numeric|min:0',
            'percentage_rate' => 'nullable|numeric|min:0|max:100',
            'order_fee_type' => 'nullable|in:percentage,fixed',
            'fixed_order_fee' => 'nullable|numeric|min:0',
            'orders_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'auto_renew' => 'sometimes|boolean',
            'notes' => 'nullable|string',
            'duration_quantity' => 'nullable|integer|min:1|max:100',
            'payment_type' => 'nullable|string|in:cash,deferred',
            'payment_method' => 'nullable|string|in:cash,bank_transfer,check,online',
            'payment_reference' => 'nullable|string|max:100',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_card_id' => 'nullable|integer|exists:payment_cards,id',
        ], [
            'orders_limit.min' => 'حد الطلبات يجب أن يكون 1 على الأقل',
            'users_count.required' => 'عدد المستخدمين مطلوب',
            'users_count.min' => 'عدد المستخدمين يجب أن يكون 1 على الأقل',
        ]);

        // الحصول على سعر المستخدم
        $pricePerUser = $data['price_per_user'] ?? CompanySubscription::getPricePerUser($companyCode, $data['plan_type']);
        $usersCount = $data['users_count'];
        $yearsCount = $data['years_count'] ?? 1;
        $durationQuantity = $data['duration_quantity'] ?? 1;

        // حساب إجمالي المبلغ بناءً على عدد المستخدمين
        $totalAmount = CompanySubscription::calculateTotalAmount(
            $data['plan_type'],
            $usersCount,
            $pricePerUser,
            $durationQuantity,
            $yearsCount
        );

        // حساب عدد الأيام حسب نوع الخطة
        $totalDays = CompanySubscription::calculateDays($data['plan_type'], $durationQuantity, $yearsCount);

        // خصم أيام السماح المستخدمة من الاشتراك السابق
        if ($existing && $existing->days_to_deduct > 0) {
            $totalDays = max(1, $totalDays - $existing->days_to_deduct);
        }

        // حساب تاريخ النهاية
        if (empty($data['end_date']) && $totalDays > 0) {
            $data['end_date'] = Carbon::parse($data['start_date'])->addDays($totalDays)->format('Y-m-d');
        }

        // التحقق من عدم وجود تكرار بنفس الفترة
        if (CompanySubscription::hasDuplicateSubscription($companyCode, $data['start_date'], $data['end_date'], $existing?->id)) {
            return redirect()->back()
                ->with('error', '⚠️ يوجد اشتراك بنفس الفترة الزمنية. يرجى اختيار تواريخ مختلفة.');
        }

        // تعيين قيم افتراضية للحقول الفارغة حسب نوع الخطة
        if ($data['plan_type'] === 'percentage' && empty($data['base_fee'])) {
            $data['base_fee'] = 0;
        }

        if (in_array($data['plan_type'], ['monthly', 'yearly']) && empty($data['percentage_rate'])) {
            $data['percentage_rate'] = 0;
        }

        if ($data['plan_type'] === 'trial') {
            $data['base_fee'] = $data['base_fee'] ?? 0;
            $data['percentage_rate'] = $data['percentage_rate'] ?? 0;
            $totalAmount = 0;
        }

        // تعيين نوع رسوم الطلب للخطط المناسبة
        if (in_array($data['plan_type'], ['percentage', 'hybrid'])) {
            $data['order_fee_type'] = $data['order_fee_type'] ?? 'percentage';
            if ($data['order_fee_type'] === 'percentage') {
                $data['fixed_order_fee'] = 0;
            } else {
                $data['percentage_rate'] = 0;
            }
            // نسبة من الطلبات والهجين: حد الطلبات غير مستخدم (مسموح بعدد غير محدود)
            $data['orders_limit'] = null;
        } else {
            // للخطط الشهرية والسنوية والتجريبية: لا توجد رسوم طلبات
            $data['order_fee_type'] = 'percentage';
            $data['fixed_order_fee'] = 0;
            $data['percentage_rate'] = $data['percentage_rate'] ?? 0;
        }

        // تحديد حالة الدفع بناءً على نوع الدفع
        $paymentType = $data['payment_type'] ?? null;
        $paidAmount = 0;
        $paymentStatus = 'pending';

        if ($paymentType === 'deferred') {
            // دفع آجل: المبلغ المدفوع = 0، الحالة = معلق
            $paidAmount = 0;
            $paymentStatus = 'pending';
        } elseif ($paymentType === 'cash') {
            // دفع كاش: استخدم المبلغ المدفوع من النموذج
            $paidAmount = $data['paid_amount'] ?? 0;
            if ($paidAmount >= $totalAmount) {
                $paymentStatus = 'paid';
            } elseif ($paidAmount > 0) {
                $paymentStatus = 'partial';
            } else {
                $paymentStatus = 'pending';
            }
        } else {
            // للتوافق مع الطريقة القديمة (إذا لم يتم تحديد نوع الدفع)
            $paidAmount = $data['paid_amount'] ?? 0;
            if ($paidAmount > 0) {
                $paymentStatus = $paidAmount >= $totalAmount ? 'paid' : 'partial';
            }
        }

        // حفظ الاشتراك القديم في التاريخ إذا كان موجوداً (منتهي أو معطل)
        if ($existing) {
            SubscriptionHistory::create([
                'company_code' => $companyCode,
                'subscription_id' => $existing->id,
                'plan_type' => $existing->plan_type,
                'base_fee' => $existing->base_fee ?? 0,
                'percentage_rate' => $existing->percentage_rate ?? 0,
                'order_fee_type' => $existing->order_fee_type,
                'fixed_order_fee' => $existing->fixed_order_fee ?? 0,
                'orders_limit' => $existing->orders_limit ?? null,
                'orders_used' => $existing->orders_used ?? 0,
                'start_date' => $existing->start_date,
                'end_date' => $existing->end_date,
                'actual_start_date' => $existing->start_date,
                'actual_end_date' => now(),
                'auto_renew' => $existing->auto_renew ?? false,
                'status' => $existing->status === 'active' ? 'completed' : $existing->status,
                'notes' => $existing->notes,
                'action_type' => 'renewed',
                'created_by' => Auth::id(),
                'payment_status' => $existing->payment_status ?? 'pending',
                'paid_amount' => $existing->paid_amount ?? 0,
                'paid_at' => $existing->paid_at,
                'payment_method' => $existing->payment_method,
                'payment_reference' => $existing->payment_reference,
                'extension_days' => $existing->extension_days ?? 0,
                'extension_deducted' => $existing->extension_deducted ?? false,
                'duration_quantity' => $existing->duration_quantity ?? 1,
                'total_days' => $existing->total_days,
            ]);
        }

        $subscription = CompanySubscription::updateOrCreate(
            ['company_code' => $companyCode],
            array_merge($data, [
                'created_by' => Auth::id(),
                'status' => 'active',
                'orders_used' => $existing->orders_used ?? 0,
                'users_count' => $usersCount,
                'price_per_user' => $pricePerUser,
                'total_amount' => $totalAmount,
                'base_fee' => $totalAmount, // للتوافق مع الكود القديم
                'years_count' => $yearsCount,
                'duration_quantity' => $durationQuantity,
                'total_days' => $totalDays,
                'payment_status' => $paymentStatus,
                'paid_amount' => $paidAmount,
                'paid_at' => $paidAmount >= $totalAmount ? now() : null,
                'extension_days' => 0,
                'extension_deducted' => false,
                'grace_days_used' => 0,
                'grace_period_start' => null,
                'is_in_grace_period' => false,
                'days_to_deduct' => 0,
            ])
        );

        // إنشاء فاتورة الاشتراك
        if (in_array($data['plan_type'], ['monthly', 'yearly', 'hybrid'])) {
            SubscriptionInvoice::createSubscriptionInvoice($subscription, Auth::id());
        }

        // معالجة الدفع الإلكتروني - إيداع المبلغ في البطاقة
        if ($data['payment_method'] === 'online' && !empty($data['payment_card_id'])) {
            try {
                $card = \App\Models\PaymentCard::findOrFail($data['payment_card_id']);
                $paidAmountNow = $data['paid_amount'] ?? $totalAmount;

                // إيداع المبلغ المدفوع في البطاقة (الشركة تدفع = المال يدخل للحساب)
                if ($paidAmountNow > 0) {
                    $card->deposit(
                        $paidAmountNow,
                        "دفع اشتراك شركة {$companyCode}",
                        'subscription',
                        $subscription->id,
                        $companyCode
                    );

                    // تحديث بيانات الدفع في الاشتراك
                    $subscription->update([
                        'paid_amount' => $paidAmountNow,
                        'payment_status' => $paidAmountNow >= $totalAmount ? 'paid' : 'partial',
                        'paid_at' => now(),
                        'payment_reference' => 'CARD-' . $card->id . '-' . time(),
                    ]);
                }
            } catch (\Exception $e) {
                // في حالة الفشل، تسجيل الخطأ ولكن لا نمنع الاشتراك
                \Illuminate\Support\Facades\Log::error('Payment Card Error: ' . $e->getMessage());
            }
        }

        // إنشاء سجل في التاريخ للاشتراك الجديد
        if (!$existing) {
            SubscriptionHistory::create([
                'company_code' => $companyCode,
                'subscription_id' => $subscription->id,
                'plan_type' => $subscription->plan_type,
                'base_fee' => $subscription->base_fee ?? 0,
                'percentage_rate' => $subscription->percentage_rate ?? 0,
                'orders_limit' => $subscription->orders_limit ?? null,
                'orders_used' => 0,
                'start_date' => $subscription->start_date,
                'end_date' => $subscription->end_date,
                'actual_start_date' => now(),
                'actual_end_date' => null,
                'auto_renew' => $subscription->auto_renew ?? false,
                'status' => 'active',
                'notes' => $subscription->notes,
                'action_type' => 'created',
                'created_by' => Auth::id(),
                'payment_status' => $paymentStatus,
                'paid_amount' => $paidAmount,
                'duration_quantity' => $durationQuantity,
                'total_days' => $totalDays,
            ]);
        }

        // إرسال إشعار للشركة
        $company = Company::where('code', $companyCode)->first();
        $this->sendSubscriptionNotification($company, $subscription, $existing ? 'renewed' : 'created');

        // إرسال واتساب (إذا كان متاحاً)
        $this->sendWhatsAppNotification($company, $subscription, $existing ? 'renewed' : 'created');

        return redirect()->route('subscriptions.companies')->with('success', 'تم حفظ اشتراك الشركة بنجاح ✅');
    }

    /**
     * تمديد الاشتراك - يعمل فقط عندما يكون باقي الاشتراك يومين أو أقل
     */
    public function extend(Request $request, $companyCode)
    {
        $data = $request->validate([
            'extension_days' => 'required|integer|min:1|max:365',
        ]);

        $subscription = CompanySubscription::where('company_code', $companyCode)->firstOrFail();
        $company = Company::where('code', $companyCode)->firstOrFail();

        // التحقق من أن الاشتراك نشط
        if ($subscription->status !== 'active') {
            return redirect()->back()->with('error', '⚠️ لا يمكن تمديد اشتراك غير نشط.');
        }

        // التحقق من عدد التمديدات السابقة (الحد الأقصى 5 مرات)
        $extensionCount = SubscriptionHistory::where('company_code', $companyCode)
            ->where('action_type', 'extended')
            ->count();

        if ($extensionCount >= 5) {
            return redirect()->back()->with('error', '⚠️ تم الوصول للحد الأقصى من التمديدات (5 مرات). يرجى تجديد الاشتراك بدلاً من التمديد.');
        }

        // التحقق من الأيام المتبقية (يجب أن تكون يومين أو أقل)
        $daysRemaining = $subscription->end_date ? Carbon::now()->diffInDays($subscription->end_date, false) : 0;

        if ($daysRemaining > 2) {
            return redirect()->back()->with('error', "⚠️ لا يمكن التمديد حالياً. يجب أن يكون باقي الاشتراك يومين أو أقل. (متبقي: {$daysRemaining} يوم)");
        }

        // تمديد الاشتراك
        $subscription->extend($data['extension_days']);

        // تسجيل في التاريخ
        SubscriptionHistory::create([
            'company_code' => $companyCode,
            'subscription_id' => $subscription->id,
            'plan_type' => $subscription->plan_type,
            'base_fee' => 0, // التمديد مجاني
            'percentage_rate' => $subscription->percentage_rate ?? 0,
            'orders_limit' => $subscription->orders_limit ?? null,
            'orders_used' => $subscription->orders_used ?? 0,
            'start_date' => $subscription->start_date,
            'end_date' => $subscription->end_date,
            'actual_start_date' => now(),
            'actual_end_date' => null,
            'auto_renew' => $subscription->auto_renew ?? false,
            'status' => 'active',
            'notes' => "تمديد {$data['extension_days']} يوم",
            'action_type' => 'extended',
            'created_by' => Auth::id(),
            'extension_days' => $data['extension_days'],
        ]);

        return redirect()->back()->with('success', "تم تمديد الاشتراك بنجاح بمقدار {$data['extension_days']} يوم ✅");
    }

    /**
     * خصم أيام التمديد من الاشتراك الجديد عند التجديد
     */
    public function deductExtension(Request $request, $companyCode)
    {
        $data = $request->validate([
            'deduct' => 'required|boolean',
        ]);

        $subscription = CompanySubscription::where('company_code', $companyCode)->firstOrFail();

        if ($data['deduct'] && $subscription->extension_days > 0 && !$subscription->extension_deducted) {
            // خصم أيام التمديد من الاشتراك
            $extensionDays = $subscription->extension_days;
            $newEndDate = Carbon::parse($subscription->end_date)->subDays($extensionDays);

            $subscription->update([
                'end_date' => $newEndDate,
                'extension_deducted' => true,
                'total_days' => max(0, ($subscription->total_days ?? 0) - $extensionDays),
            ]);

            return redirect()->back()->with('success', "تم خصم {$extensionDays} يوم تمديد من الاشتراك ✅");
        }

        return redirect()->back()->with('info', 'لم يتم إجراء أي تغيير.');
    }

    /**
     * عرض سجل التمديدات لشركة معينة
     */
    public function extensionHistory($companyCode)
    {
        $extensions = SubscriptionHistory::where('company_code', $companyCode)
            ->where('action_type', 'extended')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($ext) {
                return [
                    'date' => Carbon::parse($ext->created_at)->format('Y/m/d H:i'),
                    'days' => $ext->extension_days ?? 0,
                    'notes' => $ext->notes,
                ];
            });

        $totalCount = $extensions->count();
        $totalDays = $extensions->sum('days');

        return response()->json([
            'extensions' => $extensions,
            'total_count' => $totalCount,
            'total_days' => $totalDays,
        ]);
    }

    /**
     * تسديد الاشتراك
     */
    public function payment(Request $request, $companyCode)
    {
        $data = $request->validate([
            'payment_type' => 'required|string|in:cash,deferred',
            'amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|in:cash,bank_transfer,check,online',
            'payment_reference' => 'nullable|string|max:100',
            'payment_card_id' => 'nullable|integer|exists:payment_cards,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $subscription = CompanySubscription::where('company_code', $companyCode)->firstOrFail();
        $company = Company::where('code', $companyCode)->firstOrFail();

        // إذا كان الدفع آجل، لا نسجل شيء ونكتفي بالإبقاء على المبلغ كدين
        if ($data['payment_type'] === 'deferred') {
            return redirect()->back()->with('info', '⏳ تم تأجيل الدفع. المبلغ المتبقي: ' . number_format($subscription->remaining_amount, 0) . ' دينار');
        }

        // التحقق من المبلغ للدفع الكاش
        if (empty($data['amount']) || $data['amount'] <= 0) {
            return redirect()->back()->with('error', '⚠️ يجب إدخال مبلغ صحيح للدفع الكاش');
        }

        // التحقق من طريقة الدفع للدفع الكاش
        if (empty($data['payment_method'])) {
            return redirect()->back()->with('error', '⚠️ يجب اختيار طريقة الدفع');
        }

        // معالجة الدفع الإلكتروني - إيداع المبلغ في البطاقة
        if ($data['payment_method'] === 'online' && !empty($data['payment_card_id'])) {
            try {
                $card = \App\Models\PaymentCard::findOrFail($data['payment_card_id']);

                // إيداع المبلغ في البطاقة (الشركة تدفع = المال يدخل للحساب)
                // deposit($amount, $description, $referenceType, $referenceId, $companyCode)
                $card->deposit(
                    $data['amount'],
                    "دفعة اشتراك من شركة {$company->name}",
                    'subscription',
                    $subscription->id,
                    $company->code
                );
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'خطأ في معالجة الدفع الإلكتروني: ' . $e->getMessage())
                    ->withInput();
            }
        }

        // تسجيل الدفعة
        $subscription->recordPayment(
            $data['amount'],
            $data['payment_method'],
            $data['payment_reference']
        );

        // تسجيل في التاريخ
        SubscriptionHistory::create([
            'company_code' => $companyCode,
            'subscription_id' => $subscription->id,
            'plan_type' => $subscription->plan_type,
            'base_fee' => $data['amount'],
            'percentage_rate' => 0,
            'orders_limit' => null,
            'orders_used' => 0,
            'start_date' => $subscription->start_date,
            'end_date' => $subscription->end_date,
            'actual_start_date' => now(),
            'actual_end_date' => null,
            'auto_renew' => false,
            'status' => 'active',
            'notes' => $data['notes'] ?? "دفعة بقيمة {$data['amount']}",
            'action_type' => 'payment',
            'created_by' => Auth::id(),
            'payment_status' => $subscription->payment_status,
            'paid_amount' => $data['amount'],
            'paid_at' => now(),
            'payment_method' => $data['payment_method'],
            'payment_reference' => $data['payment_reference'],
        ]);

        // إرسال إشعار الدفع
        $this->sendPaymentNotification($company, $subscription, $data['amount']);

        $statusMessage = $subscription->payment_status === 'paid'
            ? 'تم تسديد الاشتراك بالكامل ✅'
            : 'تم تسجيل الدفعة بنجاح. المبلغ المتبقي: ' . number_format($subscription->remaining_amount, 0) . ' دينار';

        return redirect()->back()->with('success', $statusMessage);
    }

    /**
     * إرسال إشعار للشركة المشتركة
     */
    private function sendSubscriptionNotification($company, $subscription, $actionType)
    {
        $planNames = [
            'monthly' => 'شهري',
            'yearly' => 'سنوي',
            'trial' => 'تجريبي',
            'percentage' => 'نسبة من الطلبات',
            'hybrid' => 'هجين',
        ];

        $actionMessages = [
            'created' => 'تم إنشاء اشتراك جديد',
            'renewed' => 'تم تجديد الاشتراك',
            'extended' => 'تم تمديد الاشتراك',
        ];

        $planName = $planNames[$subscription->plan_type] ?? $subscription->plan_type;
        $duration = $subscription->duration_quantity ?? 1;
        $durationText = $subscription->plan_type === 'yearly'
            ? "{$duration} سنة"
            : "{$duration} شهر";

        Notification::create([
            'company_code' => $company->code,
            'title' => $actionMessages[$actionType] ?? 'تحديث الاشتراك',
            'message' => "مرحباً شركة {$company->name}، {$actionMessages[$actionType]} بنجاح.\n" .
                "نوع الاشتراك: {$planName}\n" .
                "المدة: {$durationText}\n" .
                "تاريخ البداية: " . Carbon::parse($subscription->start_date)->format('Y/m/d') . "\n" .
                "تاريخ النهاية: " . ($subscription->end_date ? Carbon::parse($subscription->end_date)->format('Y/m/d') : 'غير محدد'),
            'type' => 'success',
            'sent_by' => Auth::id(),
        ]);
    }

    /**
     * إرسال إشعار دفعة
     */
    private function sendPaymentNotification($company, $subscription, $amount)
    {
        Notification::create([
            'company_code' => $company->code,
            'title' => 'تم استلام دفعة',
            'message' => "مرحباً شركة {$company->name}،\n" .
                "تم استلام دفعة بقيمة: " . number_format($amount, 0) . " دينار\n" .
                "حالة السداد: " . ($subscription->payment_status === 'paid' ? 'مسدد بالكامل ✅' : 'جزئي') . "\n" .
                "المبلغ المتبقي: " . number_format($subscription->remaining_amount, 0) . " دينار",
            'type' => 'info',
            'sent_by' => Auth::id(),
        ]);
    }

    /**
     * إرسال إشعار واتساب
     */
    private function sendWhatsAppNotification($company, $subscription, $actionType)
    {
        // التحقق من وجود رقم هاتف للشركة
        if (empty($company->phone)) {
            return;
        }

        // التحقق من إعدادات الواتساب
        $whatsappEnabled = config('services.whatsapp.enabled', false);
        $whatsappApiUrl = config('services.whatsapp.api_url');
        $whatsappToken = config('services.whatsapp.token');

        if (!$whatsappEnabled || !$whatsappApiUrl) {
            return;
        }

        $planNames = [
            'monthly' => 'شهري',
            'yearly' => 'سنوي',
            'trial' => 'تجريبي',
            'percentage' => 'نسبة من الطلبات',
            'hybrid' => 'هجين',
        ];

        $planName = $planNames[$subscription->plan_type] ?? $subscription->plan_type;
        $duration = $subscription->duration_quantity ?? 1;
        $durationText = $subscription->plan_type === 'yearly'
            ? "{$duration} سنة"
            : "{$duration} شهر";

        $message = "مرحباً شركة {$company->name} 👋\n\n";

        if ($actionType === 'renewed') {
            $message .= "✅ تم تجديد اشتراككم بنجاح!\n\n";
        } else {
            $message .= "✅ تم إنشاء اشتراك جديد بنجاح!\n\n";
        }

        $message .= "📋 تفاصيل الاشتراك:\n";
        $message .= "• نوع الاشتراك: {$planName}\n";
        $message .= "• المدة: {$durationText}\n";
        $message .= "• تاريخ البداية: " . Carbon::parse($subscription->start_date)->format('Y/m/d') . "\n";

        if ($subscription->end_date) {
            $message .= "• تاريخ النهاية: " . Carbon::parse($subscription->end_date)->format('Y/m/d') . "\n";
        }

        if ($subscription->base_fee > 0) {
            $message .= "• المبلغ: " . number_format($subscription->base_fee, 0) . " دينار\n";
        }

        $message .= "\nشكراً لثقتكم بنا! 🙏";

        try {
            Http::withToken($whatsappToken)->post($whatsappApiUrl, [
                'phone' => $this->formatPhoneNumber($company->phone),
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            Log::warning('فشل إرسال رسالة واتساب: ' . $e->getMessage());
        }
    }

    /**
     * تنسيق رقم الهاتف للواتساب
     */
    private function formatPhoneNumber($phone)
    {
        // إزالة المسافات والرموز
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // إضافة رمز الدولة للعراق إذا لم يكن موجوداً
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            $phone = '964' . substr($phone, 1);
        } elseif (strlen($phone) === 11 && substr($phone, 0, 2) === '07') {
            $phone = '964' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * مراقبة الاشتراكات (سوبر أدمن)
     */
    public function monitor()
    {
        $subscriptions = CompanySubscription::with('company')
            ->orderBy('end_date', 'asc')
            ->get()
            ->map(function ($subscription) {
                $subscription->days_remaining = now()->diffInDays($subscription->end_date, false);
                return $subscription;
            });

        $stats = [
            'total_active' => CompanySubscription::where('status', 'active')->count(),
            'total_expired' => CompanySubscription::where('status', 'expired')->count(),
            'total_suspended' => CompanySubscription::where('status', 'suspended')->count(),
            'expiring_soon' => CompanySubscription::where('status', 'active')
                ->where('end_date', '<=', now()->addDays(7))
                ->count(),
        ];

        return view('subscriptions.monitor', compact('subscriptions', 'stats'));
    }

    /**
     * تقارير مالية للاشتراكات (سوبر أدمن)
     */
    public function reports()
    {
        $stats = [
            'total_active' => CompanySubscription::where('status', 'active')->count(),
            'total_expired' => CompanySubscription::where('status', 'expired')->count(),
            'total_suspended' => CompanySubscription::where('status', 'suspended')->count(),
            'by_plan' => CompanySubscription::selectRaw('plan_type, count(*) as total')->groupBy('plan_type')->get(),
        ];

        $subscriptions = CompanySubscription::orderBy('created_at', 'desc')->take(50)->get();

        return view('subscriptions.reports', compact('stats', 'subscriptions'));
    }

    /**
     * عرض فاتورة اشتراك بسيطة
     */
    public function invoice($id)
    {
        $subscription = CompanySubscription::findOrFail($id);
        $company = Company::where('code', $subscription->company_code)->first();
        $ownerCompany = Company::where('code', 'SA')->first();

        return view('subscriptions.invoice', compact('subscription', 'company', 'ownerCompany'));
    }

    /**
     * عرض إيصال دفعة
     */
    public function paymentInvoice($id)
    {
        $payment = SubscriptionHistory::where('id', $id)
            ->where('action_type', 'payment')
            ->firstOrFail();

        $company = Company::where('code', $payment->company_code)->first();
        $ownerCompany = Company::where('code', 'SA')->first();

        // جلب الاشتراك الحالي
        $subscription = CompanySubscription::where('company_code', $payment->company_code)->first();

        // حساب إجمالي المدفوعات
        $totalPaid = SubscriptionHistory::where('company_code', $payment->company_code)
            ->where('action_type', 'payment')
            ->sum('paid_amount');

        return view('subscriptions.payment-invoice', compact('payment', 'company', 'subscription', 'totalPaid', 'ownerCompany'));
    }

    /**
     * تعطيل/تفعيل شركة - يمنع جميع حساباتها من الدخول
     */
    public function toggleSuspension($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        // عكس حالة التعطيل
        $company->is_suspended = !$company->is_suspended;
        $company->save();

        // تحديث حالة الاشتراك أيضاً
        $subscription = CompanySubscription::where('company_code', $companyCode)->first();
        if ($subscription) {
            $subscription->status = $company->is_suspended ? 'suspended' : 'active';
            $subscription->save();
        }

        $message = $company->is_suspended
            ? 'تم تعطيل الشركة وجميع حساباتها بنجاح'
            : 'تم تفعيل الشركة وحساباتها بنجاح';

        return redirect()->back()->with('success', $message);
    }

    /**
     * إنهاء الاشتراك نهائياً - لا يمكن الدخول إلا بإنشاء اشتراك جديد
     */
    public function terminateSubscription($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $subscription = CompanySubscription::where('company_code', $companyCode)->first();

        if ($subscription) {
            // حفظ في التاريخ
            SubscriptionHistory::create([
                'company_code' => $companyCode,
                'subscription_id' => $subscription->id,
                'plan_type' => $subscription->plan_type,
                'base_fee' => $subscription->base_fee ?? 0,
                'percentage_rate' => $subscription->percentage_rate ?? 0,
                'orders_limit' => $subscription->orders_limit ?? null,
                'orders_used' => $subscription->orders_used ?? 0,
                'start_date' => $subscription->start_date,
                'end_date' => $subscription->end_date,
                'actual_start_date' => $subscription->start_date,
                'actual_end_date' => now(),
                'auto_renew' => $subscription->auto_renew ?? false,
                'status' => 'expired',
                'notes' => $subscription->notes,
                'action_type' => 'terminated',
                'created_by' => Auth::id(),
            ]);

            $subscription->status = 'expired';
            $subscription->end_date = now();
            $subscription->save();
        }

        return redirect()->back()->with('success', 'تم إنهاء اشتراك شركة ' . $company->name . ' نهائياً');
    }

    /**
     * التقارير المالية الشاملة - محسّنة
     */
    public function financialReports(Request $request)
    {
        // الفلاتر
        $status = $request->get('status');
        $planType = $request->get('plan_type');
        $searchQuery = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $filterMonth = $request->get('filter_month'); // الشهر (مثال: 2025-01)
        $filterYear = $request->get('filter_year'); // السنة (مثال: 2025)

        // بناء الاستعلام
        $query = CompanySubscription::with('company');

        if ($status) {
            $query->where('status', $status);
        }

        if ($planType) {
            $query->where('plan_type', $planType);
        }

        if ($searchQuery) {
            $query->whereHas('company', function ($q) use ($searchQuery) {
                $q->where('name', 'LIKE', "%{$searchQuery}%");
            });
        }

        if ($dateFrom) {
            $query->where('start_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('start_date', '<=', $dateTo);
        }

        // الاشتراكات مع الفلاتر
        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(20);

        // ===== الإجمالي الكلي (بدون فلاتر) =====
        $grandTotalRevenue = CompanySubscription::where('status', 'active')
            ->where('plan_type', '!=', 'trial')
            ->sum('base_fee');

        $grandActiveCount = CompanySubscription::where('status', 'active')->count();
        $grandExpiredCount = CompanySubscription::where('status', 'expired')->count();
        $grandSuspendedCount = CompanySubscription::where('status', 'suspended')->count();

        // ===== الإحصائيات المفلترة (حسب التاريخ/الشهر/السنة) =====
        $statsQuery = CompanySubscription::query();

        // فلتر حسب الشهر (له أولوية على السنة)
        if ($filterMonth) {
            $statsQuery->whereYear('start_date', substr($filterMonth, 0, 4))
                ->whereMonth('start_date', substr($filterMonth, 5, 2));
        }
        // فلتر حسب السنة فقط (إذا لم يكن هناك فلتر شهري)
        elseif ($filterYear) {
            $statsQuery->whereYear('start_date', $filterYear);
        }
        // فلتر حسب التاريخ المحدد (من-إلى)
        else {
            if ($dateFrom) $statsQuery->where('start_date', '>=', $dateFrom);
            if ($dateTo) $statsQuery->where('start_date', '<=', $dateTo);
        }

        $activeCount = (clone $statsQuery)->where('status', 'active')->count();
        $expiredCount = (clone $statsQuery)->where('status', 'expired')->count();
        $suspendedCount = (clone $statsQuery)->where('status', 'suspended')->count();

        $totalRevenue = (clone $statsQuery)
            ->where('status', 'active')
            ->where('plan_type', '!=', 'trial')
            ->sum('base_fee');

        $averageSubscription = $activeCount > 0 ? $totalRevenue / $activeCount : 0;

        // إحصاءات حسب نوع الخطة (مفلترة)
        $byPlanTypeQuery = CompanySubscription::selectRaw('plan_type, count(*) as count, sum(base_fee) as revenue')
            ->where('status', 'active');

        if ($filterMonth) {
            $byPlanTypeQuery->whereYear('start_date', substr($filterMonth, 0, 4))
                ->whereMonth('start_date', substr($filterMonth, 5, 2));
        } elseif ($filterYear) {
            $byPlanTypeQuery->whereYear('start_date', $filterYear);
        } else {
            if ($dateFrom) $byPlanTypeQuery->where('start_date', '>=', $dateFrom);
            if ($dateTo) $byPlanTypeQuery->where('start_date', '<=', $dateTo);
        }

        $byPlanType = $byPlanTypeQuery->groupBy('plan_type')->get();

        // الاشتراكات القريبة من الانتهاء (7 أيام)
        $expiringSoon = CompanySubscription::with('company')
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->get();

        // تحديد إذا كانت هناك فلاتر نشطة
        $hasFilters = $filterMonth || $filterYear || $dateFrom || $dateTo;

        // قائمة الشركات للـ dropdown
        $allCompanies = Company::where('code', '!=', 'SA')
            ->select('code', 'name')
            ->orderBy('name')
            ->get();

        return view('subscriptions.financial-reports', compact(
            'totalRevenue',
            'grandTotalRevenue',
            'activeCount',
            'grandActiveCount',
            'expiredCount',
            'grandExpiredCount',
            'suspendedCount',
            'grandSuspendedCount',
            'subscriptions',
            'byPlanType',
            'averageSubscription',
            'expiringSoon',
            'status',
            'planType',
            'searchQuery',
            'dateFrom',
            'dateTo',
            'filterMonth',
            'filterYear',
            'hasFilters',
            'allCompanies'
        ));
    }

    /**
     * عرض تاريخ اشتراكات شركة معينة
     */
    public function subscriptionHistory(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        // الاشتراك الحالي
        $currentSubscription = CompanySubscription::where('company_code', $companyCode)->first();

        // جميع الاشتراكات السابقة مع الفلاتر
        $query = SubscriptionHistory::where('company_code', $companyCode);

        // فلتر العملية
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        // فلتر الخطة
        if ($request->filled('plan_type')) {
            $query->where('plan_type', $request->plan_type);
        }

        // فلتر الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلتر التاريخ من
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // فلتر التاريخ إلى
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $history = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // إجمالي المدفوعات الفعلية (من الاشتراك الحالي)
        $totalPaid = $currentSubscription ? ($currentSubscription->paid_amount ?? 0) : 0;

        // عدد الاشتراكات (إنشاء أو تجديد فقط)
        $subscriptionCount = SubscriptionHistory::where('company_code', $companyCode)
            ->whereIn('action_type', ['created', 'renewed'])
            ->count();

        return view('subscriptions.company-history', compact(
            'company',
            'currentSubscription',
            'history',
            'totalPaid',
            'subscriptionCount'
        ));
    }

    /**
     * صفحة إعدادات أسعار الاشتراكات
     */
    public function pricingSettings()
    {
        // التحقق من صلاحية السوبر أدمن
        if (Auth::user()->company_code !== 'SA') {
            return redirect('/')->with('error', 'ليس لديك صلاحية الوصول لهذه الصفحة');
        }

        // إعدادات الأسعار العامة
        $settings = SubscriptionPricing::getSettings();

        // الشركات مع أسعارها الخاصة
        $companies = Company::where('code', '!=', 'SA')
            ->with('customPricing')
            ->orderBy('name')
            ->get();

        // الأسعار الخاصة بالشركات
        $companyPrices = CompanySubscriptionPrice::where('is_active', true)->get()->keyBy('company_code');

        return view('subscriptions.settings', compact('settings', 'companies', 'companyPrices'));
    }

    /**
     * تحديث إعدادات الأسعار العامة
     */
    public function updatePricingSettings(Request $request)
    {
        // التحقق من صلاحية السوبر أدمن
        if (Auth::user()->company_code !== 'SA') {
            return redirect('/')->with('error', 'ليس لديك صلاحية الوصول لهذه الصفحة');
        }

        $validated = $request->validate([
            'standard_price_monthly' => 'required|numeric|min:0',
            'default_percentage_rate' => 'required|numeric|min:0|max:100',
            'default_fixed_order_fee' => 'required|numeric|min:0',
            'grace_period_days' => 'required|integer|min:1|max:30',
            'warning_days' => 'required|integer|min:1|max:15',
            'payment_due_days' => 'required|integer|min:1|max:30',
            'trial_days' => 'required|integer|min:1|max:365',
            'notes' => 'nullable|string|max:1000',
        ]);

        // السنوي = نفس السعر الشهري (لمدة 12 شهر)، القيمة المخزنة هي سعر شهري للمقارنة والحساب
        $validated['standard_price_yearly'] = $validated['standard_price_monthly'];

        $settings = SubscriptionPricing::first();
        if ($settings) {
            $settings->update($validated);
        } else {
            SubscriptionPricing::create($validated);
        }

        return redirect()->route('subscriptions.settings')
            ->with('success', '✅ تم تحديث إعدادات الأسعار بنجاح');
    }

    /**
     * تحديث/إنشاء سعر خاص بشركة
     */
    public function updateCompanyPricing(Request $request, $companyCode)
    {
        // التحقق من صلاحية السوبر أدمن
        if (Auth::user()->company_code !== 'SA') {
            return redirect('/')->with('error', 'ليس لديك صلاحية الوصول لهذه الصفحة');
        }

        // التحقق من وجود الشركة
        $company = Company::where('code', $companyCode)->firstOrFail();

        $validated = $request->validate([
            'price_per_user_monthly' => 'nullable|numeric|min:0',
            'price_per_user_yearly' => 'nullable|numeric|min:0',
            'custom_percentage_rate' => 'nullable|numeric|min:0|max:100',
            'custom_fixed_order_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // البحث عن السعر الخاص أو إنشاء جديد
        $companyPricing = CompanySubscriptionPrice::firstOrNew(['company_code' => $companyCode]);
        $companyPricing->fill($validated);
        $companyPricing->is_active = $request->has('is_active') ? true : ($companyPricing->exists ? $companyPricing->is_active : true);
        $companyPricing->save();

        return redirect()->route('subscriptions.settings')
            ->with('success', "✅ تم تحديث السعر الخاص بشركة {$company->name} بنجاح");
    }

    /**
     * حذف السعر الخاص بشركة
     */
    public function deleteCompanyPricing($companyCode)
    {
        // التحقق من صلاحية السوبر أدمن
        if (Auth::user()->company_code !== 'SA') {
            return redirect('/')->with('error', 'ليس لديك صلاحية الوصول لهذه الصفحة');
        }

        $companyPricing = CompanySubscriptionPrice::where('company_code', $companyCode)->first();

        if ($companyPricing) {
            $companyPricing->delete();
            return redirect()->route('subscriptions.settings')
                ->with('success', '✅ تم حذف السعر الخاص بالشركة بنجاح');
        }

        return redirect()->route('subscriptions.settings')
            ->with('error', '⚠️ لم يتم العثور على سعر خاص لهذه الشركة');
    }

    /**
     * زيادة عدد المستخدمين في الاشتراك النشط
     */
    public function addUsers(Request $request)
    {
        // التحقق من صلاحية السوبر أدمن
        if (Auth::user()->company_code !== 'SA') {
            return redirect('/')->with('error', 'ليس لديك صلاحية الوصول لهذه الصفحة');
        }

        $validated = $request->validate([
            'company_code' => 'required|string',
            'additional_users' => 'required|integer|min:1',
            'payment_type' => 'required|string|in:cash,deferred',
            'payment_method' => 'nullable|string|in:cash,bank_transfer,check,online',
            'payment_card_id' => 'nullable|integer|exists:payment_cards,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $companyCode = $validated['company_code'];
        $company = Company::where('code', $companyCode)->firstOrFail();

        // جلب الاشتراك النشط
        $subscription = CompanySubscription::where('company_code', $companyCode)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return redirect()->route('subscriptions.companies')
                ->with('error', '⚠️ لا يوجد اشتراك نشط لهذه الشركة');
        }

        $oldUsersCount = $subscription->users_count;
        $additionalUsers = $validated['additional_users'];
        $newUsersCount = $oldUsersCount + $additionalUsers;

        // حساب التكلفة الإضافية: شهر كامل لكل مستخدم جديد بغض النظر عن الأيام المتبقية
        $pricePerUser = $subscription->price_per_user ?? 0;
        $additionalCost = $additionalUsers * $pricePerUser;

        // تحديد حالة الدفع بناءً على نوع الدفع
        $paymentType = $validated['payment_type'];
        $paymentStatus = 'pending';
        $paidAmount = 0;
        $paymentReference = null;

        if ($paymentType === 'deferred') {
            // دفع آجل: لا يتم دفع شيء
            $paymentStatus = 'pending';
            $paidAmount = 0;

            // تحديث الاشتراك (زيادة المستخدمين والمبلغ فقط بدون تسجيل دفع)
            $subscription->users_count = $newUsersCount;
            $subscription->total_amount += $additionalCost;
            $subscription->base_fee += $additionalCost;
            // تحديث حالة الدفع الإجمالية بناءً على المبلغ المدفوع الحالي مقارنة بالمبلغ الجديد
            if ($subscription->paid_amount >= $subscription->base_fee) {
                $subscription->payment_status = 'paid';
            } elseif ($subscription->paid_amount > 0) {
                $subscription->payment_status = 'partial';
            } else {
                $subscription->payment_status = 'pending';
            }
            $subscription->save();
        } else {
            // دفع كاش: تحديث الاشتراك وتسجيل الدفع للمبلغ الإضافي
            $subscription->users_count = $newUsersCount;
            $subscription->total_amount += $additionalCost;
            $subscription->base_fee += $additionalCost;

            // معالجة الدفع حسب طريقة الدفع المختارة
            if (!empty($validated['payment_method'])) {
                if ($validated['payment_method'] === 'online' && !empty($validated['payment_card_id'])) {
                    // دفع إلكتروني - إيداع المبلغ في البطاقة مباشرة
                    try {
                        $card = \App\Models\PaymentCard::findOrFail($validated['payment_card_id']);

                        // إيداع المبلغ في البطاقة
                        $card->deposit(
                            $additionalCost,
                            "دفع زيادة مستخدمين لشركة {$companyCode} ({$additionalUsers} مستخدم)",
                            'users_upgrade',
                            $subscription->id,
                            $companyCode
                        );

                        $paymentStatus = 'paid';
                        $paidAmount = $additionalCost;
                        $paymentReference = 'CARD-' . $card->id . '-' . time();

                        // تحديث المبلغ المدفوع في الاشتراك
                        $subscription->paid_amount = ($subscription->paid_amount ?? 0) + $additionalCost;
                    } catch (\Exception $e) {
                        Log::error('Payment Card Error in addUsers: ' . $e->getMessage());
                        return redirect()->route('subscriptions.companies')
                            ->with('error', '⚠️ حدث خطأ في معالجة الدفع الإلكتروني: ' . $e->getMessage());
                    }
                } elseif (in_array($validated['payment_method'], ['cash', 'bank_transfer', 'check'])) {
                    // دفع نقدي أو تحويل بنكي أو شيك - تسجيل الدفع مباشرة
                    $paymentStatus = 'paid';
                    $paidAmount = $additionalCost;
                    $paymentReference = strtoupper($validated['payment_method']) . '-' . time();

                    // تسجيل الدفعة في الاشتراك
                    $subscription->paid_amount = ($subscription->paid_amount ?? 0) + $additionalCost;
                }
            }

            // تحديث حالة الدفع الإجمالية
            if ($subscription->paid_amount >= $subscription->base_fee) {
                $subscription->payment_status = 'paid';
            } elseif ($subscription->paid_amount > 0) {
                $subscription->payment_status = 'partial';
            } else {
                $subscription->payment_status = 'pending';
            }
            $subscription->save();
        }

        // إنشاء فاتورة للمبلغ الإضافي
        $invoice = SubscriptionInvoice::create([
            'invoice_number' => SubscriptionInvoice::generateInvoiceNumber(),
            'company_code' => $companyCode,
            'subscription_id' => $subscription->id,
            'invoice_type' => 'additional_user',
            'period_start' => now(),
            'period_end' => $subscription->end_date,
            'users_count' => $additionalUsers,
            'price_per_user' => $pricePerUser,
            'subtotal' => $additionalCost,
            'discount' => 0,
            'total_amount' => $additionalCost,
            'payment_status' => $paymentStatus,
            'paid_amount' => $paidAmount,
            'due_date' => now()->addDays(7),
            'payment_method' => $validated['payment_method'] ?? null,
            'payment_reference' => $paymentReference,
            'paid_at' => $paymentStatus === 'paid' ? now() : null,
            'notes' => "زيادة عدد المستخدمين من {$oldUsersCount} إلى {$newUsersCount} (شهر كامل لكل مستخدم). " . ($validated['notes'] ?? ''),
            'created_by' => Auth::id(),
        ]);

        // تسجيل في التاريخ
        SubscriptionHistory::create([
            'company_code' => $companyCode,
            'subscription_id' => $subscription->id,
            'plan_type' => $subscription->plan_type,
            'base_fee' => $additionalCost,
            'start_date' => now(),
            'end_date' => $subscription->end_date,
            'actual_start_date' => now(),
            'status' => 'active',
            'notes' => "زيادة عدد المستخدمين من {$oldUsersCount} إلى {$newUsersCount} - التكلفة: " . number_format($additionalCost, 0) . " دينار",
            'action_type' => 'additional_user',
            'created_by' => Auth::id(),
            'payment_status' => $paymentStatus,
            'payment_method' => $validated['payment_method'] ?? null,
        ]);

        $successMessage = "✅ تم زيادة عدد المستخدمين لشركة {$company->name} من {$oldUsersCount} إلى {$newUsersCount}. تم إصدار فاتورة بمبلغ " . number_format($additionalCost, 0) . " دينار";

        if ($paymentType === 'deferred') {
            $successMessage .= " ⏳ (دفع آجل - سيسدد لاحقاً)";
        } elseif ($paymentStatus === 'paid') {
            $successMessage .= " ✅ تم الدفع بنجاح";
        }

        return redirect()->route('subscriptions.companies')
            ->with('success', $successMessage);
    }
}
