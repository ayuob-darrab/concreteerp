@extends('layouts.app')

@section('page-title', 'تعديل معلومات المقاول' . $Contractor->contract_name)

@section('content')

    {{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">     cols-1 يمثل عد الاعمده --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">معلومات المقاول : {{ $Contractor->contract_name }}
                </h5>
            </div>
            {!! Form::open([
                'route' => ['contractors.update', $Contractor->id ],
                'method' => 'PUT',
                'autocomplete' => 'off',
                'files' => true,
            ]) !!}

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <!-- الفرع -->
                <div class="space-y-3">
                    <label for="branch_id" class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الفرع <span class="text-danger">*</span></span>
                    </label>
                    <select name="branch_id" id="branch_id" class="form-select" required>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @if ($Contractor->branch_id == $branch->id) selected @endif>
                                {{ $branch->branch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- اسم الشركة / المقاول -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">اسم الشركة / المقاول <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" name="contract_name" class="form-input" value="{{ $Contractor->contract_name }}"
                        required>
                </div>

                <!-- اسم المدير المسؤول -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">اسم المدير المسؤول</span>
                    </label>
                    <input type="text" name="contract_adminstarter" class="form-input"
                        value="{{ $Contractor->contract_adminstarter }}">
                </div>

                <!-- الهاتف 1 -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الهاتف 1</span>
                    </label>
                    <input type="text" name="phone1" class="form-input" value="{{ $Contractor->phone1 }}" maxlength="11"
                        oninput="this.value = this.value.replace(/[^0-9]/g,'')">
                </div>

                <!-- الهاتف 2 -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الهاتف 2</span>
                    </label>
                    <input type="text" name="phone2" class="form-input" value="{{ $Contractor->phone2 }}" maxlength="11"
                        oninput="this.value = this.value.replace(/[^0-9]/g,'')">
                </div>

                <!-- الرصيد الافتتاحي -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الرصيد الافتتاحي</span>
                    </label>
                      <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الرصيد الافتتاحي</span>
                    </label>
                    <input type="text" name="opening_balance" class="form-input"
                        value="{{ $Contractor->opening_balance }}"  oninput="formatPrice(this)">
                </div>
                </div>

                <!-- العنوان -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">العنوان</span>
                    </label>
                    <input type="text" name="address" class="form-input" value="{{ $Contractor->address }}">
                </div>

                <!-- الشعار -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">شعار الشركة</span>
                    </label>
                    <input type="file" name="logo" class="form-input" accept="image/*">

                    @if ($Contractor->logo)
                        <img src="{{ asset('uploads/contractors_logo/' . $Contractor->logo) }}" class="h-16 mt-2 rounded">
                    @endif
                </div>

                <!-- الملاحظات -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">ملاحظات</span>
                    </label>
                    <textarea name="note" class="form-input">{{ $Contractor->note }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="btn btn-primary px-6 py-2" name="active" value="UpdateContractor">
                        <i class="fas fa-save"></i>
                        <span>تحديث المقاول</span>
                    </button>

                    <button type="reset" @click="openModal = false" class="btn btn-outline-secondary px-6 py-2">
                        <i class="fas fa-times"></i>
                        <span>إلغاء</span>
                    </button>
                </div>

            </div>
            {!! Form::close() !!}
        </div>

    </div>
@endsection
