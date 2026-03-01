@extends('layouts.app')

@section('page-title', 'تفاصيل السيارة - ' . ($car->car_name ?? $car->car_number))

@section('content')
    <div x-data="{ activeTab: 'info' }">
        <!-- رأس الصفحة مع معلومات السيارة الأساسية -->
        <div class="panel bg-gradient-to-r from-blue-500 to-blue-700 text-white mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="text-6xl">🚗</div>
                    <div>
                        <h2 class="text-2xl font-bold">{{ $car->car_name ?? 'سيارة' }}</h2>
                        <p class="text-blue-200 text-lg">رقم اللوحة: {{ $car->car_number }}</p>
                        <p class="text-blue-200">
                            {{ $car->carType->name ?? 'غير محدد' }} - {{ $car->car_model ?? 'موديل غير محدد' }}
                            @if ($car->mixer_capacity)
                                <span class="badge bg-white/20 text-white ml-2">سعة: {{ $car->mixer_capacity }} م³</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex flex-col gap-2 items-end">
                    {{-- حالة السيارة التشغيلية --}}
                    @php
                        $operationalStatus = $car->operational_status ?? 'available';
                        $statusConfig = [
                            'available' => ['text' => 'متاحة', 'icon' => '✅', 'class' => 'bg-green-400/30'],
                            'reserved' => ['text' => 'محجوزة', 'icon' => '📋', 'class' => 'bg-blue-400/30'],
                            'in_maintenance' => ['text' => 'في الصيانة', 'icon' => '🔧', 'class' => 'bg-yellow-400/30'],
                            'out_of_service' => ['text' => 'خارج الخدمة', 'icon' => '⛔', 'class' => 'bg-red-400/30'],
                            'scrapped' => ['text' => 'مشطوبة', 'icon' => '🗑️', 'class' => 'bg-gray-400/30'],
                        ];
                        $currentStatus = $statusConfig[$operationalStatus] ?? $statusConfig['available'];
                    @endphp
                    <span class="badge {{ $currentStatus['class'] }} text-white px-4 py-2 text-lg">
                        {{ $currentStatus['icon'] }} {{ $currentStatus['text'] }}
                    </span>
                    @if ($car->is_active)
                        <span class="badge bg-green-400/30 text-white px-3 py-1 text-sm">نشطة</span>
                    @else
                        <span class="badge bg-red-400/30 text-white px-3 py-1 text-sm">غير نشطة</span>
                    @endif
                    <span class="text-blue-200 text-sm">عمر السيارة: {{ $carAge }} يوم</span>
                </div>
            </div>
        </div>

        {{-- تنبيه إذا كانت السيارة في الصيانة --}}
        @if ($car->operational_status === 'in_maintenance')
            <div class="alert alert-warning flex items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🔧</span>
                    <div>
                        <strong>هذه السيارة في الصيانة حالياً</strong>
                        @if ($car->status_reason)
                            <br><small>السبب: {{ $car->status_reason }}</small>
                        @endif
                    </div>
                </div>
                @php
                    $activeMaintenance = $maintenances->where('status', 'in_progress')->first();
                @endphp
                @if ($activeMaintenance)
                    <a href="{{ route('car-maintenance.edit', $activeMaintenance->id) }}"
                        class="btn btn-success flex items-center gap-2">
                        <span>✅</span>
                        <span>إكمال الصيانة</span>
                    </a>
                @endif
            </div>
        @endif

        <!-- رسائل النجاح والخطأ -->
        @if (session('success'))
            <div class="alert alert-success flex items-center gap-2 mb-4">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- بطاقات الإحصائيات -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- عدد السائقين -->
            <div class="panel">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold text-primary">{{ $driverStats['total_assigned'] }}</div>
                        <div class="text-gray-500 text-sm">إجمالي تعيينات السائقين</div>
                    </div>
                    <div class="text-4xl">👨‍✈️</div>
                </div>
                <div class="mt-2 text-xs text-gray-400">
                    نشط: {{ $driverStats['currently_active'] }} | منتهي: {{ $driverStats['ended_assignments'] }}
                </div>
            </div>

            <!-- عدد الصيانات -->
            <div class="panel">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold text-orange-500">{{ $maintenanceStats['total_count'] }}</div>
                        <div class="text-gray-500 text-sm">إجمالي الصيانات</div>
                    </div>
                    <div class="text-4xl">🔧</div>
                </div>
                <div class="mt-2 text-xs text-gray-400">
                    مكتملة: {{ $maintenanceStats['completed_count'] }}
                </div>
            </div>

            <!-- إجمالي تكاليف الصيانة -->
            <div class="panel">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold text-red-500">
                            {{ number_format($maintenanceStats['total_cost'], 0) }}</div>
                        <div class="text-gray-500 text-sm">إجمالي تكاليف الصيانة (د.ع)</div>
                    </div>
                    <div class="text-4xl">💰</div>
                </div>
            </div>

            <!-- آخر صيانة -->
            <div class="panel">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xl font-bold text-green-500">
                            {{ $maintenanceStats['last_maintenance'] ? \Carbon\Carbon::parse($maintenanceStats['last_maintenance'])->format('Y/m/d') : 'لا توجد' }}
                        </div>
                        <div class="text-gray-500 text-sm">آخر صيانة</div>
                    </div>
                    <div class="text-4xl">📅</div>
                </div>
                @if ($maintenanceStats['next_scheduled'])
                    <div class="mt-2 text-xs text-yellow-600">
                        القادمة: {{ \Carbon\Carbon::parse($maintenanceStats['next_scheduled'])->format('Y/m/d') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- التبويبات -->
        <div class="panel">
            <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                <nav class="flex gap-4 -mb-px">
                    <button @click="activeTab = 'info'"
                        :class="activeTab === 'info' ? 'border-primary text-primary' :
                            'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-3 px-4 border-b-2 font-medium transition-colors flex items-center gap-2">
                        <span>📋</span>
                        <span>معلومات السيارة</span>
                    </button>
                    <button @click="activeTab = 'drivers'"
                        :class="activeTab === 'drivers' ? 'border-primary text-primary' :
                            'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-3 px-4 border-b-2 font-medium transition-colors flex items-center gap-2">
                        <span>👨‍✈️</span>
                        <span>سجل السائقين ({{ $driverStats['total_assigned'] }})</span>
                    </button>
                    <button @click="activeTab = 'maintenance'"
                        :class="activeTab === 'maintenance' ? 'border-primary text-primary' :
                            'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-3 px-4 border-b-2 font-medium transition-colors flex items-center gap-2">
                        <span>🔧</span>
                        <span>سجل الصيانة ({{ $maintenanceStats['total_count'] }})</span>
                    </button>
                </nav>
            </div>

            <!-- محتوى معلومات السيارة -->
            <div x-show="activeTab === 'info'" x-transition>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-gray-500 text-sm mb-1">اسم السيارة</div>
                        <div class="font-bold text-lg">{{ $car->car_name ?? '-' }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-gray-500 text-sm mb-1">رقم اللوحة</div>
                        <div class="font-bold text-lg text-primary">{{ $car->car_number }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-gray-500 text-sm mb-1">نوع السيارة</div>
                        <div class="font-bold text-lg">{{ $car->carType->name ?? 'غير محدد' }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-gray-500 text-sm mb-1">الموديل</div>
                        <div class="font-bold text-lg">{{ $car->car_model ?? '-' }}</div>
                    </div>
                    @if ($car->mixer_capacity)
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="text-gray-500 text-sm mb-1">سعة الخلاط</div>
                            <div class="font-bold text-lg text-blue-600">{{ $car->mixer_capacity }} م³</div>
                        </div>
                    @endif
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-gray-500 text-sm mb-1">تاريخ الإضافة</div>
                        <div class="font-bold text-lg">
                            {{ $car->add_date ? \Carbon\Carbon::parse($car->add_date)->format('Y/m/d') : '-' }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-gray-500 text-sm mb-1">السائق الحالي</div>
                        <div class="font-bold text-lg">
                            @if ($car->driver)
                                <span class="text-green-600">{{ $car->driver->name }}</span>
                            @else
                                <span class="text-gray-400">لا يوجد</span>
                            @endif
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-gray-500 text-sm mb-1">السائق الاحتياطي</div>
                        <div class="font-bold text-lg">
                            @if ($car->backupDriver)
                                <span class="text-yellow-600">{{ $car->backupDriver->name }}</span>
                            @else
                                <span class="text-gray-400">لا يوجد</span>
                            @endif
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-gray-500 text-sm mb-1">الفرع</div>
                        <div class="font-bold text-lg">{{ $car->BranchName->name ?? '-' }}</div>
                    </div>
                    @if ($car->note)
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg col-span-full">
                            <div class="text-gray-500 text-sm mb-1">ملاحظات</div>
                            <div class="font-bold">{{ $car->note }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- محتوى سجل السائقين -->
            <div x-show="activeTab === 'drivers'" x-transition>
                @if ($driverHistory->count() > 0)
                    <div class="table-responsive">
                        <table class="table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">السائق</th>
                                    <th class="text-center">نوع التكليف</th>
                                    <th class="text-center">الشفت</th>
                                    <th class="text-center">تاريخ التعيين</th>
                                    <th class="text-center">تاريخ الإنهاء</th>
                                    <th class="text-center">الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($driverHistory as $index => $assignment)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center font-medium">{{ $assignment->driver->name ?? '-' }}</td>
                                        <td class="text-center">
                                            @if ($assignment->driver_type == 'primary')
                                                <span class="badge bg-blue-500/20 text-blue-600">رئيسي 🚗</span>
                                            @else
                                                <span class="badge bg-yellow-500/20 text-yellow-600">احتياطي 🔄</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $assignment->shift->name ?? '-' }}</td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($assignment->assigned_date)->format('Y/m/d') }}</td>
                                        <td class="text-center">
                                            {{ $assignment->ended_date ? \Carbon\Carbon::parse($assignment->ended_date)->format('Y/m/d') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            @if ($assignment->is_active)
                                                <span class="badge bg-green-500/20 text-green-600">نشط ✅</span>
                                            @else
                                                <span class="badge bg-red-500/20 text-red-600">منتهي ❌</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <div class="text-4xl mb-2">👨‍✈️</div>
                        <p>لا يوجد سجل تعيينات سائقين لهذه السيارة</p>
                    </div>
                @endif
            </div>

            <!-- محتوى سجل الصيانة -->
            <div x-show="activeTab === 'maintenance'" x-transition>
                <div class="flex justify-between items-center mb-4">
                    {{-- معلومات حالة السيارة --}}
                    <div>
                        @if ($car->operational_status === 'in_maintenance')
                            <span class="badge bg-yellow-500/20 text-yellow-600 px-3 py-2 flex items-center gap-2">
                                <span>🔧</span>
                                <span>السيارة في الصيانة حالياً</span>
                            </span>
                        @else
                            <span class="badge bg-green-500/20 text-green-600 px-3 py-2 flex items-center gap-2">
                                <span>✅</span>
                                <span>السيارة متاحة</span>
                            </span>
                        @endif
                    </div>

                    {{-- أزرار الإجراءات --}}
                    <div class="flex gap-2">
                        @if ($car->operational_status === 'in_maintenance')
                            @php
                                $activeMaintenance = $maintenances->where('status', 'in_progress')->first();
                            @endphp
                            @if ($activeMaintenance)
                                <a href="{{ route('car-maintenance.edit', $activeMaintenance->id) }}"
                                    class="btn btn-success flex items-center gap-2">
                                    <span>✅</span>
                                    <span>إكمال الصيانة</span>
                                </a>
                            @endif
                        @else
                            <a href="{{ route('car-maintenance.create', $car->id) }}"
                                class="btn btn-warning flex items-center gap-2">
                                <span>🔧</span>
                                <span>بدء صيانة جديدة</span>
                            </a>
                        @endif
                    </div>
                </div>

                @if ($maintenances->count() > 0)
                    <div class="table-responsive">
                        <table class="table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">التاريخ</th>
                                    <th class="text-center">النوع</th>
                                    <th class="text-center">العنوان</th>
                                    <th class="text-center">التكلفة</th>
                                    <th class="text-center">قراءة العداد</th>
                                    <th class="text-center">الحالة</th>
                                    <th class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($maintenances as $index => $maintenance)
                                    <tr
                                        class="{{ $maintenance->status === 'in_progress' ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($maintenance->maintenance_date)->format('Y/m/d') }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge px-2 py-1 rounded"
                                                style="background-color: {{ $maintenance->type_color }}20; color: {{ $maintenance->type_color }};">
                                                {{ $maintenance->type_icon }} {{ $maintenance->type_name }}
                                            </span>
                                        </td>
                                        <td class="text-center font-medium">{{ $maintenance->title }}</td>
                                        <td class="text-center">
                                            @if ($maintenance->status === 'in_progress')
                                                <span class="text-gray-400">قيد التنفيذ</span>
                                            @else
                                                <span
                                                    class="font-bold text-red-600">{{ number_format($maintenance->total_cost, 2) }}</span>
                                                <span class="text-xs text-gray-500">د.ع</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ $maintenance->odometer_reading ? number_format($maintenance->odometer_reading) . ' كم' : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge px-2 py-1 rounded"
                                                style="background-color: {{ $maintenance->status_color }}20; color: {{ $maintenance->status_color }};">
                                                {{ $maintenance->status_icon }} {{ $maintenance->status_name }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                @if ($maintenance->status === 'in_progress')
                                                    <a href="{{ route('car-maintenance.edit', $maintenance->id) }}"
                                                        class="btn btn-sm btn-success" title="إكمال الصيانة">
                                                        ✅ إكمال
                                                    </a>
                                                @else
                                                    <a href="{{ route('car-maintenance.edit', $maintenance->id) }}"
                                                        class="btn btn-sm btn-outline-primary" title="تعديل">
                                                        ✏️
                                                    </a>
                                                @endif
                                                <form action="{{ route('car-maintenance.destroy', $maintenance->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('هل أنت متأكد من حذف هذه الصيانة؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="حذف">
                                                        🗑️
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <div class="text-4xl mb-2">🔧</div>
                        <p class="mb-4">لا يوجد سجل صيانة لهذه السيارة</p>
                        @if ($car->operational_status !== 'in_maintenance')
                            <a href="{{ route('car-maintenance.create', $car->id) }}" class="btn btn-warning">
                                🔧 بدء أول صيانة
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- زر العودة -->
        <div class="mt-6">
            <a href="{{ route('car-maintenance.index') }}" class="btn btn-outline-primary flex items-center gap-2 w-fit">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة لقائمة السيارات</span>
            </a>
        </div>
    </div>
@endsection
