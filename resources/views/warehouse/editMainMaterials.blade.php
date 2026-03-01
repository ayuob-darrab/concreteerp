@extends('layouts.app')

@section('page-title', 'تعديل معلومات مادة  : ' . $material->name)

@section('content')

    {{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">     cols-1 يمثل عد الاعمده --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">تعديل معلومات مادة : {{$material->name}}</h5>
            </div>

            {!! Form::open([
                'route' => ['warehouse.update', $material->id],
                'method' => 'PUT',
                'autocomplete' => 'off',
                'files' => true,
            ]) !!}

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <!-- اسم العنصر -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">اسم العنصر <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" name="name" id="name" placeholder="أدخل اسم العنصر"
                        value="{{ $material->name }}" class="form-input" required>
                    @error('name')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <!-- اسم الفرع -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">اسم الفرع </span>
                    </label>
                    <input type="text"  readonly value="{{ $material->branchName->branch_name }}" class="form-input" required>
                </div>

                <!-- كود المادة -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">كود المادة</span>
                    </label>
                    <input type="text"  readonly value="{{ $material->code }}" class="form-input" required>
                </div>

                <!-- الكمية الكلية -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الكمية الكلية</span>
                    </label>
                    <input type="text"  readonly 
                        value="{{ $material->quantity_total }}" class="form-input" required>
                </div>

                <!-- وحدة القياس -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">وحدة القياس <span class="text-danger">*</span></span>
                    </label>

                    <select name="unit" id="unit" class="form-select" required>
                        <option value="">اختر وحدة القياس</option>
                        @foreach ($MeasurementUnit as $unit)
                            <option value="{{ $unit->code }}"{{ $material->unit == $unit->code ? 'selected' : '' }}>{{ $unit->name }} </option>
                        @endforeach
                    </select>

                    @error('unit')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الملاحظات -->
                <div class="space-y-3 ">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">ملاحظات</span>
                    </label>
                    <textarea name="note" id="note" placeholder="أدخل أي ملاحظات" class="form-input">{{ $material->note }}</textarea>
                    @error('note')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الأزرار -->
                <div class="space-y-3 ">
                    <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
                        <button type="submit" name="active" value="EditMainMaterials"
                            class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                            <i class="fas fa-check-circle"></i>
                            <span>تحديث المعلومات</span>
                        </button>

                        <button type="reset" @click="openModal = false"
                            class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                            <i class="fas fa-times-circle"></i>
                            <span>إلغاء</span>
                        </button>
                    </div>
                </div>


            </div>

            {!! Form::close() !!}

        </div>

    </div>
@endsection
