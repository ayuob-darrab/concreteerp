@extends('layouts.app')

@section('page-title', 'اضافة موظف جديد')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <div class="flex items-center justify-between mb-5 md:absolute md:top-[25px] md:w-full md:pr-4">


                <div x-data="employeeModal()" class="relative">
                    <!-- زر فتح المودال -->
                    <button type="button" class="btn btn-primary flex items-center gap-2" @click="openModal = true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>إضافة موظف </span>
                    </button>

                    <!-- المودال -->
                    <div x-show="openModal" x-cloak
                        class="fixed inset-0 z-50 flex items-start justify-center pt-10 bg-black/50 overflow-y-auto">
                        <div x-show="openModal" x-transition
                            class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-5xl shadow-2xl border border-gray-200 dark:border-gray-700 m-4">

                            <!-- رأس المودال -->
                            <div class="flex justify-between items-center p-4 border-b bg-indigo-100 dark:bg-indigo-900">
                                <h5
                                    class="font-bold text-lg text-center w-full text-gray-50 dark:text-white bg-gray-700 dark:bg-gray-900 py-3 rounded-lg shadow-md">
                                    إضافة موظف جديد : {{ $branches[0]->Companyname->name }}</h5>

                            </div>

                            <!-- محتوى المودال -->
                            <div class="p-6">
                                {!! Form::open([
                                    'route' => 'Employees.store',
                                    'method' => 'POST',
                                    'autocomplete' => 'off',
                                    'files' => true,
                                ]) !!}

                                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                    <!-- الفرع -->
                                    <div class="space-y-3">
                                        <label for="branch_id"
                                            class="block font-medium text-gray-700 dark:text-gray-200">الفرع <span
                                                class="text-danger">*</span></label>
                                        <select name="branch_id" id="branch_id" class="form-input" required>
                                            <option value="">اختر الفرع</option>
                                            @foreach ($branches as $item)
                                                <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الاسم الكامل -->
                                    <div class="space-y-3">
                                        <label for="fullname"
                                            class="block font-medium text-gray-700 dark:text-gray-200">الاسم الكامل <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="fullname" id="fullname" placeholder="أدخل الاسم الكامل"
                                            value="{{ old('fullname') }}" class="form-input" required>
                                        @error('fullname')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- نوع الموظف -->
                                    <div class="space-y-3">
                                        <label for="employee_types_id"
                                            class="block font-medium text-gray-700 dark:text-gray-200">نوع الموظف <span
                                                class="text-danger">*</span></label>
                                        <select name="employee_types_id" id="employee_types_id" class="form-input" required>
                                            <option value="">اختر النوع</option>
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

                                    <!-- شفتات العمل -->
                                    <div class="space-y-3 col-span-2">
                                        <label class="block font-medium text-gray-700 dark:text-gray-200">شفتات العمل <span
                                                class="text-danger">*</span></label>
                                        <p class="text-xs text-gray-500 mb-2">💡 يمكنك اختيار أكثر من شفت للموظف. الشفت
                                            المحدد بـ ⭐ هو الشفت الرئيسي</p>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach ($shiftTimes as $shift)
                                                <label
                                                    class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shift-checkbox-label border-gray-200 dark:border-gray-600">
                                                    <input type="checkbox" name="shift_ids[]" value="{{ $shift->id }}"
                                                        class="form-checkbox text-primary shift-checkbox"
                                                        onchange="updateShiftSelectionModal(this)">
                                                    <div class="flex-1">
                                                        <div class="font-semibold flex items-center gap-2">
                                                            <span class="primary-star hidden text-yellow-500">⭐</span>
                                                            {{ $shift->name }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $shift->start_time }} - {{ $shift->end_time }}
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                        class="btn btn-xs btn-outline-warning set-primary-btn hidden"
                                                        onclick="setPrimaryShiftModal({{ $shift->id }})"
                                                        title="تعيين كشفت رئيسي">
                                                        ⭐
                                                    </button>
                                                </label>
                                            @endforeach
                                        </div>

                                        <!-- حقول مخفية -->
                                        <input type="hidden" name="primary_shift_id" id="primary_shift_id_modal"
                                            value="">
                                        <input type="hidden" name="shift_id" id="shift_id_modal" value="">

                                        @error('shift_ids')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- رقم الهاتف -->
                                    <div>
                                        <label class="block font-medium text-gray-700 dark:text-gray-200">رقم
                                            الهاتف</label>
                                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                            placeholder="أدخل رقم الهاتف" class="form-input w-full" required
                                            pattern="\d{11}" maxlength="11" minlength="11"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                                        @error('phone')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                        <small class="text-gray-500">يجب أن يكون الرقم مكوّنًا من 11 رقمًا فقط</small>
                                    </div>

                                    <!-- البريد الإلكتروني -->
                                    <div class="space-y-3">
                                        <label for="email"
                                            class="block font-medium text-gray-700 dark:text-gray-200">البريد
                                            الإلكتروني</label>
                                        <input type="email" name="email" id="email"
                                            placeholder="example@email.com" value="{{ old('email') }}"
                                            class="form-input">
                                        @error('email')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الراتب -->
                                    <div class="space-y-3">
                                        <label for="salary"
                                            class="block font-medium text-gray-700 dark:text-gray-200">الراتب</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" name="salary" id="salary"
                                                placeholder="أدخل الراتب" value="{{ old('salary') }}" class="form-input"
                                                maxlength="8" required>
                                            <span class="text-sm text-gray-500">IQD</span>
                                        </div>
                                        <div id="salary-error" class="text-danger text-sm"></div>
                                        @error('salary')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>



                                    <!-- ملف الموظف (PDF فقط) -->
                                    <div class="col-span-2 space-y-3">
                                        <label for="file" class="block font-medium text-gray-700 dark:text-gray-200">
                                            ملف الموظف (PDF فقط)
                                        </label>

                                        <input type="file" name="file" id="file" accept=".pdf"
                                            class="form-input" onchange="validatePDF(this)">

                                        <small class="text-gray-500">
                                            الحجم الأقصى: 5MB | الصيغة المسموحة: PDF فقط
                                        </small>

                                        <div id="file-error" class="text-danger text-sm hidden"></div>

                                        @error('file')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <script>
                                        function validatePDF(input) {
                                            const file = input.files[0];
                                            const errorLabel = document.getElementById('file-error');
                                            errorLabel.classList.add('hidden');
                                            errorLabel.textContent = '';

                                            if (file) {
                                                const fileType = file.type;
                                                const fileSize = file.size / 1024 / 1024; // بالميغابايت

                                                // التحقق من الصيغة
                                                if (fileType !== 'application/pdf') {
                                                    errorLabel.textContent = 'يُسمح فقط بتحميل ملفات PDF.';
                                                    errorLabel.classList.remove('hidden');
                                                    input.value = ''; // إلغاء الاختيار
                                                    return;
                                                }

                                                // التحقق من الحجم
                                                if (fileSize > 5) {
                                                    errorLabel.textContent = 'حجم الملف يجب أن يكون أقل من 5 ميغابايت.';
                                                    errorLabel.classList.remove('hidden');
                                                    input.value = ''; // إلغاء الاختيار
                                                    return;
                                                }
                                            }
                                        }
                                    </script>

                                    <div class="col-span-2 space-y-3">
                                        <label for="file" class="block font-medium text-gray-700 dark:text-gray-200">
                                            صورة الموظف (JPG, PNG, GIF فقط)
                                        </label>

                                        <input type="file" name="personImage" id="file" accept="image/*"
                                            class="form-input" onchange="validateImage(this)">

                                        <small class="text-gray-500">
                                            الحجم الأقصى: 5MB | الصيغ المسموحة: JPG, PNG, GIF فقط
                                        </small>

                                        <div id="file-error" class="text-danger text-sm hidden"></div>

                                        @error('file')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <script>
                                        function validateImage(input) {
                                            const file = input.files[0];
                                            const errorDiv = document.getElementById('file-error');
                                            errorDiv.classList.add('hidden');
                                            errorDiv.textContent = '';

                                            if (file) {
                                                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                                                const maxSize = 5 * 1024 * 1024; // 5MB

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
                                    </script>


                                    <div class="space-y-3">
                                        <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4">
                                            <button type="reset" @click="openModal = false"
                                                class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                                <i class="fas fa-times-circle"></i>
                                                <span>إلغاء</span>
                                            </button>

                                            <button type="submit" name="active" value="NewEmployee"
                                                class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                                <i class="fas fa-check-circle"></i>
                                                <span> اضافة موظف جديد</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- الأزرار -->


                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- سكريبت المودال -->
                <script>
                    document.addEventListener('alpine:init', () => {
                        Alpine.data('employeeModal', () => ({
                            openModal: false
                        }));
                    });

                    // التحقق من الراتب
                    const salaryInput = document.getElementById('salary');
                    const salaryError = document.getElementById('salary-error');
                    if (salaryInput) {
                        salaryInput.addEventListener('input', function(e) {
                            let value = this.value.replace(/,/g, '');
                            if (!/^\d*$/.test(value)) {
                                salaryError.textContent = 'الرجاء إدخال أرقام فقط';
                                value = value.replace(/\D/g, '');
                            } else {
                                salaryError.textContent = '';
                            }
                            if (value.length > 0 && value.length < 5) {
                                salaryError.textContent = 'الحد الأدنى 5 أرقام';
                            } else if (parseInt(value) > 9999999) {
                                salaryError.textContent = 'الحد الأقصى 7 أرقام وأقل من 9,999,999';
                            }
                            this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                        });
                    }

                    // ===== إدارة الشفتات المتعددة في المودال =====
                    let primaryShiftIdModal = null;

                    function updateShiftSelectionModal(checkbox) {
                        const label = checkbox.closest('.shift-checkbox-label');
                        const primaryBtn = label.querySelector('.set-primary-btn');
                        const primaryStar = label.querySelector('.primary-star');

                        if (checkbox.checked) {
                            label.classList.add('bg-primary/10', 'border-primary');
                            label.classList.remove('border-gray-200', 'dark:border-gray-600');
                            primaryBtn.classList.remove('hidden');

                            const checkedBoxes = document.querySelectorAll('.shift-checkbox:checked');
                            if (checkedBoxes.length === 1 || !primaryShiftIdModal) {
                                setPrimaryShiftModal(checkbox.value);
                            }
                        } else {
                            label.classList.remove('bg-primary/10', 'border-primary');
                            label.classList.add('border-gray-200', 'dark:border-gray-600');
                            primaryBtn.classList.add('hidden');
                            primaryStar.classList.add('hidden');

                            if (primaryShiftIdModal == checkbox.value) {
                                const checkedBoxes = document.querySelectorAll('.shift-checkbox:checked');
                                if (checkedBoxes.length > 0) {
                                    setPrimaryShiftModal(checkedBoxes[0].value);
                                } else {
                                    primaryShiftIdModal = null;
                                    document.getElementById('primary_shift_id_modal').value = '';
                                    document.getElementById('shift_id_modal').value = '';
                                }
                            }
                        }

                        updateLegacyShiftIdModal();
                    }

                    function setPrimaryShiftModal(shiftId) {
                        primaryShiftIdModal = shiftId;
                        document.getElementById('primary_shift_id_modal').value = shiftId;
                        document.getElementById('shift_id_modal').value = shiftId;

                        document.querySelectorAll('.primary-star').forEach(star => star.classList.add('hidden'));
                        document.querySelectorAll('.set-primary-btn').forEach(btn => {
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

                    function updateLegacyShiftIdModal() {
                        const checkedBoxes = document.querySelectorAll('.shift-checkbox:checked');
                        if (checkedBoxes.length > 0) {
                            document.getElementById('shift_id_modal').value = primaryShiftIdModal || checkedBoxes[0].value;
                        } else {
                            document.getElementById('shift_id_modal').value = '';
                        }
                    }
                </script>




            </div>
            <table id="myTable2" class="table-striped whitespace-nowrap w-full">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    قائمة الموظفين : {{ $branches[0]->Companyname->name }}
                </caption>
            </table>
        </div>
    </div>


    <script>
        const baseUrl = '{{ url('/') }}';
        document.addEventListener('alpine:init', () => {
            Alpine.data('multipleTable', () => ({
                datatable2: null,

                init() {
                    const tableData = {!! json_encode(
                        $employees->map(function ($emp) {
                            // جمع الشفتات من الجدول الجديد
                            $shifts = $emp->activeShifts->map(function ($es) {
                                    $name = $es->shift ? $es->shift->name : 'غير محدد';
                                    return $es->is_primary ? "⭐ {$name}" : $name;
                                })->toArray();
                    
                            // fallback للنظام القديم
                            if (empty($shifts) && $emp->shift) {
                                $shifts = [$emp->shift->name];
                            }
                    
                            return [
                                'id' => $emp->id,
                                'fullname' => $emp->fullname,
                                'email' => $emp->email,
                                'branch' => $emp->Branchesname ? $emp->Branchesname->branch_name : '-',
                                'employee_type' => $emp->employeeType ? $emp->employeeType->name : 'لا يوجد',
                                'shifts' => $shifts,
                                'shift' => implode(' ، ', $shifts) ?: 'لا يوجد',
                                'phone' => $emp->phone ?? 'لا يوجد',
                                'createdate' => $emp->createdate ?? 'لا يوجد',
                                'isactive' => $emp->isactive ? 'مفعل' : 'معطل',
                            ];
                        }),
                    ) !!};

                    // بناء الصفوف: إضافة عمودي عرض + تعديل
                    const rows = tableData.map(emp => [
                        emp.fullname,
                        emp.email,
                        emp.branch,
                        emp.employee_type,
                        emp.shift,
                        emp.phone,
                        emp.createdate,
                        emp.isactive,
                        emp.id, // زر عرض التفاصيل
                        emp.id // زر التعديل
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'الاسم الكامل',
                                'الايميل',
                                'الفرع',
                                'نوع الموظف',
                                'الشفت',
                                'رقم الهاتف',
                                'تاريخ التعيين',
                                'نشط',
                                'عرض',
                                'تعديل'
                            ],
                            data: rows,
                        },

                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],

                        columns: [
                            // زر العرض
                            {
                                select: 8,
                                sortable: false,
                                render: (id) => {
                                    const viewUrl =
                                        `${baseUrl}/Employees/${id}&ViewEmployeeDetails/edit`;
                                    return `
                                    <a href="${viewUrl}" class="text-green-600 hover:text-green-800" x-tooltip="عرض التفاصيل">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mx-auto">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                            <circle cx="12" cy="12" r="3" fill="currentColor" />
                                        </svg>
                                    </a>
                                `;
                                }
                            },

                            // زر التعديل
                            {
                                select: 9,
                                sortable: false,
                                render: (id) => {
                                    const editUrl =
                                        `${baseUrl}/Employees/${id}&EditEmployee/edit`;
                                    return `
                                    <a href="${editUrl}" class="text-blue-600 hover:text-blue-800" x-tooltip="تعديل">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mx-auto">
                                            <path d="M15.2869 3.15178L14.3601 4.07866L5.83882 12.5999C5.26166 13.1771 
                                                     4.97308 13.4656 4.7249 13.7838C4.43213 14.1592 4.18114 14.5653 
                                                     3.97634 14.995C3.80273 15.3593 3.67368 15.7465 3.41556 16.5208
                                                     L2.32181 19.8021L2.05445 20.6042C1.92743 20.9852 2.0266 21.4053 
                                                     2.31063 21.6894C2.59466 21.9734 3.01478 22.0726 3.39584 21.9456
                                                     L4.19792 21.6782L7.47918 20.5844C8.25353 20.3263 8.6407 20.1973 
                                                     9.00498 20.0237C9.43469 19.8189 9.84082 19.5679 10.2162 19.2751
                                                     C10.5344 19.0269 10.8229 18.7383 11.4001 18.1612L19.9213 9.63993
                                                     L20.8482 8.71306C22.3839 7.17735 22.3839 4.68748 20.8482 3.15178
                                                 C19.3125 1.61607 16.8226 1.61607 15.2869 3.15178Z"
                                                  stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </a>
                                `;
                                }
                            }
                        ],

                        firstLast: true,
                        labels: {
                            perPage: '{select}'
                        },
                        layout: {
                            top: '{search}',
                            bottom: '{info}{select}{pager}'
                        },
                    });
                },
            }));
        });
    </script>

@endsection
