@extends('layouts.app')

@section('page-title', 'تعيين البَم')

@section('content')
    <div class="panel mt-6">
        {{-- العنوان --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <div>
                <h3 class="text-xl font-bold dark:text-white-light">
                    <span class="text-2xl">🚜</span> تعيين البَم للعمل
                </h3>
                <p class="text-gray-500 mt-1">
                    أمر العمل: <span class="font-semibold text-primary">{{ $job->job_number }}</span>
                </p>
            </div>
            <a href="{{ route('companyBranch.workJob.view', $job->id) }}" class="btn btn-outline-secondary">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                رجوع
            </a>
        </div>

        {{-- معلومات أمر العمل --}}
        <div
            class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-5 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <span class="text-gray-500 text-sm block">العميل</span>
                    <span class="font-semibold">{{ $job->customer_name }}</span>
                </div>
                <div>
                    <span class="text-gray-500 text-sm block">نوع الخرسانة</span>
                    <span class="font-semibold">{{ $job->concreteType->classification ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 text-sm block">الكمية المطلوبة</span>
                    <span class="font-semibold text-lg text-primary">{{ $job->total_quantity }} م³</span>
                </div>
                <div>
                    <span class="text-gray-500 text-sm block">تاريخ التنفيذ</span>
                    <span
                        class="font-semibold">{{ $job->scheduled_date ? \Carbon\Carbon::parse($job->scheduled_date)->format('Y-m-d') : '-' }}</span>
                </div>
            </div>
        </div>

        {{-- البَم الحالي إذا كان موجود --}}
        @if ($job->default_pump_id)
            <div
                class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-5 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h5 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                            <span class="text-xl">⚠️</span> البَم الحالي المخصص:
                        </h5>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-3">
                            <div>
                                <span class="text-gray-600 dark:text-gray-400 text-sm block">رقم البَم</span>
                                <span class="font-semibold">{{ $job->defaultPump->car_number ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400 text-sm block">السائق</span>
                                <span class="font-semibold">{{ $job->defaultPumpDriver->fullname ?? 'غير محدد' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400 text-sm block">تاريخ التخصيص</span>
                                <span
                                    class="font-semibold">{{ $job->pump_assigned_at ? \Carbon\Carbon::parse($job->pump_assigned_at)->format('Y-m-d H:i') : '-' }}</span>
                            </div>
                        </div>
                        @if ($job->pump_notes)
                            <div class="mt-3">
                                <span class="text-gray-600 dark:text-gray-400 text-sm block">ملاحظات</span>
                                <p class="text-sm mt-1">{{ $job->pump_notes }}</p>
                            </div>
                        @endif
                    </div>
                    <form action="{{ route('companyBranch.workJob.removePump', $job->id) }}" method="POST"
                        onsubmit="return confirm('هل تريد إزالة البَم الحالي؟')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            إزالة البَم
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- نموذج تعيين البَم --}}
        <form action="{{ route('companyBranch.workJob.savePump', $job->id) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- البمات المتاحة --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                    <h4 class="text-lg font-semibold mb-4">
                        <span class="text-xl">🚜</span> اختيار البَم
                    </h4>

                    @if ($pumps->count() > 0)
                        <div class="space-y-3">
                            @foreach ($pumps as $pump)
                                <label
                                    class="flex items-center p-4 border rounded-lg 
                                    {{ $pump->is_available ? 'hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-gray-200' : 'bg-gray-100 dark:bg-gray-900 border-gray-300 opacity-60 cursor-not-allowed' }} 
                                    {{ $job->default_pump_id == $pump->id ? 'border-primary bg-primary/5' : '' }}
                                    transition-colors">
                                    <input type="radio" name="pump_id" value="{{ $pump->id }}"
                                        {{ $pump->is_available ? '' : 'disabled' }}
                                        {{ $job->default_pump_id == $pump->id ? 'checked' : '' }}
                                        class="form-radio text-primary" required>
                                    <div class="mr-3 flex-1">
                                        <div class="font-semibold flex items-center gap-2">
                                            {{ $pump->car_number }}
                                            @if ($job->default_pump_id == $pump->id)
                                                <span class="badge bg-primary text-xs">البَم الحالي</span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            {{ $pump->car_model }}
                                            @if ($pump->carType)
                                                - {{ $pump->carType->name }}
                                            @endif
                                        </div>
                                        @if ($pump->driver)
                                            <div class="text-xs text-gray-500 mt-1">
                                                السائق الأساسي: {{ $pump->driver->fullname }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-left">
                                        @if ($pump->is_available)
                                            <span class="badge bg-success/20 text-success text-xs">✓ متاح</span>
                                        @else
                                            <span class="badge bg-danger/20 text-danger text-xs">✗
                                                {{ $pump->status_text }}</span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="font-semibold">لا توجد بمات متاحة</p>
                            <p class="text-sm mt-1">جميع البمات مشغولة أو في الصيانة</p>
                        </div>
                    @endif
                </div>

                {{-- السائق والملاحظات --}}
                <div class="space-y-5">
                    {{-- اختيار السائق --}}
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                        <h4 class="text-lg font-semibold mb-4">
                            <span class="text-xl">👷</span> اختيار السائق (اختياري)
                        </h4>

                        @if ($drivers->count() > 0)
                            <select name="pump_driver_id" class="form-select">
                                <option value="">-- اختر السائق --</option>
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}"
                                        {{ $job->default_pump_driver_id == $driver->id ? 'selected' : '' }}
                                        {{ !$driver->is_available && $job->default_pump_driver_id != $driver->id ? 'disabled' : '' }}
                                        data-status="{{ $driver->status_text }}">
                                        {{ $driver->fullname }}
                                        @if ($driver->employeeType)
                                            ({{ $driver->employeeType->name }})
                                        @endif
                                        @if (!$driver->is_available && $job->default_pump_driver_id != $driver->id)
                                            - ✗ {{ $driver->status_text }}
                                        @elseif ($driver->is_available)
                                            - ✓ متاح
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-2">
                                💡 يمكنك اختيار السائق لاحقاً عند إنشاء الشحنات
                            </p>
                        @else
                            <div class="text-center py-4 text-gray-500">
                                <p class="text-sm">لا يوجد سائقين متاحين</p>
                            </div>
                        @endif
                    </div>

                    {{-- ملاحظات --}}
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                        <label class="text-sm font-semibold mb-2 block">ملاحظات (اختياري)</label>
                        <textarea name="pump_notes" rows="4" class="form-textarea" placeholder="أي ملاحظات حول تخصيص البَم...">{{ $job->pump_notes }}</textarea>
                    </div>

                    {{-- تطبيق على الشحنات --}}
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" name="apply_to_shipments" value="1" checked
                                class="form-checkbox text-primary mt-1">
                            <div class="mr-3">
                                <div class="font-semibold text-sm">تطبيق على جميع الشحنات القادمة</div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    سيتم تعيين هذا البَم لجميع الشحنات الجديدة والمخططة تلقائياً
                                </p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- أزرار الحفظ --}}
            <div class="flex items-center justify-between gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('companyBranch.workJob.view', $job->id) }}" class="btn btn-outline-secondary">
                    إلغاء
                </a>
                <button type="submit" class="btn btn-primary"
                    {{ $pumps->where('is_available', true)->count() == 0 ? 'disabled' : '' }}>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    حفظ البَم
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        // بيانات السائقين المرتبطين بكل مضخة
        const pumpDrivers = {
            @foreach ($pumps as $pump)
                {{ $pump->id }}: {{ $pump->driver_id ?? 'null' }},
            @endforeach
        };

        // حالة توفر السائقين
        const driverAvailability = {
            @foreach ($drivers as $driver)
                {{ $driver->id }}: {{ $driver->is_available ? 'true' : 'false' }},
            @endforeach
        };

        // تأكيد عند تغيير البَم وتحديث السائق
        document.querySelectorAll('input[name="pump_id"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const currentPumpId = {{ $job->default_pump_id ?? 'null' }};
                const selectedPumpId = this.value;

                // تحديث حقل السائق بناءً على المضخة المختارة
                const driverSelect = document.querySelector('select[name="pump_driver_id"]');
                if (driverSelect && pumpDrivers[selectedPumpId]) {
                    const suggestedDriverId = pumpDrivers[selectedPumpId];

                    // التحقق من توفر السائق المقترح
                    if (driverAvailability[suggestedDriverId]) {
                        driverSelect.value = suggestedDriverId;
                    } else {
                        // إذا كان السائق غير متاح، اختر أول سائق متاح
                        const availableOption = Array.from(driverSelect.options).find(opt =>
                            opt.value && !opt.disabled && driverAvailability[opt.value]
                        );
                        driverSelect.value = availableOption ? availableOption.value : '';
                    }
                }

                // تأكيد عند تغيير البَم الحالي
                if (currentPumpId && selectedPumpId != currentPumpId) {
                    const confirmed = confirm('سيتم تغيير البَم الحالي. هل تريد المتابعة؟');
                    if (!confirmed) {
                        // إلغاء التحديد
                        this.checked = false;
                        // تحديد البَم الحالي مرة أخرى
                        document.querySelector(`input[value="${currentPumpId}"]`).checked = true;

                        // إعادة تحديد السائق الخاص بالمضخة الحالية
                        if (driverSelect && pumpDrivers[currentPumpId]) {
                            driverSelect.value = pumpDrivers[currentPumpId];
                        }
                    }
                }
            });
        });

        // تحديد السائق عند تحميل الصفحة إذا كان هناك مضخة محددة
        document.addEventListener('DOMContentLoaded', function() {
            const selectedPump = document.querySelector('input[name="pump_id"]:checked');
            if (selectedPump) {
                const driverSelect = document.querySelector('select[name="pump_driver_id"]');
                const selectedPumpId = selectedPump.value;
                if (driverSelect && pumpDrivers[selectedPumpId]) {
                    const suggestedDriverId = pumpDrivers[selectedPumpId];
                    // فقط حدد السائق إذا كان متاحاً
                    if (driverAvailability[suggestedDriverId]) {
                        driverSelect.value = suggestedDriverId;
                    }
                }
            }
        });
    </script>
@endsection
