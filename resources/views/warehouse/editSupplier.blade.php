@extends('layouts.app')

@section('page-title', 'تعديل معلومات المورد : ' . $Supplier->supplier_name)

@section('content')

    {{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">     cols-1 يمثل عد الاعمده --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">تعديل معلومات المورد : {{ $Supplier->supplier_name }}
                </h5>
            </div>

            {!! Form::open([
                'route' => ['warehouse.update', $Supplier->id],
                'method' => 'PUT',
                'autocomplete' => 'off',
                'files' => true,
            ]) !!}

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- اسم المورد -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">اسم المورد <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" name="supplier_name" id="supplier_name" placeholder="أدخل اسم المورد"
                        value="{{ $Supplier->supplier_name }}" class="form-input" required>
                    @error('supplier_name')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- اسم الشركة -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">اسم الشركة</span>
                    </label>
                    <input type="text" name="company_name" id="company_name" placeholder="أدخل اسم الشركة"
                        value="{{ $Supplier->company_name }}" class="form-input">
                    @error('company_name')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الفرع -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الفرع <span class="text-danger">*</span></span>
                    </label>
                    <select name="branch_id" id="branch_id" class="form-select" required>
                        <option value="">اختر الفرع</option>
                        @foreach ($Branches as $branch)
                            <option value="{{ $branch->id }}" {{ $branch->id == $Supplier->branch_id ? 'selected' : '' }}>
                                {{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الرصيد الافتتاحي -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الرصيد الافتتاحي</span>
                    </label>
                    <input type="text" placeholder="أدخل الرصيد الافتتاحي" name="opening_balance" id="opening_balance"
                        value="{{ number_format($Supplier->opening_balance, 0, '.', ',') }}" oninput="formatPrice(this)"
                        class="form-input" {{ $hasHistory ? 'readonly' : '' }}>

                    @error('opening_balance')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>


                <!-- رقم الهاتف -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">رقم الهاتف</span>
                    </label>
                    <input type="text" name="phone" id="phone" placeholder="أدخل رقم الهاتف (11 رقمًا)"
                        value="{{ $Supplier->phone }}" class="form-input" maxlength="11" pattern="\d{11}"
                        title="الرجاء إدخال رقم هاتف يتكون من 11 رقمًا فقط"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,11)" required>

                    @error('phone')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- العنوان -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">العنوان</span>
                    </label>
                    <input type="text" name="address" id="address" placeholder="أدخل عنوان المورد"
                        value="{{ $Supplier->address }}" class="form-input">
                    @error('address')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الملاحظات -->
                <div class="space-y-3 lg:col-span-2">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">ملاحظات</span>
                    </label>
                    <textarea name="note" id="note" placeholder="أدخل أي ملاحظات" class="form-input">{{ $Supplier->note }}</textarea>
                    @error('note')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الأزرار -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
                    <button type="submit" name="active" value="UpdateSupplierinformation"
                        class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                        <i class="fas fa-check-circle"></i>
                        <span>تعديل المعلومات</span>
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
