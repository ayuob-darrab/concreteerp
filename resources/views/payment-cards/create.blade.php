@extends('layouts.app')

@section('page-title', 'إضافة بطاقة دفع جديدة')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('payment-cards.index') }}" class="btn btn-outline-secondary btn-sm">
                    <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    رجوع
                </a>
                <h5 class="text-lg font-semibold dark:text-white-light">
                    <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                    إضافة بطاقة دفع جديدة
                </h5>
            </div>
        </div>

        <form action="{{ route('payment-cards.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                <!-- نوع البطاقة -->
                <div>
                    <label class="mb-2 block font-semibold">
                        نوع البطاقة <span class="text-danger">*</span>
                    </label>
                    <select name="card_type" class="form-select @error('card_type') is-invalid @enderror" required>
                        <option value="">اختر نوع البطاقة</option>
                        @foreach ($cardTypes as $key => $name)
                            <option value="{{ $key }}" {{ old('card_type') == $key ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('card_type')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- اسم البطاقة -->
                <div>
                    <label class="mb-2 block font-semibold">
                        اسم البطاقة/الحساب <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="card_name" class="form-input @error('card_name') is-invalid @enderror"
                        value="{{ old('card_name') }}" placeholder="مثال: ماستر كارد الرئيسي" required>
                    @error('card_name')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                    <small class="text-gray-500 dark:text-gray-400">اسم تعريفي للبطاقة</small>
                </div>

                <!-- اسم صاحب البطاقة -->
                <div>
                    <label class="mb-2 block font-semibold">
                        اسم صاحب البطاقة <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="holder_name" class="form-input @error('holder_name') is-invalid @enderror"
                        value="{{ old('holder_name') }}" placeholder="اسم صاحب البطاقة" required>
                    @error('holder_name')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- رقم البطاقة -->
                <div>
                    <label class="mb-2 block font-semibold">
                        رقم البطاقة/الحساب <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="card_number" class="form-input @error('card_number') is-invalid @enderror"
                        value="{{ old('card_number') }}" placeholder="رقم البطاقة أو الحساب" required>
                    @error('card_number')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                    <small class="text-gray-500 dark:text-gray-400">سيتم إخفاء الرقم عند العرض</small>
                </div>

                <!-- الرصيد الافتتاحي -->
                <div>
                    <label class="mb-2 block font-semibold">
                        الرصيد الافتتاحي (دينار) <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="opening_balance_display"
                        class="form-input @error('opening_balance') is-invalid @enderror"
                        value="{{ old('opening_balance') ? number_format(old('opening_balance'), 0) : '' }}"
                        placeholder="0" required>
                    <input type="hidden" name="opening_balance" id="opening_balance"
                        value="{{ old('opening_balance', 0) }}">
                    @error('opening_balance')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                    <small class="text-gray-500 dark:text-gray-400">الافتراضي: 0</small>
                </div>

                <!-- تاريخ الانتهاء -->
                <div>
                    <label class="mb-2 block font-semibold">
                        تاريخ انتهاء الصلاحية <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="expiry_date" class="form-input @error('expiry_date') is-invalid @enderror"
                        value="{{ old('expiry_date') }}" required>
                    @error('expiry_date')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الحالة -->
                <div class="flex items-center gap-2 pt-6">
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_active" class="peer sr-only" value="1" checked>
                        <div
                            class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:top-[2px] after:start-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 dark:border-gray-600 dark:bg-gray-700 dark:peer-focus:ring-primary/30 rtl:peer-checked:after:-translate-x-full">
                        </div>
                        <span class="ms-3 text-sm font-medium">البطاقة نشطة</span>
                    </label>
                </div>

                <!-- ملاحظات -->
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="mb-2 block font-semibold">
                        ملاحظات
                    </label>
                    <textarea name="notes" class="form-textarea @error('notes') is-invalid @enderror" rows="3"
                        placeholder="أي ملاحظات إضافية">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- الأزرار -->
            <div class="mt-6 flex items-center justify-between border-t border-[#e0e6ed] pt-5 dark:border-[#1b2e4b]">
                <a href="{{ route('payment-cards.index') }}" class="btn btn-outline-danger">
                    إلغاء
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" viewBox="0 0 24 24" fill="none">
                        <path d="M5 13L9 17L19 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    حفظ البطاقة
                </button>
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
