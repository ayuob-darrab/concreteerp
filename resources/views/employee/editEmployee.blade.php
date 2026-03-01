@extends('layouts.app')

@section('page-title', 'تعديل معلومات الموظف: ' . $employee->fullname)

@section('content')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <h3 class="text-center text-2xl font-bold dark:text-white-light">
            تعديل معلومات الموظف : {{ $employee->fullname }}
        </h3>
    </div>





    {!! Form::open([
        'route' => ['Employees.update', $employee->id],
        'method' => 'PUT',
        'autocomplete' => 'off',
        'files' => true,
    ]) !!}


    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <!-- الفرع -->
        <div class="panel">
            <div class="space-y-3">
                <label for="branch_id" class="inline-flex cursor-pointer">
                    <span class="text-white-dark">الفرع <span class="text-danger">*</span></span>
                </label>
                <select name="branch_id" id="branch_id" class="form-input" required>
                    <option value="" selected disabled>اختر الفرع</option>
                    @foreach ($branches as $item)
                        <option value="{{ $item->id }}" {{ $employee->branch_id == $item->id ? 'selected' : '' }}>
                            {{ $item->branch_name }}</option>
                    @endforeach
                </select>
                @error('branch_id')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- الاسم الكامل -->
        <div class="panel">
            <div class="space-y-3">
                <label for="fullname" class="inline-flex cursor-pointer">
                    <span class="text-white-dark">الاسم الكامل <span class="text-danger">*</span></span>
                </label>
                <input type="text" name="fullname" id="fullname" placeholder="أدخل الاسم الكامل"
                    value="{{ $employee->fullname }}" class="form-input" required>
                @error('fullname')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- نوع الموظف -->
        <div class="panel">
            <div class="space-y-3">
                <label for="employee_types_id" class="inline-flex cursor-pointer">
                    <span class="text-white-dark">نوع الموظف <span class="text-danger">*</span></span>
                </label>
                <select name="employee_types_id" id="employee_types_id" class="form-input" required>
                    <option value="" selected disabled>اختر النوع</option>
                    @foreach ($employeeTypes as $type)
                        <option value="{{ $type->id }}"
                            {{ $employee->employee_types_id == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('employee_types_id')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- شفتات العمل (دعم اختيار أكثر من شفت) -->
        <div class="panel lg:col-span-2">
            <div class="space-y-3">
                <label class="inline-flex cursor-pointer">
                    <span class="text-white-dark">شفتات العمل <span class="text-danger">*</span></span>
                </label>
                <p class="text-xs text-gray-500 mb-2">💡 يمكنك اختيار أكثر من شفت للموظف. الشفت المحدد بـ ⭐ هو الشفت الرئيسي
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @php
                        $currentShiftIds = $currentShiftIds ?? [$employee->shift_id];
                        $primaryShiftId = $primaryShiftId ?? $employee->shift_id;
                    @endphp
                    @foreach ($shiftTimes as $shift)
                        <label
                            class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shift-checkbox-label {{ in_array($shift->id, $currentShiftIds) ? 'bg-primary/10 border-primary' : 'border-gray-200 dark:border-gray-600' }}">
                            <input type="checkbox" name="shift_ids[]" value="{{ $shift->id }}"
                                class="form-checkbox text-primary shift-checkbox"
                                {{ in_array($shift->id, $currentShiftIds) ? 'checked' : '' }}
                                onchange="updateShiftSelection(this)">
                            <div class="flex-1">
                                <div class="font-semibold flex items-center gap-2">
                                    <span
                                        class="primary-star {{ $primaryShiftId == $shift->id ? '' : 'hidden' }} text-yellow-500">⭐</span>
                                    {{ $shift->name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $shift->start_time }} - {{ $shift->end_time }}
                                </div>
                            </div>
                            <button type="button"
                                class="btn btn-xs {{ $primaryShiftId == $shift->id ? 'btn-warning' : 'btn-outline-warning' }} set-primary-btn {{ in_array($shift->id, $currentShiftIds) ? '' : 'hidden' }}"
                                onclick="setPrimaryShift({{ $shift->id }})" title="تعيين كشفت رئيسي">
                                ⭐
                            </button>
                        </label>
                    @endforeach
                </div>

                <!-- حقل مخفي للشفت الرئيسي -->
                <input type="hidden" name="primary_shift_id" id="primary_shift_id" value="{{ $primaryShiftId }}">

                <!-- حقل مخفي للتوافق مع النظام القديم -->
                <input type="hidden" name="shift_id" id="shift_id_legacy" value="{{ $primaryShiftId }}">

                @error('shift_ids')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <!-- رقم الهاتف -->
        <div class="panel">
            <div class="space-y-3">
                <label for="phone" class="inline-flex cursor-pointer">
                    <span class="text-white-dark">رقم الهاتف</span>
                </label>
                <input type="text" name="phone" id="phone" placeholder="أدخل رقم الهاتف"
                    value="{{ $employee->phone }}" class="form-input" maxlength="11">
                <div id="phone-error" class="text-danger text-sm"></div>
                @error('phone')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <script>
            const phoneInput = document.getElementById('phone');
            const phoneError = document.getElementById('phone-error');

            phoneInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, ''); // إزالة أي شيء غير أرقام
                this.value = value; // تحديث الحقل بالأرقام فقط

                // تحقق من طول الرقم
                if (value.length < 11) {
                    phoneError.textContent = 'رقم الهاتف يجب أن يحتوي على 11 رقمًا';
                } else if (value.length > 11) {
                    phoneError.textContent = 'رقم الهاتف لا يمكن أن يزيد عن 11 رقمًا';
                } else {
                    phoneError.textContent = '';
                }
            });
        </script>


        <!-- البريد الإلكتروني -->
        <div class="panel">
            <div class="space-y-3">
                <label for="email" class="inline-flex cursor-pointer">
                    <span class="text-white-dark">البريد الإلكتروني</span>
                </label>
                <input type="email" name="email" id="email" placeholder="example@email.com"
                    value="{{ $employee->email }}" class="form-input">
                @error('email')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <!-- الراتب -->
        <div class="panel">
            <div class="space-y-3">
                <label for="salary" class="inline-flex cursor-pointer">
                    <span class="text-white-dark">الراتب</span>
                </label>
                <div class="flex items-center gap-2">
                    <input type="text" name="salary" id="salary" placeholder="أدخل الراتب"
                        value="{{ $employee->salary }}" class="form-input" maxlength="8" required>
                    <span class="text-sm text-gray-500">IQD</span>
                </div>
                <div id="salary-error" class="text-danger text-sm"></div>
                @error('salary')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <script>
            const salaryInput = document.getElementById('salary');
            const salaryError = document.getElementById('salary-error');

            // دالة لتنسيق الرقم بالفواصل
            function formatNumberWithCommas(num) {
                return num.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }

            // دالة لتحديث حقل الراتب أثناء الكتابة
            salaryInput.addEventListener('input', function() {
                // إزالة أي فواصل
                let value = this.value.replace(/,/g, '');

                // السماح فقط بالأرقام
                value = value.replace(/\D/g, '');

                // التحقق من الحد الأقصى
                if (parseInt(value || 0) > 9999999) {
                    value = '9999999';
                    salaryError.textContent = 'الحد الأقصى هو 9,999,999';
                } else {
                    salaryError.textContent = '';
                }

                // تنسيق الرقم بالفواصل كل 3 أرقام
                this.value = formatNumberWithCommas(value);
            });

            // عند تحميل الصفحة: تنسيق القيمة القديمة
            document.addEventListener('DOMContentLoaded', function() {
                let value = salaryInput.value.replace(/,/g, '');
                if (value && /^\d+$/.test(value)) {
                    salaryInput.value = formatNumberWithCommas(value);
                }
            });
        </script>


        <!-- تاريخ التعيين -->
        <div class="panel">
            <div class="space-y-3">
                <label for="createdate" class="inline-flex cursor-pointer">
                    <span class="text-white-dark">تاريخ التعيين</span>
                </label>
                <input type="date" name="createdate" id="createdate"
                    value="{{ \Carbon\Carbon::parse($employee->createdate)->format('Y-m-d') }}" class="form-input">
                @error('createdate')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- ملف الموظف -->
        <div class="panel col-span-2">
            <div class="space-y-3">
                <label for="file" class="inline-flex cursor-pointer">
                    <span class="text-white-dark">ملف الموظف (PDF، Word، صورة)</span>
                </label>
                <input type="file" name="file" id="file" accept=".pdf,.doc,.docx,.jpg,.png"
                    class="form-input">
                <small class="text-gray-500">الحجم الأقصى: 5MB | الصيغ المسموحة: PDF, DOC, DOCX, JPG, PNG</small>
                @error('file')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- حالة الفرع (مفعل / معطل) -->
        <div class="panel">
            <div class="space-y-3">
                <label class="inline-flex cursor-pointer">
                    <span class="text-white-dark">الحالة</span>
                </label>

                <select name="isactive" id="isactive" class="form-select" required>
                    <option value="1" {{ $employee->isactive == 1 ? 'selected' : '' }}>مفعل</option>
                    <option value="0" {{ $employee->isactive == 0 ? 'selected' : '' }}>تعطل</option>
                </select>

                @error('isactive')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <!-- الأزرار -->
        <div class="panel col-span-2">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
                <button type="submit" name="active" value="editEmployeeInformation"
                    class="btn btn-primary !mt-6 px-8">
                    <i class="fas fa-check-circle me-2"></i> تحديث معلومات الموظف
                </button>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
                <button type="reset" class="btn btn-outline-secondary !mt-6 px-8">
                    <i class="fas fa-times-circle me-2"></i> إلغاء
                </button>
            </div>
        </div>

    </div>

    {!! Form::close() !!}

@endsection

@section('scripts')
    <script>
        // ===== إدارة الشفتات المتعددة =====
        let primaryShiftId = {{ $primaryShiftId ?? 'null' }};

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
                document.getElementById('shift_id_legacy').value = primaryShiftId || checkedBoxes[0].value;
            } else {
                document.getElementById('shift_id_legacy').value = '';
            }
        }
    </script>
@endsection
