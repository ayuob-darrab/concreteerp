@extends('layouts.app')

@section('page-title', 'إضافة موظف جديد')

@section('content')
    <div class="panel mt-6 max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h5 class="font-bold text-lg">
                إضافة موظف جديد : {{ $branches->first()?->Companyname?->name ?? 'الشركة' }}
            </h5>
            <a href="{{ url('Employees/ListEmployees') }}" class="btn btn-outline-secondary">رجوع للقائمة</a>
        </div>

        <form action="{{ route('Employees.store') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="space-y-3">
                    <label for="branch_id" class="block font-medium text-gray-700 dark:text-gray-200">الفرع <span
                            class="text-danger">*</span></label>
                    <select name="branch_id" id="branch_id" class="form-input" required>
                        <option value="">اختر الفرع</option>
                        @foreach ($branches as $item)
                            <option value="{{ $item->id }}" {{ old('branch_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->branch_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-3">
                    <label for="fullname" class="block font-medium text-gray-700 dark:text-gray-200">الاسم الكامل <span
                            class="text-danger">*</span></label>
                    <input type="text" name="fullname" id="fullname" placeholder="أدخل الاسم الكامل"
                        value="{{ old('fullname') }}" class="form-input" required>
                    @error('fullname')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-3">
                    <label for="employee_type_code" class="block font-medium text-gray-700 dark:text-gray-200">نوع الموظف
                        <span class="text-danger">*</span></label>
                    <select name="employee_type_code" id="employee_type_code" class="form-input" required>
                        <option value="">اختر النوع</option>
                        @foreach ($employeeTypes as $type)
                            @continue(blank($type->code))
                            <option value="{{ $type->code }}" {{ old('employee_type_code') == $type->code ? 'selected' : '' }}>
                                {{ $type->name }} — {{ $type->code }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_type_code')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-span-2">
                    <button type="button" id="toggleAccountSection"
                        class="btn btn-outline-primary flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        @if (!is_null($remainingUsers) && (int) $remainingUsers <= 0) disabled @endif>
                        <i class="fas fa-user-plus"></i>
                        <span>إضافة حساب لهذا الموظف (اختياري)</span>
                    </button>
                    @if (!is_null($remainingUsers))
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                            الحسابات المتبقية: <span class="font-bold">{{ $remainingUsers }}</span>
                            <span class="text-gray-400">({{ $activeUsersCount }} / {{ $usersLimit }})</span>
                        </div>
                    @endif
                </div>

                <div id="accountSection" class="col-span-2 hidden border rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
                    <input type="hidden" name="create_account" id="create_account" value="{{ old('create_account', '0') }}">
                    <h6 class="font-semibold mb-4 text-primary">بيانات الحساب</h6>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="username" class="block mb-1 font-medium text-sm">اسم المستخدم <span
                                    class="text-danger">*</span></label>
                            <input id="username" type="text" name="username" value="{{ old('username') }}"
                                placeholder="أدخل اسم المستخدم" class="form-input w-full account-field"
                                pattern="[a-zA-Z0-9_\-.]+" title="أحرف إنجليزية وأرقام فقط">
                            @error('username')
                                <div class="text-danger text-sm">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block mb-1 font-medium text-sm">كلمة المرور <span
                                    class="text-danger">*</span></label>
                            <input id="password" type="text" name="password" minlength="6"
                                placeholder="أدخل كلمة المرور" class="form-input w-full account-field">
                            <p class="text-xs text-gray-500 mt-1">6 أحرف على الأقل</p>
                            @error('password')
                                <div class="text-danger text-sm">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="user_type" class="block mb-1 font-medium text-sm">صلاحيات المستخدم <span
                                    class="text-danger">*</span></label>
                            <select id="user_type" name="user_type" class="form-select w-full account-field">
                                <option value="" selected disabled>اختر الصلاحيات</option>
                                @foreach ($accountUserTypes as $type)
                                    <option value="{{ $type->code }}" {{ old('user_type') == $type->code ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_type')
                                <div class="text-danger text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="space-y-3 col-span-2">
                    <label class="block font-medium text-gray-700 dark:text-gray-200">شفتات العمل <span
                            class="text-danger">*</span></label>
                    <p class="text-xs text-gray-500 mb-2">💡 يمكنك اختيار أكثر من شفت. الشفت المحدد بـ ⭐ هو الرئيسي</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach ($shiftTimes as $shift)
                            <label
                                class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shift-checkbox-label border-gray-200 dark:border-gray-600">
                                <input type="checkbox" name="shift_ids[]" value="{{ $shift->id }}"
                                    class="form-checkbox text-primary shift-checkbox" onchange="updateShiftSelection(this)"
                                    {{ is_array(old('shift_ids')) && in_array($shift->id, old('shift_ids')) ? 'checked' : '' }}>
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

                    <input type="hidden" name="primary_shift_id" id="primary_shift_id" value="{{ old('primary_shift_id') }}">
                    <input type="hidden" name="shift_id" id="shift_id" value="{{ old('shift_id') }}">
                </div>

                <div>
                    <label class="block font-medium text-gray-700 dark:text-gray-200">رقم الهاتف</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="أدخل رقم الهاتف"
                        class="form-input w-full" required pattern="\d{11}" maxlength="11" minlength="11"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                    @error('phone')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                    <small class="text-gray-500">يجب أن يكون الرقم مكوّنًا من 11 رقمًا فقط</small>
                </div>

                <div class="space-y-3">
                    <label for="salary" class="block font-medium text-gray-700 dark:text-gray-200">الراتب</label>
                    <div class="flex items-center gap-2">
                        <input type="text" name="salary" id="salary" placeholder="أدخل الراتب"
                            value="{{ old('salary') }}" class="form-input" maxlength="8" required>
                        <span class="text-sm text-gray-500">IQD</span>
                    </div>
                    <div id="salary-error" class="text-danger text-sm"></div>
                </div>

                <div class="col-span-2 space-y-3">
                    <label for="file" class="block font-medium text-gray-700 dark:text-gray-200">ملف الموظف (PDF فقط)</label>
                    <input type="file" name="file" id="file" accept=".pdf" class="form-input"
                        onchange="validatePDF(this)">
                    <small class="text-gray-500">الحجم الأقصى: 5MB | الصيغة المسموحة: PDF فقط</small>
                    <div id="file-pdf-error" class="text-danger text-sm hidden"></div>
                    @error('file')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-span-2 space-y-3">
                    <label for="personImage" class="block font-medium text-gray-700 dark:text-gray-200">صورة الموظف
                        (JPG, PNG, GIF فقط)</label>
                    <input type="file" name="personImage" id="personImage" accept="image/*" class="form-input"
                        onchange="validateImage(this)">
                    <small class="text-gray-500">الحجم الأقصى: 5MB | الصيغ المسموحة: JPG, PNG, GIF فقط</small>
                    <div id="file-image-error" class="text-danger text-sm hidden"></div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4">
                <a href="{{ url('Employees/ListEmployees') }}"
                    class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                    <i class="fas fa-times-circle"></i>
                    <span>إلغاء</span>
                </a>
                <button type="submit" name="active" value="NewEmployee"
                    class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                    <i class="fas fa-check-circle"></i>
                    <span>إضافة موظف جديد</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        function validatePDF(input) {
            const file = input.files[0];
            const errorLabel = document.getElementById('file-pdf-error');
            errorLabel.classList.add('hidden');
            errorLabel.textContent = '';

            if (file) {
                const fileType = file.type;
                const fileSize = file.size / 1024 / 1024;

                if (fileType !== 'application/pdf') {
                    errorLabel.textContent = 'يسمح فقط بتحميل ملفات PDF.';
                    errorLabel.classList.remove('hidden');
                    input.value = '';
                    return;
                }

                if (fileSize > 5) {
                    errorLabel.textContent = 'حجم الملف يجب أن يكون أقل من 5 ميغابايت.';
                    errorLabel.classList.remove('hidden');
                    input.value = '';
                }
            }
        }

        function validateImage(input) {
            const file = input.files[0];
            const errorDiv = document.getElementById('file-image-error');
            errorDiv.classList.add('hidden');
            errorDiv.textContent = '';

            if (file) {
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                const maxSize = 5 * 1024 * 1024;

                if (!validTypes.includes(file.type)) {
                    errorDiv.textContent = 'يسمح فقط بملفات الصور (JPG, PNG, GIF).';
                    errorDiv.classList.remove('hidden');
                    input.value = '';
                } else if (file.size > maxSize) {
                    errorDiv.textContent = 'حجم الصورة يجب أن لا يتجاوز 5MB.';
                    errorDiv.classList.remove('hidden');
                    input.value = '';
                }
            }
        }

        const salaryInput = document.getElementById('salary');
        const salaryError = document.getElementById('salary-error');
        if (salaryInput) {
            salaryInput.addEventListener('input', function() {
                let value = this.value.replace(/,/g, '');
                if (!/^\d*$/.test(value)) {
                    salaryError.textContent = 'الرجاء إدخال أرقام فقط';
                    value = value.replace(/\D/g, '');
                } else {
                    salaryError.textContent = '';
                }
                if (value.length > 0 && value.length < 5) {
                    salaryError.textContent = 'الحد الأدنى 5 أرقام';
                } else if (parseInt(value || '0', 10) > 9999999) {
                    salaryError.textContent = 'الحد الأقصى 7 أرقام وأقل من 9,999,999';
                }
                this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            });
        }

        let primaryShiftId = document.getElementById('primary_shift_id').value || null;

        function updateShiftSelection(checkbox) {
            const label = checkbox.closest('.shift-checkbox-label');
            const primaryBtn = label.querySelector('.set-primary-btn');
            const primaryStar = label.querySelector('.primary-star');

            if (checkbox.checked) {
                label.classList.add('bg-primary/10', 'border-primary');
                label.classList.remove('border-gray-200', 'dark:border-gray-600');
                primaryBtn.classList.remove('hidden');

                const checkedBoxes = document.querySelectorAll('.shift-checkbox:checked');
                if (checkedBoxes.length === 1 || !primaryShiftId) {
                    setPrimaryShift(checkbox.value);
                }
            } else {
                label.classList.remove('bg-primary/10', 'border-primary');
                label.classList.add('border-gray-200', 'dark:border-gray-600');
                primaryBtn.classList.add('hidden');
                primaryStar.classList.add('hidden');

                if (primaryShiftId == checkbox.value) {
                    const checkedBoxes = document.querySelectorAll('.shift-checkbox:checked');
                    if (checkedBoxes.length > 0) {
                        setPrimaryShift(checkedBoxes[0].value);
                    } else {
                        primaryShiftId = null;
                        document.getElementById('primary_shift_id').value = '';
                        document.getElementById('shift_id').value = '';
                    }
                }
            }

            updateLegacyShiftId();
        }

        function setPrimaryShift(shiftId) {
            primaryShiftId = shiftId;
            document.getElementById('primary_shift_id').value = shiftId;
            document.getElementById('shift_id').value = shiftId;

            document.querySelectorAll('.primary-star').forEach((star) => star.classList.add('hidden'));
            document.querySelectorAll('.set-primary-btn').forEach((btn) => {
                btn.classList.remove('btn-warning');
                btn.classList.add('btn-outline-warning');
            });

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
                document.getElementById('shift_id').value = primaryShiftId || checkedBoxes[0].value;
            } else {
                document.getElementById('shift_id').value = '';
            }
        }

        document.querySelectorAll('.shift-checkbox').forEach((checkbox) => {
            if (checkbox.checked) {
                updateShiftSelection(checkbox);
            }
        });

        const accountSection = document.getElementById('accountSection');
        const accountToggleBtn = document.getElementById('toggleAccountSection');
        const createAccountInput = document.getElementById('create_account');
        const accountFields = document.querySelectorAll('.account-field');
        const accountLimitReached = {{ !is_null($remainingUsers) && (int) $remainingUsers <= 0 ? 'true' : 'false' }};

        function setAccountSectionState(enabled) {
            accountSection.classList.toggle('hidden', !enabled);
            createAccountInput.value = enabled ? '1' : '0';
            accountFields.forEach((field) => {
                field.required = enabled;
            });
        }

        accountToggleBtn.addEventListener('click', function() {
            if (accountLimitReached || accountToggleBtn.disabled) {
                setAccountSectionState(false);
                return;
            }
            const isEnabled = createAccountInput.value === '1';
            setAccountSectionState(!isEnabled);
        });

        if (accountLimitReached) {
            setAccountSectionState(false);
        } else {
            setAccountSectionState(createAccountInput.value === '1');
        }
    </script>
@endsection
