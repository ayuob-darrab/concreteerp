@extends('layouts.app')

@section('page-title', 'تفاصيل الشركة والاشتراكات - ' . $company->name)

@section('content')
    <div class="grid grid-cols-1 gap-6">
        {{-- رأس الصفحة --}}
        <div class="panel">
            <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-3 flex-wrap">
                    <a href="{{ route('subscriptions.companies') }}" class="btn btn-outline-secondary btn-sm">
                        <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" viewBox="0 0 24 24" fill="none">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        رجوع لقائمة الاشتراكات
                    </a>
                    <h5 class="text-lg font-semibold dark:text-white-light">
                        عرض تفاصيل: {{ $company->name }}
                    </h5>
                    <span class="badge badge-outline-info">{{ $company->code }}</span>
                    @if ($company->is_suspended)
                        <span class="badge badge-outline-danger">معطلة</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- 1. تفاصيل الشركة العامة --}}
        <div class="panel">
            <h6 class="mb-4 text-base font-semibold dark:text-white-light border-b border-[#e0e6ed] dark:border-[#1b2e4b] pb-2">
                تفاصيل الشركة العامة
            </h6>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                    <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">اسم الشركة</div>
                    <div class="font-semibold dark:text-white">{{ $company->name }}</div>
                </div>
                <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                    <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">كود الشركة</div>
                    <div class="font-semibold dark:text-white">{{ $company->code }}</div>
                </div>
                <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                    <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">المحافظة</div>
                    <div class="font-semibold dark:text-white">{{ $company->city->name ?? '—' }}</div>
                </div>
                <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                    <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">مدير الشركة</div>
                    <div class="font-semibold dark:text-white">{{ $company->managername ?? '—' }}</div>
                </div>
                <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                    <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">الهاتف</div>
                    <div class="font-semibold dark:text-white">{{ $company->phone ?? '—' }}</div>
                </div>
                <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                    <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">البريد الإلكتروني</div>
                    <div class="font-semibold dark:text-white">{{ $company->email ?? '—' }}</div>
                </div>
                <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b] sm:col-span-2">
                    <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">العنوان</div>
                    <div class="font-semibold dark:text-white">{{ $company->address ?? '—' }}</div>
                </div>
                <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                    <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">سعر إنشاء الشركة</div>
                    <div class="font-semibold dark:text-white">{{ number_format($company->creation_price ?? 0, 0) }} د.ع</div>
                </div>
                <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                    <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">عدد الفروع</div>
                    <div class="font-semibold dark:text-white">{{ $company->branches_count ?? $company->branches()->count() }}</div>
                </div>
                <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                    <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">عدد المستخدمين</div>
                    <div class="font-semibold dark:text-white">{{ $company->users_count ?? $company->users()->count() }}</div>
                </div>
                <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                    <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">تاريخ الإنشاء</div>
                    <div class="font-semibold dark:text-white">{{ $company->created_at?->format('Y-m-d') ?? '—' }}</div>
                </div>
            </div>
        </div>

        {{-- 2. تفاصيل الاشتراك الحالي --}}
        <div class="panel">
            <h6 class="mb-4 text-base font-semibold dark:text-white-light border-b border-[#e0e6ed] dark:border-[#1b2e4b] pb-2">
                تفاصيل الاشتراك الحالي
            </h6>
            @if ($subscription)
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                        <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">نوع الخطة</div>
                        <span class="badge badge-outline-success">{{ $planLabels[$subscription->plan_type] ?? $subscription->plan_type }}</span>
                    </div>
                    <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                        <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">الحالة</div>
                        @if ($subscription->status === 'active')
                            <span class="badge badge-outline-success">نشط</span>
                        @elseif ($subscription->status === 'expired')
                            <span class="badge badge-outline-danger">منتهي</span>
                        @elseif ($subscription->status === 'suspended')
                            <span class="badge badge-outline-warning">معلق</span>
                        @else
                            <span class="badge badge-outline-secondary">{{ $subscription->status }}</span>
                        @endif
                    </div>
                    <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                        <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">تاريخ البداية</div>
                        <div class="font-semibold dark:text-white">{{ $subscription->start_date?->format('Y-m-d') ?? '—' }}</div>
                    </div>
                    <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                        <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">تاريخ النهاية</div>
                        <div class="font-semibold dark:text-white">{{ $subscription->end_date ? $subscription->end_date->format('Y-m-d') : 'مفتوح' }}</div>
                    </div>
                    <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                        <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">المستخدمين المسموح</div>
                        <div class="font-semibold dark:text-white">{{ $subscription->users_count ? $subscription->users_count . ' مستخدم' : 'غير محدد' }}</div>
                    </div>
                    <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                        <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">المبلغ الأساسي</div>
                        <div class="font-semibold dark:text-white">{{ number_format($subscription->base_fee ?? 0, 0) }} د.ع</div>
                    </div>
                    <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                        <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">المدفوع</div>
                        <div class="font-semibold text-success">{{ number_format($subscription->paid_amount ?? 0, 0) }} د.ع</div>
                    </div>
                    <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                        <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">حالة السداد</div>
                        @if (($subscription->payment_status ?? '') === 'paid')
                            <span class="badge badge-outline-success">مسدد</span>
                        @elseif (($subscription->payment_status ?? '') === 'partial')
                            <span class="badge badge-outline-warning">جزئي</span>
                        @else
                            <span class="badge badge-outline-danger">غير مسدد</span>
                        @endif
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('subscriptions.history', $company->code) }}" class="btn btn-sm btn-outline-info">سجل الاشتراكات الكامل</a>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">لا يوجد اشتراك حالياً.</p>
            @endif
        </div>

        {{-- 3. سجل الاشتراكات --}}
        <div class="panel">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-[#e0e6ed] dark:border-[#1b2e4b] pb-2">
                <h6 class="text-base font-semibold dark:text-white-light">
                    سجل الاشتراكات (آخر {{ $limitLabel($limitHistory) }} عملية)
                </h6>
                <form method="GET" action="{{ route('subscriptions.company-details', $company->code) }}" class="flex items-center gap-2">
                    <input type="hidden" name="limit_invoices" value="{{ $limitInvoices }}">
                    <input type="hidden" name="limit_payment" value="{{ $limitPayment }}">
                    <input type="hidden" name="limit_company_cards" value="{{ $limitCompanyCards }}">
                    <label for="limit_history" class="text-sm text-gray-600 dark:text-gray-400">عرض:</label>
                    <select name="limit_history" id="limit_history" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        <option value="25" {{ $limitHistory === '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $limitHistory === '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $limitHistory === '100' ? 'selected' : '' }}>100</option>
                        <option value="all" {{ $limitHistory === 'all' ? 'selected' : '' }}>الكل</option>
                    </select>
                </form>
            </div>
            @if ($subscriptionHistory->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">لا توجد عمليات مسجلة.</p>
            @else
                <div class="table-responsive">
                    <table class="table-hover">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>نوع العملية</th>
                                <th>الخطة</th>
                                <th>الحالة</th>
                                <th>من - إلى</th>
                                <th>المبلغ / المدفوع</th>
                                <th>بواسطة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subscriptionHistory as $h)
                                <tr>
                                    <td>{{ $h->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @php
                                            $actionLabels = ['created' => 'إنشاء', 'renewed' => 'تجديد', 'extended' => 'تمديد', 'payment' => 'دفعة', 'terminated' => 'إنهاء', 'expired' => 'انتهاء', 'suspended' => 'تعطيل', 'updated' => 'تحديث'];
                                        @endphp
                                        <span class="badge badge-outline-secondary">{{ $actionLabels[$h->action_type] ?? $h->action_type }}</span>
                                    </td>
                                    <td>{{ $planLabels[$h->plan_type] ?? $h->plan_type }}</td>
                                    <td>
                                        @if ($h->status === 'active')
                                            <span class="badge badge-outline-success">نشط</span>
                                        @elseif ($h->status === 'expired')
                                            <span class="badge badge-outline-danger">منتهي</span>
                                        @else
                                            <span class="badge badge-outline-secondary">{{ $h->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $h->start_date?->format('Y-m-d') ?? '—' }} → {{ $h->end_date ? $h->end_date->format('Y-m-d') : 'مفتوح' }}</td>
                                    <td>{{ number_format($h->base_fee ?? 0, 0) }} / {{ number_format($h->paid_amount ?? 0, 0) }} د.ع</td>
                                    <td>{{ $h->creator->name ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="mt-2 text-xs text-gray-500"><a href="{{ route('subscriptions.history', $company->code) }}">عرض السجل الكامل مع الفلترة</a></p>
            @endif
        </div>

        {{-- 4. فواتير الاشتراك --}}
        <div class="panel">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-[#e0e6ed] dark:border-[#1b2e4b] pb-2">
                <h6 class="text-base font-semibold dark:text-white-light">
                    فواتير الاشتراك (آخر {{ $limitLabel($limitInvoices) }} فاتورة)
                </h6>
                <form method="GET" action="{{ route('subscriptions.company-details', $company->code) }}" class="flex items-center gap-2">
                    <input type="hidden" name="limit_history" value="{{ $limitHistory }}">
                    <input type="hidden" name="limit_payment" value="{{ $limitPayment }}">
                    <input type="hidden" name="limit_company_cards" value="{{ $limitCompanyCards }}">
                    <label for="limit_invoices" class="text-sm text-gray-600 dark:text-gray-400">عرض:</label>
                    <select name="limit_invoices" id="limit_invoices" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        <option value="25" {{ $limitInvoices === '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $limitInvoices === '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $limitInvoices === '100' ? 'selected' : '' }}>100</option>
                        <option value="all" {{ $limitInvoices === 'all' ? 'selected' : '' }}>الكل</option>
                    </select>
                </form>
            </div>
            @if ($subscriptionInvoices->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">لا توجد فواتير.</p>
            @else
                <div class="table-responsive">
                    <table class="table-hover">
                        <thead>
                            <tr>
                                <th>رقم الفاتورة</th>
                                <th>النوع</th>
                                <th>الفترة</th>
                                <th>المبلغ</th>
                                <th>المدفوع</th>
                                <th>حالة السداد</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subscriptionInvoices as $inv)
                                <tr>
                                    <td class="font-medium">{{ $inv->invoice_number }}</td>
                                    <td>{{ $inv->type_name ?? $inv->invoice_type }}</td>
                                    <td>{{ $inv->period_start?->format('Y-m-d') ?? '—' }} → {{ $inv->period_end?->format('Y-m-d') ?? '—' }}</td>
                                    <td>{{ number_format($inv->total_amount ?? 0, 0) }} د.ع</td>
                                    <td>{{ number_format($inv->paid_amount ?? 0, 0) }} د.ع</td>
                                    <td>
                                        @if (($inv->payment_status ?? '') === 'paid')
                                            <span class="badge badge-outline-success">مدفوع</span>
                                        @elseif (($inv->payment_status ?? '') === 'partial')
                                            <span class="badge badge-outline-warning">جزئي</span>
                                        @else
                                            <span class="badge badge-outline-danger">قيد الانتظار</span>
                                        @endif
                                    </td>
                                    <td>{{ $inv->created_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- 5. حركات الدفع على بطاقات النظام --}}
        <div class="panel">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-[#e0e6ed] dark:border-[#1b2e4b] pb-2">
                <h6 class="text-base font-semibold dark:text-white-light">
                    حركات الدفع على بطاقات النظام (آخر {{ $limitLabel($limitPayment) }})
                </h6>
                <form method="GET" action="{{ route('subscriptions.company-details', $company->code) }}" class="flex items-center gap-2">
                    <input type="hidden" name="limit_history" value="{{ $limitHistory }}">
                    <input type="hidden" name="limit_invoices" value="{{ $limitInvoices }}">
                    <input type="hidden" name="limit_company_cards" value="{{ $limitCompanyCards }}">
                    <label for="limit_payment" class="text-sm text-gray-600 dark:text-gray-400">عرض:</label>
                    <select name="limit_payment" id="limit_payment" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        <option value="25" {{ $limitPayment === '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $limitPayment === '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $limitPayment === '100' ? 'selected' : '' }}>100</option>
                        <option value="all" {{ $limitPayment === 'all' ? 'selected' : '' }}>الكل</option>
                    </select>
                </form>
            </div>
            @if ($paymentCardTransactions->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">لا توجد حركات على بطاقات النظام لهذه الشركة.</p>
            @else
                <div class="table-responsive">
                    <table class="table-hover">
                        <thead>
                            <tr>
                                <th>رقم المعاملة</th>
                                <th>البطاقة</th>
                                <th>النوع</th>
                                <th>المبلغ</th>
                                <th>الرصيد قبل / بعد</th>
                                <th>المرجع</th>
                                <th>التاريخ</th>
                                <th>بواسطة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paymentCardTransactions as $tx)
                                <tr>
                                    <td class="font-medium">{{ $tx->transaction_number }}</td>
                                    <td>{{ $tx->paymentCard->card_name ?? '—' }}</td>
                                    <td>
                                        @if ($tx->type === 'deposit')
                                            <span class="badge badge-outline-success">إيداع</span>
                                        @else
                                            <span class="badge badge-outline-danger">سحب</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($tx->amount ?? 0, 0) }} د.ع</td>
                                    <td>{{ number_format($tx->balance_before ?? 0, 0) }} → {{ number_format($tx->balance_after ?? 0, 0) }}</td>
                                    <td>{{ $tx->reference_type_name ?? $tx->reference_type }}</td>
                                    <td>{{ $tx->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $tx->creator->name ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- 6. حركات بطاقات الشركة --}}
        <div class="panel">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-[#e0e6ed] dark:border-[#1b2e4b] pb-2">
                <h6 class="text-base font-semibold dark:text-white-light">
                    حركات بطاقات الشركة (آخر {{ $limitLabel($limitCompanyCards) }} حركة)
                </h6>
                <form method="GET" action="{{ route('subscriptions.company-details', $company->code) }}" class="flex items-center gap-2">
                    <input type="hidden" name="limit_history" value="{{ $limitHistory }}">
                    <input type="hidden" name="limit_invoices" value="{{ $limitInvoices }}">
                    <input type="hidden" name="limit_payment" value="{{ $limitPayment }}">
                    <label for="limit_company_cards" class="text-sm text-gray-600 dark:text-gray-400">عرض:</label>
                    <select name="limit_company_cards" id="limit_company_cards" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        <option value="25" {{ $limitCompanyCards === '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $limitCompanyCards === '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $limitCompanyCards === '100' ? 'selected' : '' }}>100</option>
                        <option value="all" {{ $limitCompanyCards === 'all' ? 'selected' : '' }}>الكل</option>
                    </select>
                </form>
            </div>
            @if ($companyCardTransactions->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">لا توجد حركات على بطاقات الشركة.</p>
            @else
                <div class="table-responsive">
                    <table class="table-hover">
                        <thead>
                            <tr>
                                <th>رقم المعاملة</th>
                                <th>البطاقة</th>
                                <th>الفرع</th>
                                <th>النوع</th>
                                <th>المبلغ</th>
                                <th>الرصيد قبل / بعد</th>
                                <th>الوصف</th>
                                <th>التاريخ</th>
                                <th>بواسطة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($companyCardTransactions as $tx)
                                <tr>
                                    <td class="font-medium">{{ $tx->transaction_number }}</td>
                                    <td>{{ $tx->paymentCard->card_name ?? '—' }}</td>
                                    <td>{{ $tx->branch->name ?? '—' }}</td>
                                    <td>
                                        @if ($tx->type === 'deposit')
                                            <span class="badge badge-outline-success">إيداع</span>
                                        @else
                                            <span class="badge badge-outline-danger">سحب</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($tx->amount ?? 0, 0) }} د.ع</td>
                                    <td>{{ number_format($tx->balance_before ?? 0, 0) }} → {{ number_format($tx->balance_after ?? 0, 0) }}</td>
                                    <td>{{ $tx->description ?? '—' }}</td>
                                    <td>{{ $tx->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $tx->creator->name ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
