@extends('layouts.app')

@section('title', 'شحناتي')

@section('content')
    <div x-data="driverShipments()">
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="{{ url('/') }}" class="text-primary hover:underline">الرئيسية</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-1 rtl:before:ml-1">
                <span>شحناتي</span>
            </li>
        </ul>

        <div class="pt-5">
            {{-- بطاقة الترحيب --}}
            @php
                $driverName = $employee->fullname ?? Auth::user()->fullname ?? Auth::user()->username;
                $driverImage = $employee->personImage ?? null;
                $empTypeName = $employee->employeeType->name ?? (\App\Models\EmployeeType::where('code', Auth::user()->emp_type_code)->first()->name ?? 'سائق');
            @endphp
            <div class="panel mb-5">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        @if ($driverImage && file_exists(public_path($driverImage)))
                            <img src="{{ asset($driverImage) }}"
                                class="w-16 h-16 rounded-full object-cover border-4 border-primary shadow-lg"
                                alt="{{ $driverName }}">
                        @else
                            <div class="w-16 h-16 rounded-full bg-primary flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h4 class="text-xl font-bold text-gray-800 dark:text-white">مرحباً {{ $driverName }}</h4>
                            <span class="badge bg-info">{{ $empTypeName }}</span>
                        </div>
                    </div>
                    <div class="text-center md:text-left rtl:md:text-right">
                        <p class="text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::now()->translatedFormat('l، d F Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- إحصائيات اليوم (أنماط مضمنة لضمان ظهور التدرج والنص في الثيم الفاتح والداكن) --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                <div class="panel rounded-lg shadow-sm"
                    style="background: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%); color: #fff;">
                    <div class="flex justify-between">
                        <div>
                            <div class="text-3xl font-bold">{{ $todayStats['total'] }}</div>
                            <div class="mt-1 opacity-95">إجمالي الشحنات</div>
                        </div>
                        <div style="opacity: 0.7;">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="panel rounded-lg shadow-sm"
                    style="background: linear-gradient(135deg, #eab308 0%, #facc15 100%); color: #1f2937;">
                    <div class="flex justify-between">
                        <div>
                            <div class="text-3xl font-bold">{{ $todayStats['active'] }}</div>
                            <div class="mt-1 opacity-95">نشطة</div>
                        </div>
                        <div style="opacity: 0.8;">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="panel rounded-lg shadow-sm"
                    style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: #fff;">
                    <div class="flex justify-between">
                        <div>
                            <div class="text-3xl font-bold">{{ $todayStats['completed'] }}</div>
                            <div class="mt-1 opacity-95">مكتملة</div>
                        </div>
                        <div style="opacity: 0.7;">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="panel rounded-lg shadow-sm"
                    style="background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); color: #fff;">
                    <div class="flex justify-between">
                        <div>
                            <div class="text-3xl font-bold">{{ number_format($todayStats['total_quantity'], 1) }}</div>
                            <div class="mt-1 opacity-95">م³ مخطط</div>
                        </div>
                        <div style="opacity: 0.7;">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- فلتر البحث --}}
            <div class="panel mb-5">
                <form method="GET" action="{{ route('driver.shipments.index') }}" class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[150px]">
                        <label for="status" class="block text-sm font-medium mb-2">الحالة</label>
                        <select name="status" id="status" class="form-select w-full">
                            <option value="active" {{ $status == 'active' ? 'selected' : '' }}>النشطة</option>
                            <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>المكتملة</option>
                            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>الكل</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[150px]">
                        <label for="date" class="block text-sm font-medium mb-2">التاريخ</label>
                        <input type="date" name="date" id="date" value="{{ $date }}"
                            class="form-input w-full">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <svg class="w-4 h-4 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            بحث
                        </button>
                    </div>
                </form>
            </div>

            {{-- قسم أوامر العمل لسائق البَم --}}
            @if ($isPumpDriver && $pumpJobs->count() > 0)
                <div class="panel mb-5">
                    <div class="flex items-center justify-between mb-5">
                        <h5 class="text-lg font-semibold dark:text-white-light">
                            <svg class="w-6 h-6 inline-block text-amber-500 ltr:mr-2 rtl:ml-2" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            أوامر العمل المكلف بها (البَم)
                        </h5>
                        <span class="badge bg-amber-500 text-white">{{ $pumpJobs->count() }} أمر عمل</span>
                    </div>

                    <div class="space-y-4">
                        @foreach ($pumpJobs as $job)
                            @php
                                $pumpVehicle = $job->defaultPump;
                                $pumpStatus = $job->pump_status ?? 'pending';
                                $shipmentsCount = $job->shipments->count();
                                $totalQty = $job->shipments->sum('planned_quantity');
                            @endphp
                            <div class="border-2 rounded-lg p-4 hover:shadow-md transition-shadow
                                @if ($pumpStatus === 'working') border-warning bg-warning-light
                                @elseif(in_array($pumpStatus, ['departed', 'arrived'])) border-info bg-info-light
                                @elseif($pumpStatus === 'returned') border-success bg-success-light
                                @else border-amber-300 bg-amber-50 dark:bg-amber-900/20 @endif">

                                <div class="flex flex-col md:flex-row justify-between gap-4">
                                    {{-- معلومات أمر العمل --}}
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="text-lg font-bold text-amber-600">
                                                🚜 أمر عمل #{{ $job->job_number ?? $job->id }}
                                            </span>
                                            @php
                                                $statusLabels = [
                                                    'pending' => ['جاهز للانطلاق', 'bg-gray-500'],
                                                    'departed' => ['في الطريق', 'bg-primary'],
                                                    'arrived' => ['في الموقع', 'bg-info'],
                                                    'working' => ['يعمل', 'bg-warning'],
                                                    'returned' => ['مكتمل', 'bg-success'],
                                                ];
                                                $statusInfo = $statusLabels[$pumpStatus] ?? ['غير معروف', 'bg-gray-400'];
                                            @endphp
                                            <span class="badge {{ $statusInfo[1] }}">{{ $statusInfo[0] }}</span>
                                            <span class="badge bg-blue-500">{{ $shipmentsCount }} شحنة</span>
                                        </div>

                                        {{-- معلومات البَم --}}
                                        @if ($pumpVehicle)
                                            <div class="bg-amber-100 dark:bg-amber-900/40 rounded-lg p-3 mb-3 border border-amber-300">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-12 h-12 rounded-full bg-amber-500 flex items-center justify-center text-white">
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-amber-800 dark:text-amber-300 text-lg">
                                                            {{ $pumpVehicle->car_model ?? 'بَم' }}
                                                        </p>
                                                        <p class="text-sm text-amber-700 dark:text-amber-400">
                                                            رقم البَم: <strong>{{ $pumpVehicle->car_number }}</strong>
                                                            @if ($pumpVehicle->carType)
                                                                | {{ $pumpVehicle->carType->name }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                            <div>
                                                <span class="text-gray-500">العميل:</span>
                                                <p class="font-semibold">{{ $job->customer_name ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">إجمالي الكمية:</span>
                                                <p class="font-semibold">{{ number_format($totalQty, 1) }} م³</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">عدد الشحنات:</span>
                                                <p class="font-semibold">{{ $shipmentsCount }} شحنة</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">العنوان:</span>
                                                <p class="font-semibold text-truncate" title="{{ $job->location_address ?? '' }}">
                                                    {{ \Illuminate\Support\Str::limit($job->location_address ?? '-', 30) }}
                                                </p>
                                            </div>
                                        </div>

                                        @if ($job->customer_phone)
                                            <div class="mt-3">
                                                <a href="tel:{{ $job->customer_phone }}" class="btn btn-outline-success btn-sm">
                                                    📞 {{ $job->customer_phone }}
                                                </a>
                                                @if ($job->location_map_url)
                                                    <a href="{{ $job->location_map_url }}" target="_blank"
                                                        class="btn btn-outline-info btn-sm ltr:ml-2 rtl:mr-2">
                                                        📍 الخريطة
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- أزرار الإجراءات - بطاقة واحدة لكل أمر عمل --}}
                                    <div class="flex flex-col gap-2 min-w-[180px]">
                                        @if ($pumpStatus !== 'returned')
                                            @if ($pumpStatus === 'pending')
                                                <button type="button"
                                                    @click="updatePumpStatus({{ $job->id }}, 'departed')"
                                                    class="btn btn-primary w-full text-lg py-3">
                                                    🚀 انطلاق
                                                </button>
                                            @elseif($pumpStatus === 'departed')
                                                <button type="button"
                                                    @click="updatePumpStatus({{ $job->id }}, 'arrived')"
                                                    class="btn btn-info w-full text-lg py-3">
                                                    📍 وصول للموقع
                                                </button>
                                            @elseif($pumpStatus === 'arrived')
                                                <button type="button"
                                                    @click="updatePumpStatus({{ $job->id }}, 'working')"
                                                    class="btn btn-warning w-full text-lg py-3">
                                                    🔨 بدء العمل
                                                </button>
                                            @elseif($pumpStatus === 'working')
                                                <button type="button"
                                                    @click="updatePumpStatus({{ $job->id }}, 'returned')"
                                                    class="btn btn-dark w-full text-lg py-3">
                                                    🏠 رجوع للمقر
                                                </button>
                                            @endif
                                        @else
                                            <div class="bg-success/20 text-success rounded-lg p-4 text-center">
                                                <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="font-bold text-lg">تم الإكمال ✅</span>
                                                <p class="text-sm mt-1">البَم محرر</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- شريط التقدم للبَم --}}
                                <div class="mt-4 pt-4 border-t border-amber-200 dark:border-amber-700">
                                    <div class="flex items-center justify-between">
                                        {{-- انطلاق --}}
                                        <div class="flex flex-col items-center">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $job->pump_departure_time ? 'bg-success text-white' : 'bg-gray-200 text-gray-400' }}">
                                                🚀
                                            </div>
                                            <span class="text-xs mt-1 {{ $job->pump_departure_time ? 'text-success font-semibold' : 'text-gray-400' }}">انطلاق</span>
                                            @if ($job->pump_departure_time)
                                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($job->pump_departure_time)->format('H:i') }}</span>
                                            @endif
                                        </div>
                                        <div class="flex-1 h-1 mx-2 {{ $job->pump_departure_time ? 'bg-success' : 'bg-gray-200' }}"></div>

                                        {{-- وصول --}}
                                        <div class="flex flex-col items-center">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $job->pump_arrival_time ? 'bg-info text-white' : 'bg-gray-200 text-gray-400' }}">
                                                📍
                                            </div>
                                            <span class="text-xs mt-1 {{ $job->pump_arrival_time ? 'text-info font-semibold' : 'text-gray-400' }}">وصول</span>
                                            @if ($job->pump_arrival_time)
                                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($job->pump_arrival_time)->format('H:i') }}</span>
                                            @endif
                                        </div>
                                        <div class="flex-1 h-1 mx-2 {{ $job->pump_arrival_time ? 'bg-info' : 'bg-gray-200' }}"></div>

                                        {{-- بدء العمل --}}
                                        <div class="flex flex-col items-center">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $job->pump_work_start_time ? 'bg-warning text-white' : 'bg-gray-200 text-gray-400' }}">
                                                🔨
                                            </div>
                                            <span class="text-xs mt-1 {{ $job->pump_work_start_time ? 'text-warning font-semibold' : 'text-gray-400' }}">بدء العمل</span>
                                            @if ($job->pump_work_start_time)
                                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($job->pump_work_start_time)->format('H:i') }}</span>
                                            @endif
                                        </div>
                                        <div class="flex-1 h-1 mx-2 {{ $job->pump_work_start_time ? 'bg-warning' : 'bg-gray-200' }}"></div>

                                        {{-- رجوع --}}
                                        <div class="flex flex-col items-center">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $job->pump_return_time ? 'bg-dark text-white' : 'bg-gray-200 text-gray-400' }}">
                                                🏠
                                            </div>
                                            <span class="text-xs mt-1 {{ $job->pump_return_time ? 'text-dark font-semibold' : 'text-gray-400' }}">رجوع</span>
                                            @if ($job->pump_return_time)
                                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($job->pump_return_time)->format('H:i') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- قائمة الشحنات (للخباطات والشاحنات) --}}
            <div class="panel">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="text-lg font-semibold dark:text-white-light">
                        <svg class="w-6 h-6 inline-block text-primary ltr:mr-2 rtl:ml-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        {{ $isPumpDriver ? 'شحنات إضافية (خباطة/شاحنة)' : 'الشحنات المخصصة لي' }}
                    </h5>
                    <span class="badge bg-primary">{{ $shipments->count() }} شحنة</span>
                </div>

                @if ($shipments->count() > 0)
                    @php
                        $pumpStartedByJob = $shipments->groupBy('job_id')->map(function ($group) {
                            $job = $group->first()->job ?? null;
                            return $job && $job->pump_work_start_time ? true : false;
                        });
                    @endphp
                    <div class="space-y-4">
                        @foreach ($shipments as $shipment)
                            @php
                                $isMixerDriverForShipment = (int) ($shipment->mixer_driver_id ?? 0) === (int) Auth::id();
                                $jobHasPumpDriver = (int) ($shipment->job->default_pump_driver_id ?? 0) > 0;
                                $jobPumpStarted = (bool) ($pumpStartedByJob[$shipment->job_id] ?? false);
                            @endphp
                            <div
                                class="border rounded-lg p-4 hover:shadow-md transition-shadow
                                @if ($shipment->status === 'working') border-warning bg-warning-light
                                @elseif($shipment->status === 'departed' || $shipment->status === 'arrived') border-info bg-info-light
                                @elseif(in_array($shipment->status, ['completed', 'returned'])) border-success bg-success-light
                                @else border-gray-200 @endif">

                                <div class="flex flex-col md:flex-row justify-between gap-4">
                                    {{-- معلومات الشحنة --}}
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="text-lg font-bold text-primary">
                                                شحنة #{{ $shipment->shipment_number }}
                                            </span>
                                            <span class="badge bg-{{ $shipment->status_badge }}">
                                                {{ $shipment->status_label }}
                                            </span>
                                        </div>

                                        {{-- معلومات الآلية --}}
                                        @if ($shipment->mixer)
                                            <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-3 mb-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-blue-800 dark:text-blue-300">{{ $shipment->mixer->car_model ?? 'خباطة' }}</p>
                                                        <p class="text-sm text-blue-600 dark:text-blue-400">
                                                            رقم اللوحة: {{ $shipment->mixer->car_number }}
                                                            @if ($shipment->mixer->carType)
                                                                | {{ $shipment->mixer->carType->name }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                            <div>
                                                <span class="text-gray-500">أمر العمل:</span>
                                                <p class="font-semibold">{{ $shipment->job->job_number ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">الكمية:</span>
                                                <p class="font-semibold">{{ number_format($shipment->planned_quantity, 1) }} م³</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">العميل:</span>
                                                <p class="font-semibold">{{ $shipment->job->customer_name ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">العنوان:</span>
                                                <p class="font-semibold text-truncate" title="{{ $shipment->job->location_address ?? '' }}">
                                                    {{ \Illuminate\Support\Str::limit($shipment->job->location_address ?? '-', 30) }}
                                                </p>
                                            </div>
                                        </div>

                                        @if ($shipment->job && $shipment->job->customer_phone)
                                            <div class="mt-2">
                                                <a href="tel:{{ $shipment->job->customer_phone }}" class="btn btn-outline-success btn-sm">
                                                    📞 {{ $shipment->job->customer_phone }}
                                                </a>
                                                @if ($shipment->job->location_map_url)
                                                    <a href="{{ $shipment->job->location_map_url }}" target="_blank"
                                                        class="btn btn-outline-info btn-sm ltr:ml-2 rtl:mr-2">
                                                        📍 الخريطة
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- أزرار الإجراءات --}}
                                    <div class="flex flex-col gap-2 min-w-[180px]">
                                        @if (!in_array($shipment->status, ['returned', 'cancelled']))
                                            @if ($shipment->status === 'planned' || $shipment->status === 'preparing')
                                                <button type="button" @click="updateStatus({{ $shipment->id }}, 'departed')" class="btn btn-primary w-full">
                                                    🚀 انطلاق
                                                </button>
                                            @elseif($shipment->status === 'departed')
                                                <button type="button" @click="updateStatus({{ $shipment->id }}, 'arrived')" class="btn btn-info w-full">
                                                    📍 وصلت للموقع
                                                </button>
                                            @elseif($shipment->status === 'arrived')
                                                @if (!$jobHasPumpDriver || $jobPumpStarted)
                                                    <button type="button" @click="updateStatus({{ $shipment->id }}, 'working')" class="btn btn-warning w-full">
                                                        🔨 بدء التفريغ
                                                    </button>
                                                @else
                                                    <div class="btn btn-outline-warning w-full pointer-events-none opacity-70">
                                                        ⏳ بانتظار بدء البَم
                                                    </div>
                                                @endif
                                            @elseif($shipment->status === 'working')
                                                <button type="button" @click="updateStatus({{ $shipment->id }}, 'completed')" class="btn btn-success w-full">
                                                    ✅ انتهى التفريغ
                                                </button>
                                            @elseif($shipment->status === 'completed')
                                                <button type="button" @click="updateStatus({{ $shipment->id }}, 'returned')" class="btn btn-dark w-full">
                                                    🏠 وصلت للمقر
                                                </button>
                                            @endif
                                        @else
                                            @if ($shipment->status === 'returned')
                                                <div class="bg-success/20 text-success rounded-lg p-3 text-center">
                                                    <span class="font-bold">تم الإكمال ✅</span>
                                                </div>
                                            @endif
                                        @endif

                                        <a href="{{ route('driver.shipments.show', $shipment->id) }}" class="btn btn-outline-primary w-full">
                                            👁️ التفاصيل
                                        </a>
                                    </div>
                                </div>

                                {{-- شريط التقدم --}}
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <div class="flex flex-col items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $shipment->departure_time ? 'bg-success text-white' : 'bg-gray-200' }}">🚀</div>
                                            <span class="text-xs mt-1">انطلاق</span>
                                            @if ($shipment->departure_time)
                                                <span class="text-xs text-gray-500">{{ $shipment->departure_time->format('H:i') }}</span>
                                            @endif
                                        </div>
                                        <div class="flex-1 h-1 mx-1 {{ $shipment->departure_time ? 'bg-success' : 'bg-gray-200' }}"></div>
                                        <div class="flex flex-col items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $shipment->arrival_time ? 'bg-info text-white' : 'bg-gray-200' }}">📍</div>
                                            <span class="text-xs mt-1">وصول</span>
                                            @if ($shipment->arrival_time)
                                                <span class="text-xs text-gray-500">{{ $shipment->arrival_time->format('H:i') }}</span>
                                            @endif
                                        </div>
                                        <div class="flex-1 h-1 mx-1 {{ $shipment->arrival_time ? 'bg-info' : 'bg-gray-200' }}"></div>
                                        <div class="flex flex-col items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $shipment->work_start_time ? 'bg-warning text-white' : 'bg-gray-200' }}">🔨</div>
                                            <span class="text-xs mt-1">تفريغ</span>
                                            @if ($shipment->work_start_time)
                                                <span class="text-xs text-gray-500">{{ $shipment->work_start_time->format('H:i') }}</span>
                                            @endif
                                        </div>
                                        <div class="flex-1 h-1 mx-1 {{ $shipment->work_start_time ? 'bg-warning' : 'bg-gray-200' }}"></div>
                                        <div class="flex flex-col items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $shipment->work_end_time ? 'bg-success text-white' : 'bg-gray-200' }}">✅</div>
                                            <span class="text-xs mt-1">انتهاء</span>
                                            @if ($shipment->work_end_time)
                                                <span class="text-xs text-gray-500">{{ $shipment->work_end_time->format('H:i') }}</span>
                                            @endif
                                        </div>
                                        <div class="flex-1 h-1 mx-1 {{ $shipment->work_end_time ? 'bg-success' : 'bg-gray-200' }}"></div>
                                        <div class="flex flex-col items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $shipment->return_time ? 'bg-dark text-white' : 'bg-gray-200' }}">🏠</div>
                                            <span class="text-xs mt-1">مقر</span>
                                            @if ($shipment->return_time)
                                                <span class="text-xs text-gray-500">{{ $shipment->return_time->format('H:i') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    @if (!$isPumpDriver || $pumpJobs->isEmpty())
                        <div class="text-center py-10">
                            <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            <p class="text-gray-500 text-lg">لا توجد شحنات مخصصة لك حالياً</p>
                            <p class="text-gray-400 text-sm mt-2">سيتم عرض الشحنات هنا عند تخصيصها لك</p>
                        </div>
                    @else
                        <div class="text-center py-5 text-gray-400">
                            <p>لا توجد شحنات خباطة/شاحنة إضافية</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <script>
        const baseUrl = '{{ url('/') }}';
        document.addEventListener('alpine:init', () => {
            Alpine.data('driverShipments', () => ({
                loading: false,

                async updateStatus(shipmentId, newStatus) {
                    const statusLabels = {
                        'departed': 'الانطلاق 🚀',
                        'arrived': 'الوصول للموقع 📍',
                        'working': 'بدء التفريغ 🔨',
                        'completed': 'انتهاء التفريغ ✅',
                        'returned': 'الوصول للمقر 🏠'
                    };

                    const statusDescriptions = {
                        'departed': 'سيتم تسجيل وقت انطلاقك من المحطة',
                        'arrived': 'سيتم تسجيل وقت وصولك لموقع العميل',
                        'working': 'سيتم تسجيل وقت بدء تفريغ الخرسانة',
                        'completed': 'سيتم تسجيل وقت انتهاء التفريغ',
                        'returned': 'سيتم تسجيل وقت وصولك للمقر وتحرير الآلية'
                    };

                    const result = await Swal.fire({
                        title: 'تأكيد ' + statusLabels[newStatus],
                        text: statusDescriptions[newStatus],
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، تأكيد',
                        cancelButtonText: 'إلغاء',
                        confirmButtonColor: newStatus === 'returned' ? '#1e1e1e' : '#4361ee'
                    });

                    if (!result.isConfirmed) return;

                    this.loading = true;
                    try {
                        const response = await fetch(
                            `${baseUrl}/driver/shipments/${shipmentId}/status`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    status: newStatus
                                })
                            });

                        const data = await response.json();

                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: 'تم بنجاح',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: data.message
                            });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ في الاتصال بالخادم'
                        });
                    } finally {
                        this.loading = false;
                    }
                },

                async updatePumpStatus(jobId, newStatus) {
                    const statusLabels = {
                        'departed': 'انطلاق البَم 🚀',
                        'arrived': 'وصول البَم للموقع 📍',
                        'working': 'بدء عمل البَم 🔨',
                        'returned': 'رجوع البَم للمقر 🏠'
                    };

                    const statusDescriptions = {
                        'departed': 'سيتم تسجيل وقت انطلاق البَم من المحطة',
                        'arrived': 'سيتم تسجيل وقت وصول البَم لموقع العمل',
                        'working': 'سيتم تسجيل وقت بدء عمل البَم - هذا الإجراء مرة واحدة فقط',
                        'returned': 'سيتم تسجيل وقت رجوع البَم للمقر وتحرير الآلية'
                    };

                    const result = await Swal.fire({
                        title: 'تأكيد ' + statusLabels[newStatus],
                        text: statusDescriptions[newStatus],
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، تأكيد',
                        cancelButtonText: 'إلغاء',
                        confirmButtonColor: newStatus === 'returned' ? '#1e1e1e' : '#f59e0b'
                    });

                    if (!result.isConfirmed) return;

                    this.loading = true;
                    try {
                        const response = await fetch(
                            `${baseUrl}/driver/pump-job/${jobId}/status`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    status: newStatus
                                })
                            });

                        const data = await response.json();

                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: 'تم بنجاح',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: data.message
                            });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ في الاتصال بالخادم'
                        });
                    } finally {
                        this.loading = false;
                    }
                }
            }));
        });
    </script>

    <style>
        .bg-success-light {
            background-color: rgba(0, 171, 85, 0.1);
        }

        .bg-warning-light {
            background-color: rgba(255, 171, 0, 0.1);
        }

        .bg-info-light {
            background-color: rgba(0, 184, 217, 0.1);
        }
    </style>
@endsection
