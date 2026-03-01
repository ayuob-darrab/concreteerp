@extends('layouts.app')

@section('page-title', 'اضافة شحنة جديدة لمادة : ' . $material->name)

@section('content')

    {{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">     cols-1 يمثل عد الاعمده --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light text-center">
                    <span>إضافة شحنة جديدة للمادة:</span>
                    <span class="text-primary font-bold"> {{ $material->name }} </span>
                    <span>— التابعة لفرع:</span>
                    <span class="text-primary font-bold"> {{ $material->branchName->branch_name }} </span>
                </h5>

            </div>

            {!! Form::open([
                'route' => ['warehouse.update', $material->code],
                'method' => 'PUT',
                'autocomplete' => 'off',
                'files' => true,
            ]) !!}

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">


                {!! Form::hidden('branch_id', $material->branch_id) !!}
                {!! Form::hidden('material_unit', $material->unit) !!}

                {!! Form::hidden('ReturnUrl', $ReturnUrl) !!}
                <!-- اختيار المورد -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">اسم المورد <span class="text-danger">*</span></span>
                    </label>
                    <select name="supplier_id" id="supplier_id" class="form-select" required>
                        <option value="">اختر المورد</option>
                        @foreach ($Supplier as $sup)
                            <option value="{{ $sup->id }}"
                                {{ old('supplier_id', $material->supplier_id ?? '') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- وحده القياس -->
                <div class="space-y-3">

                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">وحده القياس ({{ $material->unit }})<span
                                class="text-danger">*</span></span>
                    </label>
                    <select name="MaterialEquipment_id" id="MaterialEquipment_id" class="form-select" required
                        onchange="calculateTotal()">
                        <option value="" data-capacity="0" data-unit="">اختر وحده القياس</option>
                        @foreach ($listMaterialEquipment as $item)
                             
                                  
                                   <option value="{{ $item->id }}" {{ $item->capacity }}>
                                    {{ $item->name }} - سعة: {{ $item->capacity }} {{ $item->code }}
                                </option>
                                 
                                
                        @endforeach
                    </select>
                    @error('MaterialEquipment_id')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-3 ">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">العدد <span class="text-danger">*</span></span>
                    </label>
                    <input name="countUnit" type="number" required id="countUnit" placeholder="أدخل العدد"
                        class="form-input" min="1" oninput="calculateTotal()">
                    @error('countUnit')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- السعر -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">السعر الكلي <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" name="price" id="price" placeholder="أدخل السعر" class="form-input"
                        inputmode="decimal" inputmode="numeric" maxlength="8" required oninput="formatPrice(this)">
                    @error('price')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <input type="hidden" name="totalQuantity" id="totalQuantity" value="0">

                <script>
                    function formatPrice(input) {
                        // إزالة الفواصل القديمة
                        let value = input.value.replace(/,/g, '');

                        // منع أي أحرف غير رقمية أو فاصلة عشرية
                        if (!/^\d*\.?\d*$/.test(value)) {
                            input.value = input.value.slice(0, -1);
                            return;
                        }

                        // تقسيم العدد إلى جزء صحيح وعشري
                        const parts = value.split('.');
                        let integerPart = parts[0];
                        const decimalPart = parts[1] ? '.' + parts[1].slice(0, 2) : ''; // رقمين بعد الفاصلة فقط

                        // تنسيق الجزء الصحيح بإضافة الفواصل كل 3 أرقام
                        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

                        input.value = integerPart + decimalPart;
                    }

                    function calculateTotal() {
                        const select = document.getElementById('MaterialEquipment_id');
                        const countInput = document.getElementById('countUnit');
                        const totalHidden = document.getElementById('totalQuantity');

                        const selectedOption = select.options[select.selectedIndex];
                        const capacity = parseFloat(selectedOption.dataset.capacity) || 0;
                        const unit = selectedOption.dataset.unit || '';
                        const count = parseInt(countInput.value) || 0;

                        let total;
                        if (unit === 'ton') {
                            // إذا كانت الوحدة ton، قسّم على 20
                            total = (capacity / 20) * count;
                        } else {
                            // الوحدات الأخرى حساب عادي
                            total = capacity * count;
                        }

                        totalHidden.value = total;
                    }
                </script>


                <!-- الملاحظات -->
                <div class="space-y-3 ">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">ملاحظات الشحنة</span>
                    </label>
                    <input name="note" id="note" placeholder="أدخل الملاحظات إن وجدت" class="form-input">
                    @error('note')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الأزرار -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
                    <button type="submit" name="active" value="AddNewShipment"
                        class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                        <i class="fas fa-check-circle"></i>
                        <span> حفظ معلومات الشحنة</span>
                    </button>

                    <button type="reset"
                        class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                        <i class="fas fa-times-circle"></i>
                        <span>إلغاء</span>
                    </button>
                </div>
            </div>


            {!! Form::close() !!}

        </div>

    </div>
@endsection
