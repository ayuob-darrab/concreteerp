@extends('layouts.app')

@section('page-title', 'سجل اشتراكات ' . $company->name)

@section('content')
    <div class="container mx-auto px-4 py-6" dir="rtl">
        {{-- رأس الصفحة --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    سجل اشتراكات: {{ $company->name }}
                </h1>
                <p class="text-gray-500 text-sm">كود الشركة: {{ $company->code }}</p>
            </div>
            <a href="{{ route('subscriptions.companies') }}" class="btn btn-outline-primary btn-sm">
                ← رجوع
            </a>
        </div>

        {{-- بطاقات الملخص --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            {{-- الاشتراك الحالي --}}
            <div class="panel">
                <div class="p-4">
                    <p class="text-xs text-gray-500 mb-1">الحالة الحالية</p>
                    @if ($currentSubscription)
                        @php
                            $statusInfo = [
                                'active' => ['نشط', 'text-success', '✅'],
                                'expired' => ['منتهي', 'text-danger', '❌'],
                                'suspended' => ['معطل', 'text-warning', '⏸️'],
                            ];
                            $info = $statusInfo[$currentSubscription->status] ?? ['غير معروف', 'text-gray-500', '❓'];
                        @endphp
                        <p class="text-xl font-bold {{ $info[1] }}">{{ $info[2] }} {{ $info[0] }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            @php
                                $planNames = [
                                    'monthly' => 'شهري',
                                    'yearly' => 'سنوي',
                                    'percentage' => 'نسبة',
                                    'trial' => 'تجريبي',
                                ];
                            @endphp
                            {{ $planNames[$currentSubscription->plan_type] ?? $currentSubscription->plan_type }}
                        </p>
                    @else
                        <p class="text-xl font-bold text-gray-400">غير مشترك</p>
                    @endif
                </div>
            </div>

            {{-- إجمالي المدفوعات --}}
            <div class="panel">
                <div class="p-4">
                    <p class="text-xs text-gray-500 mb-1">إجمالي المدفوعات</p>
                    <p class="text-xl font-bold text-success">{{ number_format($totalPaid, 0) }}</p>
                    <p class="text-xs text-gray-400">دينار</p>
                </div>
            </div>

            {{-- عدد السجلات --}}
            <div class="panel">
                <div class="p-4">
                    <p class="text-xs text-gray-500 mb-1">عدد العمليات</p>
                    <p class="text-xl font-bold">{{ $history->total() }}</p>
                    <p class="text-xs text-gray-400">عملية</p>
                </div>
            </div>
        </div>

        {{-- الاشتراك الحالي (تفاصيل) --}}
        @if ($currentSubscription)
            <div class="panel border-2 border-primary/30 mb-6">
                <div class="p-4">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="badge badge-outline-primary">الاشتراك الحالي</span>
                    </div>
                    <div class="grid grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">المبلغ</p>
                            <p class="font-bold text-success">
                                {{ number_format($currentSubscription->base_fee, 0) }} د.ع
                                @if ($currentSubscription->percentage_rate > 0)
                                    <span class="text-info text-xs">+{{ $currentSubscription->percentage_rate }}%</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">من</p>
                            <p class="font-semibold">{{ $currentSubscription->start_date?->format('Y/m/d') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">إلى</p>
                            <p class="font-semibold">{{ $currentSubscription->end_date?->format('Y/m/d') ?? 'مفتوح' }}</p>
                        </div>
                        <div class="flex items-end gap-2">
                            <a href="{{ route('subscriptions.invoice', $currentSubscription->id) }}" target="_blank"
                                class="btn btn-outline-success btn-sm">
                                فاتورة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- جدول السجل --}}
        <div class="panel">
            <div class="p-4">
                <h2 class="font-semibold text-lg mb-4 dark:text-white">
                    سجل العمليات
                </h2>

                {{-- فلاتر البحث --}}
                <form method="GET" action="{{ route('subscriptions.history', $company->code) }}" class="mb-6">
                    <div class="flex flex-wrap items-end gap-3">
                        {{-- فلتر العملية --}}
                        <div class="flex-1 min-w-[120px]">
                            <label class="block text-xs font-semibold mb-1">العملية</label>
                            <select name="action_type" class="form-select form-select-sm">
                                <option value="">الكل</option>
                                <option value="created" {{ request('action_type') == 'created' ? 'selected' : '' }}>إنشاء
                                </option>
                                <option value="renewed" {{ request('action_type') == 'renewed' ? 'selected' : '' }}>تجديد
                                </option>
                                <option value="extended" {{ request('action_type') == 'extended' ? 'selected' : '' }}>تمديد
                                </option>
                                <option value="payment" {{ request('action_type') == 'payment' ? 'selected' : '' }}>دفعة
                                </option>
                                <option value="terminated" {{ request('action_type') == 'terminated' ? 'selected' : '' }}>
                                    إنهاء</option>
                                <option value="expired" {{ request('action_type') == 'expired' ? 'selected' : '' }}>انتهاء
                                </option>
                                <option value="suspended" {{ request('action_type') == 'suspended' ? 'selected' : '' }}>
                                    تعطيل</option>
                            </select>
                        </div>

                        {{-- فلتر الخطة --}}
                        <div class="flex-1 min-w-[100px]">
                            <label class="block text-xs font-semibold mb-1">الخطة</label>
                            <select name="plan_type" class="form-select form-select-sm">
                                <option value="">الكل</option>
                                <option value="monthly" {{ request('plan_type') == 'monthly' ? 'selected' : '' }}>شهري
                                </option>
                                <option value="yearly" {{ request('plan_type') == 'yearly' ? 'selected' : '' }}>سنوي
                                </option>
                                <option value="percentage" {{ request('plan_type') == 'percentage' ? 'selected' : '' }}>
                                    نسبة</option>
                                <option value="trial" {{ request('plan_type') == 'trial' ? 'selected' : '' }}>تجريبي
                                </option>
                                <option value="hybrid" {{ request('plan_type') == 'hybrid' ? 'selected' : '' }}>هجين
                                </option>
                            </select>
                        </div>

                        {{-- فلتر الحالة --}}
                        <div class="flex-1 min-w-[100px]">
                            <label class="block text-xs font-semibold mb-1">الحالة</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">الكل</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي
                                </option>
                                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معطل
                                </option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي
                                </option>
                            </select>
                        </div>

                        {{-- فلتر التاريخ من --}}
                        <div class="flex-1 min-w-[130px]">
                            <label class="block text-xs font-semibold mb-1">من تاريخ</label>
                            <input type="date" name="date_from" class="form-input form-input-sm"
                                value="{{ request('date_from') }}">
                        </div>

                        {{-- فلتر التاريخ إلى --}}
                        <div class="flex-1 min-w-[130px]">
                            <label class="block text-xs font-semibold mb-1">إلى تاريخ</label>
                            <input type="date" name="date_to" class="form-input form-input-sm"
                                value="{{ request('date_to') }}">
                        </div>

                        {{-- أزرار --}}
                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                            <a href="{{ route('subscriptions.history', $company->code) }}"
                                class="btn btn-outline-secondary btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>العملية</th>
                                <th>الخطة</th>
                                <th>المبلغ</th>
                                <th>الفترة</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th>—</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $index => $h)
                                <tr>
                                    <td class="text-gray-400">{{ $history->firstItem() + $index }}</td>
                                    <td>
                                        @php
                                            $actionBadges = [
                                                'created' => ['إنشاء', 'badge-outline-success'],
                                                'renewed' => ['تجديد', 'badge-outline-primary'],
                                                'extended' => ['تمديد', 'badge-outline-info'],
                                                'payment' => ['دفعة', 'badge-outline-warning'],
                                                'terminated' => ['إنهاء', 'badge-outline-danger'],
                                                'expired' => ['انتهاء', 'badge-outline-dark'],
                                                'suspended' => ['تعطيل', 'badge-outline-secondary'],
                                            ];
                                            $action = $actionBadges[$h->action_type] ?? [
                                                $h->action_type,
                                                'badge-outline-dark',
                                            ];
                                        @endphp
                                        <span class="badge {{ $action[1] }}">{{ $action[0] }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $planBadges = [
                                                'monthly' => ['شهري', 'badge-outline-info'],
                                                'yearly' => ['سنوي', 'badge-outline-primary'],
                                                'percentage' => ['نسبة', 'badge-outline-secondary'],
                                                'trial' => ['تجريبي', 'badge-outline-warning'],
                                            ];
                                            $plan = $planBadges[$h->plan_type] ?? [$h->plan_type, 'badge-outline-dark'];
                                        @endphp
                                        <span class="badge {{ $plan[1] }}">{{ $plan[0] }}</span>
                                    </td>
                                    <td>
                                        @if ($h->action_type === 'extended' && $h->extension_days > 0)
                                            <span class="font-semibold text-info">+{{ $h->extension_days }} يوم</span>
                                        @elseif ($h->base_fee > 0)
                                            <span
                                                class="font-semibold text-success">{{ number_format($h->base_fee, 0) }}</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="text-sm">
                                        <div>{{ $h->start_date?->format('Y/m/d') ?? '—' }}</div>
                                        <div class="text-gray-400">{{ $h->end_date?->format('Y/m/d') ?? '—' }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $statusBadges = [
                                                'active' => ['نشط', 'badge-outline-success'],
                                                'expired' => ['منتهي', 'badge-outline-danger'],
                                                'suspended' => ['معطل', 'badge-outline-warning'],
                                                'cancelled' => ['ملغي', 'badge-outline-dark'],
                                            ];
                                            $status = $statusBadges[$h->status] ?? [$h->status, 'badge-outline-dark'];
                                        @endphp
                                        <span class="badge {{ $status[1] }}">{{ $status[0] }}</span>
                                    </td>
                                    <td class="text-sm text-gray-500">
                                        {{ $h->created_at->format('Y/m/d') }}
                                        <div class="text-xs">{{ $h->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        @if ($h->action_type === 'payment')
                                            {{-- إيصال دفعة --}}
                                            <a href="{{ route('subscriptions.payment-invoice', $h->id) }}"
                                                target="_blank" class="btn btn-outline-success btn-sm p-1"
                                                title="طباعة إيصال">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                            </a>
                                        @elseif ($h->subscription_id && !in_array($h->action_type, ['extended', 'suspended']))
                                            {{-- فاتورة اشتراك --}}
                                            <a href="{{ route('subscriptions.invoice', $h->subscription_id) }}"
                                                target="_blank" class="btn btn-outline-success btn-sm p-1"
                                                title="فاتورة">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </a>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-10">
                                        <div class="text-gray-400">
                                            <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p>لا يوجد سجل</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($history->hasPages())
                    <div class="mt-4">
                        {{ $history->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ملاحظات --}}
        @if ($currentSubscription && $currentSubscription->notes)
            <div class="panel mt-6 bg-info-light dark:bg-info-dark-light">
                <div class="p-4">
                    <p class="font-semibold text-info mb-2">ملاحظات</p>
                    <p class="text-sm">{{ $currentSubscription->notes }}</p>
                </div>
            </div>
        @endif
    </div>
@endsection
