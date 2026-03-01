@extends('layouts.app')

@section('page-title', isset($maintenance) ? 'تعديل صيانة' : 'إضافة صيانة جديدة')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- رأس الصفحة -->
        <div class="panel bg-gradient-to-r from-orange-500 to-orange-600 text-white mb-6">
            <div class="flex items-center gap-4">
                <div class="text-5xl">🔧</div>
                <div>
                    <h2 class="text-xl font-bold">{{ isset($maintenance) ? 'تعديل صيانة' : 'إضافة صيانة جديدة' }}</h2>
                    <p class="text-orange-200">
                        السيارة: {{ $car->car_name ?? $car->car_number }}
                        ({{ $car->carType->name ?? 'غير محدد' }})
                    </p>
                </div>
            </div>
        </div>

        <div class="panel">
            <form
                action="{{ isset($maintenance) ? route('car-maintenance.update', $maintenance->id) : route('car-maintenance.store', $car->id) }}"
                method="POST" enctype="multipart/form-data" x-data="{
                    maintenanceType: '{{ old('maintenance_type', $maintenance->maintenance_type ?? 'periodic') }}',
                    showCostBreakdown: {{ old('parts_cost', $maintenance->parts_cost ?? 0) > 0 || old('labor_cost', $maintenance->labor_cost ?? 0) > 0 ? 'true' : 'false' }}
                }">
                @csrf
                @if (isset($maintenance))
                    @method('PUT')
                @endif

                <!-- معلومات الصيانة الأساسية -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg mb-4 flex items-center gap-2 border-b pb-2">
                        <span>📋</span>
                        <span>معلومات الصيانة</span>
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- نوع الصيانة -->
                        <div>
                            <label for="maintenance_type" class="block font-medium mb-2">
                                نوع الصيانة <span class="text-red-500">*</span>
                            </label>
                            <select name="maintenance_type" id="maintenance_type" x-model="maintenanceType"
                                class="form-select w-full @error('maintenance_type') border-red-500 @enderror">
                                @foreach ($maintenanceTypes as $key => $type)
                                    <option value="{{ $key }}"
                                        {{ old('maintenance_type', $maintenance->maintenance_type ?? '') == $key ? 'selected' : '' }}>
                                        {{ $type['icon'] }} {{ $type['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('maintenance_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- حالة الصيانة -->
                        <div>
                            <label for="status" class="block font-medium mb-2">
                                حالة الصيانة <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status"
                                class="form-select w-full @error('status') border-red-500 @enderror">
                                @foreach ($statuses as $key => $status)
                                    <option value="{{ $key }}"
                                        {{ old('status', $maintenance->status ?? 'completed') == $key ? 'selected' : '' }}>
                                        {{ $status['icon'] }} {{ $status['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- عنوان الصيانة -->
                        <div class="md:col-span-2">
                            <label for="title" class="block font-medium mb-2">
                                عنوان الصيانة <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title"
                                value="{{ old('title', $maintenance->title ?? '') }}"
                                class="form-input w-full @error('title') border-red-500 @enderror"
                                placeholder="مثال: تغيير زيت المحرك، إصلاح الفرامل...">
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- وصف الصيانة -->
                        <div class="md:col-span-2">
                            <label for="description" class="block font-medium mb-2">وصف الصيانة</label>
                            <textarea name="description" id="description" rows="3" class="form-textarea w-full"
                                placeholder="تفاصيل إضافية عن الصيانة...">{{ old('description', $maintenance->description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- التواريخ والقراءات -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg mb-4 flex items-center gap-2 border-b pb-2">
                        <span>📅</span>
                        <span>التواريخ والقراءات</span>
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- تاريخ الصيانة -->
                        <div>
                            <label for="maintenance_date" class="block font-medium mb-2">
                                تاريخ الصيانة <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="maintenance_date" id="maintenance_date"
                                value="{{ old('maintenance_date', isset($maintenance) ? $maintenance->maintenance_date->format('Y-m-d') : date('Y-m-d')) }}"
                                class="form-input w-full @error('maintenance_date') border-red-500 @enderror">
                            @error('maintenance_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- تاريخ الصيانة القادمة -->
                        <div>
                            <label for="next_maintenance_date" class="block font-medium mb-2">تاريخ الصيانة القادمة</label>
                            <input type="date" name="next_maintenance_date" id="next_maintenance_date"
                                value="{{ old('next_maintenance_date', isset($maintenance) && $maintenance->next_maintenance_date ? $maintenance->next_maintenance_date->format('Y-m-d') : '') }}"
                                class="form-input w-full">
                        </div>

                        <!-- قراءة العداد -->
                        <div>
                            <label for="odometer_reading" class="block font-medium mb-2">قراءة العداد (كم)</label>
                            <input type="number" name="odometer_reading" id="odometer_reading"
                                value="{{ old('odometer_reading', $maintenance->odometer_reading ?? ($lastMaintenance->odometer_reading ?? '')) }}"
                                class="form-input w-full" placeholder="مثال: 50000">
                            @if (isset($lastMaintenance) && $lastMaintenance->odometer_reading && !isset($maintenance))
                                <p class="text-gray-500 text-xs mt-1">
                                    آخر قراءة: {{ number_format($lastMaintenance->odometer_reading) }} كم
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- التكاليف -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg mb-4 flex items-center gap-2 border-b pb-2">
                        <span>💰</span>
                        <span>التكاليف</span>
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- التكلفة الإجمالية -->
                        <div>
                            <label for="total_cost" class="block font-medium mb-2">
                                التكلفة الإجمالية (د.ع) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="total_cost" id="total_cost" step="0.01" min="0"
                                value="{{ old('total_cost', $maintenance->total_cost ?? 0) }}"
                                class="form-input w-full @error('total_cost') border-red-500 @enderror" placeholder="0.00">
                            @error('total_cost')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- زر إظهار تفصيل التكاليف -->
                        <div class="flex items-end">
                            <button type="button" @click="showCostBreakdown = !showCostBreakdown"
                                class="btn btn-outline-info w-full">
                                <span x-text="showCostBreakdown ? 'إخفاء التفصيل' : 'تفصيل التكاليف'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- تفصيل التكاليف -->
                    <div x-show="showCostBreakdown" x-transition
                        class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <label for="parts_cost" class="block font-medium mb-2">تكلفة القطع (د.ع)</label>
                            <input type="number" name="parts_cost" id="parts_cost" step="0.01" min="0"
                                value="{{ old('parts_cost', $maintenance->parts_cost ?? 0) }}" class="form-input w-full"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label for="labor_cost" class="block font-medium mb-2">تكلفة العمالة (د.ع)</label>
                            <input type="number" name="labor_cost" id="labor_cost" step="0.01" min="0"
                                value="{{ old('labor_cost', $maintenance->labor_cost ?? 0) }}" class="form-input w-full"
                                placeholder="0.00">
                        </div>
                    </div>
                </div>

                <!-- معلومات الورشة -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg mb-4 flex items-center gap-2 border-b pb-2">
                        <span>🏭</span>
                        <span>معلومات الورشة</span>
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="workshop_name" class="block font-medium mb-2">اسم الورشة</label>
                            <input type="text" name="workshop_name" id="workshop_name"
                                value="{{ old('workshop_name', $maintenance->workshop_name ?? '') }}"
                                class="form-input w-full" placeholder="اسم ورشة الصيانة">
                        </div>
                        <div>
                            <label for="performed_by" class="block font-medium mb-2">الفني/المسؤول</label>
                            <input type="text" name="performed_by" id="performed_by"
                                value="{{ old('performed_by', $maintenance->performed_by ?? '') }}"
                                class="form-input w-full" placeholder="اسم الفني">
                        </div>
                        <div>
                            <label for="invoice_number" class="block font-medium mb-2">رقم الفاتورة</label>
                            <input type="text" name="invoice_number" id="invoice_number"
                                value="{{ old('invoice_number', $maintenance->invoice_number ?? '') }}"
                                class="form-input w-full" placeholder="رقم فاتورة الورشة">
                        </div>
                    </div>
                </div>

                <!-- ملاحظات ومرفقات -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg mb-4 flex items-center gap-2 border-b pb-2">
                        <span>📎</span>
                        <span>ملاحظات ومرفقات</span>
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="notes" class="block font-medium mb-2">ملاحظات إضافية</label>
                            <textarea name="notes" id="notes" rows="3" class="form-textarea w-full"
                                placeholder="أي ملاحظات إضافية...">{{ old('notes', $maintenance->notes ?? '') }}</textarea>
                        </div>
                        <div>
                            <label for="attachment" class="block font-medium mb-2">مرفق (صورة/PDF)</label>
                            <input type="file" name="attachment" id="attachment" class="form-input w-full"
                                accept="image/*,.pdf">
                            @if (isset($maintenance) && $maintenance->attachment)
                                <p class="text-gray-500 text-sm mt-1">
                                    <a href="{{ asset($maintenance->attachment) }}" target="_blank"
                                        class="text-primary hover:underline">
                                        📎 عرض المرفق الحالي
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- أزرار الحفظ -->
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
                    <button type="submit" class="btn btn-primary flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ isset($maintenance) ? 'تحديث الصيانة' : 'حفظ الصيانة' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
