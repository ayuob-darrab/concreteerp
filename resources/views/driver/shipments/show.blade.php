@extends('layouts.app')

@section('title', 'تفاصيل الشحنة')

@section('content')
    <div x-data="shipmentDetails()">
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="/ConcreteERP" class="text-primary hover:underline">الرئيسية</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-1 rtl:before:ml-1">
                <a href="{{ route('driver.shipments.index') }}" class="text-primary hover:underline">شحناتي</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-1 rtl:before:ml-1">
                <span>الشحنة #{{ $shipment->shipment_number }}</span>
            </li>
        </ul>

        <div class="pt-5">
            {{-- رأس الصفحة --}}
            <div class="panel mb-5">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div>
                        <h4 class="text-2xl font-bold text-gray-800 dark:text-white">
                            الشحنة #{{ $shipment->shipment_number }}
                        </h4>
                        <p class="text-gray-500 mt-1">
                            أمر العمل: {{ $shipment->job->job_number ?? '-' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="badge text-lg px-4 py-2 bg-{{ $shipment->status_badge }}">
                            {{ $shipment->status_label }}
                        </span>
                        <a href="{{ route('driver.shipments.index') }}" class="btn btn-outline-primary">
                            <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            العودة
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                {{-- العمود الأيسر --}}
                <div class="lg:col-span-2 space-y-5">
                    {{-- معلومات العميل --}}
                    <div class="panel">
                        <h5 class="text-lg font-semibold mb-4 flex items-center">
                            <svg class="w-5 h-5 text-primary ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            معلومات العميل
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-gray-500 text-sm">الاسم</label>
                                <p class="font-semibold text-lg">{{ $shipment->job->customer_name ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-sm">الهاتف</label>
                                <p class="font-semibold text-lg">
                                    @if($shipment->job && $shipment->job->customer_phone)
                                        <a href="tel:{{ $shipment->job->customer_phone }}" class="text-primary hover:underline">
                                            {{ $shipment->job->customer_phone }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-gray-500 text-sm">العنوان</label>
                                <p class="font-semibold">{{ $shipment->job->location_address ?? '-' }}</p>
                            </div>
                        </div>
                        
                        @if($shipment->job && ($shipment->job->customer_phone || $shipment->job->location_map_url))
                            <div class="mt-4 flex flex-wrap gap-2">
                                @if($shipment->job->customer_phone)
                                    <a href="tel:{{ $shipment->job->customer_phone }}" class="btn btn-success">
                                        <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        اتصال
                                    </a>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shipment->job->customer_phone) }}" target="_blank" class="btn btn-outline-success">
                                        واتساب
                                    </a>
                                @endif
                                @if($shipment->job->location_map_url)
                                    <a href="{{ $shipment->job->location_map_url }}" target="_blank" class="btn btn-info">
                                        <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        فتح الخريطة
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- معلومات الشحنة --}}
                    <div class="panel">
                        <h5 class="text-lg font-semibold mb-4 flex items-center">
                            <svg class="w-5 h-5 text-primary ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            معلومات الشحنة
                        </h5>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="text-gray-500 text-sm">الكمية المخططة</label>
                                <p class="font-bold text-2xl text-primary">{{ number_format($shipment->planned_quantity, 1) }} م³</p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-sm">الكمية الفعلية</label>
                                <p class="font-bold text-2xl text-success">{{ $shipment->actual_quantity ? number_format($shipment->actual_quantity, 1) . ' م³' : '-' }}</p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-sm">نوع الخرسانة</label>
                                <p class="font-semibold">{{ $shipment->job->concreteType->name ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-sm">الفرع</label>
                                <p class="font-semibold">{{ $shipment->job->branch->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- المركبات والسائقين --}}
                    <div class="panel">
                        <h5 class="text-lg font-semibold mb-4 flex items-center">
                            <svg class="w-5 h-5 text-primary ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            المركبات والسائقين
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @if($shipment->mixer)
                                <div class="border rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="w-12 h-12 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-2">
                                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                            </svg>
                                        </div>
                                        <h6 class="font-semibold">الخلاطة</h6>
                                        <p class="text-primary font-bold">{{ $shipment->mixer->plate_number }}</p>
                                        <p class="text-sm text-gray-500">{{ $shipment->mixerDriver->fullname ?? '-' }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($shipment->truck)
                                <div class="border rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="w-12 h-12 mx-auto bg-info/10 rounded-full flex items-center justify-center mb-2">
                                            <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                            </svg>
                                        </div>
                                        <h6 class="font-semibold">الشاحنة</h6>
                                        <p class="text-info font-bold">{{ $shipment->truck->plate_number }}</p>
                                        <p class="text-sm text-gray-500">{{ $shipment->truckDriver->fullname ?? '-' }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($shipment->pump)
                                <div class="border rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="w-12 h-12 mx-auto bg-warning/10 rounded-full flex items-center justify-center mb-2">
                                            <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                            </svg>
                                        </div>
                                        <h6 class="font-semibold">المضخة</h6>
                                        <p class="text-warning font-bold">{{ $shipment->pump->plate_number }}</p>
                                        <p class="text-sm text-gray-500">{{ $shipment->pumpDriver->fullname ?? '-' }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ملاحظات --}}
                    @if($shipment->notes || $shipment->driver_notes)
                        <div class="panel">
                            <h5 class="text-lg font-semibold mb-4 flex items-center">
                                <svg class="w-5 h-5 text-primary ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                الملاحظات
                            </h5>
                            @if($shipment->notes)
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-3">
                                    <label class="text-gray-500 text-sm block mb-1">ملاحظات الإدارة</label>
                                    <p>{{ $shipment->notes }}</p>
                                </div>
                            @endif
                            @if($shipment->driver_notes)
                                <div class="bg-primary/10 rounded-lg p-4">
                                    <label class="text-gray-500 text-sm block mb-1">ملاحظات السائق</label>
                                    <p>{{ $shipment->driver_notes }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- العمود الأيمن --}}
                <div class="space-y-5">
                    {{-- الجدول الزمني --}}
                    <div class="panel">
                        <h5 class="text-lg font-semibold mb-4 flex items-center">
                            <svg class="w-5 h-5 text-primary ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            الجدول الزمني
                        </h5>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 {{ $shipment->departure_time ? 'text-success' : 'text-gray-400' }}">
                                <div class="w-8 h-8 rounded-full {{ $shipment->departure_time ? 'bg-success' : 'bg-gray-200' }} flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold">الانطلاق</p>
                                    <p class="text-sm">{{ $shipment->departure_time ? $shipment->departure_time->format('H:i') : '-' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3 {{ $shipment->arrival_time ? 'text-info' : 'text-gray-400' }}">
                                <div class="w-8 h-8 rounded-full {{ $shipment->arrival_time ? 'bg-info' : 'bg-gray-200' }} flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold">الوصول</p>
                                    <p class="text-sm">{{ $shipment->arrival_time ? $shipment->arrival_time->format('H:i') : '-' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3 {{ $shipment->work_start_time ? 'text-warning' : 'text-gray-400' }}">
                                <div class="w-8 h-8 rounded-full {{ $shipment->work_start_time ? 'bg-warning' : 'bg-gray-200' }} flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold">بدء التفريغ</p>
                                    <p class="text-sm">{{ $shipment->work_start_time ? $shipment->work_start_time->format('H:i') : '-' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3 {{ $shipment->work_end_time ? 'text-success' : 'text-gray-400' }}">
                                <div class="w-8 h-8 rounded-full {{ $shipment->work_end_time ? 'bg-success' : 'bg-gray-200' }} flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold">انتهاء التفريغ</p>
                                    <p class="text-sm">{{ $shipment->work_end_time ? $shipment->work_end_time->format('H:i') : '-' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3 {{ $shipment->return_time ? 'text-dark' : 'text-gray-400' }}">
                                <div class="w-8 h-8 rounded-full {{ $shipment->return_time ? 'bg-dark' : 'bg-gray-200' }} flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold">العودة للمحطة</p>
                                    <p class="text-sm">{{ $shipment->return_time ? $shipment->return_time->format('H:i') : '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- الإجراءات --}}
                    @if(!in_array($shipment->status, ['completed', 'returned', 'cancelled']))
                        <div class="panel">
                            <h5 class="text-lg font-semibold mb-4 flex items-center">
                                <svg class="w-5 h-5 text-primary ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                الإجراءات
                            </h5>
                            <div class="space-y-2">
                                @if($shipment->status === 'planned' || $shipment->status === 'preparing')
                                    <button type="button" 
                                            @click="updateStatus({{ $shipment->id }}, 'departed')"
                                            class="btn btn-primary w-full">
                                        <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        تأكيد الانطلاق
                                    </button>
                                @elseif($shipment->status === 'departed')
                                    <button type="button" 
                                            @click="updateStatus({{ $shipment->id }}, 'arrived')"
                                            class="btn btn-info w-full">
                                        <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        </svg>
                                        تأكيد الوصول
                                    </button>
                                @elseif($shipment->status === 'arrived')
                                    <button type="button" 
                                            @click="updateStatus({{ $shipment->id }}, 'working')"
                                            class="btn btn-warning w-full">
                                        <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                        </svg>
                                        بدء التفريغ
                                    </button>
                                @elseif($shipment->status === 'working')
                                    <button type="button" 
                                            @click="updateStatus({{ $shipment->id }}, 'completed')"
                                            class="btn btn-success w-full">
                                        <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        اكتمل التفريغ
                                    </button>
                                @elseif($shipment->status === 'completed')
                                    <button type="button" 
                                            @click="updateStatus({{ $shipment->id }}, 'returned')"
                                            class="btn btn-dark w-full">
                                        <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                        عدت للمحطة
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('shipmentDetails', () => ({
                loading: false,

                async updateStatus(shipmentId, newStatus) {
                    const statusLabels = {
                        'departed': 'الانطلاق',
                        'arrived': 'الوصول',
                        'working': 'بدء التفريغ',
                        'completed': 'اكتمال التفريغ',
                        'returned': 'العودة للمحطة'
                    };

                    const result = await Swal.fire({
                        title: 'تأكيد',
                        text: `هل تريد تأكيد ${statusLabels[newStatus]}؟`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، تأكيد',
                        cancelButtonText: 'إلغاء'
                    });

                    if (!result.isConfirmed) return;

                    this.loading = true;
                    try {
                        const response = await fetch(`/ConcreteERP/driver/shipments/${shipmentId}/status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ status: newStatus })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: 'تم بنجاح',
                                text: data.message,
                                timer: 1500,
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
@endsection
