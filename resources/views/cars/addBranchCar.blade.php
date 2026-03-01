@extends('layouts.app')

@section('page-title', 'إضافة سيارة جديدة')

@section('content')
    <div x-data="addCarForm()" class="panel mt-6">
        <!-- رأس الصفحة -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    🚗 إضافة سيارة جديدة للفرع
                </h2>
                <p class="text-gray-500 text-sm mt-1">قم بإدخال بيانات السيارة الجديدة</p>
            </div>
            <a href="/ConcreteERP/cars/ListBranchCar" class="btn btn-outline-secondary flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>رجوع للقائمة</span>
            </a>
        </div>

        <!-- رسائل النجاح والخطأ -->
        @if (session('success'))
            <div class="alert alert-success flex items-center gap-2 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger flex items-center gap-2 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- فورم الإضافة -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            {!! Form::open([
                'route' => 'cars.store',
                'method' => 'POST',
                'autocomplete' => 'off',
            ]) !!}

            <!-- حقل مخفي للفرع الحالي -->
            <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}">
            <!-- حقل مخفي للتوجيه بعد الإضافة -->
            <input type="hidden" name="redirect_to" value="branch">

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <!-- نوع السيارة -->
                <div class="space-y-3">
                    <label for="car_type_id" class="block font-medium text-gray-700 dark:text-gray-300">
                        نوع السيارة <span class="text-danger">*</span>
                    </label>
                    <select name="car_type_id" id="car_type_id" class="form-select w-full" required
                        x-model="selectedCarType" @change="onCarTypeChange()">
                        <option value="" disabled selected>اختر نوع السيارة</option>
                        @foreach ($carstype as $type)
                            <option value="{{ $type->id }}" data-name="{{ strtolower($type->name) }}"
                                {{ old('car_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('car_type_id')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- اسم السيارة -->
                <div class="space-y-3">
                    <label for="car_name" class="block font-medium text-gray-700 dark:text-gray-300">
                        اسم السيارة <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="car_name" id="car_name" placeholder="مثال: مارسيدس"
                        value="{{ old('car_name') }}" class="form-input w-full" required>
                    @error('car_name')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- رقم السيارة -->
                <div class="space-y-3">
                    <label for="car_number" class="block font-medium text-gray-700 dark:text-gray-300">
                        رقم السيارة <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="car_number" id="car_number" placeholder="أدخل رقم السيارة"
                        value="{{ old('car_number') }}" class="form-input w-full" required>
                    @error('car_number')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- موديل السيارة -->
                <div class="space-y-3">
                    <label for="car_model" class="block font-medium text-gray-700 dark:text-gray-300">
                        موديل السيارة <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="car_model" id="car_model" placeholder="أدخل موديل السيارة"
                        value="{{ old('car_model') }}" class="form-input w-full" required>
                    @error('car_model')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- سعة الخباطة - يظهر فقط عند اختيار خباطة -->
                <div class="space-y-3" x-show="isMixer" x-transition>
                    <label for="mixer_capacity" class="block font-medium text-gray-700 dark:text-gray-300">
                        سعة الخباطة (متر مكعب) <span class="text-danger">*</span>
                    </label>
                    <input type="number" step="0.1" name="mixer_capacity" id="mixer_capacity"
                        placeholder="أدخل سعة الخباطة بالمتر المكعب" value="{{ old('mixer_capacity') }}"
                        class="form-input w-full" :required="isMixer">
                    @error('mixer_capacity')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- قسم اختيار الشفتات -->
                <div
                    class="lg:col-span-2 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-4">🕐 اختر الشفتات للسيارة</h4>
                    <div class="flex flex-wrap gap-3">
                        @isset($shifts)
                            @foreach ($shifts as $shift)
                                <label
                                    class="flex items-center gap-2 p-3 bg-white dark:bg-gray-700 rounded-lg border cursor-pointer hover:border-primary transition-colors"
                                    :class="selectedShifts.includes('{{ $shift->id }}') ? 'border-primary bg-primary/5' :
                                        'border-gray-200 dark:border-gray-600'">
                                    <input type="checkbox" value="{{ $shift->id }}" x-model="selectedShifts"
                                        @change="updateDriverFields()" class="form-checkbox text-primary">
                                    <span class="font-medium">{{ $shift->name }}</span>
                                </label>
                            @endforeach
                        @endisset
                    </div>
                    <p class="text-sm text-gray-500 mt-2">اختر شفت واحد أو أكثر حسب الحاجة</p>
                </div>

                <!-- قسم السائقين - يظهر ديناميكياً حسب الشفتات المختارة -->
                <div class="lg:col-span-2" x-show="selectedShifts.length > 0">
                    <template x-for="(shiftId, index) in selectedShifts" :key="shiftId">
                        <div class="p-4 mb-4 rounded-lg border"
                            :class="index % 2 === 0 ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700' :
                                'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-700'">
                            <h4 class="font-semibold mb-4 flex items-center gap-2"
                                :class="index % 2 === 0 ? 'text-blue-700 dark:text-blue-300' :
                                    'text-yellow-700 dark:text-yellow-300'">
                                <span x-text="'🕐 ' + getShiftName(shiftId)"></span>
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- السائق الرئيسي -->
                                <div class="space-y-3">
                                    <label class="block font-medium text-gray-700 dark:text-gray-300">
                                        🚗 السائق الرئيسي
                                    </label>
                                    <select :name="'drivers[' + shiftId + '][primary]'"
                                        class="form-select w-full driver-select" :data-shift="shiftId"
                                        @change="onDriverChange(shiftId)">
                                        <option value="">اختر السائق الرئيسي</option>
                                        <template x-for="emp in getAvailableDriversForShift(shiftId, 'primary')"
                                            :key="emp.id">
                                            <option :value="emp.id" x-text="emp.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- السائق الاحتياطي -->
                                <div class="space-y-3">
                                    <label class="block font-medium text-gray-700 dark:text-gray-300">
                                        🔄 السائق الاحتياطي <span class="text-gray-400 text-sm">(اختياري)</span>
                                    </label>
                                    <select :name="'drivers[' + shiftId + '][backup]'"
                                        class="form-select w-full driver-select" :data-shift="shiftId"
                                        @change="onDriverChange(shiftId)">
                                        <option value="">-- بدون سائق احتياطي --</option>
                                        <template x-for="emp in getAvailableDriversForShift(shiftId, 'backup')"
                                            :key="emp.id">
                                            <option :value="emp.id" x-text="emp.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- رسالة عند عدم اختيار شفت -->
                <div class="lg:col-span-2 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-center text-gray-500"
                    x-show="selectedShifts.length === 0">
                    <p>⚠️ يرجى اختيار شفت واحد على الأقل لتعيين السائقين</p>
                </div>

                <!-- ملاحظات -->
                <div class="space-y-3 lg:col-span-2">
                    <label for="note" class="block font-medium text-gray-700 dark:text-gray-300">
                        ملاحظات
                    </label>
                    <textarea name="note" id="note" placeholder="أدخل أي ملاحظات" class="form-input w-full" rows="3">{{ old('note') }}</textarea>
                    @error('note')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <!-- الأزرار -->
            <div
                class="flex flex-col sm:flex-row justify-end gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="/ConcreteERP/cars/ListBranchCar"
                    class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>إلغاء</span>
                </a>

                <button type="submit" name="active" value="AddnewCar"
                    class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>حفظ السيارة</span>
                </button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>

    <script>
        function addCarForm() {
            return {
                selectedShifts: [],
                selectedCarType: '{{ old('car_type_id', '') }}',
                isMixer: false,
                shifts: {!! json_encode($shifts->pluck('name', 'id')) !!},
                // الموظفين مجمعين حسب الشفت من جدول employee_shifts
                employeesByShift: {!! json_encode($employeesByShift ?? []) !!},
                // السائقين المعينين حالياً في سيارات أخرى (لمنع التكرار)
                assignedDrivers: {!! json_encode($assignedDrivers ?? []) !!},

                // تتبع السائقين المختارين في هذه السيارة
                currentSelections: {},

                init() {
                    // التحقق عند التحميل إذا كان هناك قيمة قديمة
                    if (this.selectedCarType) {
                        this.onCarTypeChange();
                    }
                },

                onCarTypeChange() {
                    const select = document.getElementById('car_type_id');
                    const selectedOption = select.options[select.selectedIndex];
                    const typeName = selectedOption ? selectedOption.getAttribute('data-name') : '';
                    // التحقق إذا كان النوع خباطة
                    this.isMixer = typeName && (typeName.includes('خباط') || typeName.includes('mixer') || typeName
                        .includes('خلاط'));
                },

                getShiftName(shiftId) {
                    return this.shifts[shiftId] || 'شفت غير معروف';
                },

                // الحصول على السائقين المتاحين لشفت معين مع منع التكرار في نفس الشفت فقط
                getAvailableDriversForShift(shiftId, driverType) {
                    // جلب السائقين المعينين في سيارات أخرى لهذا الشفت
                    const assignedInOtherCars = this.assignedDrivers[shiftId] || [];

                    // جلب السائق المختار حالياً في نفس الشفت (الرئيسي أو الاحتياطي)
                    const currentPrimary = this.getSelectedDriver(shiftId, 'primary');
                    const currentBackup = this.getSelectedDriver(shiftId, 'backup');

                    // جلب الموظفين من الشفت المحدد
                    const shiftEmployees = this.employeesByShift[shiftId] || [];

                    return shiftEmployees.filter(emp => {
                        // لا يمكن تعيينه في سيارة أخرى في نفس الشفت
                        if (assignedInOtherCars.includes(emp.id)) return false;

                        // لا يمكن تعيينه كرئيسي واحتياطي في نفس الشفت
                        if (driverType === 'primary' && currentBackup && emp.id == currentBackup) return false;
                        if (driverType === 'backup' && currentPrimary && emp.id == currentPrimary) return false;

                        return true;
                    });
                },

                // الحصول على السائق المختار حالياً
                getSelectedDriver(shiftId, driverType) {
                    const selectName = `drivers[${shiftId}][${driverType}]`;
                    const select = document.querySelector(`select[name="${selectName}"]`);
                    return select ? select.value : null;
                },

                // عند تغيير اختيار السائق
                onDriverChange(shiftId) {
                    // إعادة رسم القوائم لتحديث الخيارات المتاحة
                    this.$nextTick(() => {
                        // تحديث currentSelections
                        this.currentSelections = {
                            ...this.currentSelections,
                            [shiftId]: {
                                primary: this.getSelectedDriver(shiftId, 'primary'),
                                backup: this.getSelectedDriver(shiftId, 'backup')
                            }
                        };
                    });
                },

                updateDriverFields() {
                    // يتم التحديث تلقائياً بواسطة Alpine.js
                }
            }
        }
    </script>
@endsection
