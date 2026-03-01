@extends('layouts.app')

@section('page-title', 'إنشاء سلفة جديدة')

@section('content')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light text-center">
                    <span>إنشاء سلفة جديدة</span>
                </h5>
            </div>

            @if (session('error'))
                <div class="alert alert-danger mb-5">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('advances.store') }}" method="POST" autocomplete="off" id="advanceForm">
                @csrf

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                    <!-- نوع المستفيد -->
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">نوع المستفيد <span class="text-danger">*</span></span>
                        </label>
                        <select name="beneficiary_type" id="beneficiary_type" class="form-select" required>
                            <option value="">اختر نوع المستفيد</option>
                            <option value="employee" {{ old('beneficiary_type') == 'employee' ? 'selected' : '' }}>موظف
                            </option>
                            <option value="contractor" {{ old('beneficiary_type') == 'contractor' ? 'selected' : '' }}>مقاول
                            </option>
                            <option value="supplier" {{ old('beneficiary_type') == 'supplier' ? 'selected' : '' }}>مورد
                            </option>
                        </select>
                        @error('beneficiary_type')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- المستفيد -->
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark" id="beneficiary_label">المستفيد <span
                                    class="text-danger">*</span></span>
                        </label>
                        <select name="beneficiary_id" id="beneficiary_id" class="form-select" required>
                            <option value="">اختر نوع المستفيد أولاً</option>
                        </select>
                        @error('beneficiary_id')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- مبلغ السلفة -->
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">مبلغ السلفة (د.ع) <span class="text-danger">*</span></span>
                        </label>
                        <input type="text" name="amount" id="amount" placeholder="أدخل مبلغ السلفة"
                            class="form-input" inputmode="numeric" required oninput="formatNumber(this)"
                            value="{{ old('amount') }}">
                        @error('amount')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- طريقة السداد -->
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">طريقة السداد <span class="text-danger">*</span></span>
                        </label>
                        <select name="deduction_type" id="deduction_type" class="form-select" required>
                            <option value="percentage"
                                {{ old('deduction_type', 'percentage') == 'percentage' ? 'selected' : '' }}>نسبة مئوية %
                            </option>
                            <option value="fixed" {{ old('deduction_type') == 'fixed' ? 'selected' : '' }}>قسط ثابت
                            </option>
                        </select>
                        @error('deduction_type')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- قيمة الاستقطاع -->
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark" id="deduction_label">نسبة الاستقطاع <span
                                    class="text-danger">*</span></span>
                        </label>
                        <input type="number" name="deduction_value" id="deduction_value" placeholder="أدخل القيمة"
                            class="form-input" step="0.01" min="0" required
                            value="{{ old('deduction_value', 10) }}">
                        @error('deduction_value')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- الاستقطاع التلقائي -->
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">الاستقطاع التلقائي <span class="text-danger">*</span></span>
                        </label>
                        <select name="auto_deduction" id="auto_deduction" class="form-select" required>
                            <option value="1" {{ old('auto_deduction', '1') == '1' ? 'selected' : '' }}>تفعيل
                                الاستقطاع التلقائي من الراتب/المستحقات</option>
                            <option value="0" {{ old('auto_deduction') == '0' ? 'selected' : '' }}>إيقاف الاستقطاع
                                التلقائي</option>
                        </select>
                        @error('auto_deduction')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- سبب السلفة -->
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">سبب طلب السلفة</span>
                        </label>
                        <input name="reason" id="reason" placeholder="أدخل سبب طلب السلفة (اختياري)" class="form-input"
                            value="{{ old('reason') }}">
                        @error('reason')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- الملاحظات -->
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">ملاحظات</span>
                        </label>
                        <input name="notes" id="notes" placeholder="أدخل الملاحظات إن وجدت" class="form-input"
                            value="{{ old('notes') }}">
                        @error('notes')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- الأزرار -->
                    <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
                        <button type="submit"
                            class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                            <i class="fas fa-check-circle"></i>
                            <span>حفظ السلفة</span>
                        </button>

                        <a href="{{ route('advances.index') }}"
                            class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                            <i class="fas fa-times-circle"></i>
                            <span>إلغاء</span>
                        </a>
                    </div>
                </div>

            </form>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const beneficiaryType = document.getElementById('beneficiary_type');
            const beneficiaryId = document.getElementById('beneficiary_id');
            const beneficiaryLabel = document.getElementById('beneficiary_label');
            const deductionType = document.getElementById('deduction_type');
            const deductionLabel = document.getElementById('deduction_label');
            const advanceForm = document.getElementById('advanceForm');
            const amountInput = document.getElementById('amount');

            // إزالة الفواصل من المبلغ قبل إرسال النموذج
            advanceForm.addEventListener('submit', function(e) {
                if (amountInput.value) {
                    amountInput.value = amountInput.value.replace(/,/g, '');
                }
            });

            // بيانات المستفيدين
            const employeesData = @json($employees ?? []);
            const contractorsData = @json($contractors ?? []);
            const suppliersData = @json($suppliers ?? []);

            // تحديث قائمة المستفيدين
            beneficiaryType.addEventListener('change', function() {
                const type = this.value;
                beneficiaryId.innerHTML = '';

                let data = [];
                let labelText = 'المستفيد';
                let placeholder = 'اختر';

                switch (type) {
                    case 'employee':
                        data = employeesData;
                        labelText = 'الموظف';
                        placeholder = 'اختر الموظف';
                        break;
                    case 'contractor':
                        data = contractorsData;
                        labelText = 'المقاول';
                        placeholder = 'اختر المقاول';
                        break;
                    case 'supplier':
                        data = suppliersData;
                        labelText = 'المورد';
                        placeholder = 'اختر المورد';
                        break;
                    default:
                        placeholder = 'اختر نوع المستفيد أولاً';
                }

                beneficiaryLabel.innerHTML = labelText + ' <span class="text-danger">*</span>';

                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = placeholder;
                beneficiaryId.appendChild(defaultOption);

                data.forEach(function(item) {
                    const option = document.createElement('option');
                    option.value = item.id;
                    if (type === 'employee') {
                        option.textContent = item.fullname || item.name || 'غير معروف';
                    } else if (type === 'contractor') {
                        option.textContent = item.contract_name || item.name || 'غير معروف';
                    } else if (type === 'supplier') {
                        option.textContent = item.supplier_name || item.name || 'غير معروف';
                    }
                    beneficiaryId.appendChild(option);
                });
            });

            // تحديث نوع الاستقطاع
            deductionType.addEventListener('change', function() {
                if (this.value === 'percentage') {
                    deductionLabel.innerHTML = 'نسبة الاستقطاع <span class="text-danger">*</span>';
                } else {
                    deductionLabel.innerHTML = 'قيمة القسط (د.ع) <span class="text-danger">*</span>';
                }
            });
        });

        // تنسيق الأرقام
        function formatNumber(input) {
            let value = input.value.replace(/,/g, '').replace(/[^\d]/g, '');
            if (value) {
                input.value = parseInt(value).toLocaleString('en-US');
            }
        }
    </script>
@endpush
