@extends('layouts.app')

@section('page-title', 'إضافة سيارة جديدة')

@section('content')
    <div class="panel mt-6" x-data="createCarForm()">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    🚗 إضافة سيارة جديدة
                </h2>
                <p class="text-gray-500 text-sm mt-1">قم بإدخال بيانات السيارة الجديدة</p>
            </div>
            <a href="{{ $returnUrl ?: url('cars/ListCar') }}" class="btn btn-outline-secondary flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>رجوع</span>
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-6">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mb-6">{{ session('error') }}</div>
        @endif

        <form action="{{ route('cars.store') }}" method="POST" autocomplete="off">
            @csrf
            <input type="hidden" name="active" value="AddnewCar">
            <input type="hidden" name="return_url" value="{{ $returnUrl }}">

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الفرع <span class="text-danger">*</span></span>
                    </label>
                    <select name="branch_id" class="form-select" required>
                        <option value="" disabled selected>اختر الفرع</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">نوع السيارة <span class="text-danger">*</span></span>
                    </label>
                    <select name="car_type_id" class="form-select" required x-model="selectedCarType"
                        @change="onCarTypeChange()">
                        <option value="" disabled selected>اختر نوع السيارة</option>
                        @foreach ($carstype as $type)
                            <option value="{{ $type->id }}" data-code="{{ $type->code }}"
                                {{ old('car_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">اسم السيارة <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" name="car_name" placeholder="مثال: مارسيدس" value="{{ old('car_name') }}"
                        class="form-input" required>
                </div>

                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">رقم السيارة <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" name="car_number" placeholder="أدخل رقم السيارة" value="{{ old('car_number') }}"
                        class="form-input" required>
                </div>

                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">موديل السيارة <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" name="car_model" placeholder="أدخل موديل السيارة" value="{{ old('car_model') }}"
                        class="form-input" required>
                </div>

                <div class="space-y-3" x-show="isMixer" x-transition>
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">سعة الخلاطة (م³) <span class="text-danger">*</span></span>
                    </label>
                    <input type="number" step="0.1" name="mixer_capacity" value="{{ old('mixer_capacity') }}"
                        class="form-input" :required="isMixer" placeholder="مثال: 6">
                </div>

                <div class="space-y-3" x-show="isPump" x-transition>
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">طول الخرطوم (متر) <span class="text-danger">*</span></span>
                    </label>
                    <input type="number" step="0.1" name="hose_length" value="{{ old('hose_length') }}"
                        class="form-input" :required="isPump" placeholder="مثال: 36">
                </div>

                <div class="space-y-3 lg:col-span-2">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">ملاحظات</span>
                    </label>
                    <textarea name="note" class="form-input" rows="3" placeholder="أدخل أي ملاحظات">{{ old('note') }}</textarea>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-4 border-t pt-4 lg:col-span-2">
                    <button type="reset" class="btn btn-outline-secondary w-full sm:w-auto">إلغاء</button>
                    <button type="submit" class="btn btn-primary w-full sm:w-auto">حفظ السيارة</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function createCarForm() {
            return {
                selectedCarType: '{{ old('car_type_id') }}',
                isMixer: false,
                isPump: false,
                onCarTypeChange() {
                    const select = document.querySelector('select[name="car_type_id"]');
                    const option = select?.options?.[select.selectedIndex];
                    const code = (option?.dataset?.code || '').toString();
                    this.isMixer = code === 'CT-MIXER';
                    this.isPump = code === 'CT-PUMP';
                },
                init() {
                    this.onCarTypeChange();
                }
            }
        }
    </script>
@endsection

