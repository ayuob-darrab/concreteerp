@extends('layouts.app')

@section('page-title', 'إضافة بطاقة دفع جديدة')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('company-payment-cards.index') }}" class="btn btn-outline-secondary btn-sm">
                    ← رجوع
                </a>
                <h5 class="text-lg font-semibold dark:text-white-light">
                    💳 إضافة بطاقة دفع جديدة
                </h5>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger flex items-center mb-4">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('company-payment-cards.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                <!-- نوع البطاقة -->
                <div>
                    <label class="mb-2 block font-semibold">نوع البطاقة <span class="text-danger">*</span></label>
                    <select name="card_type" class="form-select @error('card_type') is-invalid @enderror" required>
                        <option value="">اختر نوع البطاقة</option>
                        @foreach ($cardTypes as $key => $name)
                            <option value="{{ $key }}" {{ old('card_type') == $key ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('card_type')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- اسم البطاقة -->
                <div>
                    <label class="mb-2 block font-semibold">اسم البطاقة/الحساب <span class="text-danger">*</span></label>
                    <input type="text" name="card_name" class="form-input @error('card_name') is-invalid @enderror"
                        value="{{ old('card_name') }}" placeholder="مثال: ماستر كارد الرئيسي" required>
                    @error('card_name')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- اسم صاحب البطاقة -->
                <div>
                    <label class="mb-2 block font-semibold">اسم صاحب البطاقة <span class="text-danger">*</span></label>
                    <input type="text" name="holder_name" class="form-input @error('holder_name') is-invalid @enderror"
                        value="{{ old('holder_name') }}" placeholder="اسم صاحب البطاقة" required>
                    @error('holder_name')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- رقم البطاقة -->
                <div>
                    <label class="mb-2 block font-semibold">رقم البطاقة/الحساب <span class="text-danger">*</span></label>
                    <input type="text" name="card_number" class="form-input @error('card_number') is-invalid @enderror"
                        value="{{ old('card_number') }}" placeholder="رقم البطاقة" required>
                    @error('card_number')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الفرع -->
                <div>
                    <label class="mb-2 block font-semibold">الفرع</label>
                    <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                        <option value="">عام (جميع الفروع)</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->branch_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                    <small class="text-gray-500">اختر فرع محدد أو اتركه عام لجميع الفروع</small>
                </div>

                <!-- الرصيد الافتتاحي -->
                <div>
                    <label class="mb-2 block font-semibold">الرصيد الافتتاحي (دينار)</label>
                    <input type="text" id="opening_balance_display"
                        class="form-input @error('opening_balance') is-invalid @enderror"
                        value="{{ old('opening_balance') ? number_format(old('opening_balance'), 0) : '' }}"
                        placeholder="0">
                    <input type="hidden" name="opening_balance" id="opening_balance" value="{{ old('opening_balance', 0) }}">
                    @error('opening_balance')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- تاريخ الانتهاء -->
                <div>
                    <label class="mb-2 block font-semibold">تاريخ انتهاء الصلاحية</label>
                    <input type="date" name="expiry_date" class="form-input @error('expiry_date') is-invalid @enderror"
                        value="{{ old('expiry_date') }}">
                    @error('expiry_date')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الحالة -->
                <div class="flex items-center gap-2 pt-6">
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_active" class="peer sr-only" value="1" checked>
                        <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:top-[2px] after:start-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all peer-checked:bg-primary peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full"></div>
                        <span class="ms-3 text-sm font-medium">البطاقة نشطة</span>
                    </label>
                </div>

                <!-- ملاحظات -->
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="mb-2 block font-semibold">ملاحظات</label>
                    <textarea name="notes" class="form-textarea" rows="3" placeholder="أي ملاحظات إضافية">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between border-t border-[#e0e6ed] pt-5 dark:border-[#1b2e4b]">
                <a href="{{ route('company-payment-cards.index') }}" class="btn btn-outline-danger">إلغاء</a>
                <button type="submit" class="btn btn-primary">✅ حفظ البطاقة</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const displayInput = document.getElementById('opening_balance_display');
            const hiddenInput = document.getElementById('opening_balance');
            displayInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/,/g, '').replace(/[^0-9]/g, '');
                hiddenInput.value = value;
                if (value) {
                    e.target.value = Number(value).toLocaleString('en-US');
                } else {
                    e.target.value = '';
                }
            });
        });
    </script>
@endsection
