@extends('layouts.app')

@section('page-title', 'أوامر العمل قيد التنفيذ 🚧')

@section('content')
    <div class="panel mt-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <h3 class="text-lg font-semibold dark:text-white-light">
                <span class="text-2xl">🚧</span> أوامر العمل قيد التنفيذ
            </h3>
            <div class="flex items-center gap-2">
                <span class="badge bg-info/20 text-info px-3 py-1.5 rounded-full text-sm font-medium">
                    {{ $jobs->count() }} أمر عمل نشط
                </span>
            </div>
        </div>

        @if ($jobs->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach ($jobs as $job)
                    <div class="panel border-2 border-info/30 rounded-lg p-4">
                        {{-- الرأس --}}
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-bold text-lg text-primary">{{ $job->job_number }}</h4>
                                <p class="text-sm text-gray-500">{{ $job->customer_name }} - {{ $job->customer_phone }}</p>
                            </div>
                            <span class="badge bg-info text-white px-3 py-1 animate-pulse">
                                🚧 قيد التنفيذ
                            </span>
                        </div>

                        {{-- شريط التقدم (نسبة الإنجاز محسوبة من المنفذ/الإجمالي ليتطابق الشريط مع الأرقام) --}}
                        @php
                            $totalQty = (float) $job->total_quantity;
                            $executedQty = (float) $job->executed_quantity;
                            $progress = $totalQty > 0 ? min(100, round(($executedQty / $totalQty) * 100, 1)) : 0;
                        @endphp
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="font-medium">نسبة الإنجاز</span>
                                <span class="font-bold text-primary">{{ number_format($progress, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-300 dark:bg-gray-600 rounded-full h-4 overflow-hidden">
                                <div class="h-4 rounded-full transition-all duration-500"
                                    style="width: {{ $progress }}%; background: linear-gradient(90deg, #0ea5e9, #3b82f6);">
                                </div>
                            </div>
                            <div class="flex justify-between text-xs mt-1 text-gray-500">
                                <span>المنفذ: {{ number_format($executedQty, 1) }} م³</span>
                                <span>المتبقي: {{ number_format($totalQty - $executedQty, 1) }} م³</span>
                                <span>الإجمالي: {{ number_format($totalQty, 1) }} م³</span>
                            </div>
                        </div>

                        {{-- معلومات سريعة --}}
                        <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                            <div class="bg-gray-50 dark:bg-gray-800 p-2 rounded">
                                <span class="text-gray-500 block">نوع الكونكريت</span>
                                <span class="font-semibold">{{ $job->concreteType->classification ?? '-' }}</span>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800 p-2 rounded">
                                <span class="text-gray-500 block">عدد الشحنات</span>
                                <span class="font-semibold">{{ $job->shipments->count() }} شحنة</span>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800 p-2 rounded">
                                <span class="text-gray-500 block">تاريخ البدء</span>
                                <span
                                    class="font-semibold">{{ $job->actual_start_date ? \Carbon\Carbon::parse($job->actual_start_date)->format('Y-m-d') : '-' }}</span>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800 p-2 rounded">
                                <span class="text-gray-500 block">المبلغ الإجمالي</span>
                                <span class="font-semibold text-success">{{ number_format($job->final_price) }} د.ع</span>
                            </div>
                        </div>

                        {{-- الشحنات النشطة --}}
                        @if ($job->shipments->whereIn('status', ['departed', 'arrived', 'working'])->count() > 0)
                            <div class="mb-4 p-3 bg-warning/10 rounded-lg">
                                <h5 class="font-semibold text-sm mb-2">🚛 الشحنات النشطة</h5>
                                @foreach ($job->shipments->whereIn('status', ['departed', 'arrived', 'working']) as $shipment)
                                    <div
                                        class="flex items-center justify-between text-sm py-1 border-b border-warning/20 last:border-0">
                                        <span>شحنة #{{ $shipment->shipment_number }}</span>
                                        <span class="badge bg-warning/20 text-warning text-xs">
                                            {{ $shipment->status_label }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- الإجراءات --}}
                        <div class="flex gap-2">
                            <a href="{{ url('companyBranch/workJob/{{ $job->id }}/view') }}"
                                class="btn btn-sm btn-outline-primary flex-1">
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                عرض التفاصيل
                            </a>
                            @if ($job->executed_quantity >= $job->total_quantity)
                                <button type="button" onclick="completeJob({{ $job->id }})"
                                    class="btn btn-sm btn-success flex-1">
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    إكمال
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12">
                <svg class="w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <p class="text-gray-500 text-lg">لا توجد أوامر عمل قيد التنفيذ حالياً</p>
            </div>
        @endif
    </div>

    <script>
        const baseUrl = '{{ url('/') }}';
        function completeJob(id) {
            if (confirm('هل تريد إكمال هذا الأمر؟ سيتم تحويله للمعالجة المالية.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${baseUrl}/companyBranch/workJob/${id}/complete`;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                form.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">`;

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
