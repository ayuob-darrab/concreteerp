@extends('layouts.app')

@section('page-title', 'تعديل بطاقة الدفع')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('company-payment-cards.index') }}" class="btn btn-outline-secondary btn-sm">← رجوع</a>
                <h5 class="text-lg font-semibold dark:text-white-light">✏️ تعديل بطاقة الدفع</h5>
            </div>
        </div>

        <form action="{{ route('company-payment-cards.update', $card->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="mb-2 block font-semibold">نوع البطاقة <span class="text-danger">*</span></label>
                    <select name="card_type" class="form-select" required>
                        @foreach ($cardTypes as $key => $name)
                            <option value="{{ $key }}" {{ $card->card_type == $key ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block font-semibold">اسم البطاقة <span class="text-danger">*</span></label>
                    <input type="text" name="card_name" class="form-input" value="{{ $card->card_name }}" required>
                </div>
                <div>
                    <label class="mb-2 block font-semibold">اسم صاحب البطاقة <span class="text-danger">*</span></label>
                    <input type="text" name="holder_name" class="form-input" value="{{ $card->holder_name }}" required>
                </div>
                <div>
                    <label class="mb-2 block font-semibold">رقم البطاقة <span class="text-danger">*</span></label>
                    <input type="text" name="card_number" class="form-input" value="{{ $card->card_number }}" required>
                </div>
                <div>
                    <label class="mb-2 block font-semibold">الفرع</label>
                    <select name="branch_id" class="form-select">
                        <option value="">عام (جميع الفروع)</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $card->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block font-semibold">تاريخ الانتهاء</label>
                    <input type="date" name="expiry_date" class="form-input" value="{{ $card->expiry_date ? $card->expiry_date->format('Y-m-d') : '' }}">
                </div>
                <div class="flex items-center gap-2 pt-6">
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_active" class="peer sr-only" value="1" {{ $card->is_active ? 'checked' : '' }}>
                        <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:top-[2px] after:start-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all peer-checked:bg-primary peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full"></div>
                        <span class="ms-3 text-sm font-medium">البطاقة نشطة</span>
                    </label>
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="mb-2 block font-semibold">ملاحظات</label>
                    <textarea name="notes" class="form-textarea" rows="3">{{ $card->notes }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between border-t border-[#e0e6ed] pt-5 dark:border-[#1b2e4b]">
                <a href="{{ route('company-payment-cards.index') }}" class="btn btn-outline-danger">إلغاء</a>
                <button type="submit" class="btn btn-primary">✅ تحديث البطاقة</button>
            </div>
        </form>
    </div>
@endsection
