@extends('layouts.app')

@section('page-title', 'تعديل مواد')

@section('content')

    {{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">     cols-1 يمثل عد الاعمده --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <h3 class="text-center text-2xl font-bold dark:text-white-light">
            تعديل معلومات المادة : {{ $material->name }}
        </h3>
    </div>


    {!! Form::open([
        'route' => ['materials.update', $material->id],
        'method' => 'PUT',
        'autocomplete' => 'off',
        'files' => true,
    ]) !!}

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Registration -->

        <!-- اسم المادة -->
        <div class="panel">
            <div class="space-y-3">
                <label class="inline-flex cursor-pointer">
                    <span class="text-white-dark">اسم المادة <span class="text-danger">*</span></span>
                </label>
                <input type="text" name="material_name" id="material_name" placeholder="أدخل اسم المادة"
                    value="{{ $material->name }}" class="form-input" required>
                @error('material_name')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- سعر المادة في المتر المربع -->

        <div class="panel">
            <div class="space-y-3">
                <label class="inline-flex cursor-pointer">
                    <span class="text-white-dark">
                        سعر المادة في المتر المربع <span class="text-danger">*</span>
                    </span>
                </label>
                <input type="text" name="price" id="price" placeholder="أدخل السعر بالمتر المربع"
                    value="{{ $material->price }}" class="form-input" required>
                <div id="price-error" class="text-danger text-sm hidden"></div>

                @error('price')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <script>
            const priceInput = document.getElementById('price');
            const priceError = document.getElementById('price-error');

            priceInput.addEventListener('input', function() {
                // إزالة الفواصل القديمة
                let value = this.value.replace(/,/g, '');

                // السماح فقط بالأرقام والفاصلة العشرية
                if (!/^\d*\.?\d*$/.test(value)) {
                    this.value = this.value.slice(0, -1);
                    return;
                }

                // الحد الأعلى لعدد الأرقام قبل الفاصلة العشرية = 6
                const parts = value.split('.');
                if (parts[0].length > 6) {
                    parts[0] = parts[0].slice(0, 6);
                    value = parts.join('.');
                }

                // تنسيق الرقم بفواصل
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                this.value = parts.join('.');

                // التحقق من النطاق المسموح به (1000 إلى 999999)
                const numericValue = parseFloat(value);
                if (numericValue < 1000) {
                    priceError.textContent = '⚠️ السعر يجب أن لا يقل عن 4 أرقام (1000).';
                    priceError.classList.remove('hidden');
                } else if (numericValue > 999999) {
                    priceError.textContent = '⚠️ السعر يجب أن لا يزيد عن 6 أرقام (999,999).';
                    priceError.classList.remove('hidden');
                } else {
                    priceError.classList.add('hidden');
                }
            });

            // التحقق قبل إرسال النموذج
            document.querySelector('form')?.addEventListener('submit', function(e) {
                const numericValue = parseFloat(priceInput.value.replace(/,/g, ''));
                if (numericValue < 1000 || numericValue > 999999 || isNaN(numericValue)) {
                    e.preventDefault();
                    priceError.textContent = '⚠️ السعر يجب أن يكون بين 1,000 و 999,999.';
                    priceError.classList.remove('hidden');
                    priceInput.focus();
                }
            });
        </script>



        <!-- الأزرار -->

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
            <button type="submit" name="active" value="UpdateMaterials" class="btn btn-primary !mt-6 px-8">
                <i class="fas fa-check-circle me-2"></i> تعديل المادة
            </button>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
            <button type="reset" class="btn btn-outline-secondary !mt-6 px-8">
                <i class="fas fa-times-circle me-2"></i> إلغاء
            </button>
        </div>




    </div>
    {!! Form::close() !!}
@endsection
