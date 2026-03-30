@extends('layouts.app')

@section('page-title', 'إدارة اشتراكات الشركات')

@section('content')


    <!-- ملاحظة هامة -->
    <div class="alert alert-info mb-5">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h3 class="font-bold">ℹ️ ملاحظة هامة</h3>
            <div class="text-sm mt-1">
                • لا يمكن تعديل الاشتراكات النشطة للحفاظ على سلامة البيانات<br>
                • لإنهاء اشتراك نشط: استخدم زر "إنهاء" أو انتظر حتى تاريخ الانتهاء<br>
                • يمكن تجديد الاشتراكات المنتهية أو المعطلة فقط<br>
                • إذا كان الاشتراك منتهياً ولا يزال ضمن فترة التمديد (السماح) يمكن التجديد من زر «تجديد»<br>
                • يمكنك تمديد الاشتراك النشط بأيام إضافية أو تسجيل دفعة جديدة
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">إدارة اشتراكات الشركات</h5>
                <a href="{{ route('subscriptions.settings') }}" class="btn btn-outline-primary btn-sm">
                    <svg class="h-5 w-5 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    إعدادات الأسعار
                </a>
            </div>

            <div class="table-responsive">
                <table class="table-hover">
                    <thead>
                        <tr>
                            <th>اللوجو</th>
                            <th>الشركة</th>
                            <th>الكود</th>
                            <th>الخطة الحالية</th>
                            <th>المستخدمين</th>
                            <th>البداية</th>
                            <th>النهاية</th>
                            <th>الأيام المتبقية</th>
                            <th>التمديدات</th>
                            <th>حالة السداد</th>
                            <th>الحالة</th>
                            <th class="text-center">إدارة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                            @php
                                $sub = $subscriptions[$company->code] ?? null;
                                $planLabels = [
                                    'monthly' => 'شهري',
                                    'yearly' => 'سنوي',
                                    'percentage' => 'نسبة من الطلبات',
                                    'trial' => 'تجريبي',
                                    'hybrid' => 'هجين',
                                ];
                                $currentPlan = $sub ? $planLabels[$sub->plan_type] ?? $sub->plan_type : 'غير مشترك';

                                // حساب الأيام المتبقية
                                $daysRemaining = null;
                                $isExpiringSoon = false;
                                if ($sub && $sub->end_date && $sub->status === 'active') {
                                    $daysRemaining = max(0, \Carbon\Carbon::now()->diffInDays($sub->end_date, false));
                                    $isExpiringSoon = $daysRemaining <= 7;
                                }

                                // حساب المبلغ المتبقي
                                $remainingAmount = $sub ? max(0, ($sub->base_fee ?? 0) - ($sub->paid_amount ?? 0)) : 0;

                                // إحصائيات التمديدات
                                $extStats = $extensionStats[$company->code] ?? null;
                                $extensionCount = $extStats->extension_count ?? 0;
                                $totalExtensionDays = $extStats->total_extension_days ?? 0;
                            @endphp
                            <tr
                                class="{{ $company->is_suspended ? 'bg-red-50 dark:bg-red-900/10' : ($isExpiringSoon ? 'bg-yellow-50 dark:bg-yellow-900/10' : '') }}">
                                {{-- عمود اللوجو --}}
                                <td class="text-center">
                                    @if ($company->logo)
                                        <img src="{{ asset($company->logo) }}" 
                                            alt="{{ $company->name }}"
                                            class="w-10 h-10 rounded-lg object-contain bg-white border border-gray-200 dark:border-gray-600 cursor-pointer hover:border-primary hover:shadow-md transition-all duration-200 mx-auto"
                                            onclick="openLogoModal('{{ asset($company->logo) }}', '{{ addslashes($company->name) }}')"
                                            title="انقر لعرض اللوجو">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto border border-gray-200 dark:border-gray-600">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td
                                    class="font-semibold {{ $company->is_suspended ? 'text-red-600 dark:text-red-400' : '' }}">
                                    {{ $company->name }}
                                    @if ($company->is_suspended)
                                        <span class="badge badge-outline-danger text-xs mr-2">معطلة</span>
                                    @endif
                                </td>
                                <td>{{ $company->code }}</td>
                                <td>
                                    <span class="badge badge-outline-{{ $sub ? 'success' : 'secondary' }}">
                                        {{ $currentPlan }}
                                    </span>
                                    @if ($sub && $sub->duration_quantity > 1)
                                        <span class="text-xs text-gray-500">({{ $sub->duration_quantity }}
                                            {{ $sub->plan_type === 'yearly' ? 'سنة' : 'شهر' }})</span>
                                    @endif
                                </td>
                                {{-- عمود عدد المستخدمين --}}
                                <td class="text-center">
                                    @if ($sub && $sub->users_count)
                                        @php
                                            $actualUsers = \App\Models\User::where(
                                                'company_code',
                                                $company->code,
                                            )->count();
                                        @endphp
                                        <span
                                            class="badge {{ $actualUsers >= $sub->users_count ? 'badge-outline-danger' : 'badge-outline-info' }}">
                                            {{ $actualUsers }}/{{ $sub->users_count }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>{{ $sub?->start_date ? \Carbon\Carbon::parse($sub->start_date)->format('Y/m/d') : '-' }}
                                </td>
                                <td>{{ $sub?->end_date ? \Carbon\Carbon::parse($sub->end_date)->format('Y/m/d') : '-' }}
                                </td>
                                <td>
                                    @if ($daysRemaining !== null)
                                        <span
                                            class="badge {{ $isExpiringSoon ? 'badge-outline-danger' : 'badge-outline-info' }}">
                                            {{ $daysRemaining }} يوم
                                            @if ($sub->extension_days > 0)
                                                <span class="text-xs">(+{{ $sub->extension_days }})</span>
                                            @endif
                                        </span>
                                        @if ($isExpiringSoon)
                                            <span class="text-xs text-red-500 block">⚠️ قريب الانتهاء</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                {{-- عمود التمديدات --}}
                                <td class="text-center">
                                    @if ($extensionCount > 0)
                                        <span class="badge badge-outline-info cursor-pointer"
                                            onclick="showExtensionHistory('{{ $company->code }}', '{{ $company->name }}')"
                                            title="انقر لعرض سجل التمديدات">
                                            {{ $extensionCount }} مرة
                                        </span>
                                        <span class="text-xs text-gray-500 block">{{ $totalExtensionDays }} يوم
                                            إجمالي</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($sub)
                                        @if ($sub->plan_type === 'percentage')
                                            <span class="badge badge-outline-info">
                                                نسبة من الطلبات
                                            </span>
                                            @if ($sub->percentage_rate)
                                                <span
                                                    class="text-xs text-gray-500 block">{{ $sub->percentage_rate }}%</span>
                                            @elseif ($sub->fixed_order_fee)
                                                <span
                                                    class="text-xs text-gray-500 block">{{ number_format($sub->fixed_order_fee, 0) }}
                                                    /طلب</span>
                                            @endif
                                        @else
                                            @php
                                                // حساب حالة الدفع الفعلية بناءً على المبالغ
                                                $actualPaymentStatus = 'pending';
                                                if ($sub->paid_amount >= $sub->base_fee && $sub->base_fee > 0) {
                                                    $actualPaymentStatus = 'paid';
                                                } elseif ($sub->paid_amount > 0) {
                                                    $actualPaymentStatus = 'partial';
                                                }

                                                $paymentColors = [
                                                    'paid' => 'success',
                                                    'partial' => 'warning',
                                                    'pending' => 'danger',
                                                ];
                                                $paymentLabels = [
                                                    'paid' => 'مسدد ✅',
                                                    'partial' => 'جزئي',
                                                    'pending' => 'غير مسدد',
                                                ];
                                            @endphp
                                            <span
                                                class="badge badge-outline-{{ $paymentColors[$actualPaymentStatus] ?? 'secondary' }}">
                                                {{ $paymentLabels[$actualPaymentStatus] ?? 'غير محدد' }}
                                            </span>
                                            @if ($sub->base_fee > 0)
                                                <span
                                                    class="text-xs text-gray-500 block">{{ number_format($sub->base_fee, 0) }}
                                                    دينار</span>
                                            @endif
                                            @if ($remainingAmount > 0)
                                                <span class="text-xs text-warning block">متبقي:
                                                    {{ number_format($remainingAmount, 0) }}</span>
                                            @endif
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($sub)
                                        @php $detailed = $sub->detailed_status ?? null; @endphp
                                        @if ($detailed && ($detailed['status'] ?? '') === 'grace_period')
                                            <span class="badge badge-outline-warning" title="فترة السماح - يمكن التجديد">
                                                منتهي (فترة تمديد)
                                            </span>
                                        @elseif ($sub->status === 'active')
                                            <span class="badge badge-outline-success">نشط</span>
                                        @elseif($sub->status === 'expired')
                                            <span class="badge badge-outline-danger">منتهي</span>
                                        @elseif($sub->status === 'suspended')
                                            <span class="badge badge-outline-warning">معلق</span>
                                        @else
                                            <span class="badge badge-outline-secondary">{{ $sub->status }}</span>
                                        @endif
                                    @else
                                        <span class="badge badge-outline-secondary">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-1 flex-wrap">
                                        {{-- زر عرض التفاصيل: تفاصيل الشركة والاشتراكات والحركات --}}
                                        <a href="{{ route('subscriptions.company-details', $company->code) }}"
                                            class="btn btn-sm btn-outline-info" title="عرض تفاصيل الشركة والاشتراكات والحركات">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            عرض تفاصيل
                                        </a>
                                        @if (!$sub)
                                            {{-- زر الاشتراك للشركات غير المشتركة --}}
                                            <a href="{{ route('subscriptions.edit', $company->code) }}"
                                                class="btn btn-sm btn-success">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-4 h-4 inline-block ltr:mr-1 rtl:ml-1" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                اشتراك
                                            </a>
                                        @elseif ($sub->status !== 'active' || ($sub->isExpired() && $sub->isInGracePeriod()))
                                            {{-- زر التجديد: للاشتراكات المنتهية أو المعطلة أو المنتهية ضمن فترة السماح --}}
                                            <a href="{{ route('subscriptions.edit', $company->code) }}"
                                                class="btn btn-sm btn-primary"
                                                title="{{ $sub->isExpired() && $sub->isInGracePeriod() ? 'الاشتراك منتهي وفي فترة التمديد - يمكن التجديد' : 'تجديد الاشتراك' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-4 h-4 inline-block ltr:mr-1 rtl:ml-1" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                    </path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                    </path>
                                                </svg>
                                                تجديد
                                            </a>
                                        @else
                                            {{-- أزرار للاشتراكات النشطة --}}

                                            {{-- زر التمديد - يظهر فقط عندما يكون باقي الاشتراك يومين أو أقل ولم يتجاوز 5 تمديدات --}}
                                            @if ($daysRemaining !== null && $daysRemaining <= 2 && $extensionCount < 5)
                                                <button type="button" class="btn btn-sm btn-info"
                                                    onclick="openExtendModal('{{ $company->code }}', '{{ $company->name }}', {{ 5 - $extensionCount }})"
                                                    title="تمديد الاشتراك (متبقي {{ $daysRemaining }} يوم) - التمديدات المتبقية: {{ 5 - $extensionCount }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>
                                            @elseif($daysRemaining !== null && $daysRemaining <= 2 && $extensionCount >= 5)
                                                <span class="text-xs text-danger"
                                                    title="تم الوصول للحد الأقصى من التمديدات">
                                                    ⚠️ انتهت التمديدات
                                                </span>
                                            @endif

                                            {{-- زر زيادة المستخدمين --}}
                                            @if ($sub->users_count)
                                                <button type="button" class="btn btn-sm btn-secondary"
                                                    onclick="openAddUsersModal('{{ $company->code }}', '{{ $company->name }}', '{{ $sub->plan_type ?? '' }}', {{ $sub->users_count }}, {{ $sub->price_per_user ?? 0 }}, {{ $daysRemaining ?? 0 }})"
                                                    title="زيادة عدد المستخدمين">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                                                        <circle cx="9" cy="7" r="4"></circle>
                                                        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"></path>
                                                        <path d="M20 8v6M23 11h-6"></path>
                                                    </svg>
                                                </button>
                                            @endif

                                            {{-- زر الدفع - يظهر عندما يكون هناك مبلغ متبقي --}}
                                            @if ($remainingAmount > 0)
                                                <button type="button" class="btn btn-sm btn-success"
                                                    onclick="openPaymentModal('{{ $company->code }}', '{{ $company->name }}', {{ $remainingAmount }})"
                                                    title="تسجيل دفعة - المتبقي: {{ number_format($remainingAmount, 0) }} دينار">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path
                                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @endif
                                        @endif

                                        @if ($sub && $sub->status === 'active')
                                            <form action="{{ route('subscriptions.terminate', $company->code) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning"
                                                    onclick="return confirm('⚠️ تحذير: سيتم إنهاء الاشتراك نهائياً ولن يتمكن المستخدمون من الدخول. هل أنت متأكد؟')"
                                                    title="إنهاء الاشتراك">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- إظهار زر التعطيل فقط إذا كان الاشتراك نشطاً وليس معطلاً --}}
                                        @if ($sub && $sub->status === 'active' && !$company->is_suspended)
                                            <form action="{{ route('subscriptions.toggleSuspension', $company->code) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('تحذير: سيتم تعطيل جميع الحسابات المرتبطة بهذه الشركة. هل أنت متأكد؟')"
                                                    title="تعطيل الشركة">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- إظهار زر التفعيل فقط إذا كان معطلاً --}}
                                        @if ($company->is_suspended)
                                            <form action="{{ route('subscriptions.toggleSuspension', $company->code) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success"
                                                    onclick="return confirm('هل أنت متأكد من تفعيل هذه الشركة؟')"
                                                    title="تفعيل الشركة">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal التمديد -->
    <div id="extendModal"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-start justify-center overflow-y-auto py-10">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md mx-4 my-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold dark:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline-block mr-2 text-info"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    تمديد الاشتراك
                </h3>
                <button type="button" onclick="closeExtendModal()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="extendForm" method="POST">
                @csrf
                <p class="text-gray-600 dark:text-gray-300 mb-2">
                    تمديد اشتراك شركة: <strong id="extendCompanyName"></strong>
                </p>
                <p class="text-sm text-info mb-4">
                    التمديدات المتبقية: <strong id="remainingExtensions"></strong> من 5
                </p>

                <div class="mb-4">
                    <label class="block font-semibold mb-2">عدد أيام التمديد <span class="text-danger">*</span></label>
                    <input type="number" name="extension_days" class="form-input w-full" min="1" max="365"
                        value="7" required>
                    <small class="text-gray-500">أدخل عدد الأيام المراد إضافتها للاشتراك</small>
                </div>

                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="closeExtendModal()" class="btn btn-outline-secondary">إلغاء</button>
                    <button type="submit" class="btn btn-info">تمديد الاشتراك</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal الدفع -->
    <div id="paymentModal"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-start justify-center overflow-y-auto py-10">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md mx-4 my-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold dark:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline-block mr-2 text-success"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    تسجيل دفعة
                </h3>
                <button type="button" onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="paymentForm" method="POST">
                @csrf
                <p class="text-gray-600 dark:text-gray-300 mb-2">
                    تسجيل دفعة لشركة: <strong id="paymentCompanyName"></strong>
                </p>
                <p class="text-sm text-warning mb-4">
                    المبلغ المتبقي: <strong id="remainingAmountText"></strong> دينار
                </p>

                <!-- نوع الدفع -->
                <div class="mb-4">
                    <label class="block font-semibold mb-2">نوع الدفع <span class="text-danger">*</span></label>
                    <select name="payment_type" id="paymentTypeSelect" class="form-select w-full" required
                        onchange="togglePaymentTypeModal()">
                        <option value="">اختر نوع الدفع</option>
                        <option value="cash">💵 كاش (دفع فوري)</option>
                        <option value="deferred">📋 آجل (تأجيل الدفع)</option>
                    </select>
                </div>

                <!-- حقول الدفع الكاش -->
                <div id="cashPaymentFields" style="display: none;">
                    <div class="mb-4">
                        <label class="block font-semibold mb-2">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="paymentAmount" class="form-input w-full"
                            min="1">
                        <small id="paymentAmountHint" class="text-gray-500"></small>
                    </div>

                    <div class="mb-4">
                        <label class="block font-semibold mb-2">طريقة الدفع <span class="text-danger">*</span></label>
                        <select name="payment_method" id="paymentMethodSelect" class="form-select w-full"
                            onchange="togglePaymentCardSelect()">
                            <option value="cash">نقدي</option>
                            <option value="bank_transfer">تحويل بنكي</option>
                            <option value="check">شيك</option>
                            <option value="online">دفع إلكتروني</option>
                        </select>
                    </div>

                    <!-- اختيار البطاقة (يظهر عند اختيار دفع إلكتروني) -->
                    <div class="mb-4" id="paymentCardContainer" style="display: none;">
                        <label class="block font-semibold mb-2">حساب الدفع الإلكتروني <span
                                class="text-danger">*</span></label>
                        <select name="payment_card_id" id="paymentCardSelect" class="form-select w-full">
                            <option value="">اختر حساب الدفع</option>
                        </select>
                        <small class="text-gray-500">سيتم إيداع المبلغ في البطاقة المختارة</small>
                    </div>

                    <div class="mb-4">
                        <label class="block font-semibold mb-2">رقم الإيصال/المرجع</label>
                        <input type="text" name="payment_reference" class="form-input w-full" placeholder="اختياري">
                    </div>

                    <div class="mb-4">
                        <label class="block font-semibold mb-2">ملاحظات</label>
                        <textarea name="notes" class="form-textarea w-full" rows="2" placeholder="اختياري"></textarea>
                    </div>
                </div>

                <!-- رسالة الدفع الآجل -->
                <div id="deferredPaymentMessage" class="mb-4 p-4 bg-warning/10 border border-warning rounded-lg"
                    style="display: none;">
                    <div class="flex items-center gap-2 text-warning">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-semibold">تأجيل الدفع</span>
                    </div>
                    <p class="text-sm mt-2 text-gray-600">
                        سيتم الإبقاء على المبلغ المتبقي كدين على الشركة.
                        لا يتم تسجيل أي دفعة في هذه العملية.
                    </p>
                </div>

                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="closePaymentModal()" class="btn btn-outline-secondary">إلغاء</button>
                    <button type="submit" class="btn btn-success" id="paymentSubmitBtn">تسجيل الدفعة</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal سجل التمديدات -->
    <div id="extensionHistoryModal"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-start justify-center overflow-y-auto py-10">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-lg mx-4 my-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold dark:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline-block mr-2 text-info"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    سجل التمديدات - <span id="extensionHistoryCompanyName"></span>
                </h3>
                <button type="button" onclick="closeExtensionHistoryModal()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div id="extensionHistoryContent">
                <div class="text-center py-4">
                    <svg class="animate-spin h-8 w-8 mx-auto text-info" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <p class="mt-2 text-gray-500">جاري التحميل...</p>
                </div>
            </div>

            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeExtensionHistoryModal()"
                    class="btn btn-outline-secondary">إغلاق</button>
            </div>
        </div>
    </div>

    <script>
        const baseUrl = '{{ url('/') }}';

        function openExtendModal(companyCode, companyName, remainingExtensions) {
            document.getElementById('extendCompanyName').textContent = companyName;
            document.getElementById('remainingExtensions').textContent = remainingExtensions;
            document.getElementById('extendForm').action = baseUrl + '/subscriptions/companies/' + companyCode + '/extend';
            document.getElementById('extendModal').classList.remove('hidden');
        }

        function closeExtendModal() {
            document.getElementById('extendModal').classList.add('hidden');
        }

        function openPaymentModal(companyCode, companyName, remainingAmount) {
            document.getElementById('paymentCompanyName').textContent = companyName;
            document.getElementById('remainingAmountText').textContent = remainingAmount.toLocaleString();
            document.getElementById('paymentAmount').value = remainingAmount;
            document.getElementById('paymentAmount').max = remainingAmount;
            document.getElementById('paymentForm').action = baseUrl + '/subscriptions/companies/' + companyCode +
                '/payment';
            // إعادة تعيين كل الحقول
            document.getElementById('paymentTypeSelect').value = '';
            document.getElementById('paymentMethodSelect').value = 'cash';
            document.getElementById('paymentCardContainer').style.display = 'none';
            document.getElementById('paymentCardSelect').value = '';
            document.getElementById('cashPaymentFields').style.display = 'none';
            document.getElementById('deferredPaymentMessage').style.display = 'none';
            document.getElementById('paymentSubmitBtn').textContent = 'تسجيل الدفعة';
            document.getElementById('paymentModal').classList.remove('hidden');

            // حفظ المبلغ المتبقي للاستخدام لاحقاً
            window.currentRemainingAmount = remainingAmount;
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }

        // إظهار/إخفاء حقول الدفع حسب نوع الدفع
        function togglePaymentTypeModal() {
            const paymentType = document.getElementById('paymentTypeSelect').value;
            const cashFields = document.getElementById('cashPaymentFields');
            const deferredMessage = document.getElementById('deferredPaymentMessage');
            const amountInput = document.getElementById('paymentAmount');
            const methodSelect = document.getElementById('paymentMethodSelect');
            const submitBtn = document.getElementById('paymentSubmitBtn');

            if (paymentType === 'cash') {
                cashFields.style.display = 'block';
                deferredMessage.style.display = 'none';
                amountInput.setAttribute('required', 'required');
                methodSelect.setAttribute('required', 'required');
                amountInput.value = window.currentRemainingAmount;
                submitBtn.textContent = 'تسجيل الدفعة';
                submitBtn.className = 'btn btn-success';
                updatePaymentAmountHint();
            } else if (paymentType === 'deferred') {
                cashFields.style.display = 'none';
                deferredMessage.style.display = 'block';
                amountInput.removeAttribute('required');
                methodSelect.removeAttribute('required');
                amountInput.value = 0;
                submitBtn.textContent = 'تأكيد التأجيل';
                submitBtn.className = 'btn btn-warning';
            } else {
                cashFields.style.display = 'none';
                deferredMessage.style.display = 'none';
                submitBtn.textContent = 'تسجيل الدفعة';
                submitBtn.className = 'btn btn-success';
            }
        }

        // تحديث رسالة المبلغ المدفوع
        function updatePaymentAmountHint() {
            const amount = parseFloat(document.getElementById('paymentAmount').value) || 0;
            const remaining = window.currentRemainingAmount || 0;
            const hint = document.getElementById('paymentAmountHint');

            if (amount >= remaining) {
                hint.innerHTML = '<span class="text-success">✅ دفع كامل</span>';
            } else if (amount > 0) {
                const stillRemaining = remaining - amount;
                hint.innerHTML = '<span class="text-warning">⚠️ دفع جزئي - سيبقى متبقي: ' + stillRemaining
                    .toLocaleString() + ' دينار</span>';
            } else {
                hint.innerHTML = '';
            }
        }

        // إضافة مستمع لتحديث الرسالة عند تغيير المبلغ
        document.getElementById('paymentAmount')?.addEventListener('input', updatePaymentAmountHint);

        // إظهار/إخفاء حقل اختيار البطاقة
        function togglePaymentCardSelect() {
            const paymentMethod = document.getElementById('paymentMethodSelect').value;
            const cardContainer = document.getElementById('paymentCardContainer');
            const cardSelect = document.getElementById('paymentCardSelect');

            if (paymentMethod === 'online') {
                cardContainer.style.display = 'block';
                cardSelect.setAttribute('required', 'required');
                loadPaymentCardsForModal();
            } else {
                cardContainer.style.display = 'none';
                cardSelect.removeAttribute('required');
                cardSelect.value = '';
            }
        }

        // تحميل البطاقات النشطة للـ modal
        async function loadPaymentCardsForModal() {
            try {
                const response = await fetch(baseUrl + '/payment-cards/api/active');
                const cards = await response.json();
                const select = document.getElementById('paymentCardSelect');

                // مسح الخيارات السابقة
                select.innerHTML = '<option value="">اختر حساب الدفع</option>';

                // إضافة البطاقات
                cards.forEach(card => {
                    const option = document.createElement('option');
                    option.value = card.id;
                    option.textContent =
                        `${card.card_name} (${card.holder_name}) - الرصيد: ${Number(card.current_balance).toLocaleString('ar-EG')} دينار`;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('خطأ في تحميل البطاقات:', error);
            }
        }

        // إغلاق Modal عند النقر خارجه
        document.getElementById('extendModal').addEventListener('click', function(e) {
            if (e.target === this) closeExtendModal();
        });

        document.getElementById('paymentModal').addEventListener('click', function(e) {
            if (e.target === this) closePaymentModal();
        });

        document.getElementById('extensionHistoryModal').addEventListener('click', function(e) {
            if (e.target === this) closeExtensionHistoryModal();
        });

        // دوال سجل التمديدات
        function showExtensionHistory(companyCode, companyName) {
            document.getElementById('extensionHistoryCompanyName').textContent = companyName;
            document.getElementById('extensionHistoryModal').classList.remove('hidden');

            // جلب سجل التمديدات
            fetch(baseUrl + '/subscriptions/companies/' + companyCode + '/extensions')
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    if (data.extensions && data.extensions.length > 0) {
                        html = `
                            <div class="mb-3 p-3 bg-info/10 rounded-lg">
                                <span class="text-info font-semibold">إجمالي التمديدات: ${data.total_count} مرة</span>
                                <span class="mx-2">|</span>
                                <span class="text-info font-semibold">إجمالي الأيام: ${data.total_days} يوم</span>
                            </div>
                            <div class="overflow-auto max-h-64">
                                <table class="table-striped w-full text-sm">
                                    <thead>
                                        <tr>
                                            <th class="p-2 text-right">التاريخ</th>
                                            <th class="p-2 text-center">الأيام</th>
                                            <th class="p-2 text-right">الملاحظات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;
                        data.extensions.forEach(ext => {
                            html += `
                                <tr>
                                    <td class="p-2">${ext.date}</td>
                                    <td class="p-2 text-center"><span class="badge badge-outline-info">+${ext.days}</span></td>
                                    <td class="p-2 text-gray-500">${ext.notes || '-'}</td>
                                </tr>
                            `;
                        });
                        html += `
                                    </tbody>
                                </table>
                            </div>
                        `;
                    } else {
                        html = '<p class="text-center text-gray-500 py-4">لا توجد تمديدات مسجلة</p>';
                    }
                    document.getElementById('extensionHistoryContent').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('extensionHistoryContent').innerHTML =
                        '<p class="text-center text-danger py-4">حدث خطأ في تحميل البيانات</p>';
                    console.error('Error:', error);
                });
        }

        function closeExtensionHistoryModal() {
            document.getElementById('extensionHistoryModal').classList.add('hidden');
        }

        // دوال زيادة المستخدمين
        function openAddUsersModal(companyCode, companyName, planType, currentUsers, pricePerUser, daysRemaining) {
            document.getElementById('addUsersCompanyCode').value = companyCode;
            document.getElementById('addUsersCompanyName').textContent = companyName;
            document.getElementById('addUsersPlanType').textContent = planType || '-';
            document.getElementById('addUsersCurrentCount').textContent = currentUsers;
            document.getElementById('addUsersPricePerUser').value = pricePerUser;
            document.getElementById('addUsersDaysRemaining').value = daysRemaining;
            document.getElementById('addUsersAdditionalInput').value = 1;

            // إعادة تعيين حقول الدفع
            document.getElementById('addUsersPaymentType').value = '';
            document.getElementById('addUsersPaymentMethod').value = '';
            document.getElementById('addUsersCashFields').style.display = 'none';
            document.getElementById('addUsersDeferredMessage').style.display = 'none';
            document.getElementById('addUsersPaymentCardDiv').style.display = 'none';
            document.getElementById('directPaymentInfo').style.display = 'none';

            calculateAdditionalCost();
            document.getElementById('addUsersModal').classList.remove('hidden');
        }

        function closeAddUsersModal() {
            document.getElementById('addUsersModal').classList.add('hidden');
        }

        function calculateAdditionalCost() {
            const currentUsers = parseInt(document.getElementById('addUsersCurrentCount').textContent);
            const additionalUsers = parseInt(document.getElementById('addUsersAdditionalInput').value) || 0;
            const pricePerUser = parseFloat(document.getElementById('addUsersPricePerUser').value);
            const daysRemaining = parseInt(document.getElementById('addUsersDaysRemaining').value) || 0;
            const planTypeRaw = (document.getElementById('addUsersPlanType')?.textContent || '').trim();

            // التحويل إلى أشهر متبقية (تقريب للأعلى): 11 شهر و 6 أيام => 12
            let monthsRemaining = Math.max(0, Math.ceil(daysRemaining / 30));
            // تقييد حسب نوع الخطة لتجنب (سنوي + 13 شهر)
            if (planTypeRaw === 'yearly') monthsRemaining = Math.min(12, monthsRemaining);
            if (planTypeRaw === 'monthly') monthsRemaining = Math.min(1, monthsRemaining);
            const monthsEl = document.getElementById('addUsersMonthsRemaining');
            if (monthsEl) monthsEl.textContent = String(monthsRemaining);

            const totalUsers = currentUsers + additionalUsers;
            const additionalCost = additionalUsers * pricePerUser * monthsRemaining;

            document.getElementById('addUsersTotalCount').textContent = totalUsers;
            document.getElementById('addUsersCostDisplay').textContent = Math.round(additionalCost).toLocaleString('ar-EG');
            document.getElementById('addUsersAdditionalCost').value = Math.round(additionalCost);

            const note = document.getElementById('addUsersProrationNote');
            if (note) {
                if (monthsRemaining <= 0) {
                    note.textContent = '⚠️ لا توجد أشهر متبقية في الاشتراك الحالي.';
                } else {
                    note.textContent = `* يتم احتساب التكلفة حسب الأشهر المتبقية: ${monthsRemaining} شهر`;
                }
            }
        }

        // إظهار/إخفاء حقول الدفع حسب نوع الدفع لزيادة المستخدمين
        function toggleAddUsersPaymentType() {
            const paymentType = document.getElementById('addUsersPaymentType').value;
            const cashFields = document.getElementById('addUsersCashFields');
            const deferredMessage = document.getElementById('addUsersDeferredMessage');
            const paymentMethodSelect = document.getElementById('addUsersPaymentMethod');
            const submitBtn = document.getElementById('addUsersSubmitBtn');
            const submitText = document.getElementById('addUsersSubmitText');

            if (paymentType === 'cash') {
                cashFields.style.display = 'block';
                deferredMessage.style.display = 'none';
                paymentMethodSelect.setAttribute('required', 'required');
                submitText.textContent = 'حفظ والدفع';
                submitBtn.className = 'btn btn-success';
            } else if (paymentType === 'deferred') {
                cashFields.style.display = 'none';
                deferredMessage.style.display = 'block';
                paymentMethodSelect.removeAttribute('required');
                paymentMethodSelect.value = '';
                document.getElementById('addUsersPaymentCardDiv').style.display = 'none';
                document.getElementById('directPaymentInfo').style.display = 'none';
                submitText.textContent = 'حفظ (دفع آجل)';
                submitBtn.className = 'btn btn-warning';
            } else {
                cashFields.style.display = 'none';
                deferredMessage.style.display = 'none';
                paymentMethodSelect.removeAttribute('required');
                submitText.textContent = 'حفظ والدفع';
                submitBtn.className = 'btn btn-success';
            }
        }

        // إظهار/إخفاء قائمة البطاقات حسب طريقة الدفع
        function togglePaymentCards() {
            const paymentMethod = document.getElementById('addUsersPaymentMethod').value;
            const cardDiv = document.getElementById('addUsersPaymentCardDiv');
            const directPaymentInfo = document.getElementById('directPaymentInfo');
            const cardSelect = document.getElementById('addUsersPaymentCard');
            const hintText = document.getElementById('paymentMethodHint');
            const submitBtn = document.getElementById('addUsersSubmitBtn');
            const submitText = document.getElementById('addUsersSubmitText');

            // إخفاء كل شيء أولاً
            cardDiv.style.display = 'none';
            directPaymentInfo.style.display = 'none';
            cardSelect.removeAttribute('required');
            hintText.textContent = '';

            if (paymentMethod === 'online') {
                // دفع إلكتروني - إظهار اختيار البطاقة
                cardDiv.style.display = 'block';
                cardSelect.setAttribute('required', 'required');
                loadPaymentCardsForAddUsers();
                hintText.innerHTML = '<span class="text-info">💳 سيتم خصم المبلغ من البطاقة المحددة</span>';
                submitText.textContent = 'دفع إلكتروني وحفظ';
                submitBtn.className = 'btn btn-info';
            } else if (paymentMethod === 'cash' || paymentMethod === 'bank_transfer' || paymentMethod === 'check') {
                // دفع نقدي أو تحويل بنكي أو شيك
                directPaymentInfo.style.display = 'block';
                cardSelect.innerHTML = '<option value="">-- اختر بطاقة الدفع --</option>';
                submitText.textContent = 'تأكيد الدفع وحفظ';
                submitBtn.className = 'btn btn-success';
                if (paymentMethod === 'cash') {
                    hintText.innerHTML = '<span class="text-success">💵 دفع نقدي - سيتم تأكيد الدفعة فوراً</span>';
                } else if (paymentMethod === 'bank_transfer') {
                    hintText.innerHTML = '<span class="text-success">🏦 تحويل بنكي - سيتم تأكيد الدفعة فوراً</span>';
                } else if (paymentMethod === 'check') {
                    hintText.innerHTML = '<span class="text-success">📄 شيك - سيتم تأكيد الدفعة فوراً</span>';
                }
            } else {
                // لم يتم اختيار طريقة دفع
                submitText.textContent = 'حفظ والدفع';
                submitBtn.className = 'btn btn-success';
            }
        }

        // تحميل البطاقات النشطة
        async function loadPaymentCardsForAddUsers() {
            try {
                const response = await fetch(baseUrl + '/payment-cards/api/active');
                const cards = await response.json();

                const select = document.getElementById('addUsersPaymentCard');
                select.innerHTML = '<option value="">-- اختر بطاقة الدفع --</option>';

                cards.forEach(card => {
                    const option = document.createElement('option');
                    option.value = card.id;
                    option.textContent =
                        `${card.card_name} (${card.card_number_masked}) - الرصيد: ${Number(card.current_balance).toLocaleString('ar-EG')} دينار`;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('خطأ في تحميل البطاقات:', error);
            }
        }

        // إغلاق Modal عند النقر خارجه
        document.getElementById('addUsersModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeAddUsersModal();
        });
    </script>

    {{-- Modal زيادة المستخدمين --}}
    <div id="addUsersModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/60">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="panel w-full max-w-md">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h5 class="text-lg font-bold">زيادة عدد المستخدمين</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-600"
                        onclick="closeAddUsersModal()">✕</button>
                </div>

                <div class="mb-4 p-3 bg-info/10 rounded-lg">
                    <p class="text-sm leading-relaxed">
                        الشركة: <strong id="addUsersCompanyName"></strong><br>
                        نوع الاشتراك: <strong id="addUsersPlanType"></strong><br>
                        المتبقي: <strong><span id="addUsersMonthsRemaining">0</span></strong> شهر<br>
                        العدد الحالي: <strong id="addUsersCurrentCount"></strong> مستخدم
                    </p>
                </div>

                <form action="{{ url('subscriptions/add-users') }}" method="POST">
                    @csrf
                    <input type="hidden" name="company_code" id="addUsersCompanyCode">
                    <input type="hidden" id="addUsersPricePerUser">
                    <input type="hidden" id="addUsersDaysRemaining">
                    <input type="hidden" name="additional_cost" id="addUsersAdditionalCost">

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">عدد المستخدمين المراد إضافتهم</label>
                        <input type="number" name="additional_users" id="addUsersAdditionalInput" class="form-input"
                            min="1" value="1" required onchange="calculateAdditionalCost()"
                            oninput="calculateAdditionalCost()">
                    </div>

                    <div class="mb-4 p-4 bg-primary/10 rounded-lg">
                        <div class="flex justify-between mb-2">
                            <span>العدد الإجمالي بعد الإضافة:</span>
                            <span class="font-bold"><span id="addUsersTotalCount">0</span> مستخدم</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-primary">
                            <span>التكلفة الإضافية:</span>
                            <span><span id="addUsersCostDisplay">0</span> دينار</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2" id="addUsersProrationNote">* يتم احتساب التكلفة حسب الأشهر المتبقية</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">نوع الدفع <span
                                class="text-danger">*</span></label>
                        <select name="payment_type" class="form-select" id="addUsersPaymentType"
                            onchange="toggleAddUsersPaymentType()" required>
                            <option value="">-- اختر نوع الدفع --</option>
                            <option value="cash">💵 كاش (دفع فوري)</option>
                            <option value="deferred">📋 آجل (دفع لاحقاً)</option>
                        </select>
                    </div>

                    <!-- حقول الدفع الكاش -->
                    <div id="addUsersCashFields" style="display: none;">
                        <div class="mb-4">
                            <label class="block text-sm font-semibold mb-2">طريقة الدفع <span
                                    class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" id="addUsersPaymentMethod"
                                onchange="togglePaymentCards()">
                                <option value="">-- اختر طريقة الدفع --</option>
                                <option value="cash">💵 نقدي (دفع مباشر)</option>
                                <option value="bank_transfer">🏦 تحويل بنكي (دفع مباشر)</option>
                                <option value="check">📄 شيك (دفع مباشر)</option>
                                <option value="online">💳 دفع إلكتروني (يُخصم من البطاقة)</option>
                            </select>
                            <small class="text-gray-500 mt-1 block" id="paymentMethodHint"></small>
                        </div>

                        <div class="mb-4" id="addUsersPaymentCardDiv" style="display: none;">
                            <label class="block text-sm font-semibold mb-2">اختر البطاقة <span
                                    class="text-danger">*</span></label>
                            <select name="payment_card_id" class="form-select" id="addUsersPaymentCard">
                                <option value="">-- اختر بطاقة الدفع --</option>
                            </select>
                            <small class="text-success">✅ سيتم إيداع المبلغ في البطاقة المختارة مباشرة</small>
                        </div>

                        <!-- رسالة توضيحية للدفع النقدي/البنكي -->
                        <div class="mb-4 p-3 bg-success/10 rounded-lg" id="directPaymentInfo" style="display: none;">
                            <div class="flex items-center gap-2 text-success">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-semibold">سيتم تسجيل الدفعة مباشرة عند الحفظ</span>
                            </div>
                        </div>
                    </div>

                    <!-- رسالة الدفع الآجل -->
                    <div class="mb-4 p-3 bg-warning/10 border border-warning rounded-lg" id="addUsersDeferredMessage"
                        style="display: none;">
                        <div class="flex items-center gap-2 text-warning">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold">دفع آجل</span>
                        </div>
                        <p class="text-sm mt-2 text-gray-600">
                            سيتم تسجيل التكلفة الإضافية كدين على الشركة.
                            المبلغ الواصل: <strong>0</strong> دينار
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">ملاحظات</label>
                        <textarea name="notes" class="form-textarea" rows="2" placeholder="أي ملاحظات إضافية..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" class="btn btn-outline-secondary"
                            onclick="closeAddUsersModal()">إلغاء</button>
                        <button type="submit" class="btn btn-success" id="addUsersSubmitBtn">
                            <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            <span id="addUsersSubmitText">حفظ والدفع</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal عرض اللوجو - يُضاف للـ body مباشرة عبر JavaScript -->
    <script>
        // إنشاء المودال ديناميكياً وإضافته للـ body
        document.addEventListener('DOMContentLoaded', function() {
            const modalHTML = `
                <div id="logoModalOverlay" onclick="closeLogoModal()" style="
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    background: rgba(0,0,0,0.6);
                    z-index: 99999;
                    justify-content: center;
                    align-items: center;
                ">
                    <div onclick="event.stopPropagation()" style="
                        position: relative;
                        background: white;
                        border-radius: 12px;
                        padding: 16px;
                        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
                        max-width: 280px;
                    ">
                        <button onclick="closeLogoModal()" style="
                            position: absolute;
                            top: -12px;
                            right: -12px;
                            width: 32px;
                            height: 32px;
                            background: #ef4444;
                            color: white;
                            border: none;
                            border-radius: 50%;
                            cursor: pointer;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 18px;
                            font-weight: bold;
                            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
                        ">×</button>
                        <img id="logoModalImg" src="" alt="" style="
                            width: 220px;
                            height: 220px;
                            object-fit: contain;
                            border-radius: 8px;
                            background: #f3f4f6;
                            display: block;
                        ">
                        <p id="logoModalName" style="
                            margin: 12px 0 0 0;
                            text-align: center;
                            font-weight: 600;
                            color: #1f2937;
                            font-size: 14px;
                        "></p>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        });

        function openLogoModal(logoUrl, companyName) {
            const overlay = document.getElementById('logoModalOverlay');
            const img = document.getElementById('logoModalImg');
            const name = document.getElementById('logoModalName');
            
            img.src = logoUrl;
            img.alt = companyName;
            name.textContent = companyName;
            
            overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeLogoModal() {
            const overlay = document.getElementById('logoModalOverlay');
            if (overlay) {
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLogoModal();
        });
    </script>
@endsection
