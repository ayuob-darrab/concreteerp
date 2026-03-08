@extends('layouts.app')

@section('page-title', 'صيانة السيارات')

@section('content')
    {{-- تنسيق الثيم الفاتح والداكن لصفحة الصيانة --}}
    <style>
        .car-maintenance-page .panel { color: inherit; }
        body:not(.dark) .car-maintenance-page .panel { color: #1f2937; }
        body:not(.dark) .car-maintenance-page .panel h4,
        body:not(.dark) .car-maintenance-page .panel h5,
        body:not(.dark) .car-maintenance-page .panel h6 { color: #1f2937 !important; }
        body:not(.dark) .car-maintenance-page .panel .text-gray-500,
        body:not(.dark) .car-maintenance-page .panel .text-gray-700 { color: #4b5563 !important; }
        body:not(.dark) .car-maintenance-page .stat-card { color: #fff !important; }
        body:not(.dark) .car-maintenance-page .stat-card * { color: inherit !important; }
        body:not(.dark) .car-maintenance-page .stat-card-blue { background: linear-gradient(to right, #3b82f6, #2563eb) !important; }
        body:not(.dark) .car-maintenance-page .stat-card-green { background: linear-gradient(to right, #22c55e, #16a34a) !important; }
        body:not(.dark) .car-maintenance-page .stat-card-yellow { background: linear-gradient(to right, #eab308, #ca8a04) !important; }
        body:not(.dark) .car-maintenance-page .stat-card-orange { background: linear-gradient(to right, #f97316, #ea580c) !important; }
        body:not(.dark) .car-maintenance-page .stat-card-purple { background: linear-gradient(to right, #a855f7, #9333ea) !important; }
        body:not(.dark) .car-maintenance-page .stat-card-red { background: linear-gradient(to right, #ef4444, #dc2626) !important; }
        body.dark .car-maintenance-page .panel { background-color: #1f2937 !important; border: 1px solid #374151; }
        body.dark .car-maintenance-page .panel h4,
        body.dark .car-maintenance-page .panel h5,
        body.dark .car-maintenance-page .panel h6,
        body.dark .car-maintenance-page .panel .font-bold:not(.text-primary):not(.text-red-600):not(.text-orange-600) { color: #e5e7eb !important; }
        body.dark .car-maintenance-page .panel .text-gray-500,
        body.dark .car-maintenance-page .panel .text-gray-700 { color: #9ca3af !important; }
        body.dark .car-maintenance-page .stat-card-blue { background: linear-gradient(to right, #1d4ed8, #1e40af) !important; }
        body.dark .car-maintenance-page .stat-card-green { background: linear-gradient(to right, #15803d, #166534) !important; }
        body.dark .car-maintenance-page .stat-card-yellow { background: linear-gradient(to right, #a16207, #854d0e) !important; }
        body.dark .car-maintenance-page .stat-card-orange { background: linear-gradient(to right, #c2410c, #9a3412) !important; }
        body.dark .car-maintenance-page .stat-card-purple { background: linear-gradient(to right, #6b21a8, #581c87) !important; }
        body.dark .car-maintenance-page .stat-card-red { background: linear-gradient(to right, #b91c1c, #991b1b) !important; }
    </style>
    <div class="car-maintenance-page" x-data="carMaintenancePage()">
        <!-- بطاقات الإحصائيات -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="panel stat-card stat-card-blue bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ $stats['total_cars'] }}</div>
                    <div class="text-blue-100 text-sm">إجمالي السيارات</div>
                </div>
            </div>
            <div class="panel stat-card stat-card-green bg-gradient-to-r from-green-500 to-green-600 text-white">
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ $stats['active_cars'] - ($stats['in_maintenance_cars'] ?? 0) }}</div>
                    <div class="text-green-100 text-sm">متاحة</div>
                </div>
            </div>
            <div class="panel stat-card stat-card-yellow bg-gradient-to-r from-yellow-500 to-yellow-600 text-white">
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ $stats['in_maintenance_cars'] ?? 0 }}</div>
                    <div class="text-yellow-100 text-sm">في الصيانة</div>
                </div>
            </div>
            <div class="panel stat-card stat-card-orange bg-gradient-to-r from-orange-500 to-orange-600 text-white">
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ $stats['total_maintenances'] }}</div>
                    <div class="text-orange-100 text-sm">إجمالي الصيانات</div>
                </div>
            </div>
            <div class="panel stat-card stat-card-purple bg-gradient-to-r from-purple-500 to-purple-600 text-white">
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ $stats['in_progress_maintenances'] ?? 0 }}</div>
                    <div class="text-purple-100 text-sm">قيد التنفيذ</div>
                </div>
            </div>
            <div class="panel stat-card stat-card-red bg-gradient-to-r from-red-500 to-red-600 text-white">
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ number_format($stats['total_cost'], 0) }}</div>
                    <div class="text-red-100 text-sm">التكاليف (د.ع)</div>
                </div>
            </div>
        </div>

        <!-- شريط التحكم -->
        <div class="panel mb-6 bg-white dark:bg-gray-800 dark:border-gray-700">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h5 class="text-lg font-bold flex items-center gap-2 text-gray-900 dark:text-white">
                    🚗 سيارات الفرع - إدارة الصيانة
                </h5>
                <div class="flex items-center gap-3">
                    <a href="{{ route('car-maintenance.report') }}" class="btn btn-info btn-sm flex items-center gap-2">
                        📊 <span>تقرير الصيانات</span>
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success flex items-center gap-2 mb-4">
                ✅ <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger flex items-center gap-2 mb-4">
                ❌ <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($cars->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- القائمة الجانبية: اختيار السيارة -->
                <div class="lg:col-span-1">
                    <div class="panel sticky top-4 bg-white dark:bg-gray-800 dark:border-gray-700">
                        <h6 class="font-bold text-gray-800 dark:text-gray-300 mb-1">عرض السيارات</h6>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">اختر سيارة لعرض تفاصيلها وصياناتها</p>
                        <input type="text" x-model="search" class="form-input w-full mb-3"
                            placeholder="🔍 بحث بالاسم أو الرقم أو النوع...">
                        <div class="space-y-2 max-h-[500px] overflow-y-auto ltr:pr-2 rtl:pl-2">
                            @foreach ($cars as $index => $car)
                                <button type="button"
                                    x-show="search === '' ||
                                        '{{ $car->car_name ?? '' }}'.toLowerCase().includes(search.toLowerCase()) ||
                                        '{{ $car->car_number }}'.toLowerCase().includes(search.toLowerCase()) ||
                                        '{{ $car->carType->name ?? '' }}'.toLowerCase().includes(search.toLowerCase())"
                                    @click="selectedCar = {{ $index }}"
                                    class="w-full text-right p-3 rounded-lg border-2 transition-all duration-200 hover:shadow-md"
                                    :class="selectedCar === {{ $index }}
                                        ? 'border-primary bg-primary/10 shadow-md'
                                        : 'border-gray-200 dark:border-gray-600 hover:border-primary/50'">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="font-bold text-sm text-gray-800 dark:text-gray-200">{{ $car->car_name ?? 'بدون اسم' }}</div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ $car->car_number }} · {{ $car->carType->name ?? '' }}</div>
                                        </div>
                                        @php $opStatus = $car->operational_status ?? 'available'; @endphp
                                        @if ($opStatus === 'in_maintenance')
                                            <span class="w-3 h-3 rounded-full bg-yellow-500 shrink-0"></span>
                                        @elseif ($car->is_active)
                                            <span class="w-3 h-3 rounded-full bg-green-500 shrink-0"></span>
                                        @else
                                            <span class="w-3 h-3 rounded-full bg-red-500 shrink-0"></span>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- المحتوى الرئيسي: تفاصيل السيارة المختارة -->
                <div class="lg:col-span-3">
                    @foreach ($cars as $index => $car)
                        <div x-show="selectedCar === {{ $index }}" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100">

                            <!-- بطاقة معلومات السيارة -->
                            <div class="panel mb-6 bg-white dark:bg-gray-800 dark:border-gray-700">
                                <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                                    <div>
                                        <h4 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                            🚗 {{ $car->car_name ?? 'بدون اسم' }}
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ $car->carType->name ?? '' }} · {{ $car->car_model ?? '' }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @php $opStatus = $car->operational_status ?? 'available'; @endphp
                                        @if ($opStatus === 'in_maintenance')
                                            <span class="badge bg-yellow-500/20 text-yellow-600 px-3 py-1 rounded-full text-sm">🔧 في الصيانة</span>
                                        @elseif ($car->is_active)
                                            <span class="badge bg-success/20 text-success px-3 py-1 rounded-full text-sm">✅ متاحة</span>
                                        @else
                                            <span class="badge bg-danger/20 text-danger px-3 py-1 rounded-full text-sm">❌ غير نشطة</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- تفاصيل السيارة في شبكة -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">رقم السيارة</div>
                                        <div class="font-bold text-primary">{{ $car->car_number }}</div>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">الموديل</div>
                                        <div class="font-bold text-gray-800 dark:text-gray-200">{{ $car->car_model ?? '-' }}</div>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">السائق</div>
                                        <div class="font-bold text-gray-800 dark:text-gray-200">{{ $car->driver->fullname ?? $car->driver_name ?: 'لا يوجد' }}</div>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">سعة الخباطة</div>
                                        <div class="font-bold text-gray-800 dark:text-gray-200">{{ $car->mixer_capacity ? $car->mixer_capacity . ' م³' : '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- إحصائيات الصيانة + الإجراءات: ترتيب ثابت (عدد الصيانات ← إجمالي التكاليف ← التفاصيل ← صيانة جديدة) -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                <div class="panel text-center bg-white dark:bg-gray-800 dark:border-gray-700">
                                    <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $car->maintenance_count ?? 0 }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">عدد الصيانات</div>
                                </div>
                                <div class="panel text-center bg-white dark:bg-gray-800 dark:border-gray-700">
                                    <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ number_format($car->total_maintenance_cost ?? 0, 0) }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">إجمالي التكاليف (د.ع)</div>
                                </div>
                                <div class="panel flex items-center justify-center bg-white dark:bg-gray-800 dark:border-gray-700">
                                    <a href="{{ route('car-maintenance.car-details', $car->id) }}"
                                        class="btn btn-info flex items-center gap-2 w-full justify-center">
                                        📋 التفاصيل
                                    </a>
                                </div>
                                <div class="panel flex items-center justify-center bg-white dark:bg-gray-800 dark:border-gray-700">
                                    @if (($car->operational_status ?? 'available') === 'in_maintenance')
                                        <a href="{{ route('car-maintenance.car-details', $car->id) }}"
                                            class="btn btn-success flex items-center gap-2 w-full justify-center">
                                            ✅ إكمال الصيانة
                                        </a>
                                    @else
                                        <a href="{{ route('car-maintenance.create', $car->id) }}"
                                            class="btn btn-warning flex items-center gap-2 w-full justify-center">
                                            🔧 صيانة جديدة
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- آخر الصيانات لهذه السيارة -->
                            @php
                                $recentMaintenances = \App\Models\CarMaintenance::where('car_id', $car->id)
                                    ->orderBy('created_at', 'desc')
                                    ->take(5)
                                    ->get();
                            @endphp
                            @if ($recentMaintenances->count() > 0)
                                <div class="panel bg-white dark:bg-gray-800 dark:border-gray-700">
                                    <h6 class="font-bold text-gray-800 dark:text-gray-300 mb-4 flex items-center gap-2">
                                        📜 آخر الصيانات
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">التاريخ</th>
                                                    <th class="text-center">نوع الصيانة</th>
                                                    <th class="text-center">الوصف</th>
                                                    <th class="text-center">التكلفة</th>
                                                    <th class="text-center">الحالة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($recentMaintenances as $m)
                                                    <tr>
                                                        <td class="text-center text-sm text-gray-800 dark:text-gray-200">{{ $m->created_at->format('Y-m-d') }}</td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info/20 text-info px-2 py-1 rounded text-xs">
                                                                {{ $m->maintenance_type ?? '-' }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center text-sm text-gray-800 dark:text-gray-200">{{ Str::limit($m->description ?? '-', 40) }}</td>
                                                        <td class="text-center font-bold text-red-600 text-sm">{{ number_format($m->total_cost ?? 0, 0) }} د.ع</td>
                                                        <td class="text-center">
                                                            @if ($m->status === 'completed')
                                                                <span class="badge bg-success/20 text-success px-2 py-1 rounded-full text-xs">مكتملة</span>
                                                            @elseif ($m->status === 'in_progress')
                                                                <span class="badge bg-warning/20 text-warning px-2 py-1 rounded-full text-xs">قيد التنفيذ</span>
                                                            @elseif ($m->status === 'scheduled')
                                                                <span class="badge bg-primary/20 text-primary px-2 py-1 rounded-full text-xs">مجدولة</span>
                                                            @else
                                                                <span class="badge bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 px-2 py-1 rounded-full text-xs">{{ $m->status }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ($car->maintenance_count > 5)
                                        <div class="text-center mt-3">
                                            <a href="{{ route('car-maintenance.car-details', $car->id) }}"
                                                class="text-primary hover:underline text-sm">
                                                عرض كل الصيانات ({{ $car->maintenance_count }}) ←
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="panel text-center py-8 bg-white dark:bg-gray-800 dark:border-gray-700">
                                    <div class="text-4xl mb-3">🔧</div>
                                    <p class="text-gray-600 dark:text-gray-400 mb-3">لا توجد صيانات مسجلة لهذه السيارة</p>
                                    @if (($car->operational_status ?? 'available') !== 'in_maintenance')
                                        <a href="{{ route('car-maintenance.create', $car->id) }}"
                                            class="btn btn-warning btn-sm">
                                            🔧 إضافة صيانة جديدة
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="panel bg-white dark:bg-gray-800 dark:border-gray-700">
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="text-6xl mb-4">🚗</div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-300 mb-2">لا توجد سيارات</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">لم يتم إضافة أي سيارات لهذا الفرع بعد</p>
                    <a href="{{ url('cars/addBranchCar') }}" class="btn btn-primary">
                        إضافة سيارة جديدة
                    </a>
                </div>
            </div>
        @endif
    </div>

    <script>
        function carMaintenancePage() {
            return {
                search: '',
                selectedCar: 0,
            }
        }
    </script>
@endsection
