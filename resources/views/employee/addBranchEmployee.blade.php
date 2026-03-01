@extends('layouts.app')

@section('page-title', 'إضافة موظف جديد')

@section('content')
    <div class="panel mt-6">
        <!-- رأس الصفحة -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    👷 إضافة موظف جديد للفرع
                </h2>
                <p class="text-gray-500 text-sm mt-1">قم بإدخال بيانات الموظف الجديد</p>
            </div>
            <a href="/ConcreteERP/Employees/listBranchemployees" class="btn btn-outline-secondary flex items-center gap-2">
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
                'route' => 'Employees.store',
                'method' => 'POST',
                'autocomplete' => 'off',
                'files' => true,
            ]) !!}

            <!-- حقل مخفي للفرع الحالي -->
            <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}">
            <!-- حقل مخفي للتوجيه بعد الإضافة -->
            <input type="hidden" name="redirect_to" value="branch">

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <!-- الاسم الكامل -->
                <div class="space-y-3">
                    <label for="fullname" class="block font-medium text-gray-700 dark:text-gray-300">
                        الاسم الكامل <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="fullname" id="fullname" placeholder="أدخل الاسم الكامل"
                        value="{{ old('fullname') }}" class="form-input w-full" required>
                    @error('fullname')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- نوع الموظف -->
                <div class="space-y-3">
                    <label for="employee_types_id" class="block font-medium text-gray-700 dark:text-gray-300">
                        نوع الموظف <span class="text-danger">*</span>
                    </label>
                    <select name="employee_types_id" id="employee_types_id" class="form-select w-full" required>
                        <option value="" disabled selected>اختر نوع الموظف</option>
                        @foreach ($employeeTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ old('employee_types_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_types_id')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- شفتات العمل (دعم اختيار أكثر من شفت) -->
                <div class="space-y-3 lg:col-span-2">
                    <label class="block font-medium text-gray-700 dark:text-gray-300">
                        شفتات العمل <span class="text-danger">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-2">💡 يمكنك اختيار أكثر من شفت للموظف. الشفت المحدد بـ ⭐ هو الشفت
                        الرئيسي</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach ($shiftTimes as $shift)
                            <label
                                class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shift-checkbox-label {{ in_array($shift->id, old('shift_ids', [])) ? 'bg-primary/10 border-primary' : 'border-gray-200 dark:border-gray-600' }}">
                                <input type="checkbox" name="shift_ids[]" value="{{ $shift->id }}"
                                    class="form-checkbox text-primary shift-checkbox"
                                    {{ in_array($shift->id, old('shift_ids', [])) ? 'checked' : '' }}
                                    onchange="updateShiftSelection(this)">
                                <div class="flex-1">
                                    <div class="font-semibold flex items-center gap-2">
                                        <span class="primary-star hidden text-yellow-500">⭐</span>
                                        {{ $shift->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $shift->start_time }} - {{ $shift->end_time }}
                                    </div>
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-warning set-primary-btn hidden"
                                    onclick="setPrimaryShift({{ $shift->id }})" title="تعيين كشفت رئيسي">
                                    ⭐
                                </button>
                            </label>
                        @endforeach
                    </div>

                    <!-- حقل مخفي للشفت الرئيسي -->
                    <input type="hidden" name="primary_shift_id" id="primary_shift_id"
                        value="{{ old('primary_shift_id') }}">

                    <!-- حقل مخفي للتوافق مع النظام القديم -->
                    <input type="hidden" name="shift_id" id="shift_id_legacy" value="{{ old('shift_id') }}">

                    @error('shift_ids')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- رقم الهاتف -->
                <div class="space-y-3">
                    <label for="phone" class="block font-medium text-gray-700 dark:text-gray-300">
                        رقم الهاتف <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="phone" id="phone" placeholder="أدخل رقم الهاتف"
                        value="{{ old('phone') }}" class="form-input w-full" required pattern="\d{11}" maxlength="11"
                        minlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                    <small class="text-gray-500">يجب أن يكون الرقم مكوّنًا من 11 رقمًا فقط</small>
                    @error('phone')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- البريد الإلكتروني -->
                <div class="space-y-3">
                    <label for="email" class="block font-medium text-gray-700 dark:text-gray-300">
                        البريد الإلكتروني
                    </label>
                    <input type="email" name="email" id="email" placeholder="example@email.com"
                        value="{{ old('email') }}" class="form-input w-full">
                    @error('email')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الراتب -->
                <div class="space-y-3">
                    <label for="salary" class="block font-medium text-gray-700 dark:text-gray-300">
                        الراتب <span class="text-danger">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="text" name="salary" id="salary" placeholder="أدخل الراتب"
                            value="{{ old('salary') }}" class="form-input flex-1" maxlength="15" required
                            oninput="formatSalary(this)">
                        <span class="text-sm text-gray-500 font-medium">IQD</span>
                    </div>
                    @error('salary')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- ملف الموظف -->
                <div class="space-y-3">
                    <label for="file" class="block font-medium text-gray-700 dark:text-gray-300">
                        ملف الموظف (PDF فقط)
                    </label>
                    <input type="file" name="file" id="file" accept=".pdf" class="form-input w-full"
                        onchange="validatePDF(this)">
                    <small class="text-gray-500">الحجم الأقصى: 5MB | الصيغة المسموحة: PDF فقط</small>
                    <div id="file-error" class="text-danger text-sm hidden"></div>
                    @error('file')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- صورة الموظف -->
                <div class="space-y-3">
                    <label for="personImage" class="block font-medium text-gray-700 dark:text-gray-300">
                        صورة الموظف
                    </label>
                    <input type="file" name="personImage" id="personImage" accept="image/*"
                        class="form-input w-full" onchange="validateImage(this)">
                    <small class="text-gray-500">الصيغ المسموحة: JPG, PNG, GIF | الحجم الأقصى: 5MB</small>
                    <div id="image-error" class="text-danger text-sm hidden"></div>
                    @error('personImage')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <!-- الأزرار -->
            <div
                class="flex flex-col sm:flex-row justify-end gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="/ConcreteERP/Employees/listBranchemployees"
                    class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>إلغاء</span>
                </a>

                <button type="submit" name="active" value="NewEmployee"
                    class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>حفظ الموظف</span>
                </button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>

    <script>
        // تنسيق الراتب بالفواصل
        function formatSalary(input) {
            let value = input.value.replace(/[^0-9]/g, '');
            input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        // التحقق من ملف PDF
        function validatePDF(input) {
            const file = input.files[0];
            const errorLabel = document.getElementById('file-error');
            errorLabel.classList.add('hidden');
            errorLabel.textContent = '';

            if (file) {
                const fileType = file.type;
                const fileSize = file.size / 1024 / 1024;

                if (fileType !== 'application/pdf') {
                    errorLabel.textContent = '❌ يُسمح فقط بتحميل ملفات PDF.';
                    errorLabel.classList.remove('hidden');
                    input.value = '';
                    return;
                }

                if (fileSize > 5) {
                    errorLabel.textContent = '❌ حجم الملف يجب أن يكون أقل من 5 ميغابايت.';
                    errorLabel.classList.remove('hidden');
                    input.value = '';
                    return;
                }
            }
        }

        // التحقق من الصورة
        function validateImage(input) {
            const file = input.files[0];
            const errorDiv = document.getElementById('image-error');
            errorDiv.classList.add('hidden');
            errorDiv.textContent = '';

            if (file) {
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                const maxSize = 5 * 1024 * 1024;

                if (!validTypes.includes(file.type)) {
                    errorDiv.textContent = '❌ يُسمح فقط بملفات الصور (JPG, PNG, GIF).';
                    errorDiv.classList.remove('hidden');
                    input.value = '';
                } else if (file.size > maxSize) {
                    errorDiv.textContent = '❌ حجم الصورة يجب أن لا يتجاوز 5MB.';
                    errorDiv.classList.remove('hidden');
                    input.value = '';
                }
            }
        }

        // ===== إدارة الشفتات المتعددة =====
        let primaryShiftId = null;

        function updateShiftSelection(checkbox) {
            const label = checkbox.closest('.shift-checkbox-label');
            const primaryBtn = label.querySelector('.set-primary-btn');
            const primaryStar = label.querySelector('.primary-star');

            if (checkbox.checked) {
                label.classList.add('bg-primary/10', 'border-primary');
                label.classList.remove('border-gray-200', 'dark:border-gray-600');
                primaryBtn.classList.remove('hidden');

                // إذا لم يكن هناك شفت رئيسي، اجعل هذا الشفت رئيسياً
                const checkedBoxes = document.querySelectorAll('.shift-checkbox:checked');
                if (checkedBoxes.length === 1 || !primaryShiftId) {
                    setPrimaryShift(checkbox.value);
                }
            } else {
                label.classList.remove('bg-primary/10', 'border-primary');
                label.classList.add('border-gray-200', 'dark:border-gray-600');
                primaryBtn.classList.add('hidden');
                primaryStar.classList.add('hidden');

                // إذا كان هذا هو الشفت الرئيسي، اختر شفتاً آخر
                if (primaryShiftId == checkbox.value) {
                    const checkedBoxes = document.querySelectorAll('.shift-checkbox:checked');
                    if (checkedBoxes.length > 0) {
                        setPrimaryShift(checkedBoxes[0].value);
                    } else {
                        primaryShiftId = null;
                        document.getElementById('primary_shift_id').value = '';
                        document.getElementById('shift_id_legacy').value = '';
                    }
                }
            }

            updateLegacyShiftId();
        }

        function setPrimaryShift(shiftId) {
            primaryShiftId = shiftId;
            document.getElementById('primary_shift_id').value = shiftId;
            document.getElementById('shift_id_legacy').value = shiftId;

            // إزالة النجمة من جميع الشفتات
            document.querySelectorAll('.primary-star').forEach(star => star.classList.add('hidden'));
            document.querySelectorAll('.set-primary-btn').forEach(btn => {
                btn.classList.remove('btn-warning');
                btn.classList.add('btn-outline-warning');
            });

            // إضافة النجمة للشفت المحدد
            const checkbox = document.querySelector(`.shift-checkbox[value="${shiftId}"]`);
            if (checkbox && checkbox.checked) {
                const label = checkbox.closest('.shift-checkbox-label');
                label.querySelector('.primary-star').classList.remove('hidden');
                const btn = label.querySelector('.set-primary-btn');
                btn.classList.add('btn-warning');
                btn.classList.remove('btn-outline-warning');
            }
        }

        function updateLegacyShiftId() {
            const checkedBoxes = document.querySelectorAll('.shift-checkbox:checked');
            if (checkedBoxes.length > 0) {
                // استخدم الشفت الرئيسي أو الأول
                document.getElementById('shift_id_legacy').value = primaryShiftId || checkedBoxes[0].value;
            } else {
                document.getElementById('shift_id_legacy').value = '';
            }
        }

        // تهيئة عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            const checkedBoxes = document.querySelectorAll('.shift-checkbox:checked');
            checkedBoxes.forEach(cb => updateShiftSelection(cb));
        });
    </script>
@endsection
