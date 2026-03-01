@extends('layouts.app')

@section('page-title', 'اضافة شحنة جديدة لمادة : ' . $Chemical->name)

@section('content')


    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light text-center">
                    <span>إضافة شحنة جديدة للمادة:</span>
                    <span class="text-primary font-bold"> {{ $Chemical->name }} </span>
                    <span>— التابعة لفرع:</span>
                    <span class="text-primary font-bold"> {{ $Chemical->branchName->branch_name }} </span>
                </h5>

            </div>

            {!! Form::open([
                'route' => ['warehouse.update', $Chemical->id],
                'method' => 'PUT',
                'autocomplete' => 'off',
                'files' => true,
            ]) !!}

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">


                {!! Form::hidden('branch_id', $Chemical->branch_id) !!}
                {!! Form::hidden('material_unit', $Chemical->unit) !!}

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
                                {{ old('supplier_id', $Chemical->supplier_id ?? '') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الكمية الكلية -->
                <div class="space-y-3">

                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الكمية الكلية {{ $Chemical->unit }}<span
                                class="text-danger">*</span></span>
                    </label>
                    <select name="MaterialEquipment_id" id="MaterialEquipment_id" class="form-select" required>
                        <option value="">اختر الكمية</option>
                        @foreach ($listMaterialEquipment as $item)
                            @if ($Chemical->unit == $item->code)
                                <option value="{{ $item->id }}">{{ $item->name }} - {{ $item->code }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('MaterialEquipment_id')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-3 ">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الكمية</span>
                    </label>
                    <input name="countUnit" type="number" min="1" step="any" required id="countUnit"
                        placeholder="اختر الكمية" class="form-input">
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
                        inputmode="decimal"  inputmode="numeric" maxlength="8" required oninput="formatPrice(this)">
                    @error('price')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                


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
                    <button type="submit" name="active" value="AddNewChemicalShipment"
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
