@extends('layouts.app')

@section('page-title', 'التقرير المالي للصيانة')

@section('content')
<div x-data="{ showFilters: true, activeTab: 'overview' }">
    
    <!-- رأس الصفحة -->
    <div class="panel bg-gradient-to-r from-orange-500 to-red-600 text-white mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                    <span class="text-4xl">🔧</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">التقرير المالي لصيانة السيارات</h2>
                    <p class="text-orange-200 mt-1">
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
                <a href="{{ route('manager-reports.maintenance.print', request()->query()) }}" target="_blank" 
                   class="btn bg-white text-orange-600 hover:bg-orange-50">
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
        <form method="GET" action="{{ route('manager-reports.maintenance') }}">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- الفرع -->
                <div>
                    <label class="block font-semibold mb-2">🏢 الفرع</label>
                    <select name="branch_id" class="form-select w-full" onchange="this.form.submit()">
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

                <!-- نوع الصيانة -->
                <div>
                    <label class="block font-semibold mb-2">🔧 نوع الصيانة</label>
                    <select name="maintenance_type" class="form-select w-full">
                        <option value="">جميع الأنواع</option>
                        @foreach($maintenanceTypes as $key => $type)
                            <option value="{{ $key }}" {{ $maintenanceType == $key ? 'selected' : '' }}>
                                {{ $type['icon'] }} {{ $type['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- السيارة -->
                <div>
                    <label class="block font-semibold mb-2">🚗 السيارة</label>
                    <select name="car_id" class="form-select w-full">
                        <option value="">جميع السيارات</option>
                        @foreach($cars as $car)
                            <option value="{{ $car->id }}" {{ $carId == $car->id ? 'selected' : '' }}>
                                {{ $car->car_name ?? $car->car_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <!-- أزرار سريعة للتاريخ -->
                <div class="flex gap-2">
                    <a href="{{ route('manager-reports.maintenance', ['from_date' => now()->startOfMonth()->format('Y-m-d'), 'to_date' => now()->format('Y-m-d')]) }}" 
                       class="btn btn-sm btn-outline-primary">هذا الشهر</a>
                    <a href="{{ route('manager-reports.maintenance', ['from_date' => now()->subMonths(3)->startOfMonth()->format('Y-m-d'), 'to_date' => now()->format('Y-m-d')]) }}" 
                       class="btn btn-sm btn-outline-primary">آخر 3 أشهر</a>
                    <a href="{{ route('manager-reports.maintenance', ['from_date' => now()->startOfYear()->format('Y-m-d'), 'to_date' => now()->format('Y-m-d')]) }}" 
                       class="btn btn-sm btn-outline-secondary">هذه السنة</a>
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ route('manager-reports.maintenance') }}" class="btn btn-outline-secondary">
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
        <!-- إجمالي الصيانات -->
        <div class="panel">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm mb-1">عدد الصيانات</div>
                    <div class="text-3xl font-bold text-primary">{{ number_format($financialStats['total_maintenances']) }}</div>
                    <div class="text-xs text-gray-400 mt-1">
                        <span class="text-green-500">{{ $financialStats['completed_maintenances'] }} مكتملة</span>
                        <span class="mx-1">|</span>
                        <span class="text-orange-500">{{ $financialStats['in_progress_maintenances'] }} جارية</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">🔧</span>
                </div>
            </div>
        </div>

        <!-- إجمالي التكاليف -->
        <div class="panel">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm mb-1">إجمالي التكاليف</div>
                    <div class="text-3xl font-bold text-red-600">{{ number_format($financialStats['total_cost'], 0) }}</div>
                    <div class="text-xs text-gray-400 mt-1">دينار عراقي</div>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">💰</span>
                </div>
            </div>
        </div>

        <!-- تكلفة القطع -->
        <div class="panel">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm mb-1">تكلفة القطع</div>
                    <div class="text-3xl font-bold text-purple-600">{{ number_format($financialStats['parts_cost'], 0) }}</div>
                    <div class="text-xs text-gray-400 mt-1">
                        {{ $financialStats['total_cost'] > 0 ? round(($financialStats['parts_cost'] / $financialStats['total_cost']) * 100) : 0 }}% من الإجمالي
                    </div>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">⚙️</span>
                </div>
            </div>
        </div>

        <!-- متوسط التكلفة -->
        <div class="panel">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm mb-1">متوسط التكلفة</div>
                    <div class="text-3xl font-bold text-amber-600">{{ number_format($financialStats['average_cost'], 0) }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $financialStats['unique_cars'] }} سيارة مختلفة</div>
                </div>
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">📊</span>
                </div>
            </div>
        </div>
    </div>

    <!-- تفصيل التكاليف -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="panel bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30">
            <div class="flex items-center gap-3 mb-2">
                <span class="text-2xl">⚙️</span>
                <span class="font-bold text-lg">تكلفة القطع والمواد</span>
            </div>
            <div class="text-3xl font-bold text-blue-600">{{ number_format($financialStats['parts_cost'], 2) }} <span class="text-sm">د.ع</span></div>
        </div>
        <div class="panel bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30">
            <div class="flex items-center gap-3 mb-2">
                <span class="text-2xl">👷</span>
                <span class="font-bold text-lg">تكلفة العمالة</span>
            </div>
            <div class="text-3xl font-bold text-green-600">{{ number_format($financialStats['labor_cost'], 2) }} <span class="text-sm">د.ع</span></div>
        </div>
        <div class="panel bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/30">
            <div class="flex items-center gap-3 mb-2">
                <span class="text-2xl">💵</span>
                <span class="font-bold text-lg">الإجمالي الكلي</span>
            </div>
            <div class="text-3xl font-bold text-red-600">{{ number_format($financialStats['total_cost'], 2) }} <span class="text-sm">د.ع</span></div>
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
            <button @click="activeTab = 'types'" 
                    :class="activeTab === 'types' ? 'border-primary text-primary' : 'border-transparent text-gray-500'"
                    class="px-6 py-3 border-b-2 font-semibold whitespace-nowrap">
                🔧 حسب النوع
            </button>
            <button @click="activeTab = 'cars'" 
                    :class="activeTab === 'cars' ? 'border-primary text-primary' : 'border-transparent text-gray-500'"
                    class="px-6 py-3 border-b-2 font-semibold whitespace-nowrap">
                🚗 حسب السيارة
            </button>
            <button @click="activeTab = 'branches'" 
                    :class="activeTab === 'branches' ? 'border-primary text-primary' : 'border-transparent text-gray-500'"
                    class="px-6 py-3 border-b-2 font-semibold whitespace-nowrap">
                🏢 حسب الفرع
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
                <!-- الرسم البياني الشهري -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                    <h4 class="font-bold mb-4">📈 تطور تكاليف الصيانة (آخر 6 أشهر)</h4>
                    <canvas id="monthlyMaintenanceChart" height="200"></canvas>
                </div>

                <!-- توزيع أنواع الصيانة -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                    <h4 class="font-bold mb-4">📊 توزيع أنواع الصيانة</h4>
                    <div class="space-y-3">
                        @foreach($typeStats as $type => $stat)
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl">{{ $stat['icon'] }}</span>
                                    <span class="font-medium">{{ $stat['name'] }}</span>
                                </div>
                                <div class="text-left">
                                    <div class="font-bold" style="color: {{ $stat['color'] }}">{{ $stat['count'] }} صيانة</div>
                                    <div class="text-xs text-gray-500">{{ number_format($stat['total_cost'], 0) }} د.ع</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- محتوى حسب النوع -->
        <div x-show="activeTab === 'types'" x-transition>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($typeStats as $type => $stat)
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border-2 border-transparent hover:border-primary/30 transition-all"
                         style="background: linear-gradient(135deg, {{ $stat['color'] }}10, {{ $stat['color'] }}05);">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-3xl">{{ $stat['icon'] }}</span>
                            <span class="text-2xl font-bold" style="color: {{ $stat['color'] }}">{{ $stat['count'] }}</span>
                        </div>
                        <h4 class="font-bold text-lg mb-2">{{ $stat['name'] }}</h4>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">إجمالي التكلفة</span>
                            <span class="font-bold text-red-600">{{ number_format($stat['total_cost'], 0) }} د.ع</span>
                        </div>
                        @if($stat['count'] > 0)
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-gray-500 text-sm">متوسط التكلفة</span>
                                <span class="font-semibold text-gray-700">{{ number_format($stat['total_cost'] / $stat['count'], 0) }} د.ع</span>
                            </div>
                        @endif
                        <div class="mt-3">
                            @php
                                $percentage = $financialStats['total_cost'] > 0 
                                    ? round(($stat['total_cost'] / $financialStats['total_cost']) * 100, 1) 
                                    : 0;
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full" style="width: {{ $percentage }}%; background-color: {{ $stat['color'] }}"></div>
                            </div>
                            <span class="text-xs text-gray-500 mt-1">{{ $percentage }}% من إجمالي التكاليف</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- محتوى حسب السيارة -->
        <div x-show="activeTab === 'cars'" x-transition>
            <h4 class="font-bold mb-4">🚗 أعلى 10 سيارات تكلفة في الصيانة</h4>
            <div class="overflow-x-auto">
                <table class="table-striped w-full">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800">
                            <th class="text-center py-3 px-4">#</th>
                            <th class="text-center py-3 px-4">السيارة</th>
                            <th class="text-center py-3 px-4">النوع</th>
                            <th class="text-center py-3 px-4">رقم اللوحة</th>
                            <th class="text-center py-3 px-4">عدد الصيانات</th>
                            <th class="text-center py-3 px-4">إجمالي التكلفة</th>
                            <th class="text-center py-3 px-4">آخر صيانة</th>
                            <th class="text-center py-3 px-4">النسبة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carStats as $index => $stat)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="text-center py-3 px-4">
                                    @if($index == 0)
                                        <span class="text-2xl">🥇</span>
                                    @elseif($index == 1)
                                        <span class="text-2xl">🥈</span>
                                    @elseif($index == 2)
                                        <span class="text-2xl">🥉</span>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </td>
                                <td class="text-center py-3 px-4 font-semibold">{{ $stat['car_name'] }}</td>
                                <td class="text-center py-3 px-4">{{ $stat['car_type'] }}</td>
                                <td class="text-center py-3 px-4">
                                    <span class="bg-gray-100 px-2 py-1 rounded font-mono text-sm">{{ $stat['plate_number'] }}</span>
                                </td>
                                <td class="text-center py-3 px-4">
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-bold">
                                        {{ $stat['count'] }}
                                    </span>
                                </td>
                                <td class="text-center py-3 px-4 font-bold text-red-600">
                                    {{ number_format($stat['total_cost'], 0) }} د.ع
                                </td>
                                <td class="text-center py-3 px-4 text-gray-500">
                                    {{ $stat['last_maintenance'] ? \Carbon\Carbon::parse($stat['last_maintenance'])->format('Y/m/d') : '-' }}
                                </td>
                                <td class="text-center py-3 px-4">
                                    @php
                                        $percentage = $financialStats['total_cost'] > 0 
                                            ? round(($stat['total_cost'] / $financialStats['total_cost']) * 100, 1) 
                                            : 0;
                                    @endphp
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-red-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $percentage }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
                            <th class="text-center py-3 px-4">عدد الصيانات</th>
                            <th class="text-center py-3 px-4">المكتملة</th>
                            <th class="text-center py-3 px-4">عدد السيارات</th>
                            <th class="text-center py-3 px-4">تكلفة القطع</th>
                            <th class="text-center py-3 px-4">تكلفة العمالة</th>
                            <th class="text-center py-3 px-4">الإجمالي</th>
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
                                <td class="text-center py-3 px-4">{{ $stat['unique_cars'] }} سيارة</td>
                                <td class="text-center py-3 px-4 text-purple-600">{{ number_format($stat['parts_cost'], 0) }}</td>
                                <td class="text-center py-3 px-4 text-green-600">{{ number_format($stat['labor_cost'], 0) }}</td>
                                <td class="text-center py-3 px-4 font-bold text-red-600">
                                    {{ number_format($stat['total_cost'], 0) }} د.ع
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-200 dark:bg-gray-700 font-bold">
                            <td colspan="2" class="text-center py-3 px-4">الإجمالي</td>
                            <td class="text-center py-3 px-4">{{ $financialStats['total_maintenances'] }}</td>
                            <td class="text-center py-3 px-4">{{ $financialStats['completed_maintenances'] }}</td>
                            <td class="text-center py-3 px-4">{{ $financialStats['unique_cars'] }}</td>
                            <td class="text-center py-3 px-4 text-purple-600">{{ number_format($financialStats['parts_cost'], 0) }}</td>
                            <td class="text-center py-3 px-4 text-green-600">{{ number_format($financialStats['labor_cost'], 0) }}</td>
                            <td class="text-center py-3 px-4 text-red-600">{{ number_format($financialStats['total_cost'], 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- محتوى التفاصيل -->
        <div x-show="activeTab === 'details'" x-transition>
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-bold">📝 تفاصيل الصيانات ({{ $maintenances->count() }} سجل)</h4>
            </div>
            
            @if($maintenances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-striped w-full text-sm">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800">
                                <th class="text-center py-2 px-3">#</th>
                                <th class="text-center py-2 px-3">التاريخ</th>
                                <th class="text-center py-2 px-3">الفرع</th>
                                <th class="text-center py-2 px-3">السيارة</th>
                                <th class="text-center py-2 px-3">النوع</th>
                                <th class="text-center py-2 px-3">العنوان</th>
                                <th class="text-center py-2 px-3">القطع</th>
                                <th class="text-center py-2 px-3">العمالة</th>
                                <th class="text-center py-2 px-3">الإجمالي</th>
                                <th class="text-center py-2 px-3">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($maintenances->take(50) as $index => $maintenance)
                                @php
                                    $typeInfo = $maintenanceTypes[$maintenance->maintenance_type] ?? ['name' => '-', 'icon' => '🔧', 'color' => '#6B7280'];
                                @endphp
                                <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="text-center py-2 px-3">{{ $index + 1 }}</td>
                                    <td class="text-center py-2 px-3">{{ \Carbon\Carbon::parse($maintenance->maintenance_date)->format('m/d') }}</td>
                                    <td class="text-center py-2 px-3">{{ $maintenance->branch->branch_name ?? '-' }}</td>
                                    <td class="text-center py-2 px-3 font-semibold">
                                        {{ $maintenance->car->car_name ?? $maintenance->car->car_number ?? '-' }}
                                    </td>
                                    <td class="text-center py-2 px-3">
                                        <span class="text-lg" title="{{ $typeInfo['name'] }}">{{ $typeInfo['icon'] }}</span>
                                    </td>
                                    <td class="text-center py-2 px-3">{{ Str::limit($maintenance->title, 25) }}</td>
                                    <td class="text-center py-2 px-3 text-purple-600">{{ number_format($maintenance->parts_cost, 0) }}</td>
                                    <td class="text-center py-2 px-3 text-green-600">{{ number_format($maintenance->labor_cost, 0) }}</td>
                                    <td class="text-center py-2 px-3 font-bold text-red-600">{{ number_format($maintenance->total_cost, 0) }}</td>
                                    <td class="text-center py-2 px-3">
                                        @php
                                            $statusColors = [
                                                'scheduled' => 'bg-yellow-100 text-yellow-700',
                                                'in_progress' => 'bg-blue-100 text-blue-700',
                                                'completed' => 'bg-green-100 text-green-700',
                                                'cancelled' => 'bg-red-100 text-red-700',
                                            ];
                                            $statusNames = [
                                                'scheduled' => 'مجدولة',
                                                'in_progress' => 'جارية',
                                                'completed' => 'مكتملة',
                                                'cancelled' => 'ملغية',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 rounded text-xs {{ $statusColors[$maintenance->status] ?? 'bg-gray-100' }}">
                                            {{ $statusNames[$maintenance->status] ?? $maintenance->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($maintenances->count() > 50)
                    <div class="text-center mt-4 text-gray-500">
                        عرض أول 50 سجل من أصل {{ $maintenances->count() }} - استخدم الطباعة لعرض الكل
                    </div>
                @endif
            @else
                <div class="text-center py-12 text-gray-500">
                    <span class="text-6xl">🔧</span>
                    <p class="mt-4">لا توجد صيانات في الفترة المحددة</p>
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
    // الرسم البياني الشهري
    const monthlyCtx = document.getElementById('monthlyMaintenanceChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: @json($monthlyChartData['labels']),
                datasets: [
                    {
                        label: 'تكلفة القطع',
                        data: @json($monthlyChartData['parts_costs']),
                        backgroundColor: 'rgba(139, 92, 246, 0.7)',
                        borderColor: '#8B5CF6',
                        borderWidth: 1
                    },
                    {
                        label: 'تكلفة العمالة',
                        data: @json($monthlyChartData['labor_costs']),
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: '#22C55E',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        rtl: true,
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'التكلفة (د.ع)'
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
