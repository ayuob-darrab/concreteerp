@extends('layouts.app')

@section('page-title', 'إضافة شركة جديدة 🏢')

@section('content')
    <div class="space-y-6">


        {{-- بطاقة النموذج --}}
        <div class="panel">
            {{-- رأس البطاقة --}}
            <div class="mb-5 flex items-center justify-between border-b pb-4">
                <h5 class="text-lg font-semibold dark:text-white-light flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>إضافة شركة جديدة</span>
                </h5>
                <a href="{{ route('companies.show', 'ListCompanies') }}" class="btn btn-outline-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block ltr:mr-1 rtl:ml-1" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة للقائمة
                </a>
            </div>

            {{-- النموذج --}}
            <div class="pt-5">
                {!! Form::open([
                    'route' => 'companies.store',
                    'method' => 'POST',
                    'autocomplete' => 'off',
                    'files' => true,
                ]) !!}

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    {{-- اسم الشركة --}}
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">اسم الشركة <span class="text-danger">*</span></span>
                        </label>
                        <input type="text" name="name" placeholder="أدخل اسم الشركة" value="{{ old('name') }}"
                            class="form-input" required>
                        @error('name')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- اسم مدير الشركة --}}
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">اسم مدير الشركة <span class="text-danger">*</span></span>
                        </label>
                        <input type="text" name="managername" placeholder="أدخل اسم مدير الشركة"
                            value="{{ old('managername') }}" class="form-input" required>
                        @error('managername')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- المحافظة --}}
                    <div class="space-y-3">
                        <label for="city_id" class="inline-flex cursor-pointer">
                            <span class="text-white-dark">المحافظة <span class="text-danger">*</span></span>
                        </label>
                        <select name="city_id" id="city_id" class="form-select" required>
                            <option value="" disabled selected>اختر المحافظة</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name_ar }}
                                </option>
                            @endforeach
                        </select>
                        @error('city_id')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- الهاتف --}}
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">الهاتف</span>
                        </label>
                        <input type="text" name="phone" placeholder="أدخل رقم الهاتف" value="{{ old('phone') }}"
                            class="form-input" maxlength="11" minlength="8">
                        @error('phone')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- البريد الإلكتروني --}}
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">البريد الإلكتروني</span>
                        </label>
                        <input type="email" name="email" placeholder="example@domain.com" value="{{ old('email') }}"
                            class="form-input">
                        @error('email')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- عنوان الشركة --}}
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">العنوان</span>
                        </label>
                        <input type="text" name="address" placeholder="أدخل عنوان الشركة" value="{{ old('address') }}"
                            class="form-input">
                        @error('address')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- شعار الشركة --}}
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">شعار الشركة</span>
                        </label>
                        <input type="file" name="logo" class="form-input" accept="image/*">
                        <p class="text-xs text-white-dark mt-1">يفضل أن يكون حجم الصورة 200x200 بكسل</p>
                        @error('logo')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- الملاحظات --}}
                    <div class="space-y-3 lg:col-span-2">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">ملاحظات</span>
                        </label>
                        <textarea name="note" placeholder="أدخل أي ملاحظات" class="form-input" rows="4">{{ old('note') }}</textarea>
                        @error('note')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- سعر إنشاء الشركة --}}
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">سعر إنشاء الشركة</span>
                        </label>
                        @php
                            $creationPriceOld = old('creation_price');
                            $creationPriceDisplay = $creationPriceOld !== null && $creationPriceOld !== '' ? number_format((float) preg_replace('/[^0-9.]/', '', $creationPriceOld), 0, '.', ',') : '';
                        @endphp
                        <input type="text" name="creation_price" id="creation_price" placeholder="مثال: 100,000"
                            value="{{ $creationPriceDisplay }}" class="form-input" inputmode="numeric" autocomplete="off"
                            data-formatted>
                        <p class="text-xs text-white-dark mt-1">اتركه فارغاً أو 0 إذا لم تكن هناك رسوم إنشاء. </p>
                        @error('creation_price')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- الأزرار --}}
                <div class="flex gap-4 justify-center mt-8 pt-5 border-t">
                    <button type="submit" name="active" value="AddNewCompany"
                        class="btn btn-primary flex items-center gap-2 px-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        حفظ الشركة
                    </button>
                    <a href="{{ route('companies.show', 'ListCompanies') }}"
                        class="btn btn-outline-secondary flex items-center gap-2 px-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        إلغاء
                    </a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <script>
        (function() {
            var el = document.getElementById('creation_price');
            if (!el) return;

            function formatWithCommas(val) {
                var parts = String(val).split('.');
                parts[0] = parts[0].replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                return parts.length > 1 ? parts[0] + '.' + parts[1].replace(/\D/g, '').slice(0, 2) : parts[0];
            }

            el.addEventListener('input', function() {
                this.value = formatWithCommas(this.value);
            });
        })();
    </script>
@endsection
