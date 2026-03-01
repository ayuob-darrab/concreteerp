@extends('layouts.app')

@section('page-title', 'سجل الشحنات')

@section('content')
    <div class="mt-6 space-y-6">
        <!-- رأس الصفحة -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h4 class="text-xl font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <i class="fas fa-history text-primary"></i>
                سجل الشحنات
            </h4>
            <a href="{{ route('driver.dashboard') }}" class="btn btn-outline-primary inline-flex items-center gap-2">
                <i class="fas fa-home"></i>
                <span>الرئيسية</span>
            </a>
        </div>

        <!-- ملخص الإحصائيات -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="panel text-center" style="background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%); color:#fff;">
                <div class="text-2xl font-bold mb-1">{{ $stats['total'] }}</div>
                <div class="text-sm opacity-90">إجمالي الشحنات</div>
            </div>
            <div class="panel text-center" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color:#fff;">
                <div class="text-2xl font-bold mb-1">{{ number_format($stats['total_quantity'], 1) }}</div>
                <div class="text-sm opacity-90">إجمالي الكمية (م³)</div>
            </div>
            <div class="panel text-center" style="background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%); color:#fff;">
                <div class="text-2xl font-bold mb-1">{{ $stats['avg_per_day'] }}</div>
                <div class="text-sm opacity-90">متوسط / يوم</div>
            </div>
        </div>

        <!-- الفلتر -->
        <div class="panel">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-filter text-gray-500 dark:text-gray-400"></i>
                <span class="font-medium text-gray-700 dark:text-gray-200">تصفية حسب التاريخ</span>
            </div>
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[140px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">من</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="form-input w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                </div>
                <div class="flex-1 min-w-[140px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">إلى</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="form-input w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                </div>
                <button type="submit" class="btn btn-primary inline-flex items-center gap-2">
                    <i class="fas fa-search"></i>
                    <span>بحث</span>
                </button>
            </form>
        </div>

        <!-- قائمة الشحنات حسب التاريخ -->
        @php
            $grouped = $shipments->groupBy(fn($s) => optional($s->created_at)->format('Y-m-d') ?? 'unknown');
        @endphp
        @forelse($grouped as $date => $dayShipments)
            <div class="panel">
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
                    <strong class="text-gray-800 dark:text-gray-100">
                        {{ $date !== 'unknown' ? \Carbon\Carbon::parse($date)->locale('ar')->format('l j F Y') : 'غير محدد' }}
                    </strong>
                    <span class="badge bg-secondary rounded-full px-3 py-1 text-xs">
                        {{ $dayShipments->count() }} شحنة
                    </span>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($dayShipments as $shipment)
                        <div class="py-4 first:pt-0">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <div class="font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $shipment->job->job_number ?? '-' }}
                                        <span class="text-gray-500 dark:text-gray-400 font-normal">#{{ $shipment->shipment_number }}</span>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $shipment->job->customer_name ?? '-' }}
                                    </div>
                                    @if ($shipment->departure_time || $shipment->arrival_time || $shipment->return_time)
                                        <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            @if ($shipment->departure_time)
                                                <span><i class="fas fa-truck w-4"></i> {{ $shipment->departure_time->format('H:i') }}</span>
                                            @endif
                                            @if ($shipment->arrival_time)
                                                <span><i class="fas fa-map-marker-alt w-4"></i> {{ $shipment->arrival_time->format('H:i') }}</span>
                                            @endif
                                            @if ($shipment->return_time)
                                                <span><i class="fas fa-home w-4"></i> {{ $shipment->return_time->format('H:i') }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="text-left flex flex-col items-end gap-1">
                                    <span class="badge bg-{{ $shipment->status_badge }} rounded-full px-3 py-1 text-xs whitespace-nowrap">
                                        {{ $shipment->status_label }}
                                    </span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-100">
                                        {{ number_format($shipment->actual_quantity ?? $shipment->planned_quantity, 1) }} م³
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="panel text-center py-12">
                <div class="text-4xl text-gray-400 dark:text-gray-500 mb-3">
                    <i class="fas fa-inbox"></i>
                </div>
                <p class="text-gray-600 dark:text-gray-400">لا توجد شحنات في السجل</p>
                <a href="{{ route('driver.dashboard') }}" class="btn btn-outline-primary mt-4">العودة للرئيسية</a>
            </div>
        @endforelse

        <!-- ترقيم الصفحات -->
        @if($shipments->hasPages())
            <div class="flex justify-center pt-2">
                {{ $shipments->links() }}
            </div>
        @endif
    </div>
@endsection
