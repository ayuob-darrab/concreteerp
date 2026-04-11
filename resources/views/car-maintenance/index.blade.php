@extends('layouts.app')

@section('page-title', 'صيانة السيارات')

@section('content')
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
        .car-maintenance-page .cm-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .car-maintenance-page .cm-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .car-maintenance-page .cm-table thead th {
            font-size: 0.75rem; font-weight: 700; text-transform: none;
            padding: 0.75rem 0.5rem; border-bottom: 2px solid rgba(0,0,0,.08);
            white-space: nowrap; background: rgba(0,0,0,.03);
        }
        body.dark .car-maintenance-page .cm-table thead th {
            border-bottom-color: #374151; background: rgba(255,255,255,.04);
        }
        .car-maintenance-page .cm-table tbody td {
            padding: 0.65rem 0.5rem; vertical-align: middle;
            border-bottom: 1px solid rgba(0,0,0,.06); font-size: 0.875rem;
        }
        body.dark .car-maintenance-page .cm-table tbody td { border-bottom-color: #374151; }
        .car-maintenance-page .cm-table tbody tr:hover td { background: rgba(59, 130, 246, .06); }
        body.dark .car-maintenance-page .cm-table tbody tr:hover td { background: rgba(59, 130, 246, .12); }
        .car-maintenance-page .cm-actions { display: flex; flex-wrap: wrap; gap: 0.35rem; justify-content: center; }
    </style>

    <div class="car-maintenance-page" x-data="carMaintenanceIndex()">
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
            <div class="panel mb-4 bg-white dark:bg-gray-800 dark:border-gray-700">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h5 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2 mb-1">
                            🚗 سيارات الفرع
                        </h5>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            الفرع:
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $branchName ?? '—' }}</span>
                        </p>
                    </div>
                    <a href="{{ route('car-maintenance.report') }}" class="btn btn-info btn-sm flex items-center justify-center gap-2 shrink-0">
                        📊 <span>تقرير الصيانات</span>
                    </a>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <label for="car-maintenance-search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">بحث في السيارات</label>
                    <div class="relative max-w-xl">
                        <span class="absolute inset-y-0 start-3 flex items-center pointer-events-none text-gray-400" aria-hidden="true">🔍</span>
                        <input id="car-maintenance-search" type="search" x-model="search" class="form-input w-full pe-3 ps-10"
                            placeholder="بحث بالاسم، رقم السيارة، النوع، الموديل، السائق..." autocomplete="off">
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2" x-show="search.trim() !== ''" x-cloak>
                        المعروض: <span class="font-semibold text-primary" x-text="visibleCount"></span> من {{ $cars->count() }}
                    </p>
                </div>
            </div>

            <div class="panel bg-white dark:bg-gray-800 dark:border-gray-700 p-0 overflow-hidden">
                <div class="cm-table-wrap">
                    <table class="cm-table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-start">السيارة</th>
                                <th class="text-center">الرقم</th>
                                <th class="text-start">النوع</th>
                                <th class="text-start">الموديل</th>
                                <th class="text-start">السائق</th>
                                <th class="text-center">الحالة</th>
                                <th class="text-center">الصيانات</th>
                                <th class="text-center">التكاليف (د.ع)</th>
                                <th class="text-center min-w-[200px]">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cars as $index => $car)
                                @php
                                    $opStatus = $car->operational_status ?? 'available';
                                    $driverLabel = $car->driver->fullname ?? $car->driver_name ?: '—';
                                    $filterHaystack = mb_strtolower(
                                        trim(
                                            ($car->car_name ?? '') . ' ' .
                                            $car->car_number . ' ' .
                                            ($car->carType->name ?? '') . ' ' .
                                            ($car->car_model ?? '') . ' ' .
                                            $driverLabel
                                        ),
                                        'UTF-8'
                                    );
                                @endphp
                                <tr x-show="rowMatch({{ json_encode($filterHaystack) }})"
                                    data-filter-hay="{{ htmlspecialchars($filterHaystack, ENT_QUOTES, 'UTF-8') }}">
                                    <td class="text-center text-gray-500 font-mono text-sm">{{ $index + 1 }}</td>
                                    <td class="text-start font-semibold text-gray-900 dark:text-gray-100">{{ $car->car_name ?? 'بدون اسم' }}</td>
                                    <td class="text-center font-mono text-primary">{{ $car->car_number }}</td>
                                    <td class="text-start text-gray-700 dark:text-gray-300">{{ $car->carType->name ?? '—' }}</td>
                                    <td class="text-start text-gray-600 dark:text-gray-400">{{ $car->car_model ?? '—' }}</td>
                                    <td class="text-start text-gray-700 dark:text-gray-300">{{ $driverLabel }}</td>
                                    <td class="text-center">
                                        @if ($opStatus === 'in_maintenance')
                                            <span class="badge bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 px-2 py-1 rounded-full text-xs whitespace-nowrap">في الصيانة</span>
                                        @elseif ($car->is_active)
                                            <span class="badge bg-success/20 text-success px-2 py-1 rounded-full text-xs whitespace-nowrap">متاحة</span>
                                        @else
                                            <span class="badge bg-danger/20 text-danger px-2 py-1 rounded-full text-xs whitespace-nowrap">غير نشطة</span>
                                        @endif
                                    </td>
                                    <td class="text-center font-bold text-orange-600 dark:text-orange-400">{{ $car->maintenance_count ?? 0 }}</td>
                                    <td class="text-center font-semibold text-red-600 dark:text-red-400">{{ number_format($car->total_maintenance_cost ?? 0, 0) }}</td>
                                    <td class="text-center">
                                        <div class="cm-actions">
                                            <a href="{{ route('car-maintenance.car-details', $car->id) }}" class="btn btn-outline-info btn-xs whitespace-nowrap">
                                                التفاصيل
                                            </a>
                                            @if ($opStatus === 'in_maintenance')
                                                <a href="{{ route('car-maintenance.car-details', $car->id) }}" class="btn btn-success btn-xs whitespace-nowrap">
                                                    إكمال الصيانة
                                                </a>
                                            @else
                                                <a href="{{ route('car-maintenance.create', $car->id) }}" class="btn btn-warning btn-xs whitespace-nowrap">
                                                    إضافة للصيانة
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
        function carMaintenanceIndex() {
            return {
                search: '',
                get visibleCount() {
                    const q = (this.search || '').trim().toLowerCase();
                    const rows = document.querySelectorAll('.car-maintenance-page .cm-table tbody tr[data-filter-hay]');
                    if (!q) return rows.length;
                    let n = 0;
                    rows.forEach((row) => {
                        const hay = (row.getAttribute('data-filter-hay') || '').toLowerCase();
                        if (hay.includes(q)) n++;
                    });
                    return n;
                },
                rowMatch(haystack) {
                    const q = (this.search || '').trim().toLowerCase();
                    if (!q) return true;
                    return (haystack || '').toLowerCase().includes(q);
                },
            };
        }
    </script>
@endsection
