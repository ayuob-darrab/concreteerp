@extends('layouts.app')

@section('page-title', 'تفاصيل أمر العمل')

@section('content')
    <div class="panel mt-6">
        {{-- العنوان والأزرار --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <div>
                <h3 class="text-xl font-bold dark:text-white-light">
                    <span class="text-2xl">📋</span> أمر العمل #{{ $job->job_number }}
                </h3>
                <p class="text-gray-500 mt-1">
                    تاريخ الإنشاء: {{ \Carbon\Carbon::parse($job->created_at)->format('Y-m-d H:i') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                @switch($job->status)
                    @case('pending')
                        <span class="badge badge-lg bg-warning/20 text-warning px-4 py-2 rounded-full text-base font-bold">
                            ⏳ في الانتظار
                        </span>
                    @break

                    @case('assigned')
                        <span class="badge badge-lg bg-info/20 text-info px-4 py-2 rounded-full text-base font-bold">
                            👷 تم التعيين
                        </span>
                    @break

                    @case('in_progress')
                        <span
                            class="badge badge-lg bg-primary/20 text-primary px-4 py-2 rounded-full text-base font-bold animate-pulse">
                            🔄 قيد التنفيذ
                        </span>
                    @break

                    @case('completed')
                        <span class="badge badge-lg bg-success/20 text-success px-4 py-2 rounded-full text-base font-bold">
                            ✅ مكتمل
                        </span>
                    @break

                    @case('cancelled')
                        <span class="badge badge-lg bg-danger/20 text-danger px-4 py-2 rounded-full text-base font-bold">
                            ❌ ملغي
                        </span>
                    @break
                @endswitch
            </div>
        </div>

        {{-- معلومات الطلب الأساسية --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- معلومات العميل --}}
            <div
                class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl p-5">
                <h4 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-4">
                    <span class="text-xl">👤</span> معلومات العميل
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">اسم العميل:</span>
                        <span class="font-semibold">{{ $job->customer_name ?: $job->order->sender->fullname ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">رقم الهاتف:</span>
                        <span class="font-semibold">{{ $job->customer_phone ?: $job->order->sender->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">نوع العميل:</span>
                        <span class="font-semibold">{{ $job->customer_type == 'contractor' ? 'مقاول' : 'عميل' }}</span>
                    </div>
                </div>
            </div>

            {{-- معلومات الطلب --}}
            <div
                class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 rounded-xl p-5">
                <h4 class="text-lg font-semibold text-green-800 dark:text-green-300 mb-4">
                    <span class="text-xl">📦</span> معلومات الطلب
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">رقم الطلب:</span>
                        <span class="font-semibold">{{ $job->order->id ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">نوع الخرسانة:</span>
                        <span class="font-semibold">{{ $job->concreteType->classification ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">الكمية المطلوبة:</span>
                        <span class="font-semibold text-lg text-green-600">{{ $job->total_quantity ?? 0 }} م³</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- الآليات والمشرف --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- البَم المخصص --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold">
                        <span class="text-xl">🚜</span> البَم المخصص
                    </h4>
                    @if ($job->status !== 'completed' && $job->status !== 'cancelled')
                        <a href="{{ route('companyBranch.workJob.assignPump', $job->id) }}"
                            class="btn btn-{{ $job->default_pump_id ? 'outline-primary' : 'primary' }} btn-sm">
                            {{ $job->default_pump_id ? '🔄 تغيير' : '➕ إضافة بَم' }}
                        </a>
                    @endif
                </div>

                @if ($job->default_pump_id && $job->defaultPump)
                    <div
                        class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-gray-600 dark:text-gray-400 text-xs block">رقم البَم</span>
                                <span class="font-bold text-green-700 dark:text-green-300">
                                    {{ $job->defaultPump->car_number }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400 text-xs block">السائق</span>
                                <span class="font-semibold">
                                    {{ $job->defaultPumpDriver->name ?? 'غير محدد' }}
                                </span>
                            </div>
                        </div>
                        @if ($job->pump_notes)
                            <div class="mt-2 pt-2 border-t border-green-200 dark:border-green-700">
                                <p class="text-xs text-gray-600">{{ $job->pump_notes }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div
                        class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 text-center border-2 border-dashed border-gray-300 dark:border-gray-700">
                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <p class="text-gray-500 text-sm mb-2">لم يتم تخصيص بَم بعد</p>
                        @if ($job->status !== 'completed' && $job->status !== 'cancelled')
                            <a href="{{ route('companyBranch.workJob.assignPump', $job->id) }}"
                                class="btn btn-primary btn-sm">
                                ➕ تخصيص بَم الآن
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- المشرف والملاحظات --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                <h4 class="text-lg font-semibold mb-4">
                    <span class="text-xl">👷</span> المشرف والملاحظات
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">المشرف:</span>
                        <span class="font-semibold">{{ $job->supervisor->name ?? 'غير محدد' }}</span>
                    </div>
                    @if ($job->notes)
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 text-sm block mb-1">ملاحظات:</span>
                            <p class="text-sm bg-gray-50 dark:bg-gray-900/50 p-3 rounded">{{ $job->notes }}</p>
                        </div>
                    @endif
                    @if ($job->internal_notes)
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 text-sm block mb-1">ملاحظات داخلية:</span>
                            <p class="text-sm bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded">{{ $job->internal_notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- معلومات التنفيذ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            {{-- التواريخ --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                <h4 class="text-lg font-semibold mb-4">
                    <span class="text-xl">📅</span> التواريخ
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">التاريخ المطلوب:</span>
                        <span
                            class="font-semibold">{{ $job->scheduled_date ? \Carbon\Carbon::parse($job->scheduled_date)->format('Y-m-d') : '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاريخ البدء:</span>
                        <span
                            class="font-semibold">{{ $job->started_at ? \Carbon\Carbon::parse($job->started_at)->format('Y-m-d H:i') : '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاريخ الانتهاء:</span>
                        <span
                            class="font-semibold">{{ $job->completed_at ? \Carbon\Carbon::parse($job->completed_at)->format('Y-m-d H:i') : '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- التقدم (نسبة الإنجاز من المنفذ/الإجمالي ليتطابق الشريط مع الأرقام) --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                <h4 class="text-lg font-semibold mb-4">
                    <span class="text-xl">📊</span> نسبة الإنجاز
                </h4>
                @php
                    $totalQuantity = (float) ($job->total_quantity ?? 0);
                    $executedQuantity = (float) ($job->executed_quantity ?? 0);
                    $progress = $totalQuantity > 0 ? min(100, round(($executedQuantity / $totalQuantity) * 100, 1)) : 0;
                    $remainingQuantity = max(0, $totalQuantity - $executedQuantity);
                    $lossQuantity = $job->losses->sum('quantity_lost') ?? 0;
                @endphp
                <div class="relative pt-4">
                    <div class="h-6 bg-gray-300 dark:bg-gray-600 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500"
                            style="width: {{ $progress }}%; background: linear-gradient(90deg, #3b82f6, #10b981);">
                        </div>
                    </div>
                    <div class="flex justify-between mt-2 text-sm">
                        <span>المنفذ: {{ number_format($executedQuantity, 1) }} م³</span>
                        <span class="font-bold text-lg">{{ number_format($progress, 1) }}%</span>
                    </div>
                    @if ($lossQuantity > 0)
                        <div class="mt-2 text-sm text-danger">
                            <span>⚠️ التلف: {{ $lossQuantity }} م³</span>
                        </div>
                    @endif
                    <div class="mt-1 text-xs text-gray-500">
                        المتبقي: {{ number_format($remainingQuantity, 1) }} م³ من {{ number_format($totalQuantity, 1) }} م³
                    </div>
                </div>
            </div>

            {{-- الموقع --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                <h4 class="text-lg font-semibold mb-4">
                    <span class="text-xl">📍</span> الموقع
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">العنوان:</span>
                        <span class="font-semibold">{{ $job->location_address ?? '-' }}</span>
                    </div>
                    @if ($job->latitude && $job->longitude)
                        <a href="https://www.google.com/maps?q={{ $job->latitude }},{{ $job->longitude }}"
                            target="_blank" class="btn btn-sm btn-outline-primary w-full">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            فتح في خرائط جوجل
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- الشحنات (الخباطات) - تظهر للمستخدم فقط المكتملة وقيد العمل؛ مخطط يظهر للمخضض ومدير الفرع --}}
        @php
            $isBM = Auth::user()->usertype_id === 'BM';
            $visibleShipments = $job->shipments->filter(function ($s) use ($isBM) {
                if (in_array($s->status, ['departed', 'arrived', 'working', 'completed', 'returned'])) {
                    return true;
                }
                if (in_array($s->status, ['planned', 'preparing']) && ($isBM || (int)($s->created_by ?? 0) === (int) Auth::id())) {
                    return true;
                }
                return false;
            });
        @endphp
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-semibold">
                    <span class="text-xl">🚛</span> الشحنات - الخباطات
                    <span class="badge bg-primary/20 text-primary">{{ $visibleShipments->count() }}</span>
                </h4>
                @if ($job->status === 'in_progress' || $job->status === 'pending' || $job->status === 'materials_reserved')
                    <button type="button" onclick="openAddShipmentModal()" class="btn btn-primary btn-sm">
                        ➕ إضافة شحنة (خباطة)
                    </button>
                @endif
            </div>

            @if ($visibleShipments->count() > 0)
                <div class="table-responsive">
                    <table class="table-striped">
                        <thead>
                            <tr>
                                <th>رقم الشحنة</th>
                                <th>اسم الآلية</th>
                                <th>السائق</th>
                                <th>الكمية</th>
                                <th>الانطلاق</th>
                                <th>الوصول</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($visibleShipments as $shipment)
                                <tr
                                    class="{{ $shipment->status === 'working' ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                                    <td>
                                        <span class="font-semibold">{{ $shipment->shipment_number }}</span>
                                        @if ($shipment->status === 'completed')
                                            <button type="button" onclick="showShipmentDetails({{ $shipment->id }})"
                                                class="text-xs text-primary hover:underline block mt-1">
                                                📋 عرض التفاصيل
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($shipment->mixer)
                                            <span class="font-semibold">{{ $shipment->mixer->car_model ?? '-' }}</span>
                                            <span
                                                class="text-xs text-gray-500 block">{{ $shipment->mixer->car_number }}</span>
                                            @if ($shipment->mixer->carType)
                                                <span
                                                    class="text-xs text-primary block">{{ $shipment->mixer->carType->name ?? '' }}</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $shipment->mixerDriver->fullname ?? '-' }}</td>
                                    <td>
                                        {{ $shipment->planned_quantity }} م³
                                        @if ($shipment->actual_quantity && $shipment->status === 'completed')
                                            <span class="text-success text-xs block">(تم تسليم:
                                                {{ $shipment->actual_quantity }} م³)</span>
                                        @endif
                                        @if ($shipment->losses && $shipment->losses->count() > 0)
                                            <span class="text-danger text-xs block">(تلف:
                                                {{ $shipment->losses->sum('quantity_lost') }} م³)</span>
                                        @endif
                                    </td>
                                    <td>{{ $shipment->departure_time ? \Carbon\Carbon::parse($shipment->departure_time)->format('H:i') : '-' }}
                                    </td>
                                    <td>{{ $shipment->arrival_time ? \Carbon\Carbon::parse($shipment->arrival_time)->format('H:i') : '-' }}
                                    </td>
                                    <td>
                                        @switch($shipment->status)
                                            @case('planned')
                                                <span class="badge bg-secondary">مخطط</span>
                                            @break

                                            @case('preparing')
                                                <span class="badge bg-info">تحضير</span>
                                            @break

                                            @case('departed')
                                                <span class="badge bg-warning">في الطريق</span>
                                            @break

                                            @case('arrived')
                                                <span class="badge bg-primary">وصل</span>
                                            @break

                                            @case('working')
                                                <span class="badge bg-info animate-pulse">🔨 يعمل</span>
                                            @break

                                            @case('completed')
                                                <span class="badge bg-success">✅ مكتمل</span>
                                            @break

                                            @case('returned')
                                                <span class="badge bg-success">عاد</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">{{ $shipment->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="flex gap-1 flex-wrap">
                                            {{-- زر الانطلاق - للمخضض بالشحنة فقط (من أضافها) --}}
                                            @if ($shipment->status === 'planned' && (int)($shipment->created_by ?? 0) === (int) Auth::id())
                                                <form
                                                    action="{{ url('companyBranch/shipment/{{ $shipment->id }}/depart') }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-success"
                                                        title="انطلاق">🚀 انطلاق</button>
                                                </form>
                                            @endif

                                            {{-- زر الوصول - للشحنات المنطلقة --}}
                                            @if ($shipment->status === 'departed')
                                                <form
                                                    action="{{ url('companyBranch/shipment/{{ $shipment->id }}/arrive') }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-primary"
                                                        title="وصل">📍 وصول</button>
                                                </form>
                                            @endif

                                            {{-- زر بدء العمل - للشحنات التي وصلت --}}
                                            @if ($shipment->status === 'arrived')
                                                <form
                                                    action="{{ url('companyBranch/shipment/{{ $shipment->id }}/startWork') }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-warning"
                                                        title="بدء العمل">🔨 بدء العمل</button>
                                                </form>
                                            @endif

                                            {{-- أزرار إنهاء العمل والتلف - للشحنات قيد العمل --}}
                                            @if ($shipment->status === 'working')
                                                <button type="button"
                                                    onclick="openCompleteModal({{ $shipment->id }}, {{ $shipment->planned_quantity }})"
                                                    class="btn btn-xs btn-success" title="إنهاء العمل">
                                                    ✅ إنهاء
                                                </button>
                                                <button type="button"
                                                    onclick="openLossModal({{ $shipment->id }}, {{ $shipment->planned_quantity }})"
                                                    class="btn btn-xs btn-danger" title="تسجيل تلف">
                                                    ⚠️ تلف
                                                </button>
                                            @endif

                                            {{-- زر عرض التفاصيل للشحنات المكتملة --}}
                                            @if ($shipment->status === 'completed')
                                                <button type="button" onclick="showShipmentDetails({{ $shipment->id }})"
                                                    class="btn btn-xs btn-outline-info" title="تفاصيل">
                                                    📋 تفاصيل
                                                </button>
                                            @endif

                                            {{-- زر إلغاء الشحنة - لمدير الفرع فقط، وقبل البدء (مخطط) فقط --}}
                                            @if ($shipment->status === 'planned' && Auth::user()->usertype_id === 'BM')
                                                <button type="button"
                                                    onclick="cancelShipment({{ $shipment->id }}, '{{ $shipment->shipment_number }}')"
                                                    class="btn btn-xs btn-outline-danger" title="إلغاء الشحنة">
                                                    ❌ إلغاء
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div
                    class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-8 text-center border-2 border-dashed border-gray-300 dark:border-gray-700">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-500 mb-3 font-semibold">لا توجد شحنات (خباطات) حتى الآن</p>
                    <p class="text-gray-400 text-sm">ابدأ بإضافة الخباطات لتنفيذ العمل</p>
                </div>
            @endif
        </div>

        {{-- قسم التلف/الخسائر --}}
        @if ($job->losses && $job->losses->count() > 0)
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-xl p-5 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-red-800 dark:text-red-300">
                        <span class="text-xl">⚠️</span> التلف والخسائر ({{ $job->losses->count() }})
                    </h4>
                    <span class="text-xl font-bold text-red-600">{{ $job->losses->sum('quantity_lost') }} م³</span>
                </div>
                <div class="table-responsive">
                    <table class="table-striped text-sm">
                        <thead>
                            <tr class="bg-red-100 dark:bg-red-800/50">
                                <th>نوع التلف</th>
                                <th>الكمية</th>
                                <th>الوصف</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($job->losses as $loss)
                                <tr>
                                    <td>
                                        @switch($loss->loss_type)
                                            @case('spillage')
                                                <span class="badge bg-warning">انسكاب</span>
                                            @break

                                            @case('material_spoilage')
                                                <span class="badge bg-danger">تلف مواد</span>
                                            @break

                                            @case('rejection')
                                                <span class="badge bg-info">رفض عميل</span>
                                            @break

                                            @case('vehicle_breakdown')
                                                <span class="badge bg-secondary">عطل آلية</span>
                                            @break

                                            @case('accident')
                                                <span class="badge bg-danger">حادث</span>
                                            @break

                                            @case('weather')
                                                <span class="badge bg-primary">ظروف جوية</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">{{ $loss->loss_type }}</span>
                                        @endswitch
                                    </td>
                                    <td class="font-bold text-red-600">{{ $loss->quantity_lost }} م³</td>
                                    <td>{{ $loss->description ?? '-' }}</td>
                                    <td>{{ $loss->created_at ? $loss->created_at->format('Y-m-d H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- أزرار الإجراءات --}}
        <div class="flex flex-wrap gap-3 justify-center">
            <a href="{{ url('companyBranch/workJobs/{{ $job->status === 'completed' ? 'completed' : ($job->status === 'in_progress' ? 'active' : 'pending') }}') }}"
                class="btn btn-outline-secondary">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                رجوع
            </a>

            @if ($job->status === 'pending')
                <form action="{{ url('companyBranch/workJob/{{ $job->id }}/start') }}" method="POST"
                    class="inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        </svg>
                        بدء التنفيذ
                    </button>
                </form>
            @endif

            @if ($job->status === 'in_progress')
                @php
                    $totalDelivered = $job->shipments->where('status', 'completed')->sum('actual_quantity') ?? 0;
                    $remaining = $job->total_quantity - $totalDelivered;
                    $canComplete = $remaining <= 0;
                @endphp
                @if ($canComplete)
                    <form action="{{ url('companyBranch/workJob/{{ $job->id }}/complete') }}" method="POST"
                        class="inline" onsubmit="return confirm('هل أنت متأكد من إكمال أمر العمل؟')">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            إتمام أمر العمل
                        </button>
                    </form>
                @else
                    <button type="button" onclick="showRemainingAlert({{ $remaining }})" class="btn btn-warning">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        إتمام أمر العمل
                    </button>
                @endif
            @endif

            @if ($job->status === 'completed')
                <a href="{{ url('companyBranch/workJob/{{ $job->id }}/invoice') }}" class="btn btn-primary">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    عرض الفاتورة
                </a>
            @endif
        </div>
    </div>

    {{-- Modal إضافة شحنة --}}
    <div id="addShipmentModal"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-start justify-center pt-20">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4 shadow-2xl">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-xl font-bold flex items-center gap-2">
                    <span class="text-2xl">🚛</span>
                    إضافة شحنة جديدة
                </h3>
                <button type="button" onclick="closeAddShipmentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ url('companyBranch/workJob/' . $job->id . '/addShipment') }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold mb-2">🚛 اختر الخباطة</label>
                        <select name="mixer_id" id="mixer_select" class="form-select w-full" required
                            onchange="updateMixerSelection()">
                            <option value="" data-capacity="" data-driver-id="" data-driver-name=""
                                data-backup-id="" data-backup-name="">-- اختر الخباطة --</option>
                            @if (isset($mixers))
                                @foreach ($mixers as $mixer)
                                    @php
                                        // استخدام mixer_capacity من السيارة أولاً، ثم capacity من النوع
                                        $capacity = $mixer->mixer_capacity ?? ($mixer->carType->capacity ?? 0);
                                    @endphp
                                    @if ($capacity == 0)
                                        @continue
                                    @endif
                                    @if ($mixer->is_in_maintenance ?? false)
                                        <option value="" disabled class="text-orange-500">
                                            🔧 {{ $mixer->car_number }} - {{ $mixer->car_model }} (في الصيانة -
                                            {{ $capacity }} م³)
                                        </option>
                                    @elseif (!$mixer->is_busy && !$mixer->is_reserved)
                                        <option value="{{ $mixer->id }}" data-capacity="{{ $capacity }}"
                                            data-driver-id="{{ $mixer->driver_id ?? '' }}"
                                            data-driver-name="{{ $mixer->driver->fullname ?? '' }}"
                                            data-backup-id="{{ $mixer->backup_driver_id ?? '' }}"
                                            data-backup-name="{{ $mixer->backupDriver->fullname ?? '' }}">
                                            ✅ {{ $mixer->car_number }} - {{ $mixer->car_model }} ({{ $capacity }} م³)
                                        </option>
                                    @elseif ($mixer->is_reserved)
                                        <option value="" disabled class="text-yellow-600">
                                            🟡 {{ $mixer->car_number }} - {{ $mixer->car_model }} (محجوزة -
                                            {{ $capacity }} م³)
                                        </option>
                                    @else
                                        <option value="" disabled class="text-gray-400">
                                            🔴 {{ $mixer->car_number }} - {{ $mixer->car_model }} (غير متاحة -
                                            {{ $capacity }} م³)
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <div class="flex gap-3 mt-2 text-xs">
                            <span class="text-green-600">✅ متاحة</span>
                            <span class="text-yellow-600">🟡 محجوزة</span>
                            <span class="text-red-600">🔴 مشغولة</span>
                            <span class="text-orange-600">🔧 صيانة</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2">👷 اختر السائق</label>
                        <select name="driver_id" id="driver_select" class="form-select w-full" required>
                            <option value="">اختر الخباطة أولاً</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">⭐ السائق الرئيسي سيتم اختياره تلقائياً</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2">📦 الكمية (م³)</label>
                        <input type="number" name="quantity" id="quantity_input" step="0.5" min="0.5"
                            class="form-input w-full text-lg font-bold" required placeholder="مثال: 8">
                        <p class="text-xs text-gray-500 mt-1">سيتم تعبئة السعة الافتراضية تلقائياً</p>
                    </div>
                </div>

                <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" class="btn btn-primary flex-1">
                        ✅ حفظ الشحنة
                    </button>
                    <button type="button" onclick="closeAddShipmentModal()" class="btn btn-outline-secondary flex-1">
                        ✖️ إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal إكمال الشحنة --}}
    <div id="completeShipmentModal"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-start justify-center pt-20">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold mb-4 text-success">✅ إكمال الشحنة</h3>
            <form id="completeShipmentForm" action="" method="POST">
                @csrf
                <input type="hidden" id="complete_shipment_id" name="shipment_id">
                <div class="space-y-4">
                    <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">الكمية المخططة:</p>
                        <p class="text-2xl font-bold text-green-600"><span id="complete_planned_display">0</span> م³</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">الكمية الفعلية المسلمة (م³)</label>
                        <input type="number" name="actual_quantity" id="complete_actual_quantity" step="0.5"
                            min="0" class="form-input w-full text-lg font-bold" required>
                        <p class="text-xs text-gray-500 mt-1">أدخل الكمية الفعلية التي تم تسليمها للعميل</p>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn btn-success flex-1">✅ تأكيد الإكمال</button>
                    <button type="button" onclick="closeCompleteModal()"
                        class="btn btn-outline-secondary flex-1">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal تسجيل التلف --}}
    <div id="lossModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-start justify-center pt-20">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold mb-4 text-danger">⚠️ تسجيل تلف / خسارة</h3>
            <form id="lossForm" action="" method="POST">
                @csrf
                <input type="hidden" id="loss_shipment_id" name="shipment_id">
                <div class="space-y-4">
                    <div class="bg-red-50 dark:bg-red-900/30 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">الحد الأقصى للتلف: <span
                                id="loss_max_quantity" class="font-bold">0</span> م³</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">نوع التلف</label>
                        <select name="loss_type" class="form-select w-full" required>
                            <option value="">اختر نوع التلف</option>
                            <option value="spillage">انسكاب</option>
                            <option value="material_spoilage">تلف المواد (تجمد/جفاف)</option>
                            <option value="rejection">رفض من العميل</option>
                            <option value="vehicle_breakdown">عطل الآلية</option>
                            <option value="accident">حادث</option>
                            <option value="weather">ظروف جوية</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">كمية التلف (م³)</label>
                        <input type="number" name="quantity_lost" id="loss_quantity" step="0.5" min="0.5"
                            class="form-input w-full" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">وصف / ملاحظات</label>
                        <textarea name="description" rows="3" class="form-textarea w-full" placeholder="اشرح سبب التلف..."></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn btn-danger flex-1"
                        onclick="return confirm('هل أنت متأكد من تسجيل هذا التلف؟')">
                        ⚠️ تسجيل التلف
                    </button>
                    <button type="button" onclick="closeLossModal()"
                        class="btn btn-outline-secondary flex-1">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal تفاصيل الشحنة --}}
    <div id="shipmentDetailsModal"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-start justify-center pt-10 overflow-y-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-lg mx-4 my-10">
            <h3 class="text-lg font-semibold mb-4">📋 تفاصيل الشحنة</h3>
            <div class="space-y-4">
                {{-- معلومات أساسية --}}
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <h4 class="font-semibold text-sm text-gray-500 mb-3">معلومات الشحنة</h4>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500">رقم الشحنة:</span>
                            <span id="detail_number" class="font-semibold block">-</span>
                        </div>
                        <div>
                            <span class="text-gray-500">اسم الآلية:</span>
                            <span id="detail_mixer" class="font-semibold block">-</span>
                        </div>
                        <div>
                            <span class="text-gray-500">السائق:</span>
                            <span id="detail_driver" class="font-semibold block">-</span>
                        </div>
                    </div>
                </div>

                {{-- الكميات --}}
                <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4">
                    <h4 class="font-semibold text-sm text-blue-600 mb-3">الكميات</h4>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500">الكمية المخططة:</span>
                            <span id="detail_planned" class="font-semibold text-lg block">-</span>
                        </div>
                        <div>
                            <span class="text-gray-500">الكمية المسلمة:</span>
                            <span id="detail_actual" class="font-semibold text-lg text-success block">-</span>
                        </div>
                    </div>
                </div>

                {{-- التوقيتات --}}
                <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-4">
                    <h4 class="font-semibold text-sm text-green-600 mb-3">⏱️ التوقيتات</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center py-1 border-b border-green-200">
                            <span class="text-gray-500">🚀 وقت الانطلاق:</span>
                            <span id="detail_departure" class="font-semibold">-</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-green-200">
                            <span class="text-gray-500">📍 وقت الوصول:</span>
                            <span id="detail_arrival" class="font-semibold">-</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-green-200">
                            <span class="text-gray-500">🔨 بدء العمل:</span>
                            <span id="detail_work_start" class="font-semibold">-</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-green-200">
                            <span class="text-gray-500">✅ انتهاء العمل:</span>
                            <span id="detail_work_end" class="font-semibold">-</span>
                        </div>
                        <div class="flex justify-between items-center py-1 bg-green-100 dark:bg-green-800/50 rounded px-2">
                            <span class="text-gray-600 font-medium">⏳ مدة العمل:</span>
                            <span id="detail_duration" class="font-bold text-green-600">-</span>
                        </div>
                    </div>
                </div>

                {{-- الملاحظات --}}
                <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded-lg p-4">
                    <h4 class="font-semibold text-sm text-yellow-600 mb-2">📝 ملاحظات</h4>
                    <p id="detail_notes" class="text-sm text-gray-600">-</p>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeDetailsModal()" class="btn btn-primary flex-1">إغلاق</button>
            </div>
        </div>
    </div>

    <script>
        // بيانات جميع السائقين
        const allDrivers = [
            @if (isset($drivers))
                @foreach ($drivers as $driver)
                    {
                        id: {{ $driver->id }},
                        name: "{{ $driver->fullname }}"
                    },
                @endforeach
            @endif
        ];

        function openAddShipmentModal() {
            document.getElementById('addShipmentModal').classList.remove('hidden');
            // إعادة تعيين القوائم
            document.getElementById('mixer_select').value = '';
            document.getElementById('driver_select').innerHTML = '<option value="">اختر الخباطة أولاً</option>';
            document.getElementById('quantity_input').value = '';
        }

        function closeAddShipmentModal() {
            document.getElementById('addShipmentModal').classList.add('hidden');
        }

        function updateMixerSelection() {
            const mixerSelect = document.getElementById('mixer_select');
            const driverSelect = document.getElementById('driver_select');
            const quantityInput = document.getElementById('quantity_input');
            const selectedOption = mixerSelect.options[mixerSelect.selectedIndex];

            // تحديث الكمية
            const capacity = selectedOption.getAttribute('data-capacity');
            if (capacity) {
                quantityInput.value = capacity;
            }

            // تحديث قائمة السائقين
            const driverId = selectedOption.getAttribute('data-driver-id');
            const driverName = selectedOption.getAttribute('data-driver-name');
            const backupId = selectedOption.getAttribute('data-backup-id');
            const backupName = selectedOption.getAttribute('data-backup-name');

            driverSelect.innerHTML = '';

            if (!mixerSelect.value) {
                driverSelect.innerHTML = '<option value="">اختر الخباطة أولاً</option>';
                return;
            }

            // إضافة الخيار الافتراضي
            driverSelect.innerHTML = '<option value="">اختر السائق</option>';

            // إضافة السائق الرئيسي أولاً إذا موجود
            if (driverId && driverName) {
                const option = document.createElement('option');
                option.value = driverId;
                option.textContent = '⭐ ' + driverName + ' (السائق الرئيسي)';
                option.style.fontWeight = 'bold';
                option.style.color = '#10b981';
                driverSelect.appendChild(option);
            }

            // إضافة السائق الاحتياطي ثانياً إذا موجود
            if (backupId && backupName) {
                const option = document.createElement('option');
                option.value = backupId;
                option.textContent = '🔄 ' + backupName + ' (السائق الاحتياطي)';
                option.style.color = '#3b82f6';
                driverSelect.appendChild(option);
            }

            // إضافة خط فاصل إذا كان هناك سائقين مخصصين
            if (driverId || backupId) {
                const separator = document.createElement('option');
                separator.disabled = true;
                separator.textContent = '──── سائقين آخرين ────';
                driverSelect.appendChild(separator);
            }

            // إضافة باقي السائقين
            allDrivers.forEach(driver => {
                // تجاوز السائق الرئيسي والاحتياطي (تم إضافتهم بالأعلى)
                if (driver.id == driverId || driver.id == backupId) {
                    return;
                }

                const option = document.createElement('option');
                option.value = driver.id;
                option.textContent = driver.name;
                driverSelect.appendChild(option);
            });

            // تحديد السائق الرئيسي تلقائياً إذا موجود
            if (driverId) {
                driverSelect.value = driverId;
            }
        }

        // إغلاق Modal عند النقر خارجه
        document.getElementById('addShipmentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddShipmentModal();
            }
        });

        // ========== إكمال الشحنة ==========
        function openCompleteModal(shipmentId, plannedQuantity) {
            document.getElementById('complete_shipment_id').value = shipmentId;
            document.getElementById('complete_actual_quantity').value = plannedQuantity;
            document.getElementById('complete_planned_display').textContent = plannedQuantity;
            document.getElementById('completeShipmentForm').action = '/companyBranch/shipment/' + shipmentId +
                '/complete';
            document.getElementById('completeShipmentModal').classList.remove('hidden');
        }

        function closeCompleteModal() {
            document.getElementById('completeShipmentModal').classList.add('hidden');
        }

        document.getElementById('completeShipmentModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeCompleteModal();
        });

        // ========== تسجيل التلف ==========
        function openLossModal(shipmentId, plannedQuantity) {
            document.getElementById('loss_shipment_id').value = shipmentId;
            document.getElementById('loss_max_quantity').textContent = plannedQuantity;
            document.getElementById('loss_quantity').max = plannedQuantity;
            document.getElementById('lossForm').action = '/companyBranch/shipment/' + shipmentId +
                '/reportLoss';
            document.getElementById('lossModal').classList.remove('hidden');
        }

        function closeLossModal() {
            document.getElementById('lossModal').classList.add('hidden');
        }

        document.getElementById('lossModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeLossModal();
        });

        // ========== عرض تفاصيل الشحنة ==========
        const shipmentsData = {
            @foreach ($job->shipments as $shipment)
                {{ $shipment->id }}: {
                    number: '{{ $shipment->shipment_number }}',
                    mixer: '{{ ($shipment->mixer->car_model ?? '') . ($shipment->mixer ? ' (' . $shipment->mixer->car_number . ')' : '-') }}',
                    driver: '{{ $shipment->mixerDriver->fullname ?? '-' }}',
                    planned: {{ $shipment->planned_quantity }},
                    actual: {{ $shipment->actual_quantity ?? 0 }},
                    departure: '{{ $shipment->departure_time ? \Carbon\Carbon::parse($shipment->departure_time)->format('Y-m-d H:i') : '-' }}',
                    arrival: '{{ $shipment->arrival_time ? \Carbon\Carbon::parse($shipment->arrival_time)->format('Y-m-d H:i') : '-' }}',
                    workStart: '{{ $shipment->work_start_time ? \Carbon\Carbon::parse($shipment->work_start_time)->format('Y-m-d H:i') : '-' }}',
                    workEnd: '{{ $shipment->work_end_time ? \Carbon\Carbon::parse($shipment->work_end_time)->format('Y-m-d H:i') : '-' }}',
                    status: '{{ $shipment->status }}',
                    notes: '{{ addslashes($shipment->driver_notes ?? '') }}',
                },
            @endforeach
        };

        function showShipmentDetails(shipmentId) {
            const data = shipmentsData[shipmentId];
            if (!data) return;

            document.getElementById('detail_number').textContent = data.number;
            document.getElementById('detail_mixer').textContent = data.mixer;
            document.getElementById('detail_driver').textContent = data.driver;
            document.getElementById('detail_planned').textContent = data.planned + ' م³';
            document.getElementById('detail_actual').textContent = data.actual + ' م³';
            document.getElementById('detail_departure').textContent = data.departure;
            document.getElementById('detail_arrival').textContent = data.arrival;
            document.getElementById('detail_work_start').textContent = data.workStart;
            document.getElementById('detail_work_end').textContent = data.workEnd;
            document.getElementById('detail_notes').textContent = data.notes || '-';

            // حساب مدة العمل
            if (data.workStart !== '-' && data.workEnd !== '-') {
                const start = new Date(data.workStart);
                const end = new Date(data.workEnd);
                const diffMs = end - start;
                const diffMins = Math.round(diffMs / 60000);
                document.getElementById('detail_duration').textContent = diffMins + ' دقيقة';
            } else {
                document.getElementById('detail_duration').textContent = '-';
            }

            document.getElementById('shipmentDetailsModal').classList.remove('hidden');
        }

        function closeDetailsModal() {
            document.getElementById('shipmentDetailsModal').classList.add('hidden');
        }

        document.getElementById('shipmentDetailsModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeDetailsModal();
        });

        // ========== تنبيه الكمية المتبقية ==========
        function showRemainingAlert(remaining) {
            alert('⚠️ لا يمكن إتمام أمر العمل!\n\nباقي ' + remaining +
                ' م³ من الكمية الإجمالية.\n\nيرجى إضافة شحنات لإكمال الكمية المطلوبة.');
        }

        // ========== إلغاء الشحنة ==========
        function cancelShipment(shipmentId, shipmentNumber) {
            if (confirm('هل أنت متأكد من إلغاء الشحنة رقم ' + shipmentNumber + '؟\n\nسيتم إلغاء الشحنة وتحرير الآلية.')) {
                // إنشاء نموذج للإرسال
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url('companyBranch/shipment') }}/' + shipmentId + '/cancel';
                form.style.display = 'none';

                // إضافة CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
