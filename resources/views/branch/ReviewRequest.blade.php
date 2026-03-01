@extends('layouts.app')

@section('page-title', 'مراجعة طلب #' . $WorkOrder->id)

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
                            {{ $WorkOrder->request_date ? \Carbon\Carbon::parse($WorkOrder->request_date)->format('Y-m-d H:i') : '-' }}
                        </p>
                    </div>
                    {{-- موعد التسليم --}}
                    <div class="flex items-center gap-2 mr-4 pr-4 border-r border-gray-200 dark:border-gray-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-warning/10 text-warning">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">موعد التسليم</p>
                            <p class="font-bold text-warning">
                                {{ $WorkOrder->delivery_datetime ? \Carbon\Carbon::parse($WorkOrder->delivery_datetime)->format('Y-m-d H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @php
                        $statusColors = [
                            'new' => 'bg-info',
                            'under_review' => 'bg-warning',
                            'waiting_customer' => 'bg-secondary',
                            'approved' => 'bg-success',
                            'rejected' => 'bg-danger',
                            'scheduled' => 'bg-primary',
                            'in_progress' => 'bg-warning',
                            'completed' => 'bg-success',
                            'cancelled' => 'bg-dark',
                        ];
                        $statusLabels = [
                            'new' => 'جديد',
                            'under_review' => 'قيد المراجعة',
                            'waiting_customer' => 'بانتظار العميل',
                            'approved' => 'معتمد',
                            'rejected' => 'مرفوض',
                            'scheduled' => 'مجدول',
                            'in_progress' => 'قيد التنفيذ',
                            'completed' => 'مكتمل',
                            'cancelled' => 'ملغي',
                        ];
                        $status = $WorkOrder->status_code ?? 'new';
                    @endphp
                    <span
                        class="badge {{ $statusColors[$status] ?? 'bg-gray-500' }} text-white px-4 py-2 text-sm rounded-full">
                        {{ $statusLabels[$status] ?? $status }}
                    </span>
                </div>
            </div>
        </div>

        @php
            $costPrice = $totalMaterialsCostPerMeter ?? 0;
            $salePrice = $WorkOrder->concreteMix->salePrice ?? 0;
            $quantity = $WorkOrder->quantity ?? 0;
            $totalCost = $totalMaterialsCost ?? 0;
            $totalSale = $salePrice * $quantity;
            // الربح = 0 إذا لم يتم اختيار سعر بيع، وإلا إجمالي البيع - تكاليف المواد
            $realProfit = $totalSale > 0 ? $totalSale - $totalCost : 0;
            $profit = $realProfit;
            $profitMargin = $totalSale > 0 ? ($realProfit / $totalSale) * 100 : 0;
        @endphp

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            {{-- العمود الأيمن: الإجراءات --}}
            <div class="xl:col-span-1 space-y-6 order-first xl:order-last">
                {{-- الموافقة على الطلب --}}
                @if (!$WorkOrder->branch_approval_status || $WorkOrder->branch_approval_status == 'pending')
                    <div class="panel border-2 border-success/30 bg-success/5">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-success text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h5 class="text-lg font-semibold text-success">الموافقة على الطلب</h5>
                        </div>

                        {!! Form::open([
                            'route' => ['companyBranch.update', $WorkOrder->id],
                            'method' => 'PUT',
                            'autocomplete' => 'off',
                        ]) !!}
                        <div class="space-y-4">
                            <div>
                                <label class="font-semibold text-gray-700 dark:text-gray-300 mb-2 block">السعر المقترح (ألف
                                    دينار)</label>

                                {{-- الفئات السعرية --}}
                                @if (isset($pricingCategories) && count($pricingCategories) > 0)
                                    <div class="mb-3">
                                        <label class="text-sm text-gray-600 dark:text-gray-400 mb-1 block">اختر الفئة
                                            السعرية</label>
                                        <select id="priceCategorySelect" class="form-select w-full"
                                            onchange="updatePriceFromCategory()">
                                            <option value="">-- اختر فئة سعرية --</option>
                                            @foreach ($pricingCategories as $category)
                                                <option value="{{ $category['total_price'] }}"
                                                    data-price-per-meter="{{ $category['price_per_meter'] }}">
                                                    {{ $category['name'] }} -
                                                    {{ number_format($category['price_per_meter'], 0) }} د/م³
                                                    (الإجمالي: {{ number_format($category['total_price'], 0) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="flex flex-col gap-2">
                                    <input type="number" step="0.01" id="priceInput" name="price" class="form-input"
                                        value="{{ $totalSale }}" placeholder="أدخل السعر الإجمالي" required>
                                    <div
                                        class="rounded-lg bg-gradient-to-r from-primary/10 to-primary/5 p-3 border border-primary/20 text-center">
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">العرض المُنسّق</p>
                                        <p id="formattedPrice" class="font-bold text-primary text-lg">
                                            {{ number_format($totalSale, 0) }}</p>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">السعر الافتراضي: <span
                                        class="font-semibold">{{ number_format($totalSale, 0) }}</span> ألف دينار</p>
                            </div>
                            <div>
                                <label class="font-semibold text-gray-700 dark:text-gray-300 mb-2 block">ملاحظات
                                    للمقاول</label>
                                <textarea name="branch_approval_note" class="form-input" rows="3"
                                    placeholder="أدخل أي ملاحظات تريد إرسالها للمقاول..."></textarea>
                            </div>
                            <button type="submit" name="active" value="branch_approval_status"
                                class="btn btn-success w-full">
                                <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                الموافقة وإرسال العرض
                            </button>
                        </div>
                        {!! Form::close() !!}
                    </div>

                    {{-- رفض الطلب --}}
                    <div class="panel border-2 border-danger/30 bg-danger/5">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-danger text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <h5 class="text-lg font-semibold text-danger">رفض الطلب</h5>
                        </div>

                        {!! Form::open([
                            'route' => ['companyBranch.update', $WorkOrder->id],
                            'method' => 'PUT',
                            'autocomplete' => 'off',
                        ]) !!}
                        <div class="space-y-4">
                            <div>
                                <label class="font-semibold text-gray-700 dark:text-gray-300 mb-2 block">سبب الرفض</label>
                                <textarea name="branch_reject_note" class="form-input" rows="3" placeholder="أدخل سبب رفض الطلب..." required></textarea>
                            </div>
                            <button type="submit" name="active" value="branch_reject" class="btn btn-danger w-full">
                                <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                رفض الطلب
                            </button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                @endif

                {{-- حالة الموافقة السابقة --}}
                @if ($WorkOrder->branch_approval_status == 'approved')
                    <div class="panel border-2 border-success bg-success/10">
                        <div class="text-center py-4">
                            <div
                                class="flex h-16 w-16 mx-auto items-center justify-center rounded-full bg-success text-white mb-3">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h5 class="text-lg font-bold text-success">تمت الموافقة على الطلب</h5>
                            <p class="text-sm text-gray-600 mt-2">
                                بتاريخ:
                                {{ $WorkOrder->branch_approval_date ? \Carbon\Carbon::parse($WorkOrder->branch_approval_date)->format('Y-m-d H:i') : '-' }}
                            </p>
                            <p class="text-sm text-gray-600">السعر المعتمد: {{ number_format($WorkOrder->price ?? 0, 0) }}
                                ألف دينار</p>
                            @if ($WorkOrder->branch_approval_note)
                                <p class="text-sm text-gray-500 mt-2 p-2 bg-white dark:bg-gray-800 rounded">
                                    {{ $WorkOrder->branch_approval_note }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($WorkOrder->branch_approval_status == 'rejected')
                    <div class="panel border-2 border-danger bg-danger/10">
                        <div class="text-center py-4">
                            <div
                                class="flex h-16 w-16 mx-auto items-center justify-center rounded-full bg-danger text-white mb-3">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <h5 class="text-lg font-bold text-danger">تم رفض الطلب</h5>
                            <p class="text-sm text-gray-600 mt-2">
                                بتاريخ:
                                {{ $WorkOrder->branch_approval_date ? \Carbon\Carbon::parse($WorkOrder->branch_approval_date)->format('Y-m-d H:i') : '-' }}
                            </p>
                            @if ($WorkOrder->branch_approval_note)
                                <p class="text-sm text-gray-500 mt-2 p-2 bg-white dark:bg-gray-800 rounded">
                                    {{ $WorkOrder->branch_approval_note }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- دورة حياة الطلب --}}
                <div class="panel">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-info/10 text-info">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h5 class="text-lg font-semibold dark:text-white-light">دورة حياة الطلب</h5>
                    </div>

                    <div class="space-y-4">
                        @php
                            $stages = [
                                ['key' => 'new', 'label' => 'طلب جديد', 'done' => true],
                                [
                                    'key' => 'branch_review',
                                    'label' => 'مراجعة الفرع',
                                    'done' => $WorkOrder->branch_approval_status != null,
                                ],
                                [
                                    'key' => 'customer_approval',
                                    'label' => 'موافقة المقاول',
                                    'done' => $WorkOrder->requester_approval_status == 'approved',
                                ],
                                [
                                    'key' => 'scheduled',
                                    'label' => 'جدولة التنفيذ',
                                    'done' => in_array($WorkOrder->status_code, [
                                        'scheduled',
                                        'in_progress',
                                        'completed',
                                    ]),
                                ],
                                [
                                    'key' => 'execution',
                                    'label' => 'التنفيذ',
                                    'done' => in_array($WorkOrder->status_code, ['in_progress', 'completed']),
                                ],
                                [
                                    'key' => 'completed',
                                    'label' => 'مكتمل',
                                    'done' => $WorkOrder->status_code == 'completed',
                                ],
                            ];
                        @endphp

                        @foreach ($stages as $index => $stage)
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full {{ $stage['done'] ? 'bg-success text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500' }}">
                                    @if ($stage['done'])
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <span
                                    class="{{ $stage['done'] ? 'text-success font-semibold' : 'text-gray-500' }}">{{ $stage['label'] }}</span>
                            </div>
                            @if ($index < count($stages) - 1)
                                <div
                                    class="mr-4 border-r-2 h-4 {{ $stage['done'] ? 'border-success' : 'border-gray-200 dark:border-gray-700' }}">
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- أزرار إضافية --}}
                <div class="panel">
                    <a href="{{ url('companyBranch/listNewRequestOrders') }}"
                        class="btn btn-outline-primary w-full mb-2">
                        <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            {{-- العمود الأيسر: معلومات الطلب --}}
            <div class="xl:col-span-2 space-y-6">
                {{-- معلومات صاحب الطلب وتفاصيل الطلب --}}
                <div class="panel">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-success/10 text-success">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h5 class="text-lg font-semibold dark:text-white-light">معلومات صاحب الطلب وتفاصيل الطلب</h5>
                    </div>

                    {{-- معلومات صاحب الطلب --}}
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                            <span class="text-xs text-gray-500">الاسم</span>
                            <p class="font-semibold text-gray-800 dark:text-white">
                                {{ $WorkOrder->sender->fullname ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                            <span class="text-xs text-gray-500">نوع صاحب الطلب</span>
                            <p class="font-semibold text-gray-800 dark:text-white">
                                @if ($WorkOrder->sender_type == 'cont')
                                    مقاول
                                @elseif($WorkOrder->sender_type == 'delegate')
                                    مندوب
                                @elseif($WorkOrder->sender_type == 'direct')
                                    عميل مباشر
                                @else
                                    {{ $WorkOrder->sender_type ?? '-' }}
                                @endif
                            </p>
                        </div>
                        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                            <span class="text-xs text-gray-500">رقم الهاتف</span>
                            <p class="font-semibold text-gray-800 dark:text-white">
                                {{ $WorkOrder->customer_phone ?? ($WorkOrder->sender->phone ?? '-') }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                            <span class="text-xs text-gray-500">الفرع</span>
                            <p class="font-semibold text-gray-800 dark:text-white">
                                {{ $WorkOrder->branch->branch_name ?? '-' }}</p>
                        </div>
                        {{-- تفاصيل الطلب داخل نفس الشبكة --}}
                        <div
                            class="rounded-lg bg-gradient-to-br from-primary/5 to-primary/10 p-4 border border-primary/20">
                            <span class="text-xs text-gray-500">نوع الخرسانة</span>
                            <p class="font-bold text-primary text-lg">{{ $WorkOrder->concreteMix->classification ?? '-' }}
                            </p>
                        </div>
                        <div
                            class="rounded-lg bg-gradient-to-br from-success/5 to-success/10 p-4 border border-success/20">
                            <span class="text-xs text-gray-500">الكمية المطلوبة</span>
                            <p class="font-bold text-success text-lg">{{ $WorkOrder->quantity ?? 0 }} م³</p>
                        </div>
                    </div>

                    {{-- فاصل --}}
                    <div class="my-5 border-t border-gray-200 dark:border-gray-700"></div>

                    {{-- حساب التكاليف والأسعار --}}
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-warning/10 text-warning">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h6 class="font-semibold text-gray-700 dark:text-gray-300">حساب التكاليف والأسعار</h6>
                    </div>
                    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                        <div
                            class="rounded-lg bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-800 text-center">
                            <span class="text-xs text-red-600 dark:text-red-400">سعر التكلفة / م³</span>
                            <p id="costPriceDisplayInner" class="font-bold text-red-600 text-xl mt-1">
                                {{ number_format($costPrice, 0) }}</p>
                            <span class="text-xs text-gray-500">دينار</span>
                        </div>
                        <div
                            class="rounded-lg bg-green-50 dark:bg-green-900/20 p-4 border border-green-200 dark:border-green-800 text-center">
                            <span class="text-xs text-green-600 dark:text-green-400">سعر البيع / م³</span>
                            <p id="salePriceDisplayInner" class="font-bold text-green-600 text-xl mt-1">
                                {{ number_format($salePrice, 0) }}</p>
                            <span class="text-xs text-gray-500">دينار</span>
                        </div>
                        <div
                            class="rounded-lg bg-red-100 dark:bg-red-900/30 p-4 border border-red-300 dark:border-red-700 text-center">
                            <span class="text-xs text-red-700 dark:text-red-300">إجمالي التكلفة</span>
                            <p id="totalCostDisplayInner" class="font-bold text-red-700 text-xl mt-1">
                                {{ number_format($totalCost, 0) }}</p>
                            <span class="text-xs text-gray-500">دينار</span>
                        </div>
                        <div
                            class="rounded-lg bg-green-100 dark:bg-green-900/30 p-4 border border-green-300 dark:border-green-700 text-center">
                            <span class="text-xs text-green-700 dark:text-green-300">إجمالي البيع</span>
                            <p id="totalSaleDisplayInner" class="font-bold text-green-700 text-xl mt-1">
                                {{ number_format($totalSale, 0) }}</p>
                            <span class="text-xs text-gray-500">دينار</span>
                        </div>
                    </div>

                    {{-- الربح المتوقع --}}
                    <div class="mt-4">
                        <div class="rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 p-4 text-white text-center">
                            <span class="text-sm opacity-80">الربح المتوقع</span>
                            <p id="profitDisplayInner" class="font-bold text-2xl mt-1">
                                {{ number_format($realProfit, 0) }} دينار</p>
                        </div>
                    </div>

                    {{-- فاصل --}}
                    <div class="my-5 border-t border-gray-200 dark:border-gray-700"></div>

                    {{-- موقع العمل --}}
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
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
                            <span class="text-xs text-yellow-600 dark:text-yellow-400">ملاحظات صاحب الطلب</span>
                            <p class="text-gray-800 dark:text-white">{{ $WorkOrder->note }}</p>
                        </div>
                    @endif
                </div>

                {{-- الفئات السعرية المتاحة --}}
                @if (
                    $WorkOrder->concreteMix &&
                        $WorkOrder->concreteMix->categoryPrices &&
                        $WorkOrder->concreteMix->categoryPrices->count() > 0)
                    <div class="panel">
                        <div class="mb-5 flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <h5 class="text-lg font-semibold dark:text-white-light">الفئات السعرية المتاحة</h5>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-800">
                                        <th class="p-3 text-right font-semibold text-gray-700 dark:text-gray-300">الفئة
                                        </th>
                                        <th class="p-3 text-center font-semibold text-gray-700 dark:text-gray-300">سعر
                                            البيع / م³</th>
                                        <th class="p-3 text-center font-semibold text-gray-700 dark:text-gray-300">الإجمالي
                                            ({{ $quantity }} م³)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($WorkOrder->concreteMix->categoryPrices as $catPrice)
                                        @if ($catPrice->is_active && $catPrice->pricingCategory)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                <td class="p-3">
                                                    <span
                                                        class="font-semibold text-gray-800 dark:text-white">{{ $catPrice->pricingCategory->name }}</span>
                                                    @if ($catPrice->pricingCategory->description)
                                                        <p class="text-xs text-gray-500">
                                                            {{ $catPrice->pricingCategory->description }}</p>
                                                    @endif
                                                </td>
                                                <td class="p-3 text-center font-semibold text-primary">
                                                    {{ number_format($catPrice->price_per_meter, 0) }}</td>
                                                <td class="p-3 text-center font-bold text-success">
                                                    {{ number_format($catPrice->price_per_meter * $quantity, 0) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-500 mt-3 text-center">* جميع الأسعار بالألف دينار عراقي</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // المتغيرات الأساسية
        const quantity = {{ $quantity }};
        const baseCostPrice = {{ $costPrice }};
        const baseSalePrice = {{ $salePrice }};
        const totalMaterialsCost = {{ $totalMaterialsCost ?? 0 }};

        // تنسيق السعر مع فاصلات كل 3 أرقام
        function formatPriceDisplay(value) {
            if (!value) return '0';
            const numValue = parseInt(value);
            return numValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // تحديث حساب التكاليف والأسعار
        function updateCostCalculations(salePricePerMeter, totalSalePrice) {
            // حساب القيم
            const costPricePerMeter = baseCostPrice;
            const totalCost = totalMaterialsCost; // التكلفة الإجمالية المحسوبة من المواد
            const totalSale = totalSalePrice || (salePricePerMeter * quantity);
            // الربح = 0 إذا لم يتم اختيار سعر، وإلا إجمالي البيع - التكلفة
            const realProfit = totalSale > 0 ? (totalSale - totalMaterialsCost) : 0;
            const profitMargin = totalSale > 0 ? (realProfit / totalSale) * 100 : 0;

            // تحديث العرض - القسم الداخلي
            const salePriceDisplay = document.getElementById('salePriceDisplayInner');
            const totalCostDisplay = document.getElementById('totalCostDisplayInner');
            const totalSaleDisplay = document.getElementById('totalSaleDisplayInner');
            const profitDisplay = document.getElementById('profitDisplayInner');
            const profitMarginDisplay = document.getElementById('profitMarginDisplayInner');

            if (salePriceDisplay) salePriceDisplay.textContent = formatPriceDisplay(salePricePerMeter);
            if (totalCostDisplay) totalCostDisplay.textContent = formatPriceDisplay(totalCost);
            if (totalSaleDisplay) totalSaleDisplay.textContent = formatPriceDisplay(totalSale);
            if (profitDisplay) profitDisplay.textContent = formatPriceDisplay(realProfit) + ' دينار';
            if (profitMarginDisplay) profitMarginDisplay.textContent = profitMargin.toFixed(1) + '%';
        }

        // تحديث السعر عند اختيار فئة سعرية
        function updatePriceFromCategory() {
            const select = document.getElementById('priceCategorySelect');
            const priceInput = document.getElementById('priceInput');
            const formattedPriceDisplay = document.getElementById('formattedPrice');

            console.log('updatePriceFromCategory called');
            console.log('select:', select);
            console.log('select.value:', select ? select.value : 'null');

            if (select && priceInput) {
                const selectedOption = select.options[select.selectedIndex];
                const pricePerMeter = parseFloat(selectedOption.getAttribute('data-price-per-meter')) || 0;
                const totalPrice = parseFloat(select.value) || 0;

                console.log('pricePerMeter:', pricePerMeter);
                console.log('totalPrice:', totalPrice);

                if (totalPrice > 0) {
                    priceInput.value = totalPrice;
                    if (formattedPriceDisplay) {
                        formattedPriceDisplay.textContent = formatPriceDisplay(totalPrice);
                    }

                    // تحديث حساب التكاليف
                    updateCostCalculations(pricePerMeter, totalPrice);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const priceInput = document.getElementById('priceInput');
            const formattedPriceDisplay = document.getElementById('formattedPrice');

            if (priceInput && formattedPriceDisplay) {
                priceInput.addEventListener('input', function(e) {
                    const totalPrice = parseFloat(this.value) || 0;
                    formattedPriceDisplay.textContent = formatPriceDisplay(totalPrice) || '0';

                    // حساب سعر المتر من السعر الإجمالي
                    const salePricePerMeter = quantity > 0 ? totalPrice / quantity : 0;
                    updateCostCalculations(salePricePerMeter, totalPrice);
                });
                formattedPriceDisplay.textContent = formatPriceDisplay(priceInput.value);
            }
        });
    </script>
@endsection
