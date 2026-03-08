@extends('layouts.app')

@section('page-title', 'إنشاء حساب للموظف: ' . $employee->fullname)

@section('content')
    <div x-data="createAccountForm()" class="max-w-4xl mx-auto">
        <div class="panel mt-6">
            <!-- رأس الصفحة -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        👤 إنشاء حساب مستخدم للموظف
                    </h2>
                    <p class="text-gray-500 text-sm mt-1">قم بإنشاء حساب تسجيل دخول للموظف</p>
                </div>
                <a href="{{ url('Employees/listBranchemployees') }}"
                    class="btn btn-outline-secondary flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
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

            <!-- معلومات الموظف -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">📋 معلومات الموظف</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">الاسم:</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $employee->fullname }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">نوع الموظف:</span>
                        <span class="font-medium text-gray-800 dark:text-white">
                            {{ $employee->employeeType->name ?? 'غير محدد' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500">الشفت:</span>
                        <span class="font-medium text-gray-800 dark:text-white">
                            {{ $employee->shift->name ?? 'غير محدد' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500">الفرع:</span>
                        <span class="font-medium text-gray-800 dark:text-white">
                            {{ $employee->Branchesname->branch_name ?? 'غير محدد' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500">الهاتف:</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $employee->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">البريد الحالي:</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $employee->email ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- فورم إنشاء الحساب -->
            <form action="{{ route('employee.storeAccount', $employee->id) }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- نوع الموظف -->
                    <div class="space-y-3">
                        <label for="emp_type_id" class="block font-medium text-gray-700 dark:text-gray-300">
                            نوع الموظف <span class="text-danger">*</span>
                        </label>
                        <select name="emp_type_id" id="emp_type_id" class="form-select w-full" required>
                            <option value="">-- اختر نوع الموظف --</option>
                            @foreach ($employeeTypes as $type)
                                <option value="{{ $type->id }}"
                                    {{ old('emp_type_id', $employee->employee_types_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('emp_type_id')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- اسم المستخدم -->
                    <div class="space-y-3">
                        <label for="username" class="block font-medium text-gray-700 dark:text-gray-300">
                            اسم المستخدم <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}"
                            placeholder="أدخل اسم المستخدم للدخول" class="form-input w-full" required
                            pattern="[a-zA-Z0-9_\-.]+" title="أحرف إنجليزية وأرقام فقط">
                        @error('username')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- كلمة المرور -->
                    <div class="space-y-3">
                        <label for="password" class="block font-medium text-gray-700 dark:text-gray-300">
                            كلمة المرور <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="password" id="password"
                            placeholder="أدخل كلمة المرور (6 أحرف على الأقل)" class="form-input w-full" required
                            minlength="6">
                        @error('password')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- قسم السائق - يظهر فقط للسائقين -->
                @if ($employee->is_driver)
                    <div
                        class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <h3 class="font-semibold text-blue-700 dark:text-blue-300 mb-4 flex items-center gap-2">
                            🚗 تعيين على آلية (اختياري)
                        </h3>
                        <p class="text-sm text-blue-600 dark:text-blue-400 mb-4">
                            هذا الموظف سائق. يمكنك تعيينه على آلية مباشرة أو تركه بدون تعيين.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- نوع التعيين -->
                            <div class="space-y-3">
                                <label for="assignment_type" class="block font-medium text-gray-700 dark:text-gray-300">
                                    نوع التعيين
                                </label>
                                <select name="assignment_type" id="assignment_type" class="form-select w-full"
                                    x-model="assignmentType" @change="filterCars()">
                                    <option value="">-- بدون تعيين --</option>
                                    <option value="primary">سائق رئيسي</option>
                                    <option value="backup">سائق احتياطي</option>
                                </select>
                            </div>

                            <!-- اختيار الآلية -->
                            <div class="space-y-3" x-show="assignmentType">
                                <label for="car_id" class="block font-medium text-gray-700 dark:text-gray-300">
                                    اختر الآلية
                                </label>
                                <select name="car_id" id="car_id" class="form-select w-full">
                                    <option value="">-- اختر آلية --</option>
                                    @foreach ($availableCars as $car)
                                        <option value="{{ $car->id }}"
                                            data-has-primary="{{ $car->driver_id ? 'true' : 'false' }}"
                                            data-has-backup="{{ $car->backup_driver_id ? 'true' : 'false' }}">
                                            {{ $car->car_number }} - {{ $car->car_model }}
                                            ({{ $car->carType->name ?? 'غير محدد' }})
                                            @if (!$car->driver_id)
                                                <span class="text-green-600">- بدون سائق رئيسي</span>
                                            @endif
                                            @if (!$car->backup_driver_id)
                                                <span class="text-yellow-600">- بدون احتياطي</span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if ($availableCars->isEmpty())
                            <div
                                class="mt-4 p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded text-yellow-700 dark:text-yellow-400 text-sm">
                                ⚠️ لا توجد آليات متاحة للتعيين في الوقت الحالي
                            </div>
                        @endif
                    </div>
                @endif

                <!-- الأزرار -->
                <div
                    class="flex flex-col sm:flex-row justify-end gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ url('Employees/listBranchemployees') }}"
                        class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>إلغاء</span>
                    </a>

                    <button type="submit" class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>إنشاء الحساب</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function createAccountForm() {
            return {
                assignmentType: '',

                filterCars() {
                    const select = document.getElementById('car_id');
                    const options = select.querySelectorAll('option[value]');

                    options.forEach(option => {
                        if (!option.value) return;

                        const hasPrimary = option.dataset.hasPrimary === 'true';
                        const hasBackup = option.dataset.hasBackup === 'true';

                        if (this.assignmentType === 'primary') {
                            option.style.display = hasPrimary ? 'none' : '';
                        } else if (this.assignmentType === 'backup') {
                            option.style.display = hasBackup ? 'none' : '';
                        } else {
                            option.style.display = '';
                        }
                    });

                    // إعادة تعيين الاختيار
                    select.value = '';
                }
            }
        }
    </script>
@endsection
