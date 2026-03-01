@extends('layouts.app')

@section('page-title', 'تحرير اشتراك الشركة')

@section('content')
    <div class="grid grid-cols-1 gap-6">
        <!-- معلومات الشركة -->
        <div class="panel">
            <div class="mb-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('subscriptions.companies') }}" class="btn btn-outline-secondary btn-sm">
                        <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" viewBox="0 0 24 24" fill="none">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        رجوع
                    </a>
                    <h5 class="text-lg font-semibold dark:text-white-light">
                        تحرير اشتراك: {{ $company->name }}
                    </h5>
                </div>
                <span class="badge badge-outline-info">{{ $company->code }}</span>
            </div>

            <!-- بطاقة المعلومات -->
            <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                    <div class="mb-1 text-xs text-gray-500">اسم الشركة</div>
                    <div class="font-semibold">{{ $company->name }}</div>
                </div>
                <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                    <div class="mb-1 text-xs text-gray-500">كود الشركة</div>
                    <div class="font-semibold">{{ $company->code }}</div>
                </div>
                <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                    <div class="mb-1 text-xs text-gray-500">الاشتراك الحالي</div>
                    @if ($subscription)
                        @php
                            $statusColors = ['active' => 'success', 'expired' => 'danger', 'suspended' => 'warning'];
                            $statusLabels = ['active' => 'نشط', 'expired' => 'منتهي', 'suspended' => 'معلق'];
                        @endphp
                        <span class="badge badge-outline-{{ $statusColors[$subscription->status] ?? 'secondary' }}">
                            {{ $statusLabels[$subscription->status] ?? 'غير محدد' }}
                        </span>
                    @else
                        <span class="badge badge-outline-secondary">غير مشترك</span>
                    @endif
                </div>
                <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                    <div class="mb-1 text-xs text-gray-500">حالة السداد</div>
                    @if ($subscription)
                        @php
                            $paymentColors = ['paid' => 'success', 'partial' => 'warning', 'pending' => 'danger'];
                            $paymentLabels = ['paid' => 'مسدد ✅', 'partial' => 'جزئي', 'pending' => 'غير مسدد'];
                        @endphp
                        <span class="badge badge-outline-{{ $paymentColors[$subscription->payment_status ?? 'pending'] }}">
                            {{ $paymentLabels[$subscription->payment_status ?? 'pending'] ?? 'غير محدد' }}
                        </span>
                    @else
                        <span class="badge badge-outline-secondary">-</span>
                    @endif
                </div>
            </div>

            <!-- تنبيه الاشتراك السابق -->
            @if ($lastSubscription)
                <div class="alert alert-info mb-5">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <strong>آخر اشتراك:</strong>
                        سعر المستخدم كان <span
                            class="font-bold">{{ number_format($lastSubscription->base_fee / max(1, $lastSubscription->duration_quantity ?? 1), 0) }}</span>
                        دينار
                    </div>
                </div>
            @endif

            <!-- تنبيه السعر الخاص -->
            @if ($companyPricing && $companyPricing->price_per_user_monthly)
                <div class="alert alert-success mb-5">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <strong>هذه الشركة لديها سعر خاص:</strong>
                        {{ number_format($companyPricing->price_per_user_monthly, 0) }} دينار للمستخدم الواحد
                    </div>
                </div>
            @endif
        </div>

        <!-- نموذج الاشتراك -->
        <form action="{{ route('subscriptions.subscribe', $company->code) }}" method="POST" class="panel"
            x-data="subscriptionForm()" id="subscriptionForm">
            @csrf

            <div class="mb-5">
                <h6 class="text-base font-semibold">تفاصيل الاشتراك</h6>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                <!-- نوع الخطة -->
                <div>
                    <label class="mb-2 block font-semibold">
                        نوع الخطة <span class="text-danger">*</span>
                    </label>
                    <select name="plan_type" x-model="planType" class="form-select" required @change="updateCalculations()">
                        <option value="">اختر نوع الخطة</option>
                        <option value="monthly">شهري (حسب عدد المستخدمين)</option>
                        <option value="yearly">سنوي (حسب عدد المستخدمين)</option>
                        <option value="percentage">نسبة من الطلبات</option>
                        <option value="hybrid">هجين (رسم شهري + نسبة من الطلبات)</option>
                        <option value="trial">تجريبي</option>
                    </select>
                </div>

                <!-- عدد المستخدمين -->
                <div x-show="planType === 'monthly' || planType === 'yearly' || planType === 'hybrid'">
                    <label class="mb-2 block font-semibold">
                        عدد المستخدمين <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="users_count" x-model="usersCount" class="form-input" min="1"
                        value="1" @input="updateCalculations()">
                    <small class="text-gray-500">عدد المستخدمين المسموح بهم في الشركة</small>
                </div>

                <!-- سعر المستخدم -->
                <div x-show="planType === 'monthly' || planType === 'yearly' || planType === 'hybrid'">
                    <label class="mb-2 block font-semibold">
                        سعر المستخدم الواحد (دينار)
                    </label>
                    <select name="price_per_user" x-model="pricePerUser" class="form-select" @change="updateCalculations()">
                        <option value="{{ $pricingSettings->standard_price_monthly }}">السعر الافتراضي:
                            {{ number_format($pricingSettings->standard_price_monthly, 0) }} دينار</option>
                        @if ($companyPricing && $companyPricing->price_per_user_monthly)
                            <option value="{{ $companyPricing->price_per_user_monthly }}">السعر الخاص بالشركة:
                                {{ number_format($companyPricing->price_per_user_monthly, 0) }} دينار</option>
                        @endif
                    </select>
                    <small class="text-gray-500">
                        اختر السعر المناسب للمستخدم الواحد في الشهر
                    </small>
                </div>

                <!-- عدد الأشهر (للشهري والهجين) -->
                <div x-show="planType === 'monthly' || planType === 'hybrid'">
                    <label class="mb-2 block font-semibold">
                        عدد الأشهر <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="duration_quantity" x-model="durationQuantity" class="form-input"
                        min="1" max="12" value="1" @input="updateCalculations()">
                    <small class="text-gray-500">إجمالي الأيام: <strong x-text="durationQuantity * 30"></strong> يوم</small>
                </div>

                <!-- عدد السنوات (للسنوي) -->
                <div x-show="planType === 'yearly'">
                    <label class="mb-2 block font-semibold">
                        عدد السنوات <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="years_count" x-model="yearsCount" class="form-input" min="1"
                        max="10" value="1" @input="updateCalculations()">
                    <small class="text-gray-500">إجمالي الأيام: <strong x-text="yearsCount * 365"></strong> يوم</small>
                </div>

                <!-- نوع رسوم الطلبات (لنسبة الطلبات والهجين) -->
                <div x-show="planType === 'percentage' || planType === 'hybrid'">
                    <label class="mb-2 block font-semibold">
                        نوع رسوم الطلبات <span class="text-danger">*</span>
                    </label>
                    <select name="order_fee_type" x-model="orderFeeType" class="form-select">
                        <option value="percentage">نسبة مئوية من قيمة الطلب (%)</option>
                        <option value="fixed">مبلغ ثابت على كل طلب</option>
                    </select>
                </div>

                <!-- نسبة من الطلبات -->
                <div x-show="(planType === 'percentage' || planType === 'hybrid') && orderFeeType === 'percentage'">
                    <label class="mb-2 block font-semibold">
                        نسبة من الطلبات (%) <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="percentage_rate" class="form-input" step="0.01" min="0"
                        max="100" value="{{ $pricingSettings->default_percentage_rate }}" placeholder="مثال: 5">
                    <small class="text-gray-500">النسبة المئوية من قيمة كل طلب</small>
                </div>

                <!-- مبلغ ثابت لكل طلب -->
                <div x-show="(planType === 'percentage' || planType === 'hybrid') && orderFeeType === 'fixed'">
                    <label class="mb-2 block font-semibold">
                        مبلغ ثابت على كل طلب (دينار) <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="fixed_order_fee" class="form-input" min="0"
                        value="{{ $pricingSettings->default_fixed_order_fee }}" placeholder="مثال: 1000">
                </div>

                <!-- حد الطلبات -->
                <div x-show="planType === 'percentage'">
                    <label class="mb-2 block font-semibold">
                        حد الطلبات <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="orders_limit" class="form-input" min="1"
                        placeholder="مثال: 100">
                    <small class="text-gray-500">ينتهي الاشتراك عند الوصول لهذا الحد</small>
                </div>

                <!-- حد الطلبات للهجين (اختياري) -->
                <div x-show="planType === 'hybrid'">
                    <label class="mb-2 block font-semibold">
                        حد الطلبات (اختياري)
                    </label>
                    <input type="number" name="orders_limit" class="form-input" min="1" placeholder="بدون حد">
                    <small class="text-gray-500">اتركه فارغاً للطلبات غير المحدودة</small>
                </div>

                <!-- تاريخ البداية -->
                <div>
                    <label class="mb-2 block font-semibold">
                        تاريخ البداية <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="start_date" class="form-input" :value="startDate" required>
                </div>

                <!-- تاريخ النهاية -->
                <div x-show="planType !== 'percentage'">
                    <label class="mb-2 block font-semibold">تاريخ النهاية</label>
                    <input type="date" name="end_date" class="form-input bg-gray-100" :value="endDate" readonly>
                    <small class="text-gray-500">يتم حسابه تلقائياً</small>
                </div>

                <!-- نوع الدفع (كاش/آجل) -->
                <div x-show="planType && planType !== 'trial' && planType !== 'percentage'">
                    <label class="mb-2 block font-semibold">نوع الدفع <span class="text-danger">*</span></label>
                    <select name="payment_type" class="form-select" x-model="paymentType" @change="updatePaymentType()"
                        required>
                        <option value="">اختر نوع الدفع</option>
                        <option value="cash">💵 كاش (دفع فوري)</option>
                        <option value="deferred">📋 آجل (دفع لاحقاً)</option>
                    </select>
                    <small class="text-gray-500" x-show="paymentType === 'deferred'">⚠️ سيتم تسجيل المبلغ كدين على
                        الشركة</small>
                </div>

                <!-- طريقة الدفع (تظهر فقط عند اختيار كاش) -->
                <div x-show="planType && planType !== 'trial' && planType !== 'percentage' && paymentType === 'cash'">
                    <label class="mb-2 block font-semibold">طريقة الدفع <span class="text-danger">*</span></label>
                    <select name="payment_method" class="form-select" id="payment_method" x-model="paymentMethod"
                        @change="updatePaymentMethod()" x-bind:required="paymentType === 'cash'">
                        <option value="">اختر طريقة الدفع</option>
                        <option value="cash">نقدي</option>
                        <option value="bank_transfer">تحويل بنكي</option>
                        <option value="check">شيك</option>
                        <option value="online">دفع إلكتروني</option>
                    </select>
                </div>

                <!-- اختيار بطاقة الدفع (تظهر فقط عند الدفع الكاش وطريقة الدفع إلكتروني) -->
                <div
                    x-show="planType && planType !== 'trial' && planType !== 'percentage' && paymentType === 'cash' && paymentMethod === 'online'">
                    <label class="mb-2 block font-semibold">اختر البطاقة <span class="text-danger">*</span></label>
                    <select name="payment_card_id" class="form-select" x-model="paymentCardId"
                        x-bind:required="paymentType === 'cash' && paymentMethod === 'online'">
                        <option value="">-- اختر بطاقة الدفع --</option>
                        @foreach ($paymentCards ?? [] as $card)
                            <option value="{{ $card->id }}">{{ $card->card_name }} ({{ $card->card_number_masked }})
                                - الرصيد:
                                {{ number_format($card->current_balance, 0) }} دينار</option>
                        @endforeach
                    </select>
                    <small class="text-gray-500">سيتم إيداع المبلغ في البطاقة المختارة</small>
                </div>

                <!-- المبلغ المدفوع (يظهر فقط عند الدفع الكاش) -->
                <div x-show="planType && planType !== 'trial' && planType !== 'percentage' && paymentType === 'cash'">
                    <label class="mb-2 block font-semibold">المبلغ المدفوع (دينار) <span
                            class="text-danger">*</span></label>
                    <input type="number" name="paid_amount" class="form-input" min="0" x-model="paidAmount"
                        :max="totalAmount" @input="calculateRemaining()" x-bind:required="paymentType === 'cash'"
                        placeholder="مثال: 50000">
                    <small class="text-gray-500">
                        <span x-show="paidAmount > 0 && paidAmount < totalAmount" class="text-warning">
                            ⚠️ دفع جزئي - المتبقي: <strong x-text="formatCurrency(totalAmount - paidAmount)"></strong>
                            دينار
                        </span>
                        <span x-show="paidAmount >= totalAmount" class="text-success">
                            ✅ دفع كامل
                        </span>
                        <span x-show="!paidAmount || paidAmount <= 0">
                            أدخل المبلغ المدفوع
                        </span>
                    </small>
                </div>

                <!-- رسالة الدفع الآجل -->
                <div x-show="planType && planType !== 'trial' && planType !== 'percentage' && paymentType === 'deferred'"
                    class="md:col-span-2">
                    <div class="bg-warning/10 border border-warning rounded-lg p-4">
                        <div class="flex items-center gap-2 text-warning">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold">دفع آجل</span>
                        </div>
                        <p class="text-sm mt-2 text-gray-600">
                            سيتم تسجيل المبلغ الإجمالي (<strong x-text="formatCurrency(totalAmount)"></strong> دينار) كدين
                            على الشركة.
                            يمكن تسديد المبلغ لاحقاً من خلال زر "تسجيل دفعة" في قائمة الشركات.
                        </p>
                    </div>
                </div>

                <!-- رقم المرجع (يظهر فقط عند الدفع الكاش) -->
                <div x-show="planType && planType !== 'trial' && planType !== 'percentage' && paymentType === 'cash'">
                    <label class="mb-2 block font-semibold">رقم الإيصال/المرجع</label>
                    <input type="text" name="payment_reference" class="form-input" placeholder="رقم الإيصال">
                </div>

                <!-- التجديد التلقائي -->
                <div class="flex items-center gap-2 pt-6" x-show="planType !== 'percentage'">
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="auto_renew" class="peer sr-only" value="1">
                        <div
                            class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:top-[2px] after:start-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all peer-checked:bg-primary peer-checked:after:translate-x-full">
                        </div>
                        <span class="ms-3 text-sm font-medium">التجديد التلقائي</span>
                    </label>
                </div>

                <!-- ملاحظات -->
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="mb-2 block font-semibold">ملاحظات</label>
                    <textarea name="notes" class="form-textarea" rows="2" placeholder="أي ملاحظات إضافية"></textarea>
                </div>
            </div>

            <!-- ملخص الاشتراك والتكلفة -->
            <div class="mt-5 rounded-lg border-2 border-primary bg-primary/5 p-5"
                x-show="planType === 'monthly' || planType === 'yearly' || planType === 'hybrid'">
                <h6 class="mb-4 font-semibold text-primary flex items-center gap-2">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1zm1-4a1 1 0 100 2h.01a1 1 0 100-2H7zm2 1a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm4-4a1 1 0 100 2h.01a1 1 0 100-2H13zM9 9a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zM7 8a1 1 0 000 2h.01a1 1 0 000-2H7z"
                            clip-rule="evenodd" />
                    </svg>
                    ملخص التكلفة
                </h6>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div>
                        <span class="text-sm text-gray-600">عدد المستخدمين:</span>
                        <div class="text-lg font-bold" x-text="usersCount"></div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">سعر المستخدم:</span>
                        <div class="text-lg font-bold" x-text="formatCurrency(pricePerUser) + ' دينار'"></div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">المدة:</span>
                        <div class="text-lg font-bold">
                            <span x-show="planType === 'monthly'" x-text="durationQuantity + ' شهر'"></span>
                            <span x-show="planType === 'yearly'"
                                x-text="yearsCount + ' سنة (' + (yearsCount * 12) + ' شهر)'"></span>
                        </div>
                    </div>
                    <div class="bg-primary/10 rounded-lg p-3">
                        <span class="text-sm text-gray-600">إجمالي المبلغ:</span>
                        <div class="text-2xl font-bold text-primary" x-text="formatCurrency(totalAmount) + ' دينار'">
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-sm text-gray-500" x-show="planType === 'monthly'">
                    <strong>طريقة الحساب:</strong> <span x-text="usersCount"></span> مستخدم × <span
                        x-text="formatCurrency(pricePerUser)"></span> دينار × <span x-text="durationQuantity"></span> شهر
                    = <span x-text="formatCurrency(totalAmount)"></span> دينار
                </div>
                <div class="mt-3 text-sm text-gray-500" x-show="planType === 'yearly'">
                    <strong>طريقة الحساب:</strong> <span x-text="usersCount"></span> مستخدم × <span
                        x-text="formatCurrency(pricePerUser)"></span> دينار × 12 شهر × <span x-text="yearsCount"></span>
                    سنة = <span x-text="formatCurrency(totalAmount)"></span> دينار
                </div>
                <div class="mt-3 text-sm text-gray-500" x-show="planType === 'hybrid'">
                    <strong>طريقة الحساب:</strong> <span x-text="usersCount"></span> مستخدم × <span
                        x-text="formatCurrency(pricePerUser)"></span> دينار × <span x-text="durationQuantity"></span> شهر
                    = <span x-text="formatCurrency(totalAmount)"></span> دينار<br>
                    <span class="text-warning">+ نسبة/مبلغ من كل طلب يضاف تلقائياً</span>
                </div>
            </div>

            <!-- معلومات فترة السماح -->
            <div class="mt-5 rounded-lg bg-warning/10 border border-warning p-4"
                x-show="planType === 'monthly' || planType === 'yearly' || planType === 'hybrid'">
                <h6 class="font-semibold text-warning flex items-center gap-2 mb-2">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    معلومات مهمة
                </h6>
                <ul class="text-sm space-y-1 text-gray-600">
                    <li>• فترة السماح بعد انتهاء الاشتراك: <strong>{{ $pricingSettings->grace_period_days }} أيام</strong>
                    </li>
                    <li>• مهلة الدفع: <strong>{{ $pricingSettings->payment_due_days }} أيام</strong> من تاريخ البدء</li>
                    <li>• إذا لم يتم الدفع خلال المهلة، سيتم تعليق الحساب</li>
                    <li>• أيام السماح المستخدمة ستخصم من الاشتراك التالي</li>
                </ul>
            </div>

            <!-- الأزرار -->
            <div class="mt-6 flex items-center justify-between border-t pt-5">
                <a href="{{ route('subscriptions.companies') }}" class="btn btn-outline-danger">إلغاء</a>
                <div class="flex gap-2">
                    <button type="button" onclick="printInvoice()" class="btn btn-outline-info"
                        x-show="planType === 'monthly' || planType === 'yearly' || planType === 'hybrid'">
                        <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        طباعة الفاتورة
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" viewBox="0 0 24 24" fill="none">
                            <path d="M5 13L9 17L19 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        حفظ الاشتراك
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function subscriptionForm() {
            return {
                planType: '',
                usersCount: 1,
                pricePerUser: {{ $companyPricing?->price_per_user_monthly ?? $pricingSettings->standard_price_monthly }},
                durationQuantity: 1,
                yearsCount: 1,
                orderFeeType: 'percentage',
                startDate: '{{ date('Y-m-d') }}',
                endDate: '',
                totalAmount: 0,
                paymentType: '', // كاش أو آجل
                paymentMethod: '',
                paymentCardId: '',
                paidAmount: 0,

                init() {
                    this.updateCalculations();
                },

                updateCalculations() {
                    // حساب إجمالي المبلغ - السعر المختار هو سعر الشهر الواحد
                    if (this.planType === 'monthly') {
                        this.totalAmount = this.usersCount * this.pricePerUser * this.durationQuantity;
                        this.calculateEndDate(30 * this.durationQuantity);
                    } else if (this.planType === 'yearly') {
                        // للسنوي: السعر × 12 شهر × عدد السنوات × عدد المستخدمين
                        this.totalAmount = this.usersCount * this.pricePerUser * 12 * this.yearsCount;
                        this.calculateEndDate(365 * this.yearsCount);
                    } else if (this.planType === 'hybrid') {
                        // الهجين: رسم شهري ثابت + نسبة من الطلبات
                        this.totalAmount = this.usersCount * this.pricePerUser * this.durationQuantity;
                        this.calculateEndDate(30 * this.durationQuantity);
                    } else if (this.planType === 'trial') {
                        this.totalAmount = 0;
                        this.calculateEndDate(7);
                    } else {
                        this.totalAmount = 0;
                        this.endDate = '';
                    }

                    // إعادة ضبط المبلغ المدفوع عند تغيير نوع الخطة
                    if (this.paymentType === 'cash') {
                        this.paidAmount = this.totalAmount;
                    }
                },

                calculateEndDate(days) {
                    const today = new Date();
                    const endDate = new Date(today);
                    endDate.setDate(endDate.getDate() + days);
                    this.endDate = endDate.toISOString().split('T')[0];
                },

                formatCurrency(value) {
                    return Number(value).toLocaleString('en-US');
                },

                updatePaymentType() {
                    // إعادة تعيين القيم عند تغيير نوع الدفع
                    if (this.paymentType === 'deferred') {
                        // دفع آجل: إعادة تعيين كل شيء
                        this.paymentMethod = '';
                        this.paymentCardId = '';
                        this.paidAmount = 0;
                    } else if (this.paymentType === 'cash') {
                        // دفع كاش: تعيين المبلغ المدفوع = الإجمالي افتراضياً
                        this.paidAmount = this.totalAmount;
                    }
                },

                updatePaymentMethod() {
                    // إعادة تعيين البطاقة عند تغيير طريقة الدفع
                    if (this.paymentMethod !== 'online') {
                        this.paymentCardId = '';
                    }
                },

                calculateRemaining() {
                    // لا نحتاج حساب - يتم عرضه في الواجهة
                }
            }
        }

        function printInvoice() {
            const form = document.getElementById('subscriptionForm');
            const formData = new FormData(form);

            // فتح نافذة طباعة بسيطة
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html dir="rtl">
                <head>
                    <title>فاتورة اشتراك</title>
                    <style>
                        body { font-family: 'Segoe UI', Tahoma, sans-serif; padding: 20px; }
                        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                        .info { margin-bottom: 20px; }
                        .info div { margin: 5px 0; }
                        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                        th, td { border: 1px solid #ddd; padding: 10px; text-align: right; }
                        th { background: #f5f5f5; }
                        .total { font-size: 1.5em; font-weight: bold; text-align: center; margin-top: 20px; }
                        @media print { body { -webkit-print-color-adjust: exact; } }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>فاتورة اشتراك</h1>
                        <p>التاريخ: ${new Date().toLocaleDateString('ar-EG')}</p>
                    </div>
                    <div class="info">
                        <div><strong>الشركة:</strong> {{ $company->name }}</div>
                        <div><strong>كود الشركة:</strong> {{ $company->code }}</div>
                    </div>
                    <table>
                        <tr><th>البند</th><th>القيمة</th></tr>
                        <tr><td>نوع الاشتراك</td><td id="plan-type"></td></tr>
                        <tr><td>عدد المستخدمين</td><td id="users-count"></td></tr>
                        <tr><td>سعر المستخدم</td><td id="price-per-user"></td></tr>
                        <tr><td>المدة</td><td id="duration"></td></tr>
                        <tr><td>تاريخ البداية</td><td id="start-date"></td></tr>
                        <tr><td>تاريخ النهاية</td><td id="end-date"></td></tr>
                    </table>
                    <div class="total">إجمالي المبلغ: <span id="total-amount"></span> دينار</div>
                    <script>
                        document.getElementById('plan-type').textContent = '${formData.get('plan_type') === 'monthly' ? 'شهري' : 'سنوي'}';
                        document.getElementById('users-count').textContent = '${formData.get('users_count')} مستخدم';
                        document.getElementById('price-per-user').textContent = Number('${formData.get('price_per_user')}').toLocaleString('en-US') + ' دينار';
                        document.getElementById('duration').textContent = '${formData.get('plan_type') === 'monthly' ? formData.get('duration_quantity') + ' شهر' : formData.get('years_count') + ' سنة'}';
                        document.getElementById('start-date').textContent = '${formData.get('start_date')}';
                        document.getElementById('end-date').textContent = document.querySelector('[name="end_date"]').value;
                        
                        // حساب المبلغ
                        let total = 0;
                        if ('${formData.get('plan_type')}' === 'monthly') {
                            total = ${formData.get('users_count')} * ${formData.get('price_per_user')} * ${formData.get('duration_quantity')};
                        } else {
                            total = ${formData.get('users_count')} * ${formData.get('price_per_user')} * 12 * ${formData.get('years_count')};
                        }
                        document.getElementById('total-amount').textContent = total.toLocaleString('en-US');
                        
                        window.print();
                    <\/script>
                </body>
                </html>
            `);
        }
    </script>
@endsection
