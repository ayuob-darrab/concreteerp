@extends('layouts.app')

@section('page-title', 'بدء صيانة جديدة')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- رأس الصفحة -->
        <div class="panel bg-gradient-to-r from-orange-500 to-orange-600 text-white mb-6">
            <div class="flex items-center gap-4">
                <div class="text-5xl">🔧</div>
                <div>
                    <h2 class="text-xl font-bold">بدء صيانة جديدة</h2>
                    <p class="text-orange-200">
                        السيارة: {{ $car->car_name ?? $car->car_number }}
                        ({{ $car->carType->name ?? 'غير محدد' }})
                    </p>
                </div>
            </div>
        </div>

        <!-- تحذير حالة السيارة -->
        @if ($car->operational_status === 'in_maintenance')
            <div class="alert alert-warning flex items-center gap-2 mb-6">
                <span class="text-2xl">⚠️</span>
                <div>
                    <strong>تنبيه:</strong> هذه السيارة في الصيانة بالفعل!
                    <br>
                    <small>سبب الصيانة: {{ $car->status_reason ?? 'غير محدد' }}</small>
                </div>
            </div>
        @else
            <div class="alert alert-info flex items-center gap-2 mb-6">
                <span class="text-2xl">ℹ️</span>
                <div>
                    <strong>ملاحظة:</strong> عند بدء الصيانة، ستصبح السيارة غير متاحة للحجز حتى يتم إكمال الصيانة.
                </div>
            </div>
        @endif

        <div class="panel">
            <form action="{{ route('car-maintenance.start', $car->id) }}" method="POST">
                @csrf

                <div class="mb-6">
                    <h4 class="font-bold text-lg mb-4 flex items-center gap-2 border-b pb-2">
                        <span>📋</span>
                        <span>معلومات الصيانة</span>
                    </h4>

                    <div class="space-y-4">
                        <!-- نوع الصيانة -->
                        <div>
                            <label for="maintenance_type" class="block font-medium mb-2">
                                نوع الصيانة <span class="text-red-500">*</span>
                            </label>
                            <select name="maintenance_type" id="maintenance_type"
                                class="form-select w-full @error('maintenance_type') border-red-500 @enderror" required>
                                <option value="">-- اختر نوع الصيانة --</option>
                                @foreach ($maintenanceTypes as $key => $type)
                                    <option value="{{ $key }}"
                                        {{ old('maintenance_type') == $key ? 'selected' : '' }}>
                                        {{ $type['icon'] }} {{ $type['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('maintenance_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- عنوان الصيانة -->
                        <div>
                            <label for="title" class="block font-medium mb-2">
                                عنوان الصيانة <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                class="form-input w-full @error('title') border-red-500 @enderror"
                                placeholder="مثال: تغيير زيت المحرك، إصلاح الفرامل..." required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- وصف الصيانة -->
                        <div>
                            <label for="description" class="block font-medium mb-2">وصف مبدئي (اختياري)</label>
                            <textarea name="description" id="description" rows="3" class="form-textarea w-full"
                                placeholder="وصف مبدئي للمشكلة أو الصيانة المطلوبة...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراء -->
                <div class="flex items-center justify-between gap-4 pt-4 border-t">
                    <a href="{{ route('car-maintenance.car-details', $car->id) }}"
                        class="btn btn-outline-secondary flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>إلغاء</span>
                    </a>

                    @if ($car->operational_status !== 'in_maintenance')
                        <button type="submit" class="btn btn-warning flex items-center gap-2 text-lg px-6 py-3">
                            <span class="text-2xl">🔧</span>
                            <span>بدء الصيانة الآن</span>
                        </button>
                    @else
                        <button type="button" disabled
                            class="btn btn-secondary flex items-center gap-2 opacity-50 cursor-not-allowed">
                            <span>❌</span>
                            <span>السيارة في الصيانة بالفعل</span>
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- معلومات آخر صيانة -->
        @if (isset($lastMaintenance))
            <div class="panel mt-6">
                <h4 class="font-bold text-lg mb-4 flex items-center gap-2">
                    <span>📅</span>
                    <span>آخر صيانة</span>
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">التاريخ</div>
                        <div class="font-bold">{{ $lastMaintenance->maintenance_date->format('Y/m/d') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">النوع</div>
                        <div class="font-bold">{{ $lastMaintenance->type_name }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">التكلفة</div>
                        <div class="font-bold text-red-600">{{ number_format($lastMaintenance->total_cost, 2) }} د.ع</div>
                    </div>
                    <div>
                        <div class="text-gray-500">قراءة العداد</div>
                        <div class="font-bold">
                            {{ $lastMaintenance->odometer_reading ? number_format($lastMaintenance->odometer_reading) . ' كم' : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
