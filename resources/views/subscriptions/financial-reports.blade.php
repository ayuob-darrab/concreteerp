@extends('layouts.app')

@section('page-title', 'التقارير المالية')

@section('content')
    <div class="container mx-auto px-4 py-6" dir="rtl">
        {{-- رأس الصفحة --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">التقارير المالية</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">ملخص الاشتراكات والإيرادات</p>
            </div>
            <a href="{{ route('subscriptions.companies') }}" class="btn btn-outline-primary btn-sm">
                ← رجوع
            </a>
        </div>

        {{-- ============================================= --}}
        {{-- البطاقات الإحصائية الرئيسية --}}
        {{-- ============================================= --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            {{-- إجمالي الإيرادات --}}
            <div class="rounded-lg shadow p-4 flex items-center justify-between" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff;">
                <div>
                    <p class="text-xs font-medium" style="color: rgba(255,255,255,0.95);">إجمالي الإيرادات</p>
                    <h2 class="text-2xl font-bold mt-1" style="color: #fff;">{{ number_format($grandTotalRevenue, 0) }}</h2>
                    <p class="text-xs" style="color: rgba(255,255,255,0.9);">دينار</p>
                </div>
                <div class="text-4xl opacity-50">💰</div>
            </div>

            {{-- الاشتراكات النشطة --}}
            <div class="rounded-lg shadow p-4 flex items-center justify-between" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #fff;">
                <div>
                    <p class="text-xs font-medium" style="color: rgba(255,255,255,0.95);">نشط</p>
                    <h2 class="text-2xl font-bold mt-1" style="color: #fff;">{{ $grandActiveCount }}</h2>
                    <p class="text-xs" style="color: rgba(255,255,255,0.9);">شركة</p>
                </div>
                <div class="text-4xl opacity-50">✅</div>
            </div>

            {{-- الاشتراكات المنتهية --}}
            <div class="rounded-lg shadow p-4 flex items-center justify-between" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #fff;">
                <div>
                    <p class="text-xs font-medium" style="color: rgba(255,255,255,0.95);">منتهي</p>
                    <h2 class="text-2xl font-bold mt-1" style="color: #fff;">{{ $grandExpiredCount }}</h2>
                    <p class="text-xs" style="color: rgba(255,255,255,0.9);">شركة</p>
                </div>
                <div class="text-4xl opacity-50">⏰</div>
            </div>

            {{-- متوسط الاشتراك --}}
            <div class="rounded-lg shadow p-4 flex items-center justify-between" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: #fff;">
                <div>
                    <p class="text-xs font-medium" style="color: rgba(255,255,255,0.95);">متوسط الاشتراك</p>
                    <h2 class="text-2xl font-bold mt-1" style="color: #fff;">{{ number_format($averageSubscription, 0) }}</h2>
                    <p class="text-xs" style="color: rgba(255,255,255,0.9);">دينار</p>
                </div>
                <div class="text-4xl opacity-50">📊</div>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- تنبيه الاشتراكات القريبة من الانتهاء --}}
        {{-- ============================================= --}}
        @if ($expiringSoon->count() > 0)
            <div class="panel bg-warning-light dark:bg-warning-dark-light mb-6">
                <div class="p-4">
                    <div class="flex items-start gap-3">
                        <span class="text-warning text-2xl">⚠️</span>
                        <div>
                            <h3 class="font-bold text-warning">{{ $expiringSoon->count() }} اشتراك ينتهي قريباً</h3>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach ($expiringSoon as $exp)
                                    <span class="badge badge-outline-warning">
                                        {{ $exp->company->name ?? $exp->company_code }}
                                        ({{ $exp->end_date->diffInDays(now()) }} يوم)
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ============================================= --}}
        {{-- قسم الفلاتر والبحث --}}
        {{-- ============================================= --}}
        <div class="panel mb-6">
            <div class="p-4">
                <form method="GET" action="{{ route('subscriptions.financial') }}">
                    <div class="flex flex-wrap items-end gap-3">
                        {{-- البحث بالشركة (Dropdown مع بحث) --}}
                        <div x-data="{
                            open: false,
                            search: '',
                            selected: '{{ $searchQuery }}',
                            companies: {{ Js::from($allCompanies) }},
                            get filteredCompanies() {
                                if (!this.search) return this.companies;
                                return this.companies.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            selectCompany(name) {
                                this.selected = name;
                                this.open = false;
                                this.search = '';
                            },
                            clear() {
                                this.selected = '';
                                this.search = '';
                            }
                        }" class="relative w-48">
                            <label class="text-xs text-gray-500 mb-1 block">الشركة</label>
                            <input type="hidden" name="search" :value="selected">
                            <button type="button" @click="open = !open"
                                class="form-input w-full text-sm text-right flex items-center justify-between cursor-pointer h-[38px]">
                                <span x-text="selected || 'اختر شركة...'" class="truncate"
                                    :class="{ 'text-gray-400': !selected }"></span>
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    <span x-show="selected" @click.stop="clear()"
                                        class="text-gray-400 hover:text-red-500 cursor-pointer">✕</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </button>
                            {{-- Dropdown --}}
                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute z-50 mt-1 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg">
                                <div class="p-2 border-b dark:border-gray-700">
                                    <input type="text" x-model="search" @click.stop placeholder="ابحث..."
                                        class="form-input w-full text-sm">
                                </div>
                                <div class="max-h-48 overflow-y-auto">
                                    <template x-for="company in filteredCompanies" :key="company.code">
                                        <div @click="selectCompany(company.name)"
                                            class="px-3 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 text-sm"
                                            :class="{ 'bg-primary/10 text-primary': selected === company.name }">
                                            <span x-text="company.name"></span>
                                            <span class="text-xs text-gray-400 mr-1"
                                                x-text="'(' + company.code + ')'"></span>
                                        </div>
                                    </template>
                                    <div x-show="filteredCompanies.length === 0"
                                        class="px-3 py-2 text-sm text-gray-400 text-center">
                                        لا توجد نتائج
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- الشهر --}}
                        <div class="w-36">
                            <label class="text-xs text-gray-500 mb-1 block">الشهر</label>
                            <input type="month" name="filter_month" value="{{ $filterMonth }}"
                                class="form-input w-full text-sm h-[38px]">
                        </div>

                        {{-- السنة --}}
                        <div class="w-28">
                            <label class="text-xs text-gray-500 mb-1 block">السنة</label>
                            <select name="filter_year" class="form-select w-full text-sm h-[38px]">
                                <option value="">الكل</option>
                                @for ($year = date('Y'); $year >= 2020; $year--)
                                    <option value="{{ $year }}" {{ $filterYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        {{-- الحالة --}}
                        <div class="w-28">
                            <label class="text-xs text-gray-500 mb-1 block">الحالة</label>
                            <select name="status" class="form-select w-full text-sm h-[38px]">
                                <option value="">الكل</option>
                                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="expired" {{ $status == 'expired' ? 'selected' : '' }}>منتهي</option>
                                <option value="suspended" {{ $status == 'suspended' ? 'selected' : '' }}>معطل</option>
                            </select>
                        </div>

                        {{-- نوع الخطة --}}
                        <div class="w-28">
                            <label class="text-xs text-gray-500 mb-1 block">الخطة</label>
                            <select name="plan_type" class="form-select w-full text-sm h-[38px]">
                                <option value="">الكل</option>
                                <option value="monthly" {{ $planType == 'monthly' ? 'selected' : '' }}>شهري</option>
                                <option value="yearly" {{ $planType == 'yearly' ? 'selected' : '' }}>سنوي</option>
                                <option value="percentage" {{ $planType == 'percentage' ? 'selected' : '' }}>نسبة
                                </option>
                                <option value="trial" {{ $planType == 'trial' ? 'selected' : '' }}>تجريبي</option>
                            </select>
                        </div>

                        {{-- الأزرار --}}
                        <div class="flex items-end gap-2">
                            <button type="submit" class="btn btn-primary btn-sm h-[38px]">بحث</button>
                            <a href="{{ route('subscriptions.financial') }}"
                                class="btn btn-outline-secondary btn-sm h-[38px]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </form>
                </form>

                {{-- شريط الفلاتر النشطة --}}
                @if ($hasFilters)
                    <div class="mt-4 pt-4 border-t flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-gray-500">الفلاتر النشطة:</span>
                        @if ($filterMonth)
                            <span class="badge badge-outline-primary">الشهر: {{ $filterMonth }}</span>
                        @endif
                        @if ($filterYear)
                            <span class="badge badge-outline-primary">السنة: {{ $filterYear }}</span>
                        @endif
                        @if ($status)
                            <span class="badge badge-outline-primary">الحالة: {{ $status }}</span>
                        @endif
                        @if ($planType)
                            <span class="badge badge-outline-primary">الخطة: {{ $planType }}</span>
                        @endif
                        @if ($searchQuery)
                            <span class="badge badge-outline-primary">بحث: {{ $searchQuery }}</span>
                        @endif

                        {{-- نتائج الفلترة --}}
                        <span class="mr-auto text-sm">
                            <strong class="text-success">{{ number_format($totalRevenue, 0) }}</strong> دينار من
                            <strong>{{ $activeCount + $expiredCount + $suspendedCount }}</strong> اشتراك
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- الجدول الرئيسي --}}
        {{-- ============================================= --}}
        <div class="panel mb-6">
            <div class="p-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-lg dark:text-white">
                        الاشتراكات
                        <span class="text-gray-400 text-sm font-normal">({{ $subscriptions->total() }})</span>
                    </h2>
                </div>

                <div class="table-responsive">
                    <table class="table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الشركة</th>
                                <th>الخطة</th>
                                <th>المبلغ</th>
                                <th>الفترة</th>
                                <th>الحالة</th>
                                <th class="text-center">—</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $index => $sub)
                                @php
                                    $daysRemaining = $sub->end_date ? now()->diffInDays($sub->end_date, false) : null;
                                    $isExpiringSoon =
                                        $daysRemaining !== null && $daysRemaining >= 0 && $daysRemaining <= 7;
                                @endphp
                                <tr
                                    class="{{ $sub->status === 'expired' ? 'bg-red-50 dark:bg-red-900/10' : ($isExpiringSoon ? 'bg-yellow-50 dark:bg-yellow-900/10' : '') }}">
                                    <td class="text-gray-400">{{ $subscriptions->firstItem() + $index }}</td>
                                    <td>
                                        <div class="font-semibold">{{ $sub->company->name ?? '—' }}</div>
                                        <div class="text-xs text-gray-400">{{ $sub->company_code }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $planBadges = [
                                                'monthly' => ['شهري', 'badge-outline-info'],
                                                'yearly' => ['سنوي', 'badge-outline-primary'],
                                                'percentage' => ['نسبة', 'badge-outline-secondary'],
                                                'trial' => ['تجريبي', 'badge-outline-warning'],
                                                'hybrid' => ['مختلط', 'badge-outline-dark'],
                                            ];
                                            $badge = $planBadges[$sub->plan_type] ?? ['—', 'badge-outline-dark'];
                                        @endphp
                                        <span class="badge {{ $badge[1] }}">{{ $badge[0] }}</span>
                                    </td>
                                    <td>
                                        <span class="font-semibold text-success">
                                            {{ number_format($sub->base_fee, 0) }}
                                        </span>
                                        @if ($sub->percentage_rate > 0)
                                            <span class="text-xs text-info">+{{ $sub->percentage_rate }}%</span>
                                        @endif
                                    </td>
                                    <td class="text-sm">
                                        <div>{{ $sub->start_date?->format('Y/m/d') ?? '—' }}</div>
                                        <div class="text-gray-400">
                                            {{ $sub->end_date?->format('Y/m/d') ?? 'مفتوح' }}
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusBadges = [
                                                'active' => [
                                                    $isExpiringSoon ? 'قريب الانتهاء' : 'نشط',
                                                    $isExpiringSoon ? 'badge-outline-warning' : 'badge-outline-success',
                                                ],
                                                'expired' => ['منتهي', 'badge-outline-danger'],
                                                'suspended' => ['معطل', 'badge-outline-dark'],
                                            ];
                                            $sBadge = $statusBadges[$sub->status] ?? ['—', 'badge-outline-dark'];
                                        @endphp
                                        <span class="badge {{ $sBadge[1] }}">{{ $sBadge[0] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex gap-1 justify-center">
                                            <a href="{{ route('subscriptions.history', $sub->company_code) }}"
                                                class="btn btn-outline-info btn-sm p-1" title="السجل">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('subscriptions.invoice', $sub->id) }}" target="_blank"
                                                class="btn btn-outline-success btn-sm p-1" title="فاتورة">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-10">
                                        <div class="text-gray-400">
                                            <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p>لا توجد اشتراكات</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($subscriptions->hasPages())
                    <div class="mt-4">
                        {{ $subscriptions->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- الملخص السفلي --}}
        {{-- ============================================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- توزيع حسب الخطة --}}
            <div class="panel">
                <div class="p-4">
                    <h3 class="font-semibold mb-4 dark:text-white">توزيع الخطط</h3>
                    <div class="space-y-3">
                        @foreach ($byPlanType as $plan)
                            @php
                                $planNames = [
                                    'monthly' => 'شهري',
                                    'yearly' => 'سنوي',
                                    'percentage' => 'نسبة',
                                    'trial' => 'تجريبي',
                                    'hybrid' => 'مختلط',
                                ];
                                $planName = $planNames[$plan->plan_type] ?? $plan->plan_type;
                                $percentage =
                                    $grandTotalRevenue > 0 ? round(($plan->revenue / $grandTotalRevenue) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span>{{ $planName }}</span>
                                    <span class="text-gray-500">{{ $plan->count }} شركة</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-primary h-2 rounded-full" style="width: {{ $percentage }}%">
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ number_format($plan->revenue, 0) }} د.ع ({{ $percentage }}%)
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ملخص سريع --}}
            <div class="panel">
                <div class="p-4">
                    <h3 class="font-semibold mb-4 dark:text-white">ملخص سريع</h3>
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800 rounded">
                            <span class="text-gray-600 dark:text-gray-400">إجمالي الشركات</span>
                            <span class="font-bold text-lg">{{ $activeCount + $expiredCount + $suspendedCount }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded">
                            <span class="text-gray-600 dark:text-gray-400">نسبة النشط</span>
                            @php
                                $total = $activeCount + $expiredCount + $suspendedCount;
                                $activePercentage = $total > 0 ? round(($activeCount / $total) * 100, 1) : 0;
                            @endphp
                            <span class="font-bold text-lg text-success">{{ $activePercentage }}%</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-red-50 dark:bg-red-900/20 rounded">
                            <span class="text-gray-600 dark:text-gray-400">معطل</span>
                            <span class="font-bold text-lg text-danger">{{ $grandSuspendedCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
