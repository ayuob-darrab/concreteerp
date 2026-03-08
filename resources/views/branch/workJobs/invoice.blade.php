@extends('layouts.app')

@section('page-title', 'فاتورة أمر العمل')

@section('content')
    <div class="panel mt-6">
        {{-- أزرار الطباعة --}}
        <div class="flex justify-end gap-2 mb-4 print:hidden">
            <button onclick="window.print()" class="btn btn-primary">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                طباعة الفاتورة
            </button>
            <a href="{{ url('companyBranch/workJobs/completed') }}" class="btn btn-outline-secondary">
                رجوع
            </a>
        </div>

        {{-- الفاتورة --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-8 max-w-3xl mx-auto print:shadow-none print:max-w-full">
            {{-- ترويسة الفاتورة --}}
            <div class="text-center mb-8 border-b border-gray-200 pb-6">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">فاتورة توريد خرسانة</h1>
                <p class="text-gray-500">{{ $job->branch->branch_name ?? 'الفرع' }}</p>
            </div>

            {{-- معلومات الفاتورة --}}
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div>
                    <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">معلومات أمر العمل</h4>
                    <table class="text-sm">
                        <tr>
                            <td class="text-gray-500 pl-4">رقم أمر العمل:</td>
                            <td class="font-semibold">{{ $job->job_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 pl-4">رقم الطلب:</td>
                            <td class="font-semibold">{{ $job->workOrder->order_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 pl-4">تاريخ التنفيذ:</td>
                            <td class="font-semibold">
                                {{ $job->actual_start_date ? \Carbon\Carbon::parse($job->actual_start_date)->format('Y-m-d') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 pl-4">تاريخ الإكمال:</td>
                            <td class="font-semibold">
                                {{ $job->actual_end_date ? \Carbon\Carbon::parse($job->actual_end_date)->format('Y-m-d') : '-' }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">معلومات العميل</h4>
                    <table class="text-sm">
                        <tr>
                            <td class="text-gray-500 pl-4">اسم العميل:</td>
                            <td class="font-semibold">{{ $job->workOrder->customer->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 pl-4">رقم الهاتف:</td>
                            <td class="font-semibold">{{ $job->workOrder->customer->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 pl-4">المشروع:</td>
                            <td class="font-semibold">{{ $job->workOrder->project->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 pl-4">الموقع:</td>
                            <td class="font-semibold">{{ $job->workOrder->location ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- تفاصيل الخرسانة --}}
            <div class="mb-8">
                <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 border-b pb-2">تفاصيل التوريد</h4>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="p-3 text-right">البيان</th>
                            <th class="p-3 text-center">الكمية</th>
                            <th class="p-3 text-center">الوحدة</th>
                            <th class="p-3 text-center">السعر</th>
                            <th class="p-3 text-left">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalQuantity = $job->shipments->where('status', 'completed')->sum('actual_quantity') ?? 0;
                            $unitPrice = $job->workOrder->price ?? 0;
                            $totalPrice = $totalQuantity * $unitPrice;
                        @endphp
                        <tr class="border-b">
                            <td class="p-3">
                                خرسانة {{ $job->workOrder->concrete_type ?? '-' }}
                                <span
                                    class="text-gray-500 text-xs block">{{ $job->workOrder->concrete_grade ?? '' }}</span>
                            </td>
                            <td class="p-3 text-center">{{ number_format($totalQuantity, 2) }}</td>
                            <td class="p-3 text-center">م³</td>
                            <td class="p-3 text-center">{{ number_format($unitPrice, 2) }}</td>
                            <td class="p-3 text-left font-semibold">{{ number_format($totalPrice, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- تفاصيل الشحنات --}}
            <div class="mb-8">
                <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 border-b pb-2">
                    تفاصيل الشحنات ({{ $job->shipments->where('status', 'completed')->count() }})
                </h4>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="p-2 text-right">الشحنة</th>
                            <th class="p-2 text-center">الخلاطة</th>
                            <th class="p-2 text-center">السائق</th>
                            <th class="p-2 text-center">الكمية</th>
                            <th class="p-2 text-center">الوقت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($job->shipments->where('status', 'completed') as $shipment)
                            <tr class="border-b">
                                <td class="p-2">{{ $shipment->shipment_number }}</td>
                                <td class="p-2 text-center">{{ $shipment->mixer->car_number ?? '-' }}</td>
                                <td class="p-2 text-center">{{ $shipment->mixerDriver->fullname ?? '-' }}</td>
                                <td class="p-2 text-center">{{ $shipment->actual_quantity }} م³</td>
                                <td class="p-2 text-center">
                                    {{ $shipment->departure_time ? \Carbon\Carbon::parse($shipment->departure_time)->format('H:i') : '-' }}
                                    -
                                    {{ $shipment->return_time ? \Carbon\Carbon::parse($shipment->return_time)->format('H:i') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ملخص الفاتورة --}}
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                <div class="flex justify-between items-center text-lg">
                    <span class="font-semibold">إجمالي الكمية:</span>
                    <span class="font-bold">{{ number_format($totalQuantity, 2) }} م³</span>
                </div>
                <div class="flex justify-between items-center text-lg mt-2">
                    <span class="font-semibold">سعر المتر المكعب:</span>
                    <span class="font-bold">{{ number_format($unitPrice, 2) }} د.ع</span>
                </div>
                <hr class="my-4 border-gray-300 dark:border-gray-600">
                <div class="flex justify-between items-center text-2xl">
                    <span class="font-bold text-gray-800 dark:text-white">الإجمالي:</span>
                    <span class="font-bold text-success">{{ number_format($totalPrice, 2) }} د.ع</span>
                </div>
            </div>

            {{-- التوقيعات --}}
            <div class="grid grid-cols-2 gap-12 mt-12 pt-8 border-t">
                <div class="text-center">
                    <div class="h-16 border-b border-dashed border-gray-400 mb-2"></div>
                    <p class="text-gray-600">توقيع العميل</p>
                </div>
                <div class="text-center">
                    <div class="h-16 border-b border-dashed border-gray-400 mb-2"></div>
                    <p class="text-gray-600">توقيع المسؤول</p>
                </div>
            </div>

            {{-- تذييل --}}
            <div class="text-center mt-8 pt-4 border-t text-sm text-gray-500">
                <p>تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</p>
                <p class="mt-1">شكراً لتعاملكم معنا</p>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .panel,
            .panel * {
                visibility: visible;
            }

            .panel {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .print\:hidden {
                display: none !important;
            }
        }
    </style>
@endsection
