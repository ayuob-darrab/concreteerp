@extends('layouts.app')

@section('page-title', 'تقرير صيانات السيارات')

@section('content')
    <div x-data="{ showFilters: true }">
        <!-- رأس الصفحة -->
        <div class="panel bg-gradient-to-r from-purple-500 to-purple-600 text-white mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="text-5xl">📊</div>
                    <div>
                        <h2 class="text-xl font-bold">تقرير صيانات السيارات</h2>
                        <p class="text-purple-200">عرض وتحليل جميع صيانات سيارات الفرع</p>
                    </div>
                </div>
                <button @click="showFilters = !showFilters" class="btn bg-white/20 hover:bg-white/30 text-white">
                    <span x-text="showFilters ? 'إخفاء الفلاتر' : 'إظهار الفلاتر'"></span>
                </button>
            </div>
        </div>

        <!-- بطاقات الإحصائيات -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="panel">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold text-primary">{{ $stats['total_count'] }}</div>
                        <div class="text-gray-500">عدد الصيانات</div>
                    </div>
                    <div class="text-4xl">🔧</div>
                </div>
            </div>
            <div class="panel">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold text-red-500">{{ number_format($stats['total_cost'], 0) }}</div>
                        <div class="text-gray-500">إجمالي التكاليف (د.ع)</div>
                    </div>
                    <div class="text-4xl">💰</div>
                </div>
            </div>
            <div class="panel">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-lg font-bold text-green-500">
                            {{ $stats['total_count'] > 0 ? number_format($stats['total_cost'] / $stats['total_count'], 0) : 0 }}
                        </div>
                        <div class="text-gray-500">متوسط تكلفة الصيانة (د.ع)</div>
                    </div>
                    <div class="text-4xl">📈</div>
                </div>
            </div>
        </div>

        <!-- الفلاتر -->
        <div x-show="showFilters" x-transition class="panel mb-6">
            <form method="GET" action="{{ route('car-maintenance.report') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- من تاريخ -->
                    <div>
                        <label class="block font-medium mb-2">من تاريخ</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-input w-full">
                    </div>

                    <!-- إلى تاريخ -->
                    <div>
                        <label class="block font-medium mb-2">إلى تاريخ</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-input w-full">
                    </div>

                    <!-- نوع الصيانة -->
                    <div>
                        <label class="block font-medium mb-2">نوع الصيانة</label>
                        <select name="maintenance_type" class="form-select w-full">
                            <option value="">الكل</option>
                            @foreach($maintenanceTypes as $key => $type)
                                <option value="{{ $key }}" {{ request('maintenance_type') == $key ? 'selected' : '' }}>
                                    {{ $type['icon'] }} {{ $type['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- السيارة -->
                    <div>
                        <label class="block font-medium mb-2">السيارة</label>
                        <select name="car_id" class="form-select w-full">
                            <option value="">الكل</option>
                            @foreach($cars as $car)
                                <option value="{{ $car->id }}" {{ request('car_id') == $car->id ? 'selected' : '' }}>
                                    {{ $car->car_name ?? $car->car_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <a href="{{ route('car-maintenance.report') }}" class="btn btn-outline-secondary">
                        إعادة تعيين
                    </a>
                    <button type="submit" class="btn btn-primary">
                        🔍 بحث
                    </button>
                </div>
            </form>
        </div>

        <!-- توزيع الصيانات حسب النوع -->
        @if($stats['by_type']->count() > 0)
            <div class="panel mb-6">
                <h5 class="font-bold mb-4">📊 توزيع الصيانات حسب النوع</h5>
                <div class="flex flex-wrap gap-4">
                    @foreach($stats['by_type'] as $type => $count)
                        @php
                            $typeInfo = $maintenanceTypes[$type] ?? ['name' => $type, 'icon' => '🔧', 'color' => '#6B7280'];
                        @endphp
                        <div class="flex items-center gap-2 px-4 py-2 rounded-lg" 
                            style="background-color: {{ $typeInfo['color'] }}20;">
                            <span>{{ $typeInfo['icon'] }}</span>
                            <span class="font-medium">{{ $typeInfo['name'] }}:</span>
                            <span class="font-bold" style="color: {{ $typeInfo['color'] }};">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- جدول الصيانات -->
        <div class="panel">
            <div class="flex items-center justify-between mb-4">
                <h5 class="font-bold text-lg">📋 سجل الصيانات</h5>
                <a href="{{ route('car-maintenance.index') }}" class="btn btn-outline-primary">
                    العودة للسيارات
                </a>
            </div>

            @if($maintenances->count() > 0)
                <div class="table-responsive">
                    <table class="table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">التاريخ</th>
                                <th class="text-center">السيارة</th>
                                <th class="text-center">النوع</th>
                                <th class="text-center">العنوان</th>
                                <th class="text-center">التكلفة</th>
                                <th class="text-center">الورشة</th>
                                <th class="text-center">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($maintenances as $index => $maintenance)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($maintenance->maintenance_date)->format('Y/m/d') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('car-maintenance.car-details', $maintenance->car_id) }}" 
                                            class="text-primary hover:underline font-medium">
                                            {{ $maintenance->car->car_name ?? $maintenance->car->car_number }}
                                        </a>
                                        <div class="text-xs text-gray-500">{{ $maintenance->car->carType->name ?? '' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge px-2 py-1 rounded" style="background-color: {{ $maintenance->type_color }}20; color: {{ $maintenance->type_color }};">
                                            {{ $maintenance->type_icon }} {{ $maintenance->type_name }}
                                        </span>
                                    </td>
                                    <td class="text-center font-medium">{{ Str::limit($maintenance->title, 30) }}</td>
                                    <td class="text-center">
                                        <span class="font-bold text-red-600">{{ number_format($maintenance->cost, 2) }}</span>
                                        <span class="text-xs text-gray-500">د.ع</span>
                                    </td>
                                    <td class="text-center">{{ $maintenance->workshop_name ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge px-2 py-1 rounded" style="background-color: {{ $maintenance->status_color }}20; color: {{ $maintenance->status_color }};">
                                            {{ $maintenance->status_icon }} {{ $maintenance->status_name }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 dark:bg-gray-800 font-bold">
                                <td colspan="5" class="text-center">الإجمالي</td>
                                <td class="text-center text-red-600">
                                    {{ number_format($maintenances->where('status', 'completed')->sum('cost'), 2) }} د.ع
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <div class="text-4xl mb-2">📋</div>
                    <p>لا توجد صيانات تطابق معايير البحث</p>
                </div>
            @endif
        </div>
    </div>
@endsection
