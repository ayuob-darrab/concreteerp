@extends('layouts.app')

@section('page-title', 'عرض السعر المقترح - طلب #' . $WorkOrder->id)

@section('content')
    <div class="space-y-6">
        {{-- شريط الحالة --}}
        <div class="panel">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">طلب عمل #{{ $WorkOrder->id }}</h2>
                        <p class="text-sm text-gray-500">
                            تاريخ الطلب:
                            {{ $WorkOrder->request_date ? \Carbon\Carbon::parse($WorkOrder->request_date)->format('Y-m-d H:i') : '-' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="badge bg-warning text-white px-4 py-2 text-sm rounded-full">
                        بانتظار موافقتك
                    </span>
                </div>
            </div>
        </div>

        {{-- عرض السعر المقترح بشكل بارز --}}
        <div class="panel border-2 border-primary bg-gradient-to-r from-primary/5 to-primary/10">
            <div class="text-center py-6">
                <div class="flex h-20 w-20 mx-auto items-center justify-center rounded-full bg-primary text-white mb-4">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg text-gray-600 dark:text-gray-400 mb-2">السعر المقترح من الفرع</h3>
                <p class="text-5xl font-bold text-primary mb-1">
                    {{ number_format($WorkOrder->price ?? 0, 0) }}
                </p>
                <p class="text-xl text-gray-500 mb-2">ألف دينار عراقي</p>
                @php
                    $qty = $WorkOrder->quantity ?? 0;
                    $total = $WorkOrder->price ?? 0;
                    $perMeter = $qty > 0 ? round($total / $qty, 0) : 0;
                @endphp
                <p class="text-base text-gray-600 dark:text-gray-400">
                    <span class="font-semibold">سعر المتر:</span> {{ number_format($perMeter, 0) }} ألف د.ع / م³
                </p>

                @if ($WorkOrder->branch_approval_note)
                    <div
                        class="mt-6 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 max-w-lg mx-auto">
                        <p class="text-sm text-gray-500 mb-1">ملاحظات الفرع:</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $WorkOrder->branch_approval_note }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            {{-- العمود الأيمن: إجراءات الموافقة --}}
            <div class="xl:col-span-1 space-y-6 order-first xl:order-last">
                @if ($WorkOrder->requester_approval_status === null)
                    {{-- قبول العرض --}}
                    <div class="panel border-2 border-success/30 bg-success/5">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-success text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h5 class="text-lg font-semibold text-success">قبول العرض</h5>
                        </div>

                        <form action="{{ url('contractors/' . $WorkOrder->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="active" value="ApproveRequest">

                            <div class="space-y-4">
                                <div class="rounded-lg bg-success/10 p-4 border border-success/20 text-center">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">السعر الذي ستوافق عليه</p>
                                    <p class="text-2xl font-bold text-success">
                                        {{ number_format($WorkOrder->price ?? 0, 0) }} د.ع</p>
                                    @if (($WorkOrder->quantity ?? 0) > 0)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            سعر المتر: {{ number_format(round(($WorkOrder->price ?? 0) / $WorkOrder->quantity, 0), 0) }} د.ع / م³
                                        </p>
                                    @endif
                                </div>

                                <div>
                                    <label class="font-semibold text-gray-700 dark:text-gray-300 mb-2 block">ملاحظات
                                        (اختياري)</label>
                                    <textarea name="accept_note" class="form-input" rows="3" placeholder="أدخل ملاحظاتك إن وجدت..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-success w-full">
                                    <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    موافقة على العرض
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- رفض العرض --}}
                    <div class="panel border-2 border-danger/30 bg-danger/5">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-danger text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <h5 class="text-lg font-semibold text-danger">رفض العرض</h5>
                        </div>

                        <form action="{{ url('contractors/' . $WorkOrder->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="active" value="RejectRequest">

                            <div class="space-y-4">
                                <div>
                                    <label class="font-semibold text-gray-700 dark:text-gray-300 mb-2 block">سبب
                                        الرفض</label>
                                    <textarea name="reject_note" class="form-input" rows="3" placeholder="أدخل سبب رفض العرض..." required></textarea>
                                </div>

                                <button type="submit" class="btn btn-danger w-full">
                                    <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    رفض العرض
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    {{-- حالة الموافقة/الرفض السابقة --}}
                    @if ($WorkOrder->requester_approval_status == 'approved')
                        <div class="panel border-2 border-success bg-success/10">
                            <div class="text-center py-4">
                                <div
                                    class="flex h-16 w-16 mx-auto items-center justify-center rounded-full bg-success text-white mb-3">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <h5 class="text-lg font-bold text-success">تمت الموافقة على العرض</h5>
                                <p class="text-sm text-gray-600 mt-2">
                                    بتاريخ:
                                    {{ $WorkOrder->requester_approval_date ? \Carbon\Carbon::parse($WorkOrder->requester_approval_date)->format('Y-m-d H:i') : '-' }}
                                </p>
                                <p class="text-sm text-gray-600">السعر المتفق عليه:
                                    {{ number_format($WorkOrder->price ?? 0, 0) }} د.ع</p>
                                @if (($WorkOrder->quantity ?? 0) > 0)
                                    <p class="text-sm text-gray-600">سعر المتر:
                                        {{ number_format(round(($WorkOrder->price ?? 0) / $WorkOrder->quantity, 0), 0) }} د.ع / م³</p>
                                @endif
                                @if ($WorkOrder->requester_approval_note)
                                    <p class="text-sm text-gray-500 mt-2 p-2 bg-white dark:bg-gray-800 rounded">
                                        {{ $WorkOrder->requester_approval_note }}</p>
                                @endif
                            </div>
                        </div>
                    @elseif ($WorkOrder->requester_approval_status == 'rejected')
                        <div class="panel border-2 border-danger bg-danger/10">
                            <div class="text-center py-4">
                                <div
                                    class="flex h-16 w-16 mx-auto items-center justify-center rounded-full bg-danger text-white mb-3">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <h5 class="text-lg font-bold text-danger">تم رفض العرض</h5>
                                <p class="text-sm text-gray-600 mt-2">
                                    بتاريخ:
                                    {{ $WorkOrder->requester_approval_date ? \Carbon\Carbon::parse($WorkOrder->requester_approval_date)->format('Y-m-d H:i') : '-' }}
                                </p>
                                @if ($WorkOrder->requester_approval_note)
                                    <p class="text-sm text-gray-500 mt-2 p-2 bg-white dark:bg-gray-800 rounded">
                                        {{ $WorkOrder->requester_approval_note }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif

                {{-- زر العودة --}}
                <div class="panel">
                    <a href="{{ url('contractors/CheckRequestsContractor') }}" class="btn btn-outline-primary w-full">
                        <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            {{-- العمود الأيسر: تفاصيل الطلب --}}
            <div class="xl:col-span-2 space-y-6">
                {{-- معلومات الطلب --}}
                <div class="panel">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h5 class="text-lg font-semibold dark:text-white-light">تفاصيل الطلب</h5>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div
                            class="rounded-lg bg-gradient-to-br from-primary/5 to-primary/10 p-4 border border-primary/20">
                            <span class="text-xs text-gray-500">نوع الخرسانة</span>
                            <p class="font-bold text-primary text-lg">{{ $WorkOrder->ConcreteMix->classification ?? '-' }}
                            </p>
                        </div>
                        <div
                            class="rounded-lg bg-gradient-to-br from-success/5 to-success/10 p-4 border border-success/20">
                            <span class="text-xs text-gray-500">الكمية المطلوبة</span>
                            <p class="font-bold text-success text-lg">{{ $WorkOrder->quantity ?? 0 }} م³</p>
                        </div>
                        <div
                            class="rounded-lg bg-gradient-to-br from-warning/5 to-warning/10 p-4 border border-warning/20">
                            <span class="text-xs text-gray-500">موعد التسليم</span>
                            <p class="font-bold text-warning text-lg">
                                {{ $WorkOrder->delivery_datetime ? \Carbon\Carbon::parse($WorkOrder->delivery_datetime)->format('Y-m-d H:i') : '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                            <span class="text-xs text-gray-500">الفرع</span>
                            <p class="font-semibold text-gray-800 dark:text-white">
                                {{ $WorkOrder->branch->branch_name ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                            <span class="text-xs text-gray-500">تاريخ الطلب</span>
                            <p class="font-semibold text-gray-800 dark:text-white">
                                {{ $WorkOrder->request_date ? \Carbon\Carbon::parse($WorkOrder->request_date)->format('Y-m-d H:i') : '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                        <span class="text-xs text-gray-500">موقع العمل</span>
                        <p class="font-semibold text-gray-800 dark:text-white">{{ $WorkOrder->location ?? '-' }}</p>

                        @if ($WorkOrder->location_map_url)
                            <div class="mt-3">
                                <a href="{{ $WorkOrder->location_map_url }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                    </svg>
                                    فتح الموقع على خرائط Google
                                </a>
                            </div>
                        @endif

                        @if ($WorkOrder->location_lat && $WorkOrder->location_lng)
                            <div class="mt-3 rounded-lg overflow-hidden border border-gray-200">
                                <iframe width="100%" height="200" frameborder="0" style="border:0"
                                    src="https://maps.google.com/maps?q={{ $WorkOrder->location_lat }},{{ $WorkOrder->location_lng }}&z=15&output=embed"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        @endif
                    </div>

                    @if ($WorkOrder->note)
                        <div
                            class="mt-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 p-4 border border-yellow-200 dark:border-yellow-800">
                            <span class="text-xs text-yellow-600 dark:text-yellow-400">ملاحظاتك على الطلب</span>
                            <p class="text-gray-800 dark:text-white">{{ $WorkOrder->note }}</p>
                        </div>
                    @endif
                </div>

                {{-- ملخص السعر --}}
                <div class="panel">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-warning/10 text-warning">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h5 class="text-lg font-semibold dark:text-white-light">ملخص الأسعار</h5>
                    </div>

                    @php
                        $quantity = $WorkOrder->quantity ?? 0;
                        $totalPrice = $WorkOrder->price ?? 0;
                        $pricePerMeter = $quantity > 0 ? $totalPrice / $quantity : 0;
                    @endphp

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div
                            class="rounded-lg bg-blue-50 dark:bg-blue-900/20 p-4 border border-blue-200 dark:border-blue-800 text-center">
                            <span class="text-xs text-blue-600 dark:text-blue-400">الكمية</span>
                            <p class="font-bold text-blue-600 text-xl mt-1">{{ number_format($quantity, 2) }}</p>
                            <span class="text-xs text-gray-500">متر مكعب</span>
                        </div>
                        <div
                            class="rounded-lg bg-purple-50 dark:bg-purple-900/20 p-4 border border-purple-200 dark:border-purple-800 text-center">
                            <span class="text-xs text-purple-600 dark:text-purple-400">سعر المتر المكعب</span>
                            <p class="font-bold text-purple-600 text-xl mt-1">{{ number_format($pricePerMeter, 0) }}</p>
                            <span class="text-xs text-gray-500">ألف دينار</span>
                        </div>
                        <div
                            class="rounded-lg bg-green-100 dark:bg-green-900/30 p-4 border border-green-300 dark:border-green-700 text-center">
                            <span class="text-xs text-green-700 dark:text-green-300">السعر الإجمالي</span>
                            <p class="font-bold text-green-700 text-2xl mt-1">{{ number_format($totalPrice, 0) }}</p>
                            <span class="text-xs text-gray-500">ألف دينار</span>
                        </div>
                    </div>
                </div>

                {{-- معلومات الموافقة من الفرع --}}
                <div class="panel">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-success/10 text-success">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h5 class="text-lg font-semibold dark:text-white-light">موافقة الفرع</h5>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                            <span class="text-xs text-gray-500">حالة الموافقة</span>
                            <p class="font-semibold text-success">✓ تمت الموافقة من الفرع</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                            <span class="text-xs text-gray-500">تاريخ الموافقة</span>
                            <p class="font-semibold text-gray-800 dark:text-white">
                                {{ $WorkOrder->branch_approval_date ? \Carbon\Carbon::parse($WorkOrder->branch_approval_date)->format('Y-m-d H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
