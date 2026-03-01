@extends('layouts.app')

@section('page-title', 'التقرير المالي للطلبات')

@section('content')
<div x-data="{ showFilters: true, activeTab: 'overview' }">
    
    <!-- رأس الصفحة -->
    <div class="panel bg-gradient-to-r from-blue-600 to-indigo-700 text-white mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                    <span class="text-4xl">📊</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">التقرير المالي للطلبات</h2>
                    <p class="text-blue-200 mt-1">
                        <span>{{ \Carbon\Carbon::parse($fromDate)->format('Y/m/d') }}</span>
                        <span class="mx-2">←</span>
                        <span>{{ \Carbon\Carbon::parse($toDate)->format('Y/m/d') }}</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="showFilters = !showFilters" class="btn bg-white/20 hover:bg-white/30 text-white">
                    <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <span x-text="showFilters ? 'إخفاء الفلاتر' : 'إظهار الفلاتر'"></span>
                </button>
                <a href="{{ route('manager-reports.orders.print', request()->query()) }}" target="_blank" 
                   class="btn bg-white text-blue-600 hover:bg-blue-50">
                    <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    طباعة التقرير
                </a>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div x-show="showFilters" x-transition class="panel mb-6">
        <form method="GET" action="{{ route('manager-reports.orders') }}">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- الفرع -->
                <div>
                    <label class="block font-semibold mb-2">🏢 الفرع</label>
                    <select name="branch_id" class="form-select w-full">
                        <option value="">جميع الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                                {{ $branch->branch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- من تاريخ -->
                <div>
                    <label class="block font-semibold mb-2">📅 من تاريخ</label>
                    <input type="date" name="from_date" value="{{ $fromDate }}" class="form-input w-full">
                </div>

                <!-- إلى تاريخ -->
                <div>
                    <label class="block font-semibold mb-2">📅 إلى تاريخ</label>
                    <input type="date" name="to_date" value="{{ $toDate }}" class="form-input w-full">
                </div>

                <!-- الحالة -->
                <div>
                    <label class="block font-semibold mb-2">📋 الحالة</label>
                    <select name="status" class="form-select w-full">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>معتمد</option>
                        <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>جاري التنفيذ</option>
                        <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="delivered" {{ $status == 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                        <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>

                <!-- نوع الخلطة -->
                <div>
                    <label class="block font-semibold mb-2">🧱 نوع الخلطة</label>
                    <select name="mix_type" class="form-select w-full">
                        <option value="">جميع الأنواع</option>
                        @foreach($mixes as $mix)
                            <option value="{{ $mix->id }}" {{ $mixType == $mix->id ? 'selected' : '' }}>
                                {{ $mix->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <!-- أزرار سريعة للتاريخ -->
                <div class="flex gap-2">
                    <a href="{{ route('manager-reports.orders', ['from_date' => now()->format('Y-m-d'), 'to_date' => now()->format('Y-m-d')]) }}" 
                       class="btn btn-sm btn-outline-primary">اليوم</a>
                    <a href="{{ route('manager-reports.orders', ['from_date' => now()->startOfWeek()->format('Y-m-d'), 'to_date' => now()->format('Y-m-d')]) }}" 
                       class="btn btn-sm btn-outline-primary">هذا الأسبوع</a>
                    <a href="{{ route('manager-reports.orders', ['from_date' => now()->startOfMonth()->format('Y-m-d'), 'to_date' => now()->format('Y-m-d')]) }}" 
                       class="btn btn-sm btn-outline-primary">هذا الشهر</a>
                    <a href="{{ route('manager-reports.orders', ['from_date' => now()->subMonth()->startOfMonth()->format('Y-m-d'), 'to_date' => now()->subMonth()->endOfMonth()->format('Y-m-d')]) }}" 
                       class="btn btn-sm btn-outline-secondary">الشهر السابق</a>
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ route('manager-reports.orders') }}" class="btn btn-outline-secondary">
                        إعادة تعيين
                    </a>
                    <button type="submit" class="btn btn-primary">
                        🔍 عرض التقرير
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- ملخص الإحصائيات الرئيسية -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <!-- إجمالي الطلبات -->
        <div class="panel">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm mb-1">إجمالي الطلبات</div>
                    <div class="text-3xl font-bold text-primary">{{ number_format($financialStats['total_orders']) }}</div>
                    <div class="text-xs text-gray-400 mt-1">
                        <span class="text-green-500">{{ $financialStats['completed_orders'] }} مكتمل</span>
                        <span class="mx-1">|</span>
                        <span class="text-yellow-500">{{ $financialStats['pending_orders'] }} معلق</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">📋</span>
                </div>
            </div>
        </div>

        <!-- إجمالي القيمة -->
        <div class="panel">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm mb-1">إجمالي القيمة</div>
                    <div class="text-3xl font-bold text-green-600">{{ number_format($financialStats['total_value'], 0) }}</div>
                    <div class="text-xs text-gray-400 mt-1">دينار عراقي</div>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">💰</span>
                </div>
            </div>
        </div>

        <!-- إجمالي الكميات -->
        <div class="panel">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm mb-1">إجمالي الكميات</div>
                    <div class="text-3xl font-bold text-purple-600">{{ number_format($financialStats['total_quantity'], 1) }}</div>
                    <div class="text-xs text-gray-400 mt-1">متر مكعب</div>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">📦</span>
                </div>
            </div>
        </div>

        <!-- نسبة الإنجاز -->
        <div class="panel">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm mb-1">نسبة الإنجاز</div>
                    <div class="text-3xl font-bold text-amber-600">{{ $financialStats['completion_rate'] }}%</div>
                    <div class="text-xs text-gray-400 mt-1">متوسط الطلب: {{ number_format($financialStats['average_order_value'], 0) }} د.ع</div>
                </div>
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">📈</span>
                </div>
            </div>
        </div>
    </div>

    <!-- التبويبات -->
    <div class="panel mb-6">
        <div class="flex border-b border-gray-200 dark:border-gray-700 mb-4 overflow-x-auto">
            <button @click="activeTab = 'overview'" 
                    :class="activeTab === 'overview' ? 'border-primary text-primary' : 'border-transparent text-gray-500'"
                    class="px-6 py-3 border-b-2 font-semibold whitespace-nowrap">
                📊 نظرة عامة
            </button>
            <button @click="activeTab = 'branches'" 
                    :class="activeTab === 'branches' ? 'border-primary text-primary' : 'border-transparent text-gray-500'"
                    class="px-6 py-3 border-b-2 font-semibold whitespace-nowrap">
                🏢 حسب الفرع
            </button>
            <button @click="activeTab = 'mixes'" 
                    :class="activeTab === 'mixes' ? 'border-primary text-primary' : 'border-transparent text-gray-500'"
                    class="px-6 py-3 border-b-2 font-semibold whitespace-nowrap">
                🧱 حسب الخلطة
            </button>
            <button @click="activeTab = 'details'" 
                    :class="activeTab === 'details' ? 'border-primary text-primary' : 'border-transparent text-gray-500'"
                    class="px-6 py-3 border-b-2 font-semibold whitespace-nowrap">
                📝 التفاصيل
            </button>
        </div>

        <!-- محتوى نظرة عامة -->
        <div x-show="activeTab === 'overview'" x-transition>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- الرسم البياني -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                    <h4 class="font-bold mb-4">📈 تطور الطلبات اليومي</h4>
                    <canvas id="dailyOrdersChart" height="200"></canvas>
                </div>

                <!-- توزيع الحالات -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                    <h4 class="font-bold mb-4">📊 توزيع الحالات</h4>
                    <div class="space-y-3">
                        @foreach($statusStats as $status => $stat)
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl">{{ $stat['icon'] }}</span>
                                    <span class="font-medium">{{ $stat['name'] }}</span>
                                </div>
                                <div class="text-left">
                                    <div class="font-bold" style="color: {{ $stat['color'] }}">{{ $stat['count'] }} طلب</div>
                                    <div class="text-xs text-gray-500">{{ number_format($stat['value'], 0) }} د.ع</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- محتوى حسب الفرع -->
        <div x-show="activeTab === 'branches'" x-transition>
            <div class="overflow-x-auto">
                <table class="table-striped w-full">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800">
                            <th class="text-center py-3 px-4">#</th>
                            <th class="text-center py-3 px-4">الفرع</th>
                            <th class="text-center py-3 px-4">عدد الطلبات</th>
                            <th class="text-center py-3 px-4">المكتملة</th>
                            <th class="text-center py-3 px-4">الكمية (م³)</th>
                            <th class="text-center py-3 px-4">القيمة (د.ع)</th>
                            <th class="text-center py-3 px-4">النسبة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branchStats as $index => $stat)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="text-center py-3 px-4">{{ $index + 1 }}</td>
                                <td class="text-center py-3 px-4 font-semibold">{{ $stat['branch_name'] }}</td>
                                <td class="text-center py-3 px-4">
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-bold">
                                        {{ $stat['count'] }}
                                    </span>
                                </td>
                                <td class="text-center py-3 px-4">
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-bold">
                                        {{ $stat['completed'] }}
                                    </span>
                                </td>
                                <td class="text-center py-3 px-4">{{ number_format($stat['quantity'], 1) }}</td>
                                <td class="text-center py-3 px-4 font-bold text-green-600">
                                    {{ number_format($stat['value'], 0) }}
                                </td>
                                <td class="text-center py-3 px-4">
                                    @php
                                        $percentage = $financialStats['total_value'] > 0 
                                            ? round(($stat['value'] / $financialStats['total_value']) * 100, 1) 
                                            : 0;
                                    @endphp
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-primary h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $percentage }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-200 dark:bg-gray-700 font-bold">
                            <td colspan="2" class="text-center py-3 px-4">الإجمالي</td>
                            <td class="text-center py-3 px-4">{{ $financialStats['total_orders'] }}</td>
                            <td class="text-center py-3 px-4">{{ $financialStats['completed_orders'] }}</td>
                            <td class="text-center py-3 px-4">{{ number_format($financialStats['total_quantity'], 1) }}</td>
                            <td class="text-center py-3 px-4 text-green-600">{{ number_format($financialStats['total_value'], 0) }}</td>
                            <td class="text-center py-3 px-4">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- محتوى حسب الخلطة -->
        <div x-show="activeTab === 'mixes'" x-transition>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($mixStats as $stat)
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl">🧱</span>
                            <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-bold">
                                {{ $stat['count'] }} طلب
                            </span>
                        </div>
                        <h4 class="font-bold text-lg mb-2">{{ $stat['mix_name'] }}</h4>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>الكمية:</span>
                            <span class="font-bold text-purple-600">{{ number_format($stat['quantity'], 1) }} م³</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500 mt-1">
                            <span>القيمة:</span>
                            <span class="font-bold text-green-600">{{ number_format($stat['value'], 0) }} د.ع</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- محتوى التفاصيل -->
        <div x-show="activeTab === 'details'" x-transition>
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-bold">📝 تفاصيل الطلبات ({{ $orders->count() }} طلب)</h4>
            </div>
            
            @if($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-striped w-full text-sm">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800">
                                <th class="text-center py-2 px-3">#</th>
                                <th class="text-center py-2 px-3">التاريخ</th>
                                <th class="text-center py-2 px-3">الفرع</th>
                                <th class="text-center py-2 px-3">العميل</th>
                                <th class="text-center py-2 px-3">الخلطة</th>
                                <th class="text-center py-2 px-3">الكمية</th>
                                <th class="text-center py-2 px-3">السعر</th>
                                <th class="text-center py-2 px-3">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders->take(50) as $index => $order)
                                <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="text-center py-2 px-3">{{ $index + 1 }}</td>
                                    <td class="text-center py-2 px-3">{{ \Carbon\Carbon::parse($order->created_at)->format('m/d') }}</td>
                                    <td class="text-center py-2 px-3">{{ $order->branch->branch_name ?? '-' }}</td>
                                    <td class="text-center py-2 px-3">{{ Str::limit($order->customer_name, 20) ?? '-' }}</td>
                                    <td class="text-center py-2 px-3">{{ $order->concreteMix->name ?? '-' }}</td>
                                    <td class="text-center py-2 px-3">{{ number_format($order->quantity, 1) }}</td>
                                    <td class="text-center py-2 px-3 font-bold text-green-600">
                                        {{ number_format($order->final_price ?: $order->initial_price, 0) }}
                                    </td>
                                    <td class="text-center py-2 px-3">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-700',
                                                'approved' => 'bg-blue-100 text-blue-700',
                                                'in_progress' => 'bg-purple-100 text-purple-700',
                                                'completed' => 'bg-green-100 text-green-700',
                                                'delivered' => 'bg-emerald-100 text-emerald-700',
                                                'cancelled' => 'bg-red-100 text-red-700',
                                            ];
                                            $statusNames = [
                                                'pending' => 'معلق',
                                                'approved' => 'معتمد',
                                                'in_progress' => 'جاري',
                                                'completed' => 'مكتمل',
                                                'delivered' => 'مسلم',
                                                'cancelled' => 'ملغي',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 rounded text-xs {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">
                                            {{ $statusNames[$order->status] ?? $order->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($orders->count() > 50)
                    <div class="text-center mt-4 text-gray-500">
                        عرض أول 50 طلب من أصل {{ $orders->count() }} - استخدم الطباعة لعرض الكل
                    </div>
                @endif
            @else
                <div class="text-center py-12 text-gray-500">
                    <span class="text-6xl">📋</span>
                    <p class="mt-4">لا توجد طلبات في الفترة المحددة</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // الرسم البياني اليومي
    const dailyCtx = document.getElementById('dailyOrdersChart');
    if (dailyCtx) {
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: @json($dailyChartData['labels']),
                datasets: [
                    {
                        label: 'عدد الطلبات',
                        data: @json($dailyChartData['counts']),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'القيمة (د.ع)',
                        data: @json($dailyChartData['values']),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        rtl: true,
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'عدد الطلبات'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'القيمة (د.ع)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    }
});
</script>
@endpush
