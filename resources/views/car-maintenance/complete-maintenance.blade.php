@extends('layouts.app')

@section('page-title', 'إكمال الصيانة')

@section('content')
    <div class="max-w-3xl mx-auto">
        <!-- رأس الصفحة -->
        <div class="panel bg-gradient-to-r from-green-500 to-green-600 text-white mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="text-5xl">✅</div>
                    <div>
                        <h2 class="text-xl font-bold">إكمال الصيانة</h2>
                        <p class="text-green-200">
                            السيارة: {{ $car->car_name ?? $car->car_number }}
                            ({{ $car->carType->name ?? 'غير محدد' }})
                        </p>
                    </div>
                </div>
                <div class="text-left">
                    <div class="text-sm text-green-200">بدأت في</div>
                    <div class="font-bold">{{ $maintenance->maintenance_date->format('Y/m/d') }}</div>
                </div>
            </div>
        </div>

        <!-- معلومات الصيانة الحالية -->
        <div class="panel mb-6">
            <h4 class="font-bold text-lg mb-4 flex items-center gap-2 border-b pb-2">
                <span>📋</span>
                <span>معلومات الصيانة</span>
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-gray-500 text-sm">نوع الصيانة</div>
                    <div class="font-bold flex items-center gap-2">
                        <span>{{ $maintenance->type_icon }}</span>
                        <span>{{ $maintenance->type_name }}</span>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-gray-500 text-sm">عنوان الصيانة</div>
                    <div class="font-bold">{{ $maintenance->title }}</div>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-gray-500 text-sm">الحالة</div>
                    <div class="font-bold flex items-center gap-2">
                        <span class="badge bg-yellow-500/20 text-yellow-600 px-3 py-1">
                            ⏳ قيد التنفيذ
                        </span>
                    </div>
                </div>
            </div>
            @if ($maintenance->description)
                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-gray-500 text-sm mb-1">الوصف المبدئي</div>
                    <div>{{ $maintenance->description }}</div>
                </div>
            @endif
        </div>

        <!-- نموذج إكمال الصيانة -->
        <div class="panel">
            <form action="{{ route('car-maintenance.complete', $maintenance->id) }}" method="POST">
                @csrf

                <h4 class="font-bold text-lg mb-4 flex items-center gap-2 border-b pb-2">
                    <span>📝</span>
                    <span>تفاصيل الإكمال</span>
                </h4>

                <div class="space-y-6">
                    <!-- الوصف النهائي -->
                    <div>
                        <label for="description" class="block font-medium mb-2">
                            تفاصيل الصيانة المنجزة
                        </label>
                        <textarea name="description" id="description" rows="4" class="form-textarea w-full"
                            placeholder="اكتب تفاصيل ما تم عمله في الصيانة...">{{ old('description', $maintenance->description) }}</textarea>
                    </div>

                    <!-- التكاليف -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <h5 class="font-bold mb-4 flex items-center gap-2">
                            <span>💰</span>
                            <span>التكاليف</span>
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="total_cost" class="block font-medium mb-2">
                                    التكلفة الإجمالية <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="total_cost" id="total_cost" step="0.01" min="0"
                                    value="{{ old('total_cost', $maintenance->total_cost) }}"
                                    class="form-input w-full @error('total_cost') border-red-500 @enderror"
                                    placeholder="0.00" required>
                                @error('total_cost')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="parts_cost" class="block font-medium mb-2">تكلفة القطع</label>
                                <input type="number" name="parts_cost" id="parts_cost" step="0.01" min="0"
                                    value="{{ old('parts_cost', $maintenance->parts_cost ?? 0) }}"
                                    class="form-input w-full" placeholder="0.00">
                            </div>
                            <div>
                                <label for="labor_cost" class="block font-medium mb-2">تكلفة العمالة</label>
                                <input type="number" name="labor_cost" id="labor_cost" step="0.01" min="0"
                                    value="{{ old('labor_cost', $maintenance->labor_cost ?? 0) }}"
                                    class="form-input w-full" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- معلومات الورشة -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="workshop_name" class="block font-medium mb-2">اسم الورشة</label>
                            <input type="text" name="workshop_name" id="workshop_name"
                                value="{{ old('workshop_name', $maintenance->workshop_name) }}" class="form-input w-full"
                                placeholder="اسم ورشة الصيانة">
                        </div>
                        <div>
                            <label for="performed_by" class="block font-medium mb-2">الفني المسؤول</label>
                            <input type="text" name="performed_by" id="performed_by"
                                value="{{ old('performed_by', $maintenance->performed_by) }}" class="form-input w-full"
                                placeholder="اسم الفني">
                        </div>
                        <div>
                            <label for="invoice_number" class="block font-medium mb-2">رقم الفاتورة</label>
                            <input type="text" name="invoice_number" id="invoice_number"
                                value="{{ old('invoice_number', $maintenance->invoice_number) }}" class="form-input w-full"
                                placeholder="رقم فاتورة الورشة">
                        </div>
                        <div>
                            <label for="odometer_reading" class="block font-medium mb-2">قراءة العداد (كم)</label>
                            <input type="number" name="odometer_reading" id="odometer_reading"
                                value="{{ old('odometer_reading', $maintenance->odometer_reading ?? $car->odometer_reading) }}"
                                class="form-input w-full" placeholder="قراءة العداد الحالية">
                        </div>
                    </div>

                    <!-- الصيانة القادمة -->
                    <div>
                        <label for="next_maintenance_date" class="block font-medium mb-2">تاريخ الصيانة القادمة
                            (اختياري)</label>
                        <input type="date" name="next_maintenance_date" id="next_maintenance_date"
                            value="{{ old('next_maintenance_date') }}" class="form-input w-full md:w-1/3">
                    </div>

                    <!-- ملاحظات -->
                    <div>
                        <label for="notes" class="block font-medium mb-2">ملاحظات إضافية</label>
                        <textarea name="notes" id="notes" rows="3" class="form-textarea w-full"
                            placeholder="أي ملاحظات إضافية...">{{ old('notes', $maintenance->notes) }}</textarea>
                    </div>
                </div>

                <!-- أزرار الإجراء -->
                <div class="flex items-center justify-between gap-4 pt-6 border-t mt-6">
                    <a href="{{ route('car-maintenance.car-details', $car->id) }}"
                        class="btn btn-outline-secondary flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>رجوع</span>
                    </a>

                    <button type="submit" class="btn btn-success flex items-center gap-2 text-lg px-6 py-3">
                        <span class="text-2xl">✅</span>
                        <span>إكمال الصيانة</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
