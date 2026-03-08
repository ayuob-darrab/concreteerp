@extends('layouts.app')

@section('page-title', 'تفاصيل الشحنة')

@section('content')
    <div class="panel mt-6">
        {{-- العنوان --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <div>
                <h3 class="text-xl font-bold dark:text-white-light">
                    <span class="text-2xl">🚛</span> الشحنة #{{ $shipment->shipment_number }}
                </h3>
                <p class="text-gray-500 mt-1">
                    أمر العمل: <a href="{{ url('companyBranch/workJob/{{ $shipment->job_id }}/view') }}"
                        class="text-primary hover:underline">{{ $shipment->job->job_number ?? '-' }}</a>
                </p>
            </div>
            <div>
                @switch($shipment->status)
                    @case('planned')
                        <span class="badge badge-lg bg-secondary/20 text-secondary px-4 py-2 rounded-full text-base font-bold">
                            📋 مخطط
                        </span>
                    @break

                    @case('preparing')
                        <span class="badge badge-lg bg-info/20 text-info px-4 py-2 rounded-full text-base font-bold">
                            ⏳ جاري التحضير
                        </span>
                    @break

                    @case('departed')
                        <span
                            class="badge badge-lg bg-warning/20 text-warning px-4 py-2 rounded-full text-base font-bold animate-pulse">
                            🚛 في الطريق
                        </span>
                    @break

                    @case('arrived')
                        <span class="badge badge-lg bg-primary/20 text-primary px-4 py-2 rounded-full text-base font-bold">
                            📍 وصل للموقع
                        </span>
                    @break

                    @case('working')
                        <span class="badge badge-lg bg-info/20 text-info px-4 py-2 rounded-full text-base font-bold animate-pulse">
                            🔄 يعمل
                        </span>
                    @break

                    @case('completed')
                        <span class="badge badge-lg bg-success/20 text-success px-4 py-2 rounded-full text-base font-bold">
                            ✅ مكتمل
                        </span>
                    @break

                    @case('returned')
                        <span class="badge badge-lg bg-success/20 text-success px-4 py-2 rounded-full text-base font-bold">
                            🏠 عاد للمصنع
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

        {{-- معلومات الشحنة --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            {{-- معلومات الخلاطة --}}
            <div
                class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl p-5">
                <h4 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-4">
                    <span class="text-xl">🚛</span> الخلاطة
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">رقم اللوحة:</span>
                        <span class="font-semibold">{{ $shipment->mixer->car_number ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">الموديل:</span>
                        <span class="font-semibold">{{ $shipment->mixer->car_model ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">السائق:</span>
                        <span class="font-semibold">{{ $shipment->mixerDriver->fullname ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- معلومات الكمية --}}
            <div
                class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 rounded-xl p-5">
                <h4 class="text-lg font-semibold text-green-800 dark:text-green-300 mb-4">
                    <span class="text-xl">📦</span> الكمية
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">الكمية المخططة:</span>
                        <span class="font-semibold text-lg">{{ $shipment->planned_quantity }} م³</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">الكمية الفعلية:</span>
                        <span class="font-semibold text-lg text-success">{{ $shipment->actual_quantity ?? '-' }} م³</span>
                    </div>
                    @if ($shipment->returned_quantity)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">الكمية المرتجعة:</span>
                            <span class="font-semibold text-danger">{{ $shipment->returned_quantity }} م³</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- الأوقات --}}
            <div
                class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/30 rounded-xl p-5">
                <h4 class="text-lg font-semibold text-purple-800 dark:text-purple-300 mb-4">
                    <span class="text-xl">⏱️</span> الأوقات
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">وقت الانطلاق:</span>
                        <span class="font-semibold">
                            {{ $shipment->departure_time ? \Carbon\Carbon::parse($shipment->departure_time)->format('H:i') : '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">وقت الوصول:</span>
                        <span class="font-semibold">
                            {{ $shipment->arrival_time ? \Carbon\Carbon::parse($shipment->arrival_time)->format('H:i') : '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">وقت العودة:</span>
                        <span class="font-semibold">
                            {{ $shipment->return_time ? \Carbon\Carbon::parse($shipment->return_time)->format('H:i') : '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- مخطط الرحلة --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-6">
            <h4 class="text-lg font-semibold mb-4">
                <span class="text-xl">🗺️</span> مسار الرحلة
            </h4>
            <div class="flex items-center justify-center gap-4 py-6">
                {{-- المصنع --}}
                <div class="text-center">
                    <div
                        class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-3xl mx-auto mb-2
                        {{ in_array($shipment->status, ['departed', 'arrived', 'working', 'completed', 'returned']) ? 'bg-success/20 text-success' : '' }}">
                        🏭
                    </div>
                    <span class="text-sm">المصنع</span>
                    @if ($shipment->departure_time)
                        <p class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($shipment->departure_time)->format('H:i') }}</p>
                    @endif
                </div>

                {{-- خط الانطلاق --}}
                <div
                    class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 rounded relative
                    {{ in_array($shipment->status, ['departed', 'arrived', 'working', 'completed', 'returned']) ? 'bg-success' : '' }}">
                    @if ($shipment->status == 'departed')
                        <div
                            class="absolute top-1/2 left-1/2 transform -translate-y-1/2 -translate-x-1/2 text-2xl animate-bounce">
                            🚛
                        </div>
                    @endif
                </div>

                {{-- الموقع --}}
                <div class="text-center">
                    <div
                        class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-3xl mx-auto mb-2
                        {{ in_array($shipment->status, ['arrived', 'working', 'completed', 'returned']) ? 'bg-success/20 text-success' : '' }}">
                        📍
                    </div>
                    <span class="text-sm">الموقع</span>
                    @if ($shipment->arrival_time)
                        <p class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($shipment->arrival_time)->format('H:i') }}</p>
                    @endif
                </div>

                {{-- خط العودة --}}
                <div
                    class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 rounded
                    {{ in_array($shipment->status, ['returned', 'completed']) ? 'bg-success' : '' }}">
                </div>

                {{-- العودة --}}
                <div class="text-center">
                    <div
                        class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-3xl mx-auto mb-2
                        {{ in_array($shipment->status, ['returned', 'completed']) ? 'bg-success/20 text-success' : '' }}">
                        🏠
                    </div>
                    <span class="text-sm">العودة</span>
                    @if ($shipment->return_time)
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($shipment->return_time)->format('H:i') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ملاحظات --}}
        @if ($shipment->notes)
            <div
                class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-xl p-5 mb-6">
                <h4 class="text-lg font-semibold text-yellow-800 dark:text-yellow-300 mb-2">
                    <span class="text-xl">📝</span> ملاحظات
                </h4>
                <p class="text-gray-700 dark:text-gray-300">{{ $shipment->notes }}</p>
            </div>
        @endif

        {{-- أزرار الإجراءات --}}
        <div class="flex flex-wrap gap-3 justify-center">
            <a href="{{ url('companyBranch/workJob/{{ $shipment->job_id }}/view') }}" class="btn btn-outline-secondary">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                رجوع لأمر العمل
            </a>

            @if ($shipment->status === 'planned')
                <form action="{{ url('companyBranch/shipment/{{ $shipment->id }}/depart') }}" method="POST"
                    class="inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        </svg>
                        انطلاق
                    </button>
                </form>
            @endif

            @if ($shipment->status === 'departed')
                <form action="{{ url('companyBranch/shipment/{{ $shipment->id }}/arrive') }}" method="POST"
                    class="inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        وصل للموقع
                    </button>
                </form>
            @endif

            @if (in_array($shipment->status, ['arrived', 'working']))
                <form action="{{ url('companyBranch/shipment/{{ $shipment->id }}/complete') }}" method="POST"
                    class="inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        إكمال الشحنة
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection
