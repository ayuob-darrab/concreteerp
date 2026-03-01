@extends('layouts.app')

@section('page-title', 'تعديل بطاقة الدفع')

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
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    تعديل بطاقة: {{ $card->card_name }}
                </h5>
            </div>
            <span class="badge badge-outline-{{ $card->status_color }}">{{ $card->status_text }}</span>
        </div>

        <!-- معلومات الرصيد الحالي -->
        <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">الرصيد الافتتاحي</div>
                <div class="font-semibold">{{ number_format($card->opening_balance, 0) }} دينار</div>
            </div>
            <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">الرصيد الحالي</div>
                <div class="font-semibold text-{{ $card->current_balance > 0 ? 'success' : 'danger' }}">
                    {{ number_format($card->current_balance, 0) }} دينار
                </div>
            </div>
            <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b]">
                <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">عدد المعاملات</div>
                <div class="font-semibold">{{ $card->transactions()->count() }}</div>
            </div>
        </div>

        <form action="{{ route('payment-cards.update', $card->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                <!-- نوع البطاقة -->
                <div>
                    <label class="mb-2 block font-semibold">
                        نوع البطاقة <span class="text-danger">*</span>
                    </label>
                    <select name="card_type" class="form-select @error('card_type') is-invalid @enderror" required>
                        <option value="">اختر نوع البطاقة</option>
                        @foreach ($cardTypes as $key => $name)
                            <option value="{{ $key }}"
                                {{ old('card_type', $card->card_type) == $key ? 'selected' : '' }}>
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
                        value="{{ old('card_name', $card->card_name) }}" required>
                    @error('card_name')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- اسم صاحب البطاقة -->
                <div>
                    <label class="mb-2 block font-semibold">
                        اسم صاحب البطاقة <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="holder_name" class="form-input @error('holder_name') is-invalid @enderror"
                        value="{{ old('holder_name', $card->holder_name) }}" required>
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
                        value="{{ old('card_number', $card->card_number) }}" required>
                    @error('card_number')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                    <small class="text-gray-500 dark:text-gray-400">الرقم المخفي: {{ $card->card_number_masked }}</small>
                </div>

                <!-- تاريخ الانتهاء -->
                <div>
                    <label class="mb-2 block font-semibold">
                        تاريخ انتهاء الصلاحية <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="expiry_date" class="form-input @error('expiry_date') is-invalid @enderror"
                        value="{{ old('expiry_date', $card->expiry_date?->format('Y-m-d')) }}" required>
                    @error('expiry_date')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الحالة -->
                <div class="flex items-center gap-2 pt-6">
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_active" class="peer sr-only" value="1"
                            {{ old('is_active', $card->is_active) ? 'checked' : '' }}>
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
                    <textarea name="notes" class="form-textarea @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $card->notes) }}</textarea>
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
                <div class="flex gap-2">
                    <a href="{{ route('payment-cards.show', $card->id) }}" class="btn btn-outline-info">
                        عرض التفاصيل
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" viewBox="0 0 24 24" fill="none">
                            <path d="M5 13L9 17L19 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        حفظ التعديلات
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
